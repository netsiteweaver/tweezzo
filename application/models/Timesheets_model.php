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
        return $this->db->affected_rows();
    }

    public function stopTimer()
    {
        $task_id = $this->input->post("task_id");

        $check_duration = $this->db->query("SELECT TIMESTAMPDIFF(MINUTE, start_time, NOW()) as mins FROM timesheet WHERE task_id = '{$task_id}' AND finish_time IS NULL")->row()->mins;

        if($check_duration == 0){
            $this->db->query("UPDATE timesheet SET `status` = '0' WHERE task_id = '{$task_id}' AND finish_time IS NULL");
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
        // return $this->db->affected_rows();
        return array(
            'result'    =>  true
        );
    }

    public function getTaskByDeveloperId($developerId, $customerId="", $projectId="", $sprintId="")
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
                    AND t2.closed = 0 
                    AND s.active = 1 
                    AND p.active = 1 
                    AND c.active = 1
                    AND t.developer_id  = $developerId";
        if(!empty($customerId)) $query .= " AND c.customer_id = '{$customerId}'";
        if(!empty($projectId)) $query .= " AND p.id = '{$projectId}'";
        if(!empty($sprintId)) $query .= " AND s.id = '{$sprintId}'";
        $result = $this->db->query($query)->result();
        return $result;
    }
}