<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_model extends CI_Model
{
    /**
     * Developer (freelance) report — one row per completed timesheet entry.
     *
     * @param array $filters developer_id (required), from, to, customer_id, project_id, sprint_id
     * @return array
     */
    public function developerTimesheetRows($filters = [])
    {
        $this->load->model('Timesheets_model');
        if (empty($filters['developer_id'])) {
            return [];
        }
        return $this->Timesheets_model->adminFetch($filters);
    }

    /**
     * Client report — hours grouped by task (timesheet duration only).
     *
     * @param array $filters customer_id (required), from, to, project_id, sprint_id, billable_only
     * @return array
     */
    public function clientTaskHours($filters = [])
    {
        if (empty($filters['customer_id'])) {
            return [];
        }

        $from = !empty($filters['from']) ? $filters['from'] : date('Y-m-01');
        $to = !empty($filters['to']) ? $filters['to'] : date('Y-m-t');

        $query = "SELECT
                        t2.id taskId,
                        t2.uuid taskUuid,
                        t2.name taskName,
                        t2.task_number taskNumber,
                        t2.section taskSection,
                        t2.billable,
                        t2.work_type workType,
                        s.id sprintId,
                        s.name sprintName,
                        s.code sprintCode,
                        p.id projectId,
                        p.name projectName,
                        p.code projectCode,
                        c.customer_id customerId,
                        c.company_name customerName,
                        SUM(
                            COALESCE(
                                NULLIF(t.duration_minutes, 0),
                                TIMESTAMPDIFF(MINUTE, t.start_time, t.finish_time)
                            )
                        ) AS totalMinutes,
                        COUNT(t.id) AS entryCount
                    FROM timesheet t
                    JOIN tasks t2 ON t2.id = t.task_id
                    JOIN sprints s ON s.id = t2.sprint_id
                    JOIN projects p ON p.id = s.project_id
                    JOIN customers c ON c.customer_id = p.customer_id
                    WHERE t.status = 1
                    AND t.finish_time IS NOT NULL
                    AND t2.status = 1
                    AND s.status = 1
                    AND p.status = 1
                    AND c.status = 1
                    AND c.customer_id = " . (int) $filters['customer_id'] . "
                    AND t.start_time >= " . $this->db->escape($from . ' 00:00:00') . "
                    AND t.finish_time <= " . $this->db->escape($to . ' 23:59:59');

        if (!empty($filters['project_id'])) {
            $query .= " AND p.id = " . (int) $filters['project_id'];
        }
        if (!empty($filters['sprint_id'])) {
            $query .= " AND s.id = " . (int) $filters['sprint_id'];
        }
        if (!empty($filters['billable_only'])) {
            $query .= " AND t2.billable = 1";
        }

        $query .= " GROUP BY t2.id, t2.uuid, t2.name, t2.task_number, t2.section, t2.billable, t2.work_type,
                            s.id, s.name, s.code, p.id, p.name, p.code, c.customer_id, c.company_name
                    ORDER BY p.name ASC, s.name ASC, t2.task_number ASC";

        $rows = $this->db->query($query)->result();

        if (!function_exists('task_ref')) {
            get_instance()->load->helper('general');
        }
        foreach ($rows as $row) {
            $tn = isset($row->taskNumber) ? $row->taskNumber : '';
            $row->taskRef = $tn !== ''
                ? task_ref(isset($row->projectCode) ? $row->projectCode : null, isset($row->sprintCode) ? $row->sprintCode : null, $tn)
                : '';
            $row->totalMinutes = (int) $row->totalMinutes;
            $row->totalHours = round($row->totalMinutes / 60, 2);
        }

        return $rows;
    }

    /**
     * Minutes for a timesheet row (duration_minutes or start/finish diff).
     *
     * @param object $row
     * @return int
     */
    public function rowMinutes($row)
    {
        if (!empty($row->duration_minutes)) {
            return (int) $row->duration_minutes;
        }
        if (!empty($row->start_time) && !empty($row->finish_time)) {
            $diff = strtotime($row->finish_time) - strtotime($row->start_time);
            return $diff > 0 ? (int) floor($diff / 60) : 0;
        }
        return 0;
    }

    /**
     * Format minutes as H:MM.
     *
     * @param int $minutes
     * @return string
     */
    public function formatDuration($minutes)
    {
        $minutes = max(0, (int) $minutes);
        return sprintf('%d:%02d', floor($minutes / 60), $minutes % 60);
    }

    /**
     * @param float $hours
     * @param float $rate
     * @return float
     */
    public function amount($hours, $rate)
    {
        return round((float) $hours * (float) $rate, 2);
    }

    /**
     * Persist a generated report snapshot.
     *
     * @param string $reportType developer|client
     * @param array  $header
     * @param array  $lines
     * @return array{result:bool, uuid?:string, id?:int, reason?:string}
     */
    public function saveReport($reportType, $header, $lines)
    {
        if (!in_array($reportType, ['developer', 'client'], true)) {
            return ['result' => false, 'reason' => 'Invalid report type'];
        }

        $uuid = gen_uuid();
        $reportCode = $this->generateReportCode($reportType, $header, $uuid);

        $this->db->trans_start();

        $this->db->insert('saved_reports', [
            'uuid'           => $uuid,
            'report_code'    => $reportCode,
            'report_type'    => $reportType,
            'title'          => !empty($header['title']) ? $header['title'] : null,
            'date_from'      => $header['date_from'],
            'date_to'        => $header['date_to'],
            'developer_id'   => !empty($header['developer_id']) ? (int) $header['developer_id'] : null,
            'customer_id'    => !empty($header['customer_id']) ? (int) $header['customer_id'] : null,
            'project_id'     => !empty($header['project_id']) ? (int) $header['project_id'] : null,
            'sprint_id'      => !empty($header['sprint_id']) ? (int) $header['sprint_id'] : null,
            'billable_only'  => !empty($header['billable_only']) ? 1 : 0,
            'rate'           => (float) $header['rate'],
            'currency'       => !empty($header['currency']) ? $header['currency'] : 'MUR',
            'subject_name'   => isset($header['subject_name']) ? $header['subject_name'] : null,
            'subject_email'  => isset($header['subject_email']) ? $header['subject_email'] : null,
            'total_minutes'  => (int) $header['total_minutes'],
            'total_hours'    => (float) $header['total_hours'],
            'total_amount'   => (float) $header['total_amount'],
            'entry_count'    => (int) $header['entry_count'],
            'created_by'     => !empty($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null,
            'created_on'     => date('Y-m-d H:i:s'),
            'status'         => 1,
        ]);

        $reportId = (int) $this->db->insert_id();
        $sort = 0;
        foreach ($lines as $line) {
            $this->db->insert('saved_report_lines', [
                'report_id'        => $reportId,
                'line_date'        => isset($line['line_date']) ? $line['line_date'] : null,
                'task_ref'         => isset($line['task_ref']) ? $line['task_ref'] : null,
                'task_name'        => isset($line['task_name']) ? $line['task_name'] : null,
                'task_uuid'        => isset($line['task_uuid']) ? $line['task_uuid'] : null,
                'customer_name'    => isset($line['customer_name']) ? $line['customer_name'] : null,
                'project_name'     => isset($line['project_name']) ? $line['project_name'] : null,
                'sprint_name'      => isset($line['sprint_name']) ? $line['sprint_name'] : null,
                'notes'            => isset($line['notes']) ? $line['notes'] : null,
                'start_time'       => isset($line['start_time']) ? $line['start_time'] : null,
                'finish_time'      => isset($line['finish_time']) ? $line['finish_time'] : null,
                'work_type'        => isset($line['work_type']) ? $line['work_type'] : null,
                'entry_count'      => isset($line['entry_count']) ? (int) $line['entry_count'] : 1,
                'duration_minutes' => isset($line['duration_minutes']) ? (int) $line['duration_minutes'] : 0,
                'hours'            => isset($line['hours']) ? (float) $line['hours'] : 0,
                'amount'           => isset($line['amount']) ? (float) $line['amount'] : 0,
                'sort_order'       => $sort++,
            ]);
        }

        $this->db->trans_complete();
        if (!$this->db->trans_status()) {
            return ['result' => false, 'reason' => 'Failed to save report'];
        }

        return ['result' => true, 'uuid' => $uuid, 'id' => $reportId, 'report_code' => $reportCode];
    }

    /**
     * Displayable code that changes when rate/content changes.
     * Example: RPT-D-A3F2B19C / RPT-C-91BE04AA
     *
     * @param string $reportType
     * @param array  $header
     * @param string $uuid
     * @return string
     */
    public function generateReportCode($reportType, $header, $uuid)
    {
        $type = ($reportType === 'client') ? 'C' : 'D';
        $payload = implode('|', [
            $reportType,
            isset($header['date_from']) ? $header['date_from'] : '',
            isset($header['date_to']) ? $header['date_to'] : '',
            !empty($header['developer_id']) ? (string) (int) $header['developer_id'] : '',
            !empty($header['customer_id']) ? (string) (int) $header['customer_id'] : '',
            !empty($header['project_id']) ? (string) (int) $header['project_id'] : '',
            !empty($header['sprint_id']) ? (string) (int) $header['sprint_id'] : '',
            !empty($header['billable_only']) ? '1' : '0',
            number_format((float) $header['rate'], 2, '.', ''),
            !empty($header['currency']) ? $header['currency'] : 'MUR',
            (string) (int) $header['total_minutes'],
            number_format((float) $header['total_amount'], 2, '.', ''),
            $uuid,
        ]);
        $hash = strtoupper(substr(sha1($payload), 0, 8));
        $code = 'RPT-' . $type . '-' . $hash;

        // Extremely unlikely collision; append nibble if needed
        $n = 0;
        $candidate = $code;
        while ($this->db->select('id')->from('saved_reports')->where('report_code', $candidate)->get()->row()) {
            $n++;
            $candidate = $code . strtoupper(dechex($n % 16));
            if ($n > 32) {
                $candidate = 'RPT-' . $type . '-' . strtoupper(substr(sha1($payload . '|' . $n), 0, 8));
                break;
            }
        }
        return $candidate;
    }

    /**
     * @param string $type optional developer|client
     * @return array
     */
    public function listSaved($type = '')
    {
        $this->db->select('r.*, u.name created_by_name');
        $this->db->from('saved_reports r');
        $this->db->join('users u', 'u.id = r.created_by', 'left');
        $this->db->where('r.status', 1);
        if (in_array($type, ['developer', 'client'], true)) {
            $this->db->where('r.report_type', $type);
        }
        $this->db->order_by('r.created_on', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * @param string $uuid
     * @return object|null
     */
    public function getSavedByUuid($uuid)
    {
        $report = $this->db->select('r.*, u.name created_by_name')
            ->from('saved_reports r')
            ->join('users u', 'u.id = r.created_by', 'left')
            ->where(['r.uuid' => $uuid, 'r.status' => 1])
            ->get()->row();
        if (empty($report)) {
            return null;
        }
        $report->lines = $this->db->from('saved_report_lines')
            ->where('report_id', (int) $report->id)
            ->order_by('sort_order', 'ASC')
            ->get()->result();
        return $report;
    }

    /**
     * Soft-delete a saved report.
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteSaved($uuid)
    {
        $this->db->where(['uuid' => $uuid, 'status' => 1])
            ->set('status', 0)
            ->update('saved_reports');
        return $this->db->affected_rows() > 0;
    }
}
