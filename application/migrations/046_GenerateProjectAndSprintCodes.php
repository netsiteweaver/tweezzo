<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_GenerateProjectAndSprintCodes extends CI_Migration {

    /**
     * Generate a short code from a project name (e.g. "Website Redesign" -> "WR").
     */
    private function _projectCodeFromName($name)
    {
        $name = trim(preg_replace('/[^a-zA-Z0-9\s]/', '', $name));
        if ($name === '') {
            return 'P';
        }
        $words = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY);
        if (count($words) >= 2) {
            $code = '';
            foreach (array_slice($words, 0, 4) as $w) {
                $code .= strtoupper(substr($w, 0, 1));
            }
            return substr($code, 0, 5);
        }
        return strtoupper(substr($name, 0, min(5, strlen($name))));
    }

    public function up()
    {
        // Generate codes for projects that have none
        $projects = $this->db->select('id, name')
            ->from('projects')
            ->where('code IS NULL')
            ->get()
            ->result();

        $used_codes = [];
        foreach ($projects as $p) {
            $base = $this->_projectCodeFromName($p->name);
            $code = $base;
            $n = 1;
            while (in_array($code, $used_codes, true)) {
                $code = $base . $n;
                $n++;
            }
            $used_codes[] = $code;
            $this->db->where('id', $p->id)->update('projects', ['code' => substr($code, 0, 20)]);
        }

        // Generate codes for sprints that have none (S1, S2, S3... per project)
        $sprints = $this->db->select('s.id, s.project_id')
            ->from('sprints s')
            ->where('s.code IS NULL')
            ->order_by('s.project_id ASC, s.id ASC')
            ->get()
            ->result();

        $next_per_project = [];
        foreach ($sprints as $s) {
            if (!isset($next_per_project[$s->project_id])) {
                $next_per_project[$s->project_id] = 1;
            }
            $code = 'S' . $next_per_project[$s->project_id];
            $next_per_project[$s->project_id]++;
            $this->db->where('id', $s->id)->update('sprints', ['code' => $code]);
        }
    }

    public function down()
    {
        // Clear generated codes only where we might want to re-run; optionally no-op.
        // Uncomment below to reset codes to NULL (breaks task_ref display until re-run or manual set).
        // $this->db->query("UPDATE projects SET code = NULL");
        // $this->db->query("UPDATE sprints SET code = NULL");
    }
}
