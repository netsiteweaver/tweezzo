<?php

class System_model extends CI_Model {

    public function getCounter()
    {

        $this->db->select("hits");
        $this->db->where("lang", "");
        $this->db->where("controller", "");
        $this->db->where("method", "");
        $query = $this->db->get("counter");

        return $query->row("hits");
    }

    public function getCompanyInfo()
    {

        $query = $this->db->get('company');
        return $query->row();
    }

    public function updateCompany()
    {
        $postedData = $this->input->post();
        $this->db->set('name', $postedData['name']);
        $this->db->set('legal_name', $postedData['legal_name']);
        $this->db->set('brn', $postedData['brn']);
        $this->db->set('vat', $postedData['vat']);
        $this->db->set('address1', $postedData['address1']);
        $this->db->set('address2', $postedData['address2']);
        $this->db->set('city', $postedData['city']);
        $this->db->set('country', $postedData['country']);
        $this->db->set('phone', $postedData['phone']);
        $this->db->set('mobile', $postedData['mobile']);
        $this->db->set('fax', $postedData['fax']);
        $this->db->set('email', $postedData['email']);
        $this->db->set('working_hours', $postedData['working_hours']);
        
        // //working hours
        // $working_hours_type = $postedData['working_hours_type'];

        // if($working_hours_type=='detailed'){
        //     $working_hours = array(
        //         'Monday'	=>	$postedData['Monday'],
        //         'Tuesday'	=>	$postedData['Tuesday'],
        //         'Wednesday'	=>	$postedData['Wednesday'],
        //         'Thursday'	=>	$postedData['Thursday'],
        //         'Friday'	=>	$postedData['Friday'],
        //         'Saturday'	=>	$postedData['Saturday'],
        //         'Sunday'	=>	$postedData['Sunday'],
        //         'PublicHoliday'	=>	$postedData['PublicHoliday']
        //     );
        //     $this->db->set('working_hours', json_encode($working_hours));
        // }else{//global
        //     $this->db->set('working_hours', $postedData['working_hours_global']);
        // }

		//social media        
        $this->db->set('facebook', $postedData['facebook']);
        $this->db->set('twitter', $postedData['twitter']);
        $this->db->set('linkedin', $postedData['linkedin']);
        $this->db->set('instagram', $postedData['instagram']);
        $this->db->set('youtube', $postedData['youtube']);
        $this->db->set('skype', $postedData['skype']);
        $this->db->set('whatsapp', $postedData['whatsapp']);

        $this->db->update("company");
    }

    public function getParams($keys)
    {
        // $this->db->select('title','value');
        $this->db->where_in("title", $keys);
        $obj = $this->db->get("params");
        if ($obj) {
            $result = $obj->result();
            $params = Array();
            foreach($result as $row){
                $params[$row->title]  = $row->value;
            }
            return $params;
        } else {
            return false;
        }
    }

    public function updateParams()
    {
        $postedData = $this->input->post();

        $this->load->model("accesscontrol_model");
        $notifications = $this->accesscontrol_model->authorised("settings","notifications");

        if($notifications){
            if(isset($postedData['send_enquiries_to'])) {
                foreach ($postedData['send_enquiries_to'] as $key => $value) {
                    $send_enquiries_to[] = $value;
                }
                $postedData['send_enquiries_to'] = json_encode($send_enquiries_to);
            }else{
                $postedData['send_enquiries_to'] = "[]";
            }
        }
        $postedData['smtp_settings'] = json_encode($postedData['smtp_settings']);
        foreach ($postedData as $key => $data) {
            $this->setParam($key, $data);
        }
    }

    public function getClientInfo()
    {
        $this->db->where('id', '99');
        $qry = $this->db->get('system');
        if (($qry) && ($qry->num_rows() == 1)) {
            return $qry->row();
        }
    }

    public function getVersion()
    {
        $this->db->select("version");
        $this->db->order_by("date", "DESC");
        $obj = $this->db->get("_version");
        if ($obj) {
            return $obj->row();
        } else {
            return false;
        }
    }

