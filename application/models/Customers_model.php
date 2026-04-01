<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Customers_model extends CI_Model
{

    public function get($uuid="",$page="",$rows_per_page="",$search_text="",$hide_completed=false,$active_filter="active")
    {
        if(empty($uuid)){
            $this->db->select("c.*,u.name agent");
            $this->db->from("customers c");
            $this->db->join("users u","u.id=c.created_by","left");
            
            $page = (empty($page))?1:$page;
            if(!empty($search_text)){
                $this->db->group_start();
                $this->db->like("company_name",$search_text);
                $this->db->or_like("full_name",$search_text);
                $this->db->or_like("phone_number1",$search_text);
                $this->db->or_like("phone_number2",$search_text);
                $this->db->or_like("address",$search_text);
                $this->db->or_like("city",$search_text);
                $this->db->group_end();
                // $page = 1;
            }else{
                // $page = (empty($page))?1:$page;
            }
            $offset = ($page-1) * $rows_per_page;
            $this->db->where(array("c.status"=>'1'));
            
            // Filter by active status
            if($active_filter == 'active'){
                $this->db->where("c.active", '1');
            } elseif($active_filter == 'inactive'){
                $this->db->where("c.active", '0');
            }
            // If active_filter is 'all', don't filter by active status
            
            // If hide_completed is enabled, exclude customers with all tasks completed
            if($hide_completed){
                $sql = "c.customer_id NOT IN (
                    SELECT DISTINCT c2.customer_id 
                    FROM customers c2
                    LEFT JOIN projects p ON p.customer_id = c2.customer_id AND p.status = 1 AND p.active = 1
                    LEFT JOIN sprints s ON s.project_id = p.id AND s.status = 1 AND s.active = 1
                    LEFT JOIN tasks t ON t.sprint_id = s.id AND t.status = 1 AND t.closed = 0
                    WHERE c2.status = 1
                    GROUP BY c2.customer_id
                    HAVING COUNT(t.id) > 0 AND COUNT(t.id) = SUM(CASE WHEN t.stage = 'completed' THEN 1 ELSE 0 END)
                )";
                $this->db->where($sql, NULL, FALSE);
            }
            
            $this->db->order_by("company_name");
            $this->db->limit($rows_per_page,$offset);
            $query = $this->db->get();
            return $query->result();
        }else{
            $this->db->select("c.*,u.name agent");
            $this->db->from("customers c");
            $this->db->join("users u","u.id=c.created_by","left");
            $this->db->where(array("c.uuid"=>$uuid,"c.status"=>'1'));
            $query = $this->db->get();
            $customer = $query->row();
            if(empty($customer)) return false;
            $customer->access = $this->db->select("*")->from("customer_access")->where(array("customer_id"=>$customer->customer_id,"status"=>"1"))->get()->result();
            return $customer;
        }

    }

    public function total_records($search_text="",$hide_completed=false,$active_filter="active")
    {
        $this->db->select("count(c.customer_id) as ct")
                ->from("customers c")
                ->where("c.status","1");
        
        // Filter by active status
        if($active_filter == 'active'){
            $this->db->where("c.active", '1');
        } elseif($active_filter == 'inactive'){
            $this->db->where("c.active", '0');
        }
        // If active_filter is 'all', don't filter by active status
        
        if(!empty($search_text)){
            $this->db->group_start();
            $this->db->like("c.company_name",$search_text);
            $this->db->or_like("c.full_name",$search_text);
            $this->db->or_like("c.phone_number1",$search_text);
            $this->db->or_like("c.phone_number2",$search_text);
            $this->db->or_like("c.address",$search_text);
            $this->db->or_like("c.city",$search_text);
            $this->db->group_end();
        }
        
        // If hide_completed is enabled, exclude customers with all tasks completed
        if($hide_completed){
            $sql = "c.customer_id NOT IN (
                SELECT DISTINCT c2.customer_id 
                FROM customers c2
                LEFT JOIN projects p ON p.customer_id = c2.customer_id AND p.status = 1 AND p.active = 1
                LEFT JOIN sprints s ON s.project_id = p.id AND s.status = 1 AND s.active = 1
                LEFT JOIN tasks t ON t.sprint_id = s.id AND t.status = 1 AND t.closed = 0
                WHERE c2.status = 1
                GROUP BY c2.customer_id
                HAVING COUNT(t.id) > 0 AND COUNT(t.id) = SUM(CASE WHEN t.stage = 'completed' THEN 1 ELSE 0 END)
            )";
            $this->db->where($sql, NULL, FALSE);
        }
        
        return $this->db->get()->row("ct");
    }

    public function update_portal_access()
    {
        $password = $this->input->post("password");
        $email = $this->input->post("email");
        $this->db->set("password",md5($password),true);
        $this->db->where("uuid",$this->input->post("uuid"));
        $this->db->update("customers");

        if(isset($_POST['send_password'])){

            // $email = $this->input->post("email");
            $this->load->model("Email_model3");
            $this->load->model("system_model");
            $emailData = [
                'password'          =>  $password,
                'logo'              =>  $this->system_model->getParam("logo"),
                'link'              =>  base_url('portal/customers/signin?email='.$email),
                'link_label'        =>  "Access Customer's Portal",
                'show_lifecycle'    =>  false
            ];
            $content = $this->load->view("_email/header",$emailData, true);
            $content .= $this->load->view("_email/emailPasswordToCustomer",$emailData, true);
            $content .= $this->load->view("_email/footer",[], true);
            // $path = realpath(".");
            // $h = fopen($path . "/data/taskListSent_".date('YmdHis').".html",'w');
            // if($h){
            //     fwrite($h,$content);
            //     fclose($h);    
            // }else{
            //     die('cannot create file');
            // }
            $this->Email_model3->save($email,"Your Password Has Been Updated",$content);

            flashSuccess("Email has been queued to customer with updated password");
            
        }
        
    }

    public function save()
    {
        $valid = true;
        $error_message = "";
        if(empty($this->input->post("company_name"))) {
            $error_message .= "Company Name is Mandatory<br>";
            $valid = false;
        }
        if(empty($this->input->post("full_name"))) {
            $error_message .= "Customer Name is Mandatory<br>";
            $valid = false;
        }
        // if(empty($this->input->post("phone_number1"))) {
        //     $error_message .= "Phone Number is Mandatory<br>";
        //     $valid = false;
        // }
        if(empty($this->input->post("email"))) {
            $error_message .= "Email is Mandatory<br>";
            $valid = false;
        }
        if( !$valid ) {
            return array("result"=>false,"reason"=>$error_message);
        }
        $this->db->set("company_name",$this->input->post("company_name"));
        $this->db->set("full_name",$this->input->post("full_name"));
        $this->db->set("address",$this->input->post("address"));
        $this->db->set("email",$this->input->post("email"));
        $this->db->set("phone_number1",$_POST['phone_number1']);
        $this->db->set("phone_number2",$_POST['phone_number2']);
        $this->db->set("active",isset($_POST['active'])?'1':'0');
        // $this->db->set("vat",$_POST['vat']);
        // $this->db->set("brn",$_POST['brn']);
        $this->db->set("remarks",$this->input->post("remarks"));
        $this->db->set("status","1");

        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        
        if(empty($this->input->post("uuid"))){
            $_POST['uuid'] = gen_uuid();
            $_POST['password'] = genPassword(12);
            $this->db->set("uuid",$_POST['uuid']);
            $this->db->set("created_by",$_SESSION['user_id']);
            $this->db->set("created_date","NOW()",FALSE);
            $this->db->set("active","1");
            $this->db->insert("customers");
            $customer_id = $this->db->insert_id();

            $this->db->set("password",md5($_POST['password']));
            $this->db->set("name",$this->input->post("full_name"));
            // $this->db->set("job_description",$this->input->post("job_description"));
            $this->db->set("email",$this->input->post("email"));
            $this->db->set("customer_id",$customer_id);
            $this->db->set("country_code",'mu');
            $this->db->set("created_by",$_SESSION['user_id']);
            $this->db->set("created_on","NOW()",FALSE);
            $this->db->insert("customer_access");

            $check = $this->db->error();
            if($check['code']>0){
                return array("result"=>false,"reason"=>$check['message']);
            }
            $this->email($_POST);
        }else{
            $this->db->set("active",isset($_POST['active'])?'1':'0');
            $this->db->where("uuid",$this->input->post("uuid"));
            $this->db->update("customers");
            $check = $this->db->error();
            if($check['code']>0){
                return array("result"=>false,"reason"=>$check['message']);
            }
            $_POST['uuid'] = $this->input->post("uuid");
        }

        $this->db->db_debug = $db_debug;
        return array("result"=>true,"uuid"=>$_POST['uuid']);
    }

    private function email($data)
    {
        $this->load->model("Email_model3");
        $this->load->model("system_model");

        if(!empty($this->input->post("send_introduction_email"))) {
            //send customer introduction email
            $emailData = [
                'email'        =>  $data['email'],
                // 'password'     =>  $data['password'],
                'title'         =>  'New Customer Created',
                'logo'          =>  $this->system_model->getParam("logo"),
                'link'          =>  base_url('customers/view/'.$data['uuid']),
                'link_label'    =>  'View Customer'
            ];
            $content = $this->load->view("_email/header",$emailData, true);
            $content .= $this->load->view("_email/welcomeTaskManager",$emailData, true);
            $content .= $this->load->view("_email/footer",[], true);
            $this->Email_model3->save($this->input->post('email'),"Experience our newly developed Task Manager",$content);
        }

        if(!empty($this->input->post("email_customer"))) {
            // send email to customer for account created
            $emailData = [
                'customer'      =>  $data,
                'password'      =>  $data['password'],
                'logo'          =>  $this->system_model->getParam("logo"),
                'link'          =>  base_url('portal/customers/signin/'),
                'link_label'    =>  "Access Portal",
            ];
            $content = $this->load->view("_email/header",$emailData, true);
            $content .= $this->load->view("_email/customerAdded",$emailData, true);
            $content .= $this->load->view("_email/footer",[], true);
            $this->Email_model3->save($this->input->post('email'),"New Customer Created",$content);
        }

        //send admins email for account creation
        $notification_create_customers = $this->system_model->getParam("notification_create_customers",true);
        foreach($notification_create_customers as $admin){
            $email = $this->db->select("email")->from("users")->where("id",$admin)->get()->row()->email;

            $this->load->model("Email_model3");
            $this->load->model("system_model");
            $emailData = [
                'customer'      =>  $data,
                'password'      =>  $data['password'],
                'logo'          =>  $this->system_model->getParam("logo"),
                'link'          =>  base_url('portal/customers/signin/'),
                'link_label'    =>  "Access Portal",
            ];
            $content = $this->load->view("_email/header",$emailData, true);
            $content .= $this->load->view("_email/customerAdded",$emailData, true);
            $content .= $this->load->view("_email/footer",[], true);
            $this->Email_model3->save($email,"New Customer Created",$content);
        }
    }

    public function fetch($uuid="",$searchTerm="")
    {
        $this->db->select("c.*,u.name agent");
        $this->db->from("customers c");
        $this->db->join("users u","u.id=c.created_by","left");
        if(!empty($uuid)) $this->db->where("c.uuid",$uuid);
        if(!empty($searchTerm)){
            $this->db->group_start();
            $this->db->like("c.company_name",$searchTerm);
            $this->db->or_like("c.full_name",$searchTerm);
            $this->db->or_like("c.phone_number1",$searchTerm);
            $this->db->or_like("c.phone_number2",$searchTerm);
            $this->db->or_like("c.email",$searchTerm);
            $this->db->or_like("c.vat",$searchTerm);
            $this->db->or_like("c.brn",$searchTerm);
            $this->db->or_like("c.address",$searchTerm);
            $this->db->or_like("c.city",$searchTerm);
            $this->db->group_end();
        }
        $this->db->where("c.status","1");
        $this->db->order_by("company_name");
        return $this->db->get()->result();
    }

    public function lookup()
    {
        return $this->db->select("customer_id,uuid,company_name,full_name")->from("customers")->order_by('company_name, full_name')->where(["status"=>"1","active"=>"1"])->get()->result();
    }

    public function info($uuid)
    {
        $customer = $this->db->select("*")->from("customers")->where("uuid",$uuid)->get()->row();
        if(!empty($customer)){
            $taskCount = $this->db->query("select count(t.id) as taskCount  
                                            from customers c 
                                            join projects p on p.customer_id = c.customer_id 
                                            join sprints s on s.project_id = p.id 
                                            join tasks t on t.sprint_id = s.id
                                            where c.uuid = '$uuid';")->row()->taskCount;  

            $sprintCount = $this->db->query("select count(s.id) as sprintCount
                                            from customers c 
                                            join projects p on p.customer_id = c.customer_id 
                                            join sprints s on s.project_id = p.id
                                            where c.uuid = '$uuid';")->row()->sprintCount;                            

            $projectCount = $this->db->query("select count(p.id) as projectCount
                                            from customers c 
                                            join projects p on p.customer_id = c.customer_id 
                                            where c.uuid = '$uuid'")->row()->projectCount;         
                                            
            return array(
                "customer"=>$customer,
                "taskCount"=>$taskCount,
                "sprintCount"=>$sprintCount,
                "projectCount"=>$projectCount
            );                                            
        }

        return false;
        
    }

    public function toggleActive($uuid)
    {
        // Get customer details
        $customer = $this->db->select("customer_id, active, company_name")->where("uuid",$uuid)->get("customers")->row();
        
        if(empty($customer)){
            return array("result"=>false,"reason"=>"Customer not found");
        }

        // Toggle active status
        $newActiveStatus = ($customer->active == '1') ? '0' : '1';
        
        // Update customer
        $this->db->where("uuid",$uuid)->update("customers",array("active"=>$newActiveStatus));
        $affectedCustomer = $this->db->affected_rows();

        // Get customer_id for cascading
        $customer_id = $customer->customer_id;

        // Cascade to projects
        $this->db->query("UPDATE projects SET active = '$newActiveStatus' WHERE customer_id = '$customer_id'");
        $affectedProjects = $this->db->affected_rows();

        // Cascade to sprints (via projects)
        $this->db->query("UPDATE sprints SET active = '$newActiveStatus' 
                         WHERE project_id IN (SELECT id FROM projects WHERE customer_id = '$customer_id')");
        $affectedSprints = $this->db->affected_rows();

        // Note: Tasks don't have an 'active' field, they use 'closed' field
        // If you want to cascade to tasks as well, we would need to add an 'active' field to tasks table
        // For now, we'll rely on the relationship hierarchy (inactive sprint = hide tasks)

        return array(
            "result"=>true,
            "newStatus"=>$newActiveStatus,
            "message"=> ($newActiveStatus == '1' ? "Customer activated" : "Customer deactivated"),
            "affectedCustomer"=>$affectedCustomer,
            "affectedProjects"=>$affectedProjects,
            "affectedSprints"=>$affectedSprints
        );
    }

    public function delete($uuid)
    {
        //delete customer. first we need to concatenate a timestamp to the email to make unique
        //this is because if there is a previous customer with the same email, it will not allow
        //to delete the customer because of constraint.
        $email = $this->db->where("uuid",$uuid)->get("customers")->row()->email;
        $this->db->set("email",$email."_".date("YmdHis"))->where("uuid",$uuid)->update("customers");
        $this->db->where("uuid",$uuid)->update("customers",array("status"=>"0"));
        $deletedCustomer = $this->db->affected_rows();

        //delete projects
        $this->db->query("UPDATE projects SET status = '0' WHERE customer_id = (SELECT customer_id FROM customers WHERE uuid = '$uuid')");
        $deletedProjects = $this->db->affected_rows();

        //delete sprints
        $this->db->query("UPDATE sprints SET status = '0' WHERE project_id IN (SELECT id FROM projects WHERE customer_id = (SELECT customer_id FROM customers WHERE uuid = '$uuid'))");
        $deletedSprints = $this->db->affected_rows();

        //delete tasks
        $this->db->query("UPDATE tasks SET status = '0' WHERE sprint_id IN (SELECT id FROM sprints WHERE project_id IN (SELECT id FROM projects WHERE customer_id = (SELECT customer_id FROM customers WHERE uuid = '$uuid')))");
        $deletedTasks = $this->db->affected_rows();


        $this->load->model("Email_model3");
        // $this->load->model("Tasks_model");
        $this->load->model("system_model");

        $user = $this->db->select("email, name")->from("users")->where("id",$_SESSION['user_id'])->get()->row();
        $admins = $this->system_model->getParam("notification_delete_customers",true);

        foreach($admins as $admin){
            $email = $this->db->select("email")->from("users")->where("id",$admin)->get()->row()->email;
            $emailData = [
                'user'              =>  $user,
                "deletedCustomer"   =>  $this->db->select("*")->from("customers")->where("uuid",$uuid)->get()->row(),
                "deletedProjects"   =>  $deletedProjects,
                "deletedSprints"    =>  $deletedSprints,
                "deletedTasks"      =>  $deletedTasks,
                'logo'              =>  $this->system_model->getParam("logo"),
                'link'              =>  "",
                'link_label'        =>  ""
            ];

            $content = $this->load->view("_email/header",$emailData, true);
            $content .= $this->load->view("_email/customerDeleted",$emailData, true);
            $content .= $this->load->view("_email/footer",[], true);
            $this->Email_model3->save($email,"A customer has been deleted",$content);
        }
        

        return array(
            "result"=>true
            ,"deletedCustomer"=>$deletedCustomer
            ,"deletedProjects"=>$deletedProjects
            ,"deletedSprints"=>$deletedSprints
            ,"deletedTasks"=>$deletedTasks);
    }

}
