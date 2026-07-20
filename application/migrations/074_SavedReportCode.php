<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Displayable report code (e.g. RPT-D-A3F2B19C) that changes when rate/content differs.
 */
class Migration_SavedReportCode extends CI_Migration
{
    public function up()
    {
        if (!$this->db->table_exists('saved_reports')) {
            return;
        }

        if (!$this->db->field_exists('report_code', 'saved_reports')) {
            $this->db->query("ALTER TABLE `saved_reports`
                ADD COLUMN `report_code` VARCHAR(32) NULL DEFAULT NULL AFTER `uuid`,
                ADD UNIQUE KEY `uq_saved_reports_code` (`report_code`)");
        }

        $rows = $this->db->select('*')->from('saved_reports')->where('report_code IS NULL', null, false)->get()->result();
        foreach ($rows as $row) {
            $code = $this->buildCode($row);
            // Ensure uniqueness if collision
            $n = 0;
            $candidate = $code;
            while ($this->codeExists($candidate, (int) $row->id)) {
                $n++;
                $candidate = $code . substr(strtoupper(dechex($n)), -1);
            }
            $this->db->where('id', (int) $row->id)->update('saved_reports', ['report_code' => $candidate]);
        }
    }

    public function down()
    {
        if ($this->db->table_exists('saved_reports') && $this->db->field_exists('report_code', 'saved_reports')) {
            $this->db->query("ALTER TABLE `saved_reports` DROP INDEX `uq_saved_reports_code`, DROP COLUMN `report_code`");
        }
    }

    private function buildCode($row)
    {
        $type = ($row->report_type === 'client') ? 'C' : 'D';
        $payload = implode('|', [
            $row->report_type,
            $row->date_from,
            $row->date_to,
            (string) $row->developer_id,
            (string) $row->customer_id,
            (string) $row->project_id,
            (string) $row->sprint_id,
            (string) $row->billable_only,
            number_format((float) $row->rate, 2, '.', ''),
            $row->currency,
            (string) $row->total_minutes,
            number_format((float) $row->total_amount, 2, '.', ''),
            $row->uuid,
        ]);
        $hash = strtoupper(substr(sha1($payload), 0, 8));
        return 'RPT-' . $type . '-' . $hash;
    }

    private function codeExists($code, $excludeId)
    {
        $this->db->select('id')->from('saved_reports')->where('report_code', $code);
        if ($excludeId > 0) {
            $this->db->where('id !=', $excludeId);
        }
        return (bool) $this->db->get()->row();
    }
}
