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

        $this->data['notes'] = $this->Meeting_note_model->get_all_notes();

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
        $this->form_validation->set_rules('notes', 'Notes', 'required');

        

        if ($this->form_validation->run() === FALSE) {
            $this->data["content"]=$this->load->view("meeting_notes/create",$this->data,true);
            $this->load->view("/layouts/default",$this->data);   
        } else {
            $data = [
                'customer_id' => $this->input->post('customer_id'),
                'customer_name' => $this->input->post('customer_name'),
                'meeting_datetime' => $this->input->post('meeting_date') . ' ' . $this->input->post('meeting_time'),
                'notes' => $this->input->post('notes'),
                'attendees' => $this->input->post('attendees', true),
                'lieu' => $this->input->post('lieu', true),
            ];
            $this->Meeting_note_model->insert_note($data);

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

        $this->data['note'] = $this->Meeting_note_model->get_note($id);

        $this->data['attachments'] = $this->Attachment_model->get_attachments($id);

        $this->data['page_title'] = "Edit Meeting Note";

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
        $this->form_validation->set_rules('notes', 'Notes', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->edit($id);
        } else {
            $data = [
                'customer_id' => $this->input->post('customer_id'),
                'customer_name' => $this->input->post('customer_name'),
                'meeting_datetime' => $this->input->post('meeting_date') . ' ' . $this->input->post('meeting_time'),
                'notes' => $this->input->post('notes'),
                'attendees' => $this->input->post('attendees', true),
                'lieu' => $this->input->post('lieu', true),
            ];
            $this->Meeting_note_model->update_note($id);
            redirect("meeting_notes/index");
        }
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
}
