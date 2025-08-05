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
        if (!isAuthorised(get_class(), "view")) return false;

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
        if (!isAuthorised(get_class(), "add")) return false;

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

    public function delete_image($imageId)
    {
        $this->db->where("id",$imageId)->delete("attachments");
        echo json_encode(array("result"=>true));
        exit;
    }
}