    public function getInvoiceTemplate()
    {
        //check from 'param' table to know which invoice template to use
        //invoice templates are stored in applications/views/invoices
        $this->db->select('value');
        $this->db->where("key", "invoice_template");
        $obj = $this->db->get("params");
        if ($obj) {
            $result = $obj->row();
            return $result->value;
        } else {
            return false;
        }
    }

    public function getReservationTemplate()
    {
        //check from 'param' table to know which invoice template to use
        //invoice templates are stored in applications/views/invoices
        $this->db->select('value');
        $this->db->where("key", "reservation_template");
        $obj = $this->db->get("params");
        if ($obj) {
            $result = $obj->row();
            return $result->value;
        } else {
            return false;
        }
    }

    public function getLogo()
    {
        $this->db->select('value');
        $this->db->where("title", "logo");
        $obj = $this->db->get("params");
        if ($obj) {
            $result = $obj->row();
            return $result->value;
        } else {
            return false;
        }
    }

    public function getProductRowsPerPage()
    {
        $this->db->select('value');
        $this->db->where("key", "product_rows_per_page");
        $obj = $this->db->get("params");
        if ($obj) {
            $result = $obj->row();
            return $result->value;
        } else {
            return false;
        }
    }

    public function getClientRowsPerPage()
    {
        $this->db->select('value');
        $this->db->where("key", "client_rows_per_page");
        $obj = $this->db->get("params");
        if ($obj) {
            $result = $obj->row();
            return $result->value;
        } else {
            return false;
        }
    }

    public function getSupplierRowsPerPage()
    {
        $this->db->select('value');
        $this->db->where("key", "supplier_rows_per_page");
        $obj = $this->db->get("params");
        if ($obj) {
            $result = $obj->row();
            return $result->value;
        } else {
            return false;
        }
    }

    public function getBrandRowsPerPage()
    {
        $this->db->select('value');
        $this->db->where("key", "brand_rows_per_page");
        $obj = $this->db->get("params");
        if ($obj) {
            $result = $obj->row();
            return $result->value;
        } else {
            return false;
        }
    }

    public function getCategoryRowsPerPage()
    {
        $this->db->select('value');
        $this->db->where("key", "category_rows_per_page");
        $obj = $this->db->get("params");
        if ($obj) {
            $result = $obj->row();
            return $result->value;
        } else {
            return false;
        }
    }

    public function showQuickbar()
    {
        $this->db->select('value');
        $this->db->where("key", "quickbar");
        $obj = $this->db->get("params");
        if ($obj) {
            $result = $obj->row();
            return $result->value;
        } else {
            return false;
        }
    }

    public function showBreadcrumbs()
    {
        $this->db->select('value');
        $this->db->where("key", "breadcrumbs");
        $obj = $this->db->get("params");
        if ($obj) {
            $result = $obj->row();
            return $result->value;
        } else {
            return false;
        }
    }

