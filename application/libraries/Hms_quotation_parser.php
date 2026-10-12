<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Splits the line items of an HMS quotation into task candidates.
 *
 * A line's details are free HTML, so every shape in use is read:
 *  - bulleted / numbered lists: each top-level item is a task, nested items go to its description
 *  - tables: each row is a task; a row holding one bold cell is a section heading, and a bold first
 *    row naming its columns (Section / Task Description / Notes / Hours) maps those columns
 *  - plain text with "-", "*", "•" or "1." at the start of a line
 *  - anything else: the whole line is one task
 */
class Hms_quotation_parser
{
    const NAME_MAX = 255;

    /**
     * @param array $quotation As returned by HMS api/tweezzo/quotation
     * @return array One entry per quotation line: product, sku, qty, unit, original (plain text),
     *               recurring (bool) and tasks[] of section, name, description, hours (null when unknown)
     */
    public function parse(array $quotation)
    {
        $lines = [];
        foreach (isset($quotation['lines']) ? (array) $quotation['lines'] : [] as $line) {
            $lines[] = $this->parseLine((array) $line);
        }
        return $lines;
    }

    public function parseLine(array $line)
    {
        $product = trim((string) ($line['product'] ?? ''));
        $unit = strtolower(trim((string) ($line['unit'] ?? '')));
        $qty = (float) ($line['qty'] ?? 0);
        $details = (string) ($line['details'] ?? '');
        $defaultSection = ($product !== '') ? $product : 'Quotation';

        $tasks = $this->fromHtml($details);
        if (empty($tasks)) {
            $tasks = $this->fromBulletLines($details);
        }
        if (empty($tasks)) {
            $tasks = $this->wholeLine($details, $product);
        }

        // Hours sold on the line are shared out, unless the line gives hours per task itself
        $hasOwnHours = false;
        foreach ($tasks as $task) {
            if ($task['hours'] !== null) {
                $hasOwnHours = true;
                break;
            }
        }
        $shared = null;
        if (!$hasOwnHours && $qty > 0 && count($tasks) > 0 && in_array($unit, ['hour', 'hours', 'hr', 'hrs'], true)) {
            $shared = round($qty / count($tasks), 2);
        }

        foreach ($tasks as $i => $task) {
            if ($task['section'] === '') {
                $tasks[$i]['section'] = $defaultSection;
            }
            if ($task['hours'] === null) {
                $tasks[$i]['hours'] = $shared;
            }
            $tasks[$i] = $this->fitName($tasks[$i]);
            $tasks[$i]['section'] = mb_substr($tasks[$i]['section'], 0, self::NAME_MAX);
        }

        return [
            'id' => $line['id'] ?? null,
            'sku' => (string) ($line['sku'] ?? ''),
            'product' => $product,
            'qty' => $qty,
            'unit' => $unit,
            // Hosting, domains and licences renew by the month or year: rarely work to be planned
            'recurring' => in_array($unit, ['year', 'years', 'month', 'months'], true),
            'original' => implode("\n", $this->textLines($details)),
            'tasks' => array_values($tasks),
        ];
    }

    /* ---------------------------------------------------------------- lists and tables */

