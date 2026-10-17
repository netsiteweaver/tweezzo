<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Meeting_notes extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Meeting_note_model', 'Attachment_model', 'Customers_model']);
        $this->load->helper(['form', 'url']);
        $this->load->library(['form_validation', 'upload']);

        $this->load->model("accesscontrol_model");
        $this->data['perms']['add'] = $this->accesscontrol_model->authorised("meeting_notes","create");
        $this->data['perms']['edit'] = $this->accesscontrol_model->authorised("meeting_notes","edit");
        $this->data['perms']['view'] = $this->accesscontrol_model->authorised("meeting_notes","view");
        $this->data['perms']['listing'] = $this->accesscontrol_model->authorised("meeting_notes","index");
        $this->data['perms']['pdf'] = $this->accesscontrol_model->authorised("meeting_notes","pdf");
        $this->data['perms']['delete'] = $this->accesscontrol_model->authorised("meeting_notes","delete");

        //Breadcrumbs
        $this->mybreadcrumb->add('Meeting Notes', base_url('meeting_notes/index'));
    }

    public function index()
    {
        //Access Control
        if (!isAuthorised(get_class(), "index")) return false;

        //Breadcrumbs
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        // page title
        $this->data['page_title'] = "Meeting Notes";

        $customer_id = $this->input->get('customer_id');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if (empty($start_date) && empty($end_date)) {
            $end_date = date('Y-m-d');
            $start_date = date('Y-m-d', strtotime('-29 days'));
        }

        $this->Meeting_note_model->promote_due();

        $this->data['customers'] = $this->Customers_model->lookup();
        $this->data['customer_id'] = $customer_id;
        $this->data['start_date'] = $start_date;
        $this->data['end_date'] = $end_date;
        $this->data['notes'] = $this->Meeting_note_model->get_all_notes($customer_id, $start_date, $end_date);

        $this->data["content"]=$this->load->view("meeting_notes/index",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function view($id)
    {
        //Access Control
        if (!isAuthorised(get_class(), "view")) return false;

        //Breadcrumbs
        $this->mybreadcrumb->add('View', base_url('meeting_notes/view'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        // page title
        $this->data['page_title'] = "View Meeting Notes";
        
        $this->data['note'] = $this->Meeting_note_model->get_note($id);
        $this->data['attachments'] = $this->Attachment_model->get_attachments($id);

        $this->data["content"]=$this->load->view("meeting_notes/view",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function pdf($id)
    {
        //Access Control
        if (!isAuthorised(get_class(), "pdf")) return false;

        //Breadcrumbs
        $this->mybreadcrumb->add('PDF', base_url('meeting_notes/pdf'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        // page title
        $this->data['page_title'] = "Meeting Notes - PDF";
        
        $this->data['note'] = $this->Meeting_note_model->get_note($id);
        $this->data['attachments'] = $this->Attachment_model->get_attachments($id);

        $this->data["content"]=$this->load->view("meeting_notes/pdf",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function create()
    {
        //Access Control
        if (!isAuthorised(get_class(), "create")) return false;

        //Breadcrumbs
        $this->mybreadcrumb->add('Create', base_url('meeting_notes/create'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        // page title
        $this->data['page_title'] = "Create Meeting Notes";

        $this->data['customers'] = $this->Customers_model->lookup();

        $this->form_validation->set_rules('customer_id', 'Client Name', 'required');
        $this->form_validation->set_rules('meeting_date', 'Meeting Date', 'required');
        $this->form_validation->set_rules('meeting_time', 'Meeting Time', 'required');
        // Live sessions and planned meetings start before anything is written, so notes are only required for a normal save.
        $start_live = (bool) $this->input->post('start_live');
        $plan = (bool) $this->input->post('plan_meeting');
        if (!$start_live && !$plan) {
            $this->form_validation->set_rules('notes', 'Notes', 'required');
        }
        if ($plan) {
            $this->form_validation->set_rules('meeting_time', 'Meeting Time', 'required|callback_validate_planned_datetime');
        }
        $this->form_validation->set_rules('next_meeting_date', 'Next Meeting Date', 'callback_validate_next_meeting_date');

        if ($this->form_validation->run() === FALSE) {
            $this->data["content"]=$this->load->view("meeting_notes/create",$this->data,true);
            $this->load->view("/layouts/default",$this->data);   
        } else {
            $data = [
                'customer_id' => $this->input->post('customer_id'),
                'customer_name' => $this->input->post('customer_name'),
                'meeting_datetime' => $this->input->post('meeting_date') . ' ' . $this->input->post('meeting_time'),
                'next_meeting_date' => $this->Meeting_note_model->parseNextMeetingDate(
                    $this->input->post('next_meeting_date'),
                    $this->input->post('next_meeting_time')
                ),
                'notes' => (string) $this->input->post('notes'),
                'agenda' => (string) $this->input->post('agenda'),
                'attendees' => $this->input->post('attendees', true),
                'lieu' => $this->input->post('lieu', true),
                'status' => $start_live ? 'live' : ($plan ? 'planned' : 'closed'),
            ];
            $note_id = $this->Meeting_note_model->insert_note($data);

            if ($plan) {
                flashSuccess("Meeting planned. Invite your client and developers below; it goes live automatically at the start time.");
                redirect("meeting_notes/edit/" . $note_id);
            }
            if ($start_live) {
                flashSuccess("Live session started. Share it with your client and developers below.");
                redirect("meeting_notes/edit/" . $note_id);
            }
            redirect("meeting_notes/index");
        }
    }

    public function edit($id)
    {
        //Access Control
        if (!isAuthorised(get_class(), "edit")) return false;

        //Breadcrumbs
        $this->mybreadcrumb->add('Edit', base_url('meeting_notes/edit'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        // page title
        $this->data['page_title'] = "Edit Meeting Notes";
        

        $this->data['customers'] = $this->Customers_model->lookup();

        $this->Meeting_note_model->promote_due((int) $id);
        $this->data['note'] = $this->Meeting_note_model->get_note($id);

        $this->data['attachments'] = $this->Attachment_model->get_attachments($id);

        $this->data['page_title'] = "Edit Meeting Note";
        // For the "notify about this update?" prompt on Update.
        $this->data['notify_shares'] = empty($this->data['note']) ? [] : $this->Meeting_note_model->get_shares($id);

        if (!empty($this->data['note']) && in_array($this->data['note']->status, ['planned', 'live'], true)) {
            $this->data['page_title'] = ($this->data['note']->status === 'live') ? "Live Meeting Note" : "Planned Meeting";
            $this->data['shares'] = $this->Meeting_note_model->get_shares($id);
            $this->data['shareable'] = $this->Meeting_note_model->shareable_people($this->data['note']);
            $this->data['entries'] = $this->Meeting_note_model->get_entries($id);
            $this->data['scripts'][] = base_url('assets/js/meeting-live.js') . '?' . date('YmdHis');
        }

        $this->data["content"]=$this->load->view("meeting_notes/edit",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function update($id)
    {
        //Access Control
        if (!isAuthorised(get_class(), "edit")) return false;
        
        $this->form_validation->set_rules('customer_id', 'Client', 'required');
        $this->form_validation->set_rules('customer_name', 'Client Name', 'required');
        $this->form_validation->set_rules('meeting_date', 'Meeting DateTime', 'required');
        $this->form_validation->set_rules('meeting_time', 'Meeting DateTime', 'required');
        // Planned and live meetings can still have empty notes.
        $before = $this->Meeting_note_model->get_note($id);
        if (empty($before) || $before->status === 'closed') {
            $this->form_validation->set_rules('notes', 'Notes', 'required');
        }
        $this->form_validation->set_rules('next_meeting_date', 'Next Meeting Date', 'callback_validate_next_meeting_date');

        if ($this->form_validation->run() === FALSE) {
            $this->edit($id);
        } else {
            $this->Meeting_note_model->update_note($id);
            $note = $this->Meeting_note_model->get_note($id);

            // People picked in the "notify about this update?" prompt.
            $notifyIds = array_filter(array_map('intval', (array) $this->input->post('notify_share_ids')));
            $notified = $notifyIds ? $this->Meeting_note_model->email_participants($note, 'update', $notifyIds) : 0;
            flashSuccess("Note updated." . ($notified ? " {$notified} " . ($notified === 1 ? 'person' : 'people') . " notified." : ''));

            if (!empty($note) && in_array($note->status, ['planned', 'live'], true)) {
                redirect("meeting_notes/edit/" . $id);
            }
            redirect("meeting_notes/index");
        }
    }

    // ---------------------------------------------------------------------
    // Live session endpoints (AJAX, JSON)
    // ---------------------------------------------------------------------

    private function _json($payload)
    {
        echo json_encode($payload);
        exit;
    }

    /**
     * Loads the note for a live endpoint, or answers with an error.
     * @param array|null $statuses statuses the action is allowed in; null = any
     */
    private function _live_note($id, $statuses = ['live'])
    {
        $this->Meeting_note_model->promote_due((int) $id);
        $note = $this->Meeting_note_model->get_note((int) $id);
        if (empty($note)) {
            $this->_json(['result' => false, 'reason' => 'Meeting note not found.']);
        }
        if ($statuses !== null && !in_array($note->status, $statuses, true)) {
            $reasons = ['planned' => "This meeting hasn't started yet.", 'live' => 'This meeting has already started.', 'closed' => 'This meeting has ended.'];
            $this->_json(['result' => false, 'reason' => isset($reasons[$note->status]) ? $reasons[$note->status] : 'Not available for this note.']);
        }
        return $note;
    }

    public function live_save($id)
    {
        if (!isAuthorised(get_class(), "edit")) return false;
        $this->_live_note($id);
        $version = $this->Meeting_note_model->live_save($id, $this->input->post('notes'));
        $this->_json(['result' => $version !== false, 'notes_version' => $version, 'saved_at' => date('H:i:s')]);
    }

    public function share_add($id)
    {
        if (!isAuthorised(get_class(), "edit")) return false;
        $note = $this->_live_note($id, ['planned', 'live']);
        // people[i][type|id|can_contribute]
        $people = $this->input->post('people');
        if (empty($people) || !is_array($people)) {
            $this->_json(['result' => false, 'reason' => 'Select at least one person.']);
        }
        $added = 0;
        $errors = [];
        foreach ($people as $person) {
            $type = isset($person['type']) ? $person['type'] : '';
            $result = $this->Meeting_note_model->add_share($note, $type, isset($person['id']) ? $person['id'] : 0, !empty($person['can_contribute']));
            if ($result['result']) {
                $this->Meeting_note_model->email_participants($note, 'invite', [$result['share_id']]);
                $added++;
            } else {
                $errors[] = $result['reason'];
            }
        }
        $this->_json([
            'result'    => $added > 0,
            'added'     => $added,
            'reason'    => implode(' ', array_unique($errors)),
        ] + $this->_share_state($note));
    }

    public function share_update($id)
    {
        if (!isAuthorised(get_class(), "edit")) return false;
        $this->_live_note($id, null);
        $this->Meeting_note_model->update_share($id, $this->input->post('share_id'), (int) $this->input->post('can_contribute') === 1);
        $this->_json(['result' => true]);
    }

    public function share_remove($id)
    {
        if (!isAuthorised(get_class(), "edit")) return false;
        $note = $this->_live_note($id, null);
        $removed = $this->Meeting_note_model->remove_share($id, $this->input->post('share_id'));
        $this->_json(['result' => $removed] + $this->_share_state($note));
    }

    /** Current share list plus who can still be invited, for redrawing the share panel. */
    private function _share_state($note)
    {
        return [
            'shares'    => $this->Meeting_note_model->get_shares($note->id),
            'shareable' => $this->Meeting_note_model->shareable_people($note),
        ];
    }

    public function entry_add($id)
    {
        if (!isAuthorised(get_class(), "edit")) return false;
        $this->_live_note($id);
        $entryId = $this->Meeting_note_model->add_entry($id, 'admin', $_SESSION['user_id'], $this->input->post('body'));
        $this->_json($entryId === false ? ['result' => false, 'reason' => 'Please write something first.'] : ['result' => true, 'entry_id' => $entryId]);
    }

    public function poll($id)
    {
        if (!isAuthorised(get_class(), "edit")) return false;
        $note = $this->_live_note($id, null);
        $this->Meeting_note_model->touch_admin($id, $_SESSION['user_id']);
        $payload = $this->Meeting_note_model->poll_payload($note, $this->input->get('since_version'), $this->input->get('since_entry_id'));
        unset($payload['notes']); // the admin is the author of the main notes
        $payload['shares'] = $this->Meeting_note_model->get_shares($id);
        $payload['participants'] = $this->Meeting_note_model->get_participants($id, 'admin', $_SESSION['user_id']);
        $this->_json($payload);
    }

    /** Starts a planned meeting before its scheduled time. */
    public function start_session($id)
    {
        if (!isAuthorised(get_class(), "edit")) return false;
        $this->_live_note($id, ['planned']);
        $this->Meeting_note_model->set_status($id, 'live');
        $this->_json(['result' => true]);
    }

    public function end_session($id)
    {
        if (!isAuthorised(get_class(), "edit")) return false;
        $this->_live_note($id);
        $this->Meeting_note_model->set_status($id, 'closed');
        $this->_json(['result' => true]);
    }

    public function delete($id)
    {
        //Access Control
        if (!isAuthorised(get_class(), "delete")) return false;
        
        if ($this->input->is_ajax_request()) {
            $this->Meeting_note_model->delete_note($id);
            echo json_encode(['status' => 'success']);
        } else {
            show_error('No direct access allowed');
        }
    }

    public function send_email($id) {
        $meeting = $this->Meeting_note_model->get_note($id);
        if (!$meeting ) {
            $this->session->set_flashdata('error', 'Meeting not Found');
            return redirect('meeting_notes/view/'.$id);
        }

        $attendees = $meeting->attendees;

        if (empty($attendees)) {
            $this->session->set_flashdata('error', 'No attendees to email.');
            return redirect('meeting_notes/view/'.$id);
        }

        $this->load->library('email');
        $this->email->set_mailtype("html");

        $emails = preg_split('/[\s,;]+/', $attendees);

        $this->load->model("Email_model3");
        $this->load->model("system_model");

        foreach ($emails as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            $emailData = [
                'meeting'     =>  $meeting,
                'logo'      =>  $this->system_model->getParam("logo"),
            ];
            $content = $this->load->view("_email/header",$emailData, true);
            $content .= $this->load->view("_email/meeting_notes",$emailData, true);
            $content .= $this->load->view("_email/footer",[], true);
            $this->Email_model3->save($email,"Meeting Notes",$content);
        }

        flashSuccess('Minutes emailed to attendees.');
        redirect('meeting_notes/view/'.$id);
    }


    public function delete_image($imageId)
    {
        //Access Control
        if (!isAuthorised(get_class(), "delete")) return false;

        $this->db->where("id",$imageId)->delete("attachments");
        echo json_encode(array("result"=>true));
        exit;
    }

    public function convertToTask($id)
    {
        //Access Control
        if (!isAuthorised(get_class(), "view")) return false;
        
        // Check if user has permission to create tasks
        if (!isAuthorised("Tasks", "add")) {
            flashDanger("You don't have permission to create tasks.");
            redirect('meeting_notes/view/'.$id);
            return;
        }

        $note = $this->Meeting_note_model->get_note($id);
        
        if (!$note) {
            flashDanger("Meeting note not found.");
            redirect('meeting_notes/index');
            return;
        }

        if (empty($note->customer_id)) {
            flashDanger("Cannot convert meeting note to task: No customer associated with this meeting note.");
            redirect('meeting_notes/view/'.$id);
            return;
        }

        // Redirect to intermediate form
        redirect('meeting_notes/convertToTaskForm/'.$id);
    }

    public function convertToTaskForm($id)
    {
        //Access Control
        if (!isAuthorised(get_class(), "view")) return false;
        
        // Check if user has permission to create tasks
        if (!isAuthorised("Tasks", "add")) {
            flashDanger("You don't have permission to create tasks.");
            redirect('meeting_notes/view/'.$id);
            return;
        }

        $note = $this->Meeting_note_model->get_note($id);
        
        if (!$note) {
            flashDanger("Meeting note not found.");
            redirect('meeting_notes/index');
            return;
        }

        if (empty($note->customer_id)) {
            flashDanger("Cannot convert meeting note to task: No customer associated with this meeting note.");
            redirect('meeting_notes/view/'.$id);
            return;
        }

        // Parse notes for list items
        $listItems = $this->parseListItems($note->notes);
        
        if (empty($listItems)) {
            flashDanger("No list items found in meeting notes. Please ensure your notes contain bulleted or numbered lists (ul/ol with li elements).");
            redirect('meeting_notes/view/'.$id);
            return;
        }

        //Breadcrumbs
        $this->mybreadcrumb->add('View', base_url('meeting_notes/view/'.$id));
        $this->mybreadcrumb->add('Convert to Tasks', base_url('meeting_notes/convertToTaskForm/'.$id));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        // page title
        $this->data['page_title'] = "Convert Meeting Notes to Tasks";
        
        $this->load->model('Customers_model');
        $this->data['customers'] = $this->Customers_model->lookup();
        
        $this->load->model("Developers_model");
        $this->data['developers'] = $this->Developers_model->lookup();
        
        $this->data['note'] = $note;
        $this->data['list_items'] = $listItems;

        $this->data["content"]=$this->load->view("meeting_notes/convert_to_task",$this->data,true);
        $this->load->view("/layouts/default",$this->data);
    }

    private function parseListItems($notes)
    {
        if (empty($notes)) {
            return [];
        }

        $listItems = [];
        
        // Load HTML content
        $dom = new DOMDocument();
        // Suppress warnings for malformed HTML
        libxml_use_internal_errors(true);
        
        // Try to load as HTML fragment
        $html = '<div>' . $notes . '</div>';
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        
        // Find all ul and ol elements
        $xpath = new DOMXPath($dom);
        $lists = $xpath->query('//ul | //ol');
        
        foreach ($lists as $list) {
            $listItemsInList = $xpath->query('.//li', $list);
            foreach ($listItemsInList as $li) {
                $text = trim($li->textContent);
                if (!empty($text)) {
                    $listItems[] = $text;
                }
            }
        }
        
        // If no HTML lists found, try to parse plain text lists (lines starting with -, *, or numbers)
        if (empty($listItems)) {
            $lines = explode("\n", $notes);
            foreach ($lines as $line) {
                $line = trim($line);
                // Match lines starting with -, *, or number followed by period/dash
                if (preg_match('/^[\-\*]\s+(.+)$/', $line, $matches) || 
                    preg_match('/^\d+[\.\)]\s+(.+)$/', $line, $matches)) {
                    $listItems[] = $matches[1];
                }
            }
        }
        
        return $listItems;
    }

    public function createTasksFromNote()
    {
        //Access Control
        if (!isAuthorised("Tasks", "add")) {
            echo json_encode(['result' => false, 'reason' => 'Permission denied']);
            exit;
        }

        $meeting_note_id = $this->input->post('meeting_note_id');
        $customer_id = $this->input->post('customer_id');
        $project_id = $this->input->post('project_id');
        $sprint_id = $this->input->post('sprint_id');
        $task_names = $this->input->post('task_names');
        $task_descriptions = $this->input->post('task_descriptions');
        $task_sections = $this->input->post('task_sections');
        $task_due_dates = $this->input->post('task_due_dates');
        $task_estimated_hours = $this->input->post('task_estimated_hours');
        $task_developers = $this->input->post('task_developers');
        $default_due_date = $this->input->post('default_due_date');
        $default_estimated_hours = $this->input->post('default_estimated_hours');
        $default_developers = $this->input->post('default_developers');

        if (empty($meeting_note_id) || empty($customer_id) || empty($project_id) || empty($sprint_id)) {
            echo json_encode(['result' => false, 'reason' => 'Missing required fields']);
            exit;
        }

        $note = $this->Meeting_note_model->get_note($meeting_note_id);
        if (!$note) {
            echo json_encode(['result' => false, 'reason' => 'Meeting note not found']);
            exit;
        }

        $this->load->model('Tasks_model');
        $this->load->helper('general');
        
        // Get the max task number for this sprint
        $maxTN = $this->db->query("SELECT MAX(task_number) as tn FROM tasks WHERE sprint_id = '$sprint_id' AND status = 1")->row()->tn;
        $currentTaskNumber = !empty($maxTN) ? incrementTaskNumber($maxTN) : '001';
        
        $created_tasks = [];
        // Task ids per set of developers, so everyone gets a single digest of their own tasks
        $created_groups = [];
        $errors = [];

        foreach ($task_names as $index => $task_name) {
            if (empty(trim($task_name))) {
                continue; // Skip empty task names
            }

            // Get per-task values, fallback to defaults if not set
            $task_due_date = !empty($task_due_dates[$index]) ? $task_due_dates[$index] : $default_due_date;
            $task_hours = !empty($task_estimated_hours[$index]) ? floatval($task_estimated_hours[$index]) : (!empty($default_estimated_hours) ? floatval($default_estimated_hours) : 1);
            $task_devs = !empty($task_developers[$index]) && is_array($task_developers[$index]) ? $task_developers[$index] : $default_developers;
            
            // Convert developers array to JSON for userIds
            $userIds_json = '[]';
            if (!empty($task_devs) && is_array($task_devs)) {
                $userIds_json = json_encode(array_map('intval', $task_devs));
            }

            $task_data = [
                'name' => trim($task_name),
                'description' => !empty($task_descriptions[$index]) ? trim($task_descriptions[$index]) : '',
                'section' => !empty($task_sections[$index]) ? trim($task_sections[$index]) : 'Meeting Notes',
                'sprint_id' => $sprint_id,
                'task_number' => $currentTaskNumber,
                'stage' => 'new',
                'progress' => 0,
                'due_date' => !empty($task_due_date) ? $task_due_date : null,
                'estimated_hours' => $task_hours,
                'scope_client_expectation' => '',
                'scope_not_included' => '',
                'scope_when_done' => '',
                'source' => 'meeting',
                'userIds' => $userIds_json, // Developers assigned per task or from defaults
                // One digest for the whole conversion is sent below, instead of emails per task
                '_skip_notifications' => true,
            ];
            
            // Increment task number for next task
            $currentTaskNumber = incrementTaskNumber($currentTaskNumber);

            // Generate meeting reference hash
            $meeting_hash = substr(md5($meeting_note_id . $note->meeting_datetime . $note->customer_id), 0, 8);
            $meeting_ref = 'MN-' . strtoupper($meeting_hash);
            $meeting_link = base_url('meeting_notes/view/' . $meeting_note_id);
            
            // Add meeting note reference link to description (using HTML for clickable link)
            $meeting_context = "<a href=\"{$meeting_link}\" target=\"_blank\" style=\"color: #007bff; text-decoration: underline;\">View Meeting Notes {$meeting_ref}</a>\n\n";
            
            if (!empty($task_data['description'])) {
                $task_data['description'] = $meeting_context . $task_data['description'];
            } else {
                $task_data['description'] = $meeting_context . "Converted from meeting notes.";
            }

            $response = $this->Tasks_model->save($task_data, []);
            
            if ($response['result']) {
                $created_tasks[] = $task_name;
                $created_groups[$userIds_json][] = $response['id'];
            } else {
                $errors[] = $task_name . ': ' . $response['reason'];
            }
        }

        if (!empty($created_tasks)) {
            $this->Tasks_model->notifyImportedTasks(
                $created_groups,
                count($created_tasks) . ' task(s) created from meeting notes',
                'Tasks created from meeting notes',
                '<p>' . count($created_tasks) . ' task(s) were created from <a href="' . base_url('meeting_notes/view/' . $meeting_note_id) . '">the meeting notes</a> of ' . htmlspecialchars((string) $note->meeting_datetime) . '.</p>'
            );

            echo json_encode([
                'result' => true,
                'message' => 'Successfully created ' . count($created_tasks) . ' task(s)',
                'created_tasks' => $created_tasks,
                'errors' => $errors
            ]);
        } else {
            echo json_encode([
                'result' => false,
                'reason' => 'Failed to create tasks: ' . implode(', ', $errors)
            ]);
        }
        exit;
    }

    /** A planned meeting must start in the future (it goes live automatically at that time). */
    public function validate_planned_datetime($meeting_time)
    {
        $start = strtotime(trim((string) $this->input->post('meeting_date')) . ' ' . trim((string) $meeting_time));
        if ($start === false || $start <= time()) {
            $this->form_validation->set_message('validate_planned_datetime', 'A planned meeting must start in the future. To start now, use Start Live Session.');
            return false;
        }
        return true;
    }

    public function validate_next_meeting_date($next_date)
    {
        $next_date = trim((string) $next_date);
        if ($next_date === '') {
            return true;
        }

        $next_time = trim((string) $this->input->post('next_meeting_time'));
        if ($next_time === '') {
            $this->form_validation->set_message(
                'validate_next_meeting_date',
                'Next meeting time is required when a next meeting date is set.'
            );
            return false;
        }

        $next_dt = $next_date . ' ' . $next_time;
        $meeting_date = trim((string) $this->input->post('meeting_date'));
        $meeting_time = trim((string) $this->input->post('meeting_time'));
        if ($meeting_date !== '') {
            $meeting_dt = $meeting_date . ' ' . ($meeting_time !== '' ? $meeting_time : '00:00');
            if (strtotime($next_dt) !== false && strtotime($meeting_dt) !== false && strtotime($next_dt) < strtotime($meeting_dt)) {
                $this->form_validation->set_message(
                    'validate_next_meeting_date',
                    'The next meeting cannot be before the current meeting date and time.'
                );
                return false;
            }
        }

        return true;
    }
}
