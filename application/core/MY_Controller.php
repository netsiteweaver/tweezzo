<?php
/**
 * Description of MY_Controller
 *
 * @author Your Name <Reeaz Ramoly at Netsiteweaver>
 */
class MY_Controller extends CI_Controller{
    
    public $data;
    
    public function __construct() {
        
        parent::__construct();
        
        $this->data['controller']   = str_replace("-","",$this->uri->segment(1,"dashboard"));
        $this->data['method']       = $this->uri->segment(2,"index");

        $userid = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
        $this->load->model("system_model");
        $this->data['company'] = $this->system_model->getCompanyInfo();
        $this->data['theme'] = $this->system_model->getParam("theme");
        $this->data['logo'] = $this->system_model->getParam("logo");
        $this->data['theme_color'] = $this->system_model->getParam('theme_color');
        $this->data['version'] = $this->system_model->getParam('current_version');
        $this->data['sidebar_collapse'] = $this->system_model->getParam("sidebar_collapse");
        $this->data['controller'] = str_replace("-", "", $this->uri->segment(1, "dashboard"));
        $this->data['method'] = $this->uri->segment(2, "index");
        $this->data['authenticated_user'] = isset($_SESSION['authenticated_user'])?$_SESSION['authenticated_user']:null;
        $this->load->model("menu_model");
        $this->data['menu_items'] = $this->menu_model->get();
        $this->data['page_title'] = ucfirst($this->data['method']) . ' ' . ucfirst($this->data['controller']);
        $this->data['flashSuccess']     = getFlashMessage("success");
        $this->data['flashWarning']     = getFlashMessage("warning");
        $this->data['flashDanger']      = getFlashMessage("danger");
        $this->data['flashInfo']        = getFlashMessage("info"); 
        $this->data['upload_folder'] = $this->config->item("upload_folder");

        $this->data['qs'] = $_SERVER["QUERY_STRING"];

        // $this->load->library('migration');

        $this->mybreadcrumb->add('Dashboard', base_url('dashboard/index'));

        $this->load->model("logger_model");
        $metadata = array();
        $metadata['GET'] = isset($_GET)?$_GET:'';
        $metadata['POST'] = isset($_POST)?$_POST:'';
        $metadata['FILES'] = isset($_FILES)?$_FILES:'';
        if(isset($_POST)) $notes = json_encode($metadata);
        $this->logger_model->save($notes);

        $this->load->model("accesscontrol_model");
        $this->data['perms']['myprofile'] = $this->accesscontrol_model->authorised("users","myprofile");
        $this->data['newOrder'] = $this->accesscontrol_model->authorised("orders","add");

        $this->load->model("general_model");

        $this->db->query("SET @@session.time_zone = \"+04:00\"");

        // $this->mybreadcrumb->add('Home', base_url("dashboard/index"),"fa-home");

        header('Access-Control-Allow-Origin: *');

        // if(isset($_SESSION['authenticated_user']) && (!empty($_SESSION['authenticated_user'])) && (empty($_SESSION['authenticated_user']->email)) ){
        //     if( ($this->data['controller']!='users') && ($this->data['method']!='myprofile') ){
        //         redirect(base_url('users/myprofile?email&modal=true'));
        //     }
        // }
        
    }

    public function loadJs($script){$this->loadScript($script);}
    public function loadScript($script)
    {
        $this->data['scripts'][] = $script;
    }

    public function loadCss($stylesheet){$this->loadStyleSheet($stylesheet);}
    public function loadStyleSheet($stylesheet)
    {
        $this->data['stylesheets'][] = $stylesheet;
    }

    public function addContent($content){
        $this->data['content'][] = $content;
    }

    public function errorPage($message,$url)
    {
        $this->data['error404'] = array(
            "message"   =>  $message,
            "url"       =>  $url
        );
        $this->data["content"] = $this->load->view("/shared/not-found", $this->data, true);
        $this->load->view("/layouts/default",$this->data);
    }
}
