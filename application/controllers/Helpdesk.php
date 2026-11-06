<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin side of the helpdesk: triage, assignment, replies, internal notes, conversion to tasks.
 */
class Helpdesk extends MY_Controller {

    public $data;

    public function __construct()
    {
        parent::__construct();
        $this->data['controller'] = str_replace("-", "", $this->uri->segment(1, "dashboard"));
        $this->data['method'] = $this->uri->segment(2, "index");
        $this->load->model("Tickets_model");
        foreach (['add', 'edit', 'view', 'delete'] as $perm) {
            $this->data['perms'][$perm] = $this->accesscontrol_model->authorised("helpdesk", $perm);
        }
        $this->data['qs'] = $_SERVER["QUERY_STRING"];
    }

    public function index()
    {
        $this->listing();
    }

    public function listing()
    {
        if (!isAuthorised(get_class(), "listing")) return false;

        $this->mybreadcrumb->add('Helpdesk', base_url('helpdesk/listing'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Helpdesk";

        $assigned = $this->input->get('assigned_to');
        if ($assigned === 'me') {
            $assigned = (int) $_SESSION['user_id'];
        }
        $filters = [
            'status'      => $this->input->get('status') !== null && $this->input->get('status') !== '' ? $this->input->get('status') : 'active',
            'priority'    => $this->input->get('priority'),
            'category_id' => $this->input->get('category_id'),
            'customer_id' => $this->input->get('customer_id'),
            'assigned_to' => $assigned,
            'source'      => $this->input->get('source'),
            'overdue'     => $this->input->get('overdue'),
            'search_text' => $this->input->get('search_text'),
        ];
        $this->data['filters'] = $filters;

        $page = $this->uri->segment(3);
        $per_page = !empty($this->input->get("display")) ? (int) $this->input->get("display") : (int) $this->system_model->getParam("rows_per_page");
        $this->data['tickets'] = $this->Tickets_model->listing($filters, $page, $per_page);
        $total_rows = $this->Tickets_model->count($filters);
        $this->data['total_rows'] = $total_rows;
        $this->data['pagination'] = getPagination("helpdesk/listing", $total_rows, $per_page);
        $this->data['counts'] = $this->Tickets_model->summaryCounts((int) $_SESSION['user_id']);

        $this->data['categories'] = $this->Tickets_model->categories();
        $this->data['assignees'] = $this->Tickets_model->assignableUsers();
        $this->data['customers'] = $this->Tickets_model->customersWithTickets();

        $this->data["content"] = $this->load->view("/helpdesk/listing", $this->data, true);
        $this->load->view("/layouts/default", $this->data);
    }

    public function add()
    {
        if (!isAuthorised(get_class(), "add")) return false;

        $this->mybreadcrumb->add('Helpdesk', base_url('helpdesk/listing'));
        $this->mybreadcrumb->add('New Ticket', base_url('helpdesk/add'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "New Ticket";

        $this->load->model('Customers_model');
        $this->data['customers'] = $this->Customers_model->lookup();
        $this->data['contacts'] = $this->db->select('id, customer_id, name, email')->from('customer_access')
            ->where('status', 1)->order_by('name')->get()->result();
        $this->data['projects'] = $this->db->select('id, customer_id, name')->from('projects')
            ->where(['status' => 1, 'active' => 1])->order_by('name')->get()->result();
        $this->data['categories'] = $this->Tickets_model->categories();
        $this->data['assignees'] = $this->Tickets_model->assignableUsers();

        $this->data["content"] = $this->load->view("/helpdesk/add", $this->data, true);
        $this->load->view("/layouts/default", $this->data);
    }

    public function save()
    {
        if (!isAuthorised(get_class(), "add")) return false;

        $actor = $this->Tickets_model->staffActorFromSession();
        $result = $this->Tickets_model->create([
            'subject'            => $this->input->post('subject'),
            'body'               => $this->input->post('body'),
            'priority'           => $this->input->post('priority'),
            'category_id'        => $this->input->post('category_id'),
            'customer_id'        => $this->input->post('customer_id'),
            'customer_access_id' => $this->input->post('customer_access_id'),
            'requester_name'     => $this->input->post('requester_name'),
            'requester_email'    => $this->input->post('requester_email'),
            'project_id'         => $this->input->post('project_id'),
            'assigned_to'        => $this->input->post('assigned_to'),
        ], 'staff', $actor);

        if (!$result['result']) {
            flashDanger($result['reason']);
            redirect(base_url('helpdesk/add'));
            return;
        }
        $this->flashUploadErrors($result['upload_errors']);
        flashSuccess("Ticket {$result['ticket']->ticket_number} has been created.");
        redirect(base_url('helpdesk/view?uuid=' . $result['ticket']->uuid));
    }

    public function view()
    {
        if (!isAuthorised(get_class(), "view")) return false;

        $ticket = $this->Tickets_model->getByUuid($this->input->get('uuid'));
        if (empty($ticket)) {
            flashDanger('Ticket not found.');
            redirect(base_url('helpdesk/listing'));
            return;
        }

        $this->mybreadcrumb->add('Helpdesk', base_url('helpdesk/listing'));
        $this->mybreadcrumb->add($ticket->ticket_number, base_url('helpdesk/view?uuid=' . $ticket->uuid));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "{$ticket->ticket_number} — " . $ticket->subject;

        $this->data['ticket'] = $ticket;
        $this->data['thread'] = $this->Tickets_model->getThread($ticket, true);
        $this->data['history'] = $this->Tickets_model->getHistory($ticket->id);
        $this->data['categories'] = $this->Tickets_model->categories();
        $this->data['assignees'] = $this->Tickets_model->assignableUsers();
        $this->data['sla'] = $this->Tickets_model->slaSettings();

        if ($this->data['perms']['edit']) {
            $this->load->model('Customers_model');
            $this->data['link_customers'] = $this->Customers_model->lookup();
            $this->data['link_contacts'] = $this->db->select('id, customer_id, name, email')->from('customer_access')
                ->where('status', 1)->order_by('name')->get()->result();
        }

        // Convert-to-task modal: sprints of the ticket's customer (or all when unknown).
        if ($this->data['perms']['edit'] && empty($ticket->task_id)) {
            $this->load->model('Sprints_model');
            $this->data['sprints'] = $this->Sprints_model->fetchAll(!empty($ticket->customer_id) ? $ticket->customer_id : '', 's.name', 'asc', 1, 500);
            $this->load->model('Developers_model');
            $this->data['developers'] = $this->Developers_model->lookup();
        }

        $this->data["content"] = $this->load->view("/helpdesk/view", $this->data, true);
        $this->load->view("/layouts/default", $this->data);
    }

    public function reply()
    {
        if (!isAuthorised(get_class(), "edit")) return false;

        $uuid = $this->input->post('uuid');
        $ticket = $this->Tickets_model->getByUuid($uuid);
        if (empty($ticket)) {
            flashDanger('Ticket not found.');
            redirect(base_url('helpdesk/listing'));
            return;
        }

        $actor = $this->Tickets_model->staffActorFromSession();
        $result = $this->Tickets_model->addReply(
            $ticket,
            $actor,
            $this->input->post('body'),
            (bool) $this->input->post('is_internal'),
            $this->input->post('status')
        );
        if ($result['result']) {
            $this->flashUploadErrors($result['upload_errors']);
            flashSuccess($this->input->post('is_internal') ? 'Internal note added.' : 'Reply sent.');
        } else {
            flashDanger($result['reason']);
        }
        redirect(base_url('helpdesk/view?uuid=' . urlencode($uuid)) . '#reply-' . ($result['reply_id'] ?? 'form'));
    }

    /** AJAX: change status, priority, category or assignee from the ticket sidebar. */
    public function updateField()
    {
        if (!$this->accesscontrol_model->authorised("helpdesk", "edit")) {
            return $this->json(['result' => false, 'reason' => 'You are not allowed to edit tickets.']);
        }
        $ticket = $this->Tickets_model->getByUuid($this->input->post('uuid'));
        if (empty($ticket)) {
            return $this->json(['result' => false, 'reason' => 'Ticket not found.']);
        }
        $field = $this->input->post('field');
        if (!in_array($field, ['status', 'priority', 'category_id', 'assigned_to'], true)) {
            return $this->json(['result' => false, 'reason' => 'Invalid field.']);
        }
        $value = $this->input->post('value');
        if ($field === 'assigned_to' && $value === 'me') {
            $value = (int) $_SESSION['user_id'];
        }
        $result = $this->Tickets_model->changeField($ticket, $field, $value, $this->Tickets_model->staffActorFromSession());
        unset($result['ticket']);
        return $this->json($result);
    }

    public function convertToTask()
    {
        if (!isAuthorised(get_class(), "edit")) return false;

        $uuid = $this->input->post('uuid');
        $ticket = $this->Tickets_model->getByUuid($uuid);
        if (empty($ticket)) {
            flashDanger('Ticket not found.');
            redirect(base_url('helpdesk/listing'));
            return;
        }
        $user_ids = json_decode((string) $this->input->post('userIds'), true);
        $result = $this->Tickets_model->convertToTask(
            $ticket,
            (int) $this->input->post('sprint_id'),
            is_array($user_ids) ? $user_ids : [],
            $this->input->post('estimated_hours'),
            $this->input->post('work_type'),
            $this->input->post('billable'),
            trim((string) $this->input->post('ref')),
            $this->Tickets_model->staffActorFromSession()
        );
        if ($result['result']) {
            flashSuccess("Task {$result['task_number']} has been created from this ticket.");
        } else {
            flashDanger($result['reason']);
        }
        redirect(base_url('helpdesk/view?uuid=' . urlencode($uuid)));
    }

    /** Attach a ticket (e.g. from the public form) to a customer and optionally one of its contacts. */
    public function linkCustomer()
    {
        if (!isAuthorised(get_class(), "edit")) return false;

        $uuid = $this->input->post('uuid');
        $ticket = $this->Tickets_model->getByUuid($uuid);
        if (empty($ticket)) {
            flashDanger('Ticket not found.');
            redirect(base_url('helpdesk/listing'));
            return;
        }
        $result = $this->Tickets_model->linkCustomer(
            $ticket,
            $this->input->post('customer_id'),
            $this->input->post('customer_access_id'),
            $this->Tickets_model->staffActorFromSession()
        );
        if (!$result['result']) {
            flashDanger($result['reason']);
        } elseif (!empty($result['changed'])) {
            flashSuccess($this->input->post('customer_id') ? 'Ticket attached to the customer.' : 'Ticket detached from the customer.');
        }
        redirect(base_url('helpdesk/view?uuid=' . urlencode($uuid)));
    }

    public function delete()
    {
        if (!$this->accesscontrol_model->authorised("helpdesk", "delete")) {
            return $this->json(['result' => false, 'reason' => 'You are not allowed to delete tickets.']);
        }
        $ticket = $this->Tickets_model->getByUuid($this->input->post('uuid'));
        if (empty($ticket)) {
            return $this->json(['result' => false, 'reason' => 'Ticket not found.']);
        }
        $affected = $this->Tickets_model->softDelete($ticket, $this->Tickets_model->staffActorFromSession());
        return $this->json(['result' => true, 'affected_rows' => $affected]);
    }

    public function download($id = 0)
    {
        if (!isAuthorised(get_class(), "view")) return false;
        $attachment = $this->Tickets_model->getAttachment($id);
        if (empty($attachment)) {
            show_404();
            return;
        }
        $this->Tickets_model->streamAttachment($attachment);
    }

    private function flashUploadErrors($errors)
    {
        if (!empty($errors)) {
            flashWarning('Some files were not attached: ' . htmlspecialchars(implode('; ', $errors)));
        }
    }

    private function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        return null;
    }
}