    public function saveSettings()
    {
        $count = 0;
        $alertTimeout = $this->input->post('alertTimeout');
        if (!empty($alertTimeout)) {
            $this->db->where('key', 'alert_timeout');
            $this->db->update('params', array('value' => $alertTimeout));
            $count++;
        }
        $getInvoiceTemplate = $this->input->post('getInvoiceTemplate');
        if (!empty($getInvoiceTemplate)) {
            $this->db->where('key', 'invoice_template');
            $this->db->update('params', array('value' => $getInvoiceTemplate));
            $count++;
        }
        $getReservationTemplate = $this->input->post('getReservationTemplate');
        if (!empty($getReservationTemplate)) {
            $this->db->where('key', 'reservation_template');
            $this->db->update('params', array('value' => $getReservationTemplate));
            $count++;
        }
        // echo $getLogo = $this->input->post('getLogo');
        $getProductRowsPerPage = $this->input->post('getProductRowsPerPage');
        if (!empty($getProductRowsPerPage)) {
            $this->db->where('key', 'product_rows_per_page');
            $this->db->update('params', array('value' => $getProductRowsPerPage));
            $count++;
        }
        $getClientRowsPerPage = $this->input->post('getClientRowsPerPage');
        if (!empty($getClientRowsPerPage)) {
            $this->db->where('key', 'client_rows_per_page');
            $this->db->update('params', array('value' => $getClientRowsPerPage));
            $count++;
        }
        $getSupplierRowsPerPage = $this->input->post('getSupplierRowsPerPage');
        if (!empty($getSupplierRowsPerPage)) {
            $this->db->where('key', 'supplier_rows_per_page');
            $this->db->update('params', array('value' => $getSupplierRowsPerPage));
            $count++;
        }
        $getBrandRowsPerPage = $this->input->post('getBrandRowsPerPage');
        if (!empty($getBrandRowsPerPage)) {
            $this->db->where('key', 'brand_rows_per_page');
            $this->db->update('params', array('value' => $getBrandRowsPerPage));
            $count++;
        }
        $getCategoryRowsPerPage = $this->input->post('getCategoryRowsPerPage');
        if (!empty($getCategoryRowsPerPage)) {
            $this->db->where('key', 'category_rows_per_page');
            $this->db->update('params', array('value' => $getCategoryRowsPerPage));
            $count++;
        }
        $showQuickbar = $this->input->post('showQuickbar');
        $this->db->where('key', 'quickbar');
        $this->db->update('params', array('value' => $showQuickbar));
        $count++;

        $showBreadcrumbs = $this->input->post('showBreadcrumbs');
        $this->db->where('key', 'breadcrumbs');
        $this->db->update('params', array('value' => $showBreadcrumbs));
        $count++;

        return $count;
    }

    /**
     * getParam fetch specified key from params table
     * 
     * @param String $key
     * @return String
     */
    public function getParam($key,$json=false)
    {
        $this->db->select('value');
        $this->db->where("title", $key);
        $obj = $this->db->get("params");
        // echo $this->db->last_query().'<br>';
        if ($obj) {
            $result = $obj->row();
            if(!$json){
                return ($result)?$result->value:null;
            }else{
                return ($result)?json_decode($result->value):null;
            }
        } else {
            return false;
        }
    }

    public function setParam($key, $value)
    {
        $chk = $this->db->select('count(id) as ct')->from('params')->where('title',$key)->get()->row('ct');
        if($chk=="0"){
            $this->db->set('title',$key);
            $this->db->set('value',$value);
            $this->db->insert('params');
        }else{
            $this->db->where('title', $key);
            $this->db->set('value', $value);
            $this->db->update('params');        
        }
            return true;
    }

    public function getAllParams()
    {
        $this->db->select("title,value");
        return $this->db->get("params")->result();
    }

    public function getMoney($type = ""){

        if (!empty($type))
            $this->db->where('type', $type);
        $obj = $this->db->get("money");
        if ($obj) {
            return $obj->result();
        } else {
            return null;
        }
    }

    public function saveSidebarStatus($state)
    {
        $state = ($state=="true")?"1":"0";
        $this->setParam("sidebar_collapse",$state);
        return true;
    }

    public function getCurrencies()
    {
        return $this->db->from('currencies')->where('base',0)->get()->result();
    }

    public function getRateOfExchange()
    {
        $response = file_get_contents("http://free.currencyconverterapi.com/api/v5/convert?q=USD_MUR&compact=y");
        $data = json_decode($response);

        $rate = $data->USD_MUR->val;

        return $rate;
    }

    public function updatenotifications()
    {
        $notif = json_encode(!empty($this->input->post("data"))?$this->input->post("data"):[]);
        $type = $this->input->post("type");
        $this->db->set("value", $notif)->where("title","notification_".$type)->update("params");
    }

    public function updateAllNotifications()
    {
        $notifs = [];
        foreach($_POST['notifications'] as $n){
            $notifs[$n['type']][] = $n['user_id'];
        }

        foreach($notifs as $key => $value){
            $this->db->set("value", json_encode($value) )->where("title","notification_".$key)->update("params");
        }
    }

    public function updatenotifications2()
    {
        $notif = json_encode(!empty($this->input->post("data"))?$this->input->post("data"):[]);
        $this->db->set("value",$notif)->where("title","notification_reset_password")->update("params");
    }    
    
    public function updatenotifications3()
    {
        $this->db->set("value",$this->input->post("workshop_department"))->where("title","workshop_department")->update("params");
    }
}
