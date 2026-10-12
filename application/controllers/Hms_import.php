<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Turns approved HMS quotations into tasks: pick a quotation, review the tasks its line items
 * were split into, then save. Nothing is created before the review screen is confirmed.
 */
class Hms_import extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Hms_model', 'Customers_model']);

        //Breadcrumbs
        $this->mybreadcrumb->add('Tasks', base_url('tasks/listing'));
        $this->mybreadcrumb->add('Import from HMS', base_url('hms_import/index'));
    }

    public function index()
    {
        //Access Control
        if (!isAuthorised("Tasks", "import")) return false;

        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Import from HMS";

        $show_all = ($this->input->get('show') === 'all');
        $response = $this->Hms_model->quotations(!$show_all);

        $this->data['show_all'] = $show_all;
        $this->data['error'] = $response['ok'] ? '' : $response['error'];
        $this->data['quotations'] = $response['ok'] ? $response['data']['quotations'] : [];

        $this->data["content"] = $this->load->view("hms_import/index", $this->data, true);
        $this->load->view("/layouts/default", $this->data);
    }

    public function preview($uuid = '')
    {
        //Access Control
        if (!isAuthorised("Tasks", "import")) return false;

        $response = $this->Hms_model->quotation($uuid);
        if (!$response['ok']) {
            flashDanger($response['error']);
            redirect('hms_import/index');
            return;
        }
        $quotation = $response['data']['quotation'];

        $this->load->library('Hms_quotation_parser');
        $lines = $this->hms_quotation_parser->parse($quotation);

        $task_count = 0;
        foreach ($lines as $line) {
            $task_count += count($line['tasks']);
        }
        if ($task_count == 0) {
            flashDanger("Quotation " . $quotation['number'] . " has no line items to import.");
            redirect('hms_import/index');
            return;
        }

        $this->mybreadcrumb->add($quotation['number'], base_url('hms_import/preview/' . $quotation['uuid']));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Import " . $quotation['number'] . " from HMS";

        $this->load->model("Developers_model");
        $this->data['developers'] = $this->Developers_model->lookup();
        $this->data['customers'] = $this->Customers_model->lookup();
        $this->data['matched_customer_id'] = $this->Hms_model->matchCustomer($quotation['client']);
        $this->data['already_imported'] = $this->Hms_model->importedTaskCount($quotation['number']);
        $this->data['quotation'] = $quotation;
        $this->data['lines'] = $lines;

        $this->data["content"] = $this->load->view("hms_import/preview", $this->data, true);
        $this->load->view("/layouts/default", $this->data);
    }

    public function save()
    {
        //Access Control
        if (!isAuthorised("Tasks", "add", false)) {
            echo json_encode(['result' => false, 'reason' => 'Permission denied']);
            exit;
        }

        $quotation_uuid = $this->input->post('quotation_uuid');
        $sprint_id = (int) $this->input->post('sprint_id');
        $project_id = (int) $this->input->post('project_id');
        $due_date = trim((string) $this->input->post('due_date'));
        $billable = ($this->input->post('billable') == 1) ? 1 : 0;
        $developers = $this->input->post('developers');
        // Sent as one JSON field: a long quotation would otherwise run into max_input_vars
        $tasks = json_decode((string) $this->input->post('tasks_json', false), true);

        if (empty($quotation_uuid) || $sprint_id <= 0 || $project_id <= 0) {
            echo json_encode(['result' => false, 'reason' => 'Missing required fields']);
            exit;
        }
        if (empty($tasks) || !is_array($tasks)) {
            echo json_encode(['result' => false, 'reason' => 'No tasks selected']);
            exit;
        }

        $sprint = $this->db->select('id')->from('sprints')
            ->where(['id' => $sprint_id, 'project_id' => $project_id, 'status' => 1])
            ->get()->row();
        if (empty($sprint)) {
            echo json_encode(['result' => false, 'reason' => 'Sprint not found in the selected project']);
            exit;
        }

        // Read again from HMS, so the reference and link do not depend on what the browser sent
        $response = $this->Hms_model->quotation($quotation_uuid);
        if (!$response['ok']) {
            echo json_encode(['result' => false, 'reason' => $response['error']]);
            exit;
        }
        $quotation = $response['data']['quotation'];

        $this->load->model('Tasks_model');

        $userIds_json = '[]';
        if (!empty($developers) && is_array($developers)) {
            $userIds_json = json_encode(array_map('intval', $developers));
        }

        $maxTN = $this->db->select('MAX(task_number) as tn')->from('tasks')
            ->where(['sprint_id' => $sprint_id, 'status' => 1])
            ->get()->row('tn');
        $currentTaskNumber = !empty($maxTN) ? incrementTaskNumber($maxTN) : '001';

        $label = (($quotation['type'] == 'estimate') ? 'Estimate ' : 'Quotation ') . $quotation['number'];
        $quotation_link = '<a href="' . htmlspecialchars($quotation['url'], ENT_QUOTES, 'UTF-8') . '" target="_blank" style="color: #007bff; text-decoration: underline;">'
            . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</a>';

        $created_tasks = [];
        $created_ids = [];
        $errors = [];

        foreach ($tasks as $task) {
            $name = isset($task['name']) ? mb_substr(trim((string) $task['name']), 0, 255) : '';
            if ($name === '') {
                continue; // Skip empty task names
            }
            $description = isset($task['description']) ? trim((string) $task['description']) : '';
            $section = isset($task['section']) ? mb_substr(trim((string) $task['section']), 0, 255) : '';
            $hours = (isset($task['hours']) && is_numeric($task['hours'])) ? (float) $task['hours'] : 0;

            $task_data = [
                'name' => $name,
                // Shown as HTML on the task page
                'description' => $quotation_link . ($description !== '' ? "<br><br>" . nl2br(htmlspecialchars($description, ENT_QUOTES, 'UTF-8')) : ''),
                'section' => ($section !== '') ? $section : 'Quotation',
                'sprint_id' => $sprint_id,
                'task_number' => $currentTaskNumber,
                'stage' => 'new',
                'progress' => 0,
                'due_date' => ($due_date !== '') ? $due_date : null,
                'estimated_hours' => $hours,
                'work_type' => 'development',
                'billable' => $billable,
                'ref' => $quotation['number'],
                'scope_client_expectation' => '',
                'scope_not_included' => '',
                'scope_when_done' => '',
                'source' => 'hms',
                'userIds' => $userIds_json,
                // One digest for the whole import is sent below, instead of emails per task
                '_skip_notifications' => true,
            ];

            $response = $this->Tasks_model->save($task_data, []);

            if ($response['result']) {
                $created_tasks[] = $name;
                $created_ids[] = $response['id'];
                $currentTaskNumber = incrementTaskNumber($currentTaskNumber);
            } else {
                $errors[] = $name . ': ' . $response['reason'];
                // Sprint closed or due date outside it: the same answer would come back for every task
                if (empty($created_tasks)) break;
            }
        }

        if (empty($created_tasks)) {
            echo json_encode(['result' => false, 'reason' => 'No task was created. ' . implode(', ', $errors)]);
            exit;
        }

        $this->Tasks_model->notifyImportedTasks(
            [$userIds_json => $created_ids],
            count($created_tasks) . ' task(s) imported from ' . $label,
            'Tasks imported from HMS',
            '<p>' . count($created_tasks) . ' task(s) were created from ' . $quotation_link . ' &ndash; ' . htmlspecialchars($quotation['title'], ENT_QUOTES, 'UTF-8') . '.</p>'
        );

        $exported = $this->Hms_model->markExported($quotation['uuid'], count($created_tasks));
        if (!$exported['ok']) {
            $errors[] = 'Tasks were created, but HMS could not be told: ' . $exported['error'];
        }

        echo json_encode([
            'result' => true,
            'message' => 'Successfully created ' . count($created_tasks) . ' task(s) from ' . $label,
            'created_tasks' => $created_tasks,
            'errors' => $errors
        ]);
        exit;
    }
}
