<?php

class Timesheets_model extends CI_Model
{
    public function checkRunningTask()
    {
        $task_id = $this->input->post("task_id");
        //first check if there is any task's timer running
        $check = $this->db->query("SELECT 
                                ts.start_time
                                , ts.finish_time
                                , ts.notes
                                , t.name task_name
                            FROM timesheet ts
                            JOIN tasks t ON t.id = ts.task_id
                            WHERE ts.developer_id = {$_SESSION['developer_id']}
                            AND ts.task_id <> $task_id
                            AND ts.finish_time IS NULL
                            ")->result();
        return $check;
    }

    public function startTimer()
    {
        $task_id = $this->input->post("task_id");
        $notes = $this->input->post("notes");

        $this->db->query("SET time_zone = '+04:00'");
        $this->db->query("INSERT INTO timesheet (task_id, developer_id, start_time, finish_time, notes, status) VALUES (
                             $task_id, {$_SESSION['developer_id']}, NOW(), NULL, '$notes', '1')");
        return array(
            'task_uuid'     =>  $this->db->select("uuid")->from("tasks")->where("id",$task_id)->get()->row()->uuid
        );
    }

    public function stopTimer()
    {
        $task_id = $this->input->post("task_id");

        $check_duration = $this->db->query("SELECT TIMESTAMPDIFF(MINUTE, start_time, NOW()) as mins FROM timesheet WHERE task_id = '{$task_id}' AND finish_time IS NULL")->row()->mins;

        if($check_duration == 0){
            // $this->db->query("UPDATE timesheet SET `status` = '0' WHERE task_id = '{$task_id}' AND finish_time IS NULL");
            $this->db->query("DELETE FROM timesheet WHERE task_id = '{$task_id}' AND finish_time IS NULL");
            return array(
                'result'    =>  false,
                'reason'    =>  'Duration less than one minute will be discarded'
            );
        }

        $this->db->query("SET time_zone = '+04:00'");
        $this->db->query("UPDATE timesheet
                            SET 
                                finish_time = NOW(),
                                duration_minutes = TIMESTAMPDIFF(MINUTE, start_time, NOW())
                            WHERE 
                                task_id = '{$task_id}' AND finish_time IS NULL");  

        return array(
            "result"        =>  true,
            'task_uuid'     =>  $this->db->select("uuid")->from("tasks")->where("id",$task_id)->get()->row()->uuid
        );
    }

    public function getTaskByDeveloperId($developerId, $customerId="", $projectId="", $sprintId="")
    {
        $startDate = (!empty($this->input->get("from"))) ? $this->input->get("from") : date("Y-m-01");
        $finishDate = (!empty($this->input->get("to"))) ? $this->input->get("to") : date("Y-m-t");
        $method = (!empty($this->input->get("method"))) ? $this->input->get("method") : 'start';

        $query = "SELECT
                        c.customer_id customerId, s.id sprintId, p.id projectId,
                        t2.uuid taskUuid, t2.name taskName, t2.task_number taskNumber, t2.section taskSection,
                        s.name sprintName,
                        p.name projectName,
                        c.company_name customerName,
                        t.*
                    FROM timesheet t 
                    JOIN tasks t2 on t2.id = t.task_id
                    JOIN sprints s on s.id = t2.sprint_id 
                    JOIN projects p on p.id = s.project_id 
                    JOIN customers c on c.customer_id = p.customer_id 
                    WHERE t.status = 1 
                    AND t.finish_time IS NOT NULL
                    AND t2.status = 1
                    AND s.status = 1
                    AND p.status = 1
                    AND c.status = 1
                    -- AND s.active = 1 
                    -- AND p.active = 1 
                    -- AND c.active = 1
                    AND t.developer_id  = $developerId";
        $query .= " AND t.start_time >= '" . $startDate.' 00:00:00' . "'";
        $query .= " AND t.finish_time <= '" . $finishDate.' 23:59:59' . "'";
        if(!empty($customerId)) $query .= " AND c.customer_id = '{$customerId}'";
        if(!empty($projectId)) $query .= " AND p.id = '{$projectId}'";
        if(!empty($sprintId)) $query .= " AND s.id = '{$sprintId}'";
        $query .= " ORDER BY t.start_time DESC";
        // echo $query;die;
        $result = $this->db->query($query)->result();
        return $result;
    }

    public function getSingle($id)
    {
        $query = "SELECT
                        t2.uuid taskUuid, t2.name taskName, t2.task_number taskNumber, t2.section taskSection,
                        s.name sprintName,
                        p.name projectName,
                        c.company_name customerName,
                        t.*
                    FROM timesheet t 
                    JOIN tasks t2 on t2.id = t.task_id
                    JOIN sprints s on s.id = t2.sprint_id 
                    JOIN projects p on p.id = s.project_id 
                    JOIN customers c on c.customer_id = p.customer_id 
                    WHERE t.status = 1 
                    AND t.finish_time IS NOT NULL
                    AND t2.status = 1
                    AND s.status = 1
                    AND p.status = 1
                    AND c.status = 1
                    -- AND t2.closed = 0 
                    AND s.active = 1 
                    AND p.active = 1 
                    AND c.active = 1
                    AND t.id  = $id";
        $result = $this->db->query($query)->row();
        return $result;
    }

    public function deleteTimesheet($id)
    {
        $this->db->where(array(
            "id"    =>  $id,
            "developer_id"  =>  $_SESSION['developer_id']
        ))->set("status","0")->update("timesheet");
        return $this->db->affected_rows();
    }

    public function getRunningTasks()
    {
        $result = $this->db->select("ta.uuid taskUuid")
                        ->from("timesheet t")
                        ->join("tasks ta","ta.id=t.task_id")
                        ->join("sprints s","s.id=ta.sprint_id")
                        ->join("projects p","p.id=s.project_id")
                        ->join("customers c","c.customer_id=p.customer_id")
                        ->where("t.developer_id = {$_SESSION['developer_id']} AND t.finish_time IS NULL")
                        ->get()
                        ->row('taskUuid');
        return $result;
    }

    /**
     * Admin listing of completed timesheet entries with optional filters.
     *
     * Supported filters: from, to, developer_id, customer_id, project_id, sprint_id, task_id, task_uuid
     *
     * @param array $filters
     * @return array
     */
    public function adminFetch($filters = [])
    {
        $from = !empty($filters['from']) ? $filters['from'] : date('Y-m-01');
        $to = !empty($filters['to']) ? $filters['to'] : date('Y-m-t');

        $query = "SELECT
                        c.customer_id customerId,
                        s.id sprintId,
                        p.id projectId,
                        t2.id taskId,
                        t2.uuid taskUuid,
                        t2.name taskName,
                        t2.task_number taskNumber,
                        t2.section taskSection,
                        s.name sprintName,
                        s.code sprintCode,
                        p.name projectName,
                        p.code projectCode,
                        c.company_name customerName,
                        u.id developerId,
                        u.name developerName,
                        u.email developerEmail,
                        t.*
                    FROM timesheet t
                    JOIN tasks t2 ON t2.id = t.task_id
                    JOIN sprints s ON s.id = t2.sprint_id
                    JOIN projects p ON p.id = s.project_id
                    JOIN customers c ON c.customer_id = p.customer_id
                    JOIN users u ON u.id = t.developer_id
                    WHERE t.status = 1
                    AND t.finish_time IS NOT NULL
                    AND t2.status = 1
                    AND s.status = 1
                    AND p.status = 1
                    AND c.status = 1
                    AND t.start_time >= " . $this->db->escape($from . ' 00:00:00') . "
                    AND t.finish_time <= " . $this->db->escape($to . ' 23:59:59');

        if (!empty($filters['developer_id'])) {
            $query .= " AND t.developer_id = " . (int) $filters['developer_id'];
        }
        if (!empty($filters['customer_id'])) {
            $query .= " AND c.customer_id = " . (int) $filters['customer_id'];
        }
        if (!empty($filters['project_id'])) {
            $query .= " AND p.id = " . (int) $filters['project_id'];
        }
        if (!empty($filters['sprint_id'])) {
            $query .= " AND s.id = " . (int) $filters['sprint_id'];
        }
        if (!empty($filters['task_id'])) {
            $query .= " AND t2.id = " . (int) $filters['task_id'];
        }
        if (!empty($filters['task_uuid'])) {
            $query .= " AND t2.uuid = " . $this->db->escape($filters['task_uuid']);
        }

        $query .= " ORDER BY t.start_time DESC";
        $rows = $this->db->query($query)->result();

        if (!function_exists('task_ref')) {
            get_instance()->load->helper('general');
        }
        foreach ($rows as $row) {
            $tn = isset($row->taskNumber) ? $row->taskNumber : '';
            $row->taskRef = $tn !== ''
                ? task_ref(isset($row->projectCode) ? $row->projectCode : null, isset($row->sprintCode) ? $row->sprintCode : null, $tn)
                : '';
        }

        return $rows;
    }

    /**
     * Timesheet entries for a single task (admin task view).
     *
     * @param int $taskId
     * @return array
     */
    public function getByTaskId($taskId)
    {
        return $this->adminFetch([
            'task_id' => (int) $taskId,
            'from'    => '2000-01-01',
            'to'      => '2099-12-31',
        ]);
    }

    /**
     * Admin fetch of a single completed timesheet (no portal developer restriction).
     *
     * @param int $id
     * @return object|null
     */
    public function adminGetById($id)
    {
        $id = (int) $id;
        if ($id <= 0) {
            return null;
        }

        $query = "SELECT
                        c.customer_id customerId,
                        s.id sprintId,
                        p.id projectId,
                        t2.id taskId,
                        t2.uuid taskUuid,
                        t2.name taskName,
                        t2.task_number taskNumber,
                        t2.section taskSection,
                        s.name sprintName,
                        s.code sprintCode,
                        p.name projectName,
                        p.code projectCode,
                        c.company_name customerName,
                        u.id developerId,
                        u.name developerName,
                        u.email developerEmail,
                        t.*
                    FROM timesheet t
                    JOIN tasks t2 ON t2.id = t.task_id
                    JOIN sprints s ON s.id = t2.sprint_id
                    JOIN projects p ON p.id = s.project_id
                    JOIN customers c ON c.customer_id = p.customer_id
                    JOIN users u ON u.id = t.developer_id
                    WHERE t.status = 1
                    AND t.id = {$id}";

        $row = $this->db->query($query)->row();
        if (empty($row)) {
            return null;
        }

        if (!function_exists('task_ref')) {
            get_instance()->load->helper('general');
        }
        $tn = isset($row->taskNumber) ? $row->taskNumber : '';
        $row->taskRef = $tn !== ''
            ? task_ref(isset($row->projectCode) ? $row->projectCode : null, isset($row->sprintCode) ? $row->sprintCode : null, $tn)
            : '';

        return $row;
    }

    /**
     * Soft-delete a timesheet entry (admin — any developer).
     *
     * @param int $id
     * @return bool
     */
    public function adminDelete($id)
    {
        $id = (int) $id;
        if ($id <= 0) {
            return false;
        }
        $this->db->where(['id' => $id, 'status' => 1])
            ->set('status', '0')
            ->update('timesheet');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Update a completed timesheet entry (admin).
     *
     * @param int   $id
     * @param array $data start_time, finish_time, notes
     * @return array{result:bool, reason?:string}
     */
    public function adminUpdate($id, $data)
    {
        $id = (int) $id;
        $entry = $this->adminGetById($id);
        if (empty($entry)) {
            return ['result' => false, 'reason' => 'Timesheet entry not found'];
        }

        $start = isset($data['start_time']) ? trim((string) $data['start_time']) : '';
        $finish = isset($data['finish_time']) ? trim((string) $data['finish_time']) : '';
        $notes = isset($data['notes']) ? (string) $data['notes'] : '';

        if ($start === '' || $finish === '') {
            return ['result' => false, 'reason' => 'Start and finish time are required'];
        }

        $startTs = strtotime($start);
        $finishTs = strtotime($finish);
        if ($startTs === false || $finishTs === false) {
            return ['result' => false, 'reason' => 'Invalid start or finish time'];
        }
        if ($finishTs <= $startTs) {
            return ['result' => false, 'reason' => 'Finish time must be after start time'];
        }

        $durationMinutes = (int) floor(($finishTs - $startTs) / 60);
        if ($durationMinutes < 1) {
            return ['result' => false, 'reason' => 'Duration must be at least one minute'];
        }

        $this->db->where('id', $id)->update('timesheet', [
            'start_time'       => date('Y-m-d H:i:s', $startTs),
            'finish_time'      => date('Y-m-d H:i:s', $finishTs),
            'duration_minutes' => $durationMinutes,
            'notes'            => $notes,
        ]);

        return ['result' => true];
    }
}