    private function fromHtml($html)
    {
        if (stripos($html, '<li') === false && stripos($html, '<tr') === false) {
            return [];
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"><div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        $xpath = new DOMXPath($dom);

        $tasks = [];
        $blocks = $xpath->query('//*[self::ul or self::ol or self::table][not(ancestor::ul or ancestor::ol or ancestor::table)]');
        foreach ($blocks as $block) {
            if (strtolower($block->nodeName) === 'table') {
                $tasks = array_merge($tasks, $this->fromTable($block, $xpath));
            } else {
                $tasks = array_merge($tasks, $this->fromList($block, $xpath, $this->headingBefore($block, $xpath)));
            }
        }
        return $tasks;
    }

    private function fromList(DOMNode $list, DOMXPath $xpath, $section)
    {
        $tasks = [];
        foreach ($xpath->query('./li', $list) as $li) {
            $name = $this->ownText($li);
            $nested = $xpath->query('./ul | ./ol', $li);

            if ($name === '') {
                // An item that only wraps a sub-list: its children are the tasks
                foreach ($nested as $sub) {
                    $tasks = array_merge($tasks, $this->fromList($sub, $xpath, $section));
                }
                continue;
            }

            $children = [];
            foreach ($nested as $sub) {
                foreach ($xpath->query('.//li', $sub) as $child) {
                    $text = $this->ownText($child);
                    if ($text !== '') {
                        $children[] = '- ' . $text;
                    }
                }
            }
            $tasks[] = $this->task($section, $name, implode("\n", $children));
        }
        return $tasks;
    }

    /** A short bold or colon-ended paragraph right above a list names its section. */
    private function headingBefore(DOMNode $list, DOMXPath $xpath)
    {
        $prev = $list->previousSibling;
        while ($prev && !($prev instanceof DOMElement) && $this->clean($prev->textContent) === '') {
            $prev = $prev->previousSibling;
        }
        if (!$prev) {
            return '';
        }
        $text = $this->clean($prev->textContent);
        if ($text === '' || mb_strlen($text) > 80) {
            return '';
        }
        $bold = ($prev instanceof DOMElement) && $this->isBold($prev, $xpath);
        if ($bold || substr($text, -1) === ':') {
            return rtrim($text, ': ');
        }
        return '';
    }

    private function fromTable(DOMNode $table, DOMXPath $xpath)
    {
        $tasks = [];
        $section = '';
        $columns = null;
        $first = true;

        foreach ($xpath->query('.//tr', $table) as $tr) {
            if ($this->closest($tr, 'table') !== $table) {
                continue; // row of a nested table, already part of its cell's text
            }

            $cells = [];
            $bold = [];
            foreach ($xpath->query('./td | ./th', $tr) as $cell) {
                $cells[] = $this->clean($cell->textContent);
                $bold[] = (strtolower($cell->nodeName) === 'th') || $this->isBold($cell, $xpath);
            }
            $filled = array_keys(array_filter($cells, 'strlen'));
            if (empty($filled)) {
                continue;
            }

            if ($first) {
                $first = false;
                $map = $this->headerColumns($cells, $bold, $filled);
                if ($map !== null) {
                    $columns = $map;
                    continue;
                }
            }

            if ($columns !== null) {
                $name = $cells[$columns['task']] ?? '';
                if ($name === '') {
                    continue;
                }
                $rowSection = ($columns['section'] !== null) ? ($cells[$columns['section']] ?? '') : '';
                $notes = ($columns['notes'] !== null) ? ($cells[$columns['notes']] ?? '') : '';
                $hours = ($columns['hours'] !== null) ? $this->number($cells[$columns['hours']] ?? '') : null;
                $tasks[] = $this->task($rowSection !== '' ? $rowSection : $section, $name, $notes, $hours);
                continue;
            }

            if (count($filled) === 1 && $bold[$filled[0]]) {
                $section = rtrim($cells[$filled[0]], ': ');
                continue;
            }

            $name = $cells[array_shift($filled)];
            $rest = [];
            foreach ($filled as $i) {
                $rest[] = $cells[$i];
            }
            $tasks[] = $this->task($section, $name, implode("\n", $rest));
        }
        return $tasks;
    }

    /** Column positions when the first row is a bold header naming a task column; null otherwise. */
    private function headerColumns(array $cells, array $bold, array $filled)
    {
        if (count($filled) < 2) {
            return null;
        }
        foreach ($filled as $i) {
            if (!$bold[$i]) {
                return null;
            }
        }

        $patterns = [
            'section' => '/section|module|area|category|page|screen/i',
            'hours' => '/hour|hrs|effort|duration/i',
            'notes' => '/note|remark|comment/i',
            'task' => '/task|description|item|feature|change|requirement|work|detail/i',
        ];
        $map = ['section' => null, 'task' => null, 'notes' => null, 'hours' => null];
        foreach ($filled as $i) {
            foreach ($patterns as $key => $pattern) {
                if ($map[$key] === null && preg_match($pattern, $cells[$i])) {
                    $map[$key] = $i;
                    break;
                }
            }
        }
        return ($map['task'] === null) ? null : $map;
    }

    /* ---------------------------------------------------------------- plain text */

    private function fromBulletLines($html)
    {
        $tasks = [];
        $section = '';
        foreach ($this->textLines($html) as $line) {
            if (preg_match('/^(?:[\-\*\x{2022}\x{00B7}\x{2013}\x{2014}]|\d{1,3}[\.\)])\s+(.+)$/u', $line, $m)) {
                $tasks[] = $this->task($section, trim($m[1]));
            } elseif (substr($line, -1) === ':' && mb_strlen($line) <= 80) {
                $section = rtrim($line, ': ');
            }
        }
        return $tasks;
    }

    private function wholeLine($html, $product)
    {
        $lines = $this->textLines($html);
        if (empty($lines)) {
            return ($product === '') ? [] : [$this->task('', $product)];
        }

        $section = '';
        if (count($lines) > 1 && substr($lines[0], -1) === ':' && mb_strlen($lines[0]) <= 80) {
            $section = rtrim(array_shift($lines), ': ');
        }
        $name = array_shift($lines);
        return [$this->task($section, $name, implode("\n", $lines))];
    }

    /** The details as plain text, one entry per visible line. */
    private function textLines($html)
    {
        $text = preg_replace('#<\s*br\s*/?\s*>|</\s*(p|div|li|tr|h[1-6]|table|ul|ol)\s*>#i', "\n", (string) $html);
        $text = preg_replace('#</\s*t[dh]\s*>#i', ' ', $text);
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $lines = [];
        foreach (preg_split('/\R/u', $text) as $line) {
            $line = $this->clean($line);
            if ($line !== '') {
                $lines[] = $line;
            }
        }
        return $lines;
    }

    /* ---------------------------------------------------------------- helpers */

    private function task($section, $name, $description = '', $hours = null)
    {
        return [
            'section' => trim((string) $section),
            'name' => trim((string) $name),
            'description' => trim((string) $description),
            'hours' => $hours,
        ];
    }

    /** tasks.name holds 255 characters: a longer item keeps its full text in the description. */
    private function fitName(array $task)
    {
        if (mb_strlen($task['name']) > self::NAME_MAX) {
            $task['description'] = trim($task['name'] . "\n\n" . $task['description']);
            $task['name'] = mb_substr($task['name'], 0, self::NAME_MAX - 3) . '...';
        }
        return $task;
    }

    /** Collapses whitespace, non-breaking spaces included. */
    private function clean($text)
    {
        return trim(preg_replace('/[\s\x{00A0}]+/u', ' ', (string) $text));
    }

    /** Text of a list item without the text of the lists nested in it. */
    private function ownText(DOMNode $node)
    {
        $text = '';
        foreach ($node->childNodes as $child) {
            if ($child instanceof DOMElement && in_array(strtolower($child->nodeName), ['ul', 'ol'], true)) {
                continue;
            }
            $text .= ' ' . (($child instanceof DOMElement) ? $this->ownTextDeep($child) : $child->textContent);
        }
        return $this->clean($text);
    }

    private function ownTextDeep(DOMElement $element)
    {
        $text = '';
        foreach ($element->childNodes as $child) {
            if ($child instanceof DOMElement) {
                if (in_array(strtolower($child->nodeName), ['ul', 'ol'], true)) {
                    continue;
                }
                $text .= ' ' . $this->ownTextDeep($child);
            } else {
                $text .= $child->textContent;
            }
        }
        return $text;
    }

    /** True when all of the element's text sits inside bold tags. */
    private function isBold(DOMElement $element, DOMXPath $xpath)
    {
        $all = $this->clean($element->textContent);
        if ($all === '') {
            return false;
        }
        if (in_array(strtolower($element->nodeName), ['b', 'strong', 'th', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true)) {
            return true;
        }
        $bold = '';
        foreach ($xpath->query('.//*[self::b or self::strong][not(ancestor::b or ancestor::strong)]', $element) as $b) {
            $bold .= ' ' . $b->textContent;
        }
        // Compared without spaces, which differ between adjacent bold runs
        return str_replace(' ', '', $this->clean($bold)) === str_replace(' ', '', $all);
    }

    private function closest(DOMNode $node, $tag)
    {
        for ($parent = $node->parentNode; $parent; $parent = $parent->parentNode) {
            if (strtolower($parent->nodeName) === $tag) {
                return $parent;
            }
        }
        return null;
    }

    private function number($text)
    {
        $text = str_replace(',', '.', trim((string) $text));
        return preg_match('/^\d+(\.\d+)?/', $text, $m) ? (float) $m[0] : null;
    }
}
