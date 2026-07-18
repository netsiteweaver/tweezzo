<?php

function setFlashMessage($id, $message) {
    $_SESSION[$id] = $message;
}

function getFlashMessage($id) {
    $message = isset($_SESSION[$id]) ? $_SESSION[$id] : null;
    unset($_SESSION[$id]);
    return $message;
}

function flashSuccess($text) {
    setFlashMessage("success", $text);
}

function flashDanger($text) {
    setFlashMessage("danger", $text);
}

function flashWarning($text) {
    setFlashMessage("warning", $text);
}

function flashInfo($text) {
    setFlashMessage("info", $text);
}

function front_url($url = "") {

    $baseurl = base_url();
    return substr($baseurl, 0, strrpos($baseurl, 'admin')) . $url;
}

function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

function t($text, $transform = "word", $outputMethod = '') {

    $outputMethod = (!in_array($outputMethod, array("echo", "print_r", "var")) ? 'var' : $strtolower($outputMethod));
    if (!function_exists('lang')) {
        echo "ERROR: Please enable the language helper in application/config/autoload.php";
        return $text;
    }
    $result = "";
    $transformed_text = strtolower(str_replace(" ", "_", $text));
    $transformed_text = strtolower(str_replace("-", "_", $transformed_text));
    $translated_text = lang($transformed_text);
    if (empty($translated_text)) {
        //$result = "<span class='untranslated'>".$text."</span>";
        $result = $text;
    } else {
        $result = $translated_text;
    }
    // $result = $translated_text;
    if (empty($transform))
        return $result;

    $transform = strtolower($transform);
    switch ($transform) {
        case ('lower'):
            $result = strtolower($result);
            break;

        case ('upper'):
            $result = strtoupper($result);
            break;

        case ('word'):
            $result = ucwords($result);
            break;

        case ('first'):
            $result = ucfirst($result);
            break;

        default:
    }

    switch ($outputMethod) {
        case "var":
            return $result;
            break;

        case "echo":
            echo $result;
            break;

        case "print_r":
            echo $result;
            break;

        default:
            return $result;
    }
}

function _t($text, $transform = "word", $outputMethod = '') {
    echo t($text, $transform, $outputMethod);
}

function check() {
    return true;
}

function chk($c,$m){
    $CI = &get_instance();
    $CI->load->model("accesscontrol_model");
    $chk = $CI->accesscontrol_model->authorised($c,$m);
	if($chk == 0){
        $CI->data["content"]=$CI->load->view("themes/".$CI->data['theme']."/shared/error",$CI->data,true);
        $CI->load->view("themes/".$CI->data['theme']."/layoutsAdminLTE-3.2.0//error",$CI->data);          
		return false;
	}
	return true;
}

function checkPermission($instance,$section=""){

	$chk = $instance->accesscontrol_model->check("read",$section);
	if($chk == 0){
        $instance->data["content"]=$instance->load->view("/shared/error",$instance->data,true);
        $instance->load->view("/layouts/error",$instance->data);          
		return false;
	}
	return true;

}

function isAuthorised($controller = "", $method = "", $view=true){

    $controller = (empty($controller)) ? $this->uri->segment(1) : $controller;
    $method = (!empty($method)) ? $method : $this->uri->segment(2);

    $instance = &get_instance();
    $instance->load->model("accesscontrol_model");
    
	$chk = $instance->accesscontrol_model->authorised($controller,$method);
	if($chk == 0){
        if($view){
            $instance->data["content"]=$instance->load->view("/shared/error",$instance->data,true);
            $instance->load->view("/layouts/error",$instance->data);          
        }else{
            echo "<h1 class='error'>Prohibited</h1>";
        }
		return false;
	}
	return true;
}

function canAccess($instance, $section = "", $controller = "") {
    $CI = &get_instance();
    $CI->load->model("accesscontrol_model");
    $chk = $CI->accesscontrol_model->check("read", $section, $controller);

    if ($chk == 0) {
        return false;
    }
    return true;
}

function checkPermissions($instance, $section = "") {
    //This function will return all 4 possible permissions
    //create, read, update and delete

    $result = new stdClass();
    $perms = $instance->accesscontrol_model->check("all");
    $result->read = $perms->read;
    $result->create = $perms->create;
    $result->update = $perms->update;
    $result->delete = $perms->delete;
    return $result;
}

function getControllers() {
    $CI = &get_instance();
    $controllers = array();
    $CI->load->helper('file');

    // Scan files in the /application/controllers directory
    // Set the second param to TRUE or remove it if you 
    // don't have controllers in sub directories
    $files = get_dir_file_info(APPPATH . 'controllers', FALSE);

    // Loop through file names removing .php extension
    foreach (array_keys($files) as $file) {
        $controllers[] = strtolower(str_replace('.php', '', $file));
    }

    $unwanted = array_search('index.html', $controllers);
    unset($controllers[$unwanted]);
    asort($controllers);

    return $controllers;
}

function getPagination($url,$total_rows,$per_page,$uri_segment=3)
{
    $config['base_url'] = base_url($url);
    $config['total_rows'] = $total_rows;
    $config['per_page'] = $per_page;
    $config['uri_segment'] = $uri_segment;
    $config['use_page_numbers'] = TRUE;
    $config['num_links'] = 10;
    $config['full_tag_open'] = "<nav><ul class='pagination'>";
    $config['full_tag_close'] = "</ul></nav>";
    $config['first_link'] = false;
    $config['last_link'] = false;
    $config['next_link'] = false;
    $config['prev_link'] = false;
    $config['num_tag_open'] = "<li class='page-item'>";
    $config['num_tag_close'] = "</li>";
    $config['cur_tag_open'] = "<li class='page-item active'><a class='page-link' href='#'>";
    $config['cur_tag_close'] = "</a></li>";
    $config['attributes'] = array('class' => 'page-link');
    $config['reuse_query_string'] = TRUE;
    $CI = & get_instance();
    $CI->load->library('pagination');
    $CI->pagination->initialize($config);
    $pagination = $CI->pagination->create_links();
    return $pagination;
}
/*
Bootstrap 5.x
<nav aria-label="Page navigation example">
  <ul class="pagination">
    <li class="page-item"><a class="page-link" href="#">Previous</a></li>
    <li class="page-item"><a class="page-link" href="#">1</a></li>
    <li class="page-item"><a class="page-link" href="#">2</a></li>
    <li class="page-item"><a class="page-link" href="#">3</a></li>
    <li class="page-item"><a class="page-link" href="#">Next</a></li>
  </ul>
</nav> 
 */
function json_reply($result=true,$reason="",$header_code="200")
{
    //$result will be true or false
    //$dataOrReason will contain data if result is true. Otherwise i will contain a message(reason of result false)
    $CI = & get_instance();
    return $CI->output
            ->set_content_type('application/json')
            ->set_status_header($header_code)
            ->set_output(json_encode(array(
                'result'    =>  $result,
                'reason'    =>  $reason
            )));
}

function getQueryString($fromSegment=1,$toSegment=null) {
    $temp = uri_string();
    $segments = explode('/',$temp);
    $qs="/";
    for($i=$fromSegment-1;$i<count($segments);$i++){
        $qs .= $segments[$i].'/';
    }
    return $qs;
}

function getDocumentNumber($type,$increment=true) {
    $CI = & get_instance();
    $CI->load->model('system_model');
    $prefix = $CI->system_model->getParam($type.'_prefix');
    $number = $CI->system_model->getParam($type.'_last_number');
    $maxlength = $CI->system_model->getParam($type.'_maxlength');
    $len = $maxlength - strlen($prefix);
    $doc_num = $prefix . str_pad($number, $len, '0', STR_PAD_LEFT);
    if($increment==true)$CI->db->query("UPDATE params SET value = value +1  WHERE title = '".$type. "_last_number'");
    return $doc_num;
}

function format_number($number) {
    $CI = & get_instance();
    $CI->load->model("system_model");
    $currency = $CI->system_model->getParam('currency');
    return $currency.' '.number_format($number,2);
}

function debug($data,$die=true){
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    if($die)die;
}

/*-------------------------------------------
        NUMBER TO STRING CONVERTER
-------------------------------------------*/

function numberTowords($num,$currency='rupees')
{
    //check if number includes decimal
    if(strpos($num, '.')=='') { //number is integer
        return convertNum($num).' '.$currency;
    }
    $parts = explode('.', $num);
    return convertNum($parts[0]) . " $currency and {convertNum($parts[1])} cents";
}


function convertNum($num) {
    $num = (int) $num;    // make sure it's an integer
     
    if ($num < 0)
        return "negative ".convertTri(-$num, 0);
     
    if ($num == 0)
        return "zero";
     
    return convertTri($num, 0);
}

// recursive fn, converts three digits per pass
function convertTri($num, $tri) {
    $ones = array(""," one"," two"," three"," four"," five"," six"," seven"," eight"," nine"," ten"," eleven"," twelve"," thirteen"," fourteen"," fifteen"," sixteen"," seventeen"," eighteen"," nineteen");
    $tens = array("",""," twenty"," thirty"," forty"," fifty"," sixty"," seventy"," eighty"," ninety");
    $triplets = array(""," thousand and"," million"," billion");

    // chunk the number, ...rxyy
    $r = (int) ($num / 1000);
    $x = ($num / 100) % 10;
    $y = $num % 100;
     
    // init the output string
    $str = "";

    // do hundreds
    if ($x > 0) $str .= "{$ones[$x]} hundred and ";

    // do ones and tens
    if ($y < 20)
        $str .= $ones[$y];
    else
        $str .= $tens[(int) ($y / 10)] . $ones[$y % 10];

    // add triplet modifier only if there
    // is some output to be modified...
    if ($str != "") $str .= $triplets[$tri];

    // continue recursing?
    if ($r > 0)
        return convertTri($r, $tri+1).$str;
    else
        return $str;
}

function genPassword($length=12)
{
    $password = substr(hash('sha512',rand()),0,$length);    
    return $password;
}

function randomName($length=12,$ts=false)
{
    $word = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    $output = "";
    for($i=1;$i<=$length;$i++){
        $pos = rand(0,strlen($word));
        $output .= substr($word,$pos,1);
    }
    if($ts) $output .= date('YmdHis');
    return $output;
}

function ceiling($amount,$precision=10)
{
    /**
     * @author Reeaz Ramoly <reeaz@ramoly.info>
     * @param amount is the amount that you want to round off
     * @param precision determines how you want to round off. If you want to round off 144 to 150, that will be to nearest 10. 
     *                  If you want to round off 545 to 600, then this will be to the nearest 100
     * 2021-10-30
     * 
     */
    if($amount%$precision==0)$amount++;
    return ceil($amount/$precision)*$precision;
}

/**
 * This function will render the delete button easily. 
 * 
 * @author Reeaz Ramoly (reeaz@netsiteweaver.com)
 * @version 1.0 2021-11-01
 * 
 * @param url the url to access controller/method to process the deletion
 * @param pk this can be either id or uuid (preferred)
 * @param pk_value this will hold the either the id or the uuid of the selected record
 * @param message this is an optional message to be displayed for the warning popup
 * @param message_complete this is an optional message to be displayed after successful deletion
 * @return none No value is returned, it will be displayed where it has been called
 * 
 */
function DeleteButton(String $url="",String $pk="uuid",String $pk_value="",String $message="Are you sure you want to delete this record?",String $message_complete="Record has been deleted")
{
    if( (empty($url)) || (empty($pk)) || (empty($pk_value)) ) return false;
    $pk = strtolower(trim($pk));
    if(!in_array($pk,array('id','uuid'))) $pk = 'uuid';

    $button = "<div class='btn btn-flat btn-danger deleteAjax' ";
    $button .= "data-url='".base_url($url)."' ";
    $button .= "data-pk='{$pk}' ";
    $button .= "data-message='{$message}' ";
    $button .= "data-message-complete='{$message_complete}' ";
    $button .= "data-".$pk."='$pk_value'>";
    $button .= "<i class='fa fa-trash'></i></div>";

    echo $button;
}

function DeleteButton2(String $table="",String $pk="uuid",String $pk_value="",String $message="Are you sure you want to delete this record?",String $message_complete="Record has been deleted", String $button_size="sm",$label=true)
{
    if( (empty($table)) || (empty($pk)) || (empty($pk_value)) ) return false;
    $pk = strtolower(trim($pk));
    if(!in_array($pk,array('id','uuid'))) $pk = 'uuid';

    $message = !empty($message)?$message:"Are you sure you want to delete this record?";
    $message_complete = !empty($message_complete)?$message_complete:"Record has been deleted";
    
    $button = "<div class='btn btn-$button_size btn-flat btn-danger delete_row' ";
    $button .= "data-url='".base_url('ajax/misc/delete')."' ";
    $button .= "data-table='{$table}' ";
    $button .= "data-pk='{$pk}' ";
    $button .= "data-message='{$message}' ";
    $button .= "data-message-complete='{$message_complete}'";
    $button .= "data-".$pk."='$pk_value'>";
    $button .= "<i class='fa fa-trash'></i> <span class='button-label'>";
    if($label) $button .= " Delete";
    $button .= "</span></div>";

    echo $button;
}

/**
 * Simple function that will return only a certain amount of characters
 * If the string passed to the function exceed the length specified, only 
 * the begining of the string up to length specified will be returned followed by '...'
 */
function ellipsis($string,$length=20,$centered=true)
{
    if(strlen(trim($string))>$length){
        if($centered){
            return substr($string,0,$length/2).". . .".substr($string,strlen($string)-$length/2);
        }else{
            return substr($string,0,$length).". . .";
        }
        
    }else{
        return trim($string);
    }
}

function blockContentEditor($result,$l,$uploadFolder='pages')
{
    ?>
    <?php foreach($result->fields as $row):?>
        <div class="form-group">                            
        <?php if(substr($row,0,5)=='image'):?>
            <?php $label = $row;//ucwords(str_replace('_',' ',str_replace('image','',$row))); ?>
        <label for=""><?php echo $label;?></label>
        <p class="notes">Please upload an image (png/jpg) </p>
            <input type="hidden" name="<?php echo $row;?>" value="<?php echo isset($result->{'content_'.$l->abbr}->{$row})?$result->{'content_'.$l->abbr}->{$row}:'';?>">
            <?php if(isset($result->{'content_'.$l->abbr}->{$row})):?>
            <img style='border:1px solid #ccc; background-color:#ffffff; padding:10px; width:100%;' src="<?php echo base_url('../uploads/'.$uploadFolder.'/'.$result->{'content_'.$l->abbr}->{$row});?>" alt="">
            <?php endif;?>
            <!-- <input type="text" class='form-control' name="<?php //echo $row;?>" value="<?php //echo isset($result->{'content_'.$l->abbr}->{$row})?$result->{'content_'.$l->abbr}->{$row}:'';?>"> -->
            <input type="file" accept="image/jpeg, image/png" name="<?php echo $row;?>">
        <?php elseif(substr($row,0,5)=='video'):?>
        <?php $label = $row;//ucwords(str_replace('_',' ',str_replace('video','',$row))); ?>
        <label for=""><?php echo $label;?></label>
        <p class="notes">Please upload a video (mp4) </p>
            <input type="hidden" name="<?php echo $row;?>" value="<?php echo isset($result->{'content_'.$l->abbr}->{$row})?$result->{'content_'.$l->abbr}->{$row}:'';?>">
            <?php if(isset($result->{'content_'.$l->abbr}->{$row})):?>
                <video src="<?php echo base_url('../uploads/'.$uploadFolder.'/'.$result->{'content_'.$l->abbr}->{$row});?>" class="video-thumbnail" controls="" disablepictureinpicture="" preload="none" poster="<?php echo base_url('../uploads/'.$uploadFolder.'/'.$result->{'content_'.$l->abbr}->{$row});?>" controlslist="nodownload" width='500'></video>
                <?php //echo base_url('../uploads/'.$uploadFolder.'/'.$result->{'content_'.$l->abbr}->{$row});?>
            <?php endif;?>
            <input type="file" accept="video/mp4" name="<?php echo $row;?>">
        <?php elseif(substr($row,0,7)=='youtube'):?>
            <?php $label = $row;//ucwords(str_replace('_',' ',str_replace('youtube','',$row))); ?>
            <label for=""><?php echo $label;?></label>
            <textarea placeholder="Please enter the YouTube embed code" name="<?php echo $row;?>" class="form-control" rows="10"><?php echo (isset($result->{'content_'.$l->abbr}->{$row}))?$result->{'content_'.$l->abbr}->{$row}:'';?></textarea>
        <?php elseif(substr($row,0,4)=='text'):?>
            <label for=""><?php echo ucfirst($row);?></label>
            <textarea name="<?php echo $row;?>" class="summernote form-control" rows="10"><?php echo (isset($result->{'content_'.$l->abbr}->{$row}))?$result->{'content_'.$l->abbr}->{$row}:'';?></textarea>
        <?php else:?>
            <label for=""><?php echo ucfirst($row);?></label>
            <input type="text" name="<?php echo $row;?>" class="form-control" value="<?php echo (isset($result->{'content_'.$l->abbr}->{$row}))?$result->{'content_'.$l->abbr}->{$row}:'';?>">
        <?php endif;?>
        </div>
        <hr style='border:1px solid #FFF;width:100%;'>
    <?php endforeach;?>
<?php
}

function getStockRef()
{
    $ci = & get_instance();
    $stockref_lastnumber = $ci->db->select("value")->from("params")->where("title","stockref_lastnumber")->get()->row("value");
    $stockref_length = $ci->db->select("value")->from("params")->where("title","stockref_length")->get()->row("value");

    $ci->db->set("value","value+1",FALSE);
    $ci->db->where("title","stockref_lastnumber");
    $ci->db->update("params");

    return str_pad($stockref_lastnumber,$stockref_length,'0',STR_PAD_LEFT);
}

function isDateValid($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function lockRecord($table_name,$uuid,$url)
{
    $ci = & get_instance();
    $editing = $ci->general_model->lockTable($table_name,$uuid);

    if($editing['result'] == false) {
        flashDanger($editing['reason']);
        redirect(base_url($url));
    }
}

function unlockRecord($table_name,$uuid,$url)
{
    $ci = & get_instance();
    $editing = $ci->general_model->unlockTable($table_name,$uuid);
    if($editing['result'] == false) {
        flashDanger($editing['reason']);
        redirect(base_url($url));
        return false;
    }
    return true;
}

function validatePassword($password) {
    $errors = [];

    // Check for minimum length of 8
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    // Check for at least one uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter.";
    }

    // Check for at least one lowercase letter
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter.";
    }

    // Check for at least one symbol (non-alphanumeric character)
    if (!preg_match('/[\W_]/', $password)) {
        $errors[] = "Password must contain at least one symbol (e.g., !@#$%^&*).";
    }

    return $errors;
}

function incrementTaskNumber($taskNumber) {
    // Match the prefix and the numeric part
    if (preg_match('/^([A-Z]+)(\d+)$/', $taskNumber, $matches)) {
        $prefix = $matches[1];
        $number = $matches[2];

        // Increment the numeric part and pad with leading zeros to match original length
        $incremented = str_pad($number + 1, strlen($number), '0', STR_PAD_LEFT);

        return $prefix . $incremented;
    } else {
        return str_pad(intval($taskNumber) + 1, 3, '0', STR_PAD_LEFT);
    }

    // Return original if it doesn't match the expected format
    // return $taskNumber;
}

/**
 * Build combined task reference from project code, sprint code and task number.
 * When both codes are set returns e.g. "WR-S3-001"; otherwise returns task_number only.
 *
 * @param string|null $project_code
 * @param string|null $sprint_code
 * @param string      $task_number
 * @return string
 */
function task_ref($project_code, $sprint_code, $task_number) {
    $project_code = trim((string) $project_code);
    $sprint_code  = trim((string) $sprint_code);
    if ($project_code !== '' && $sprint_code !== '') {
        return $project_code . '-' . $sprint_code . '-' . $task_number;
    }
    return $task_number;
}

/**
 * Allowed task intake sources (tasks.source).
 *
 * @return array<string, string>
 */
function task_source_options() {
    return [
        'admin'    => 'Tweezzo Platform',
        'whatsapp' => 'WhatsApp',
        'email'    => 'Email',
        'phone'    => 'Phone',
        'bulk'     => 'Bulk import',
        'meeting'  => 'Meeting notes',
        'other'    => 'Other',
    ];
}

/**
 * Sources set automatically (bulk import, meeting notes) — not selectable on manual add.
 *
 * @return string[]
 */
function task_source_auto_only() {
    return ['bulk', 'meeting'];
}

/**
 * Whether a source option should be disabled on add/edit forms.
 *
 * @param string      $source_value
 * @param string|null $current_source Current task source on edit; empty on add.
 * @return bool
 */
function task_source_disabled_in_form($source_value, $current_source = '') {
    if (!in_array($source_value, task_source_auto_only(), true)) {
        return false;
    }
    return $source_value !== (string) $current_source;
}

/**
 * @param string|null $source
 * @return string
 */
function task_source_label($source) {
    $options = task_source_options();
    $source = (string) $source;
    return isset($options[$source]) ? $options[$source] : ($source !== '' ? ucfirst(str_replace('_', ' ', $source)) : '—');
}

/**
 * Escaped source label for HTML emails.
 *
 * @param string|null $source
 * @return string
 */
function task_source_email_label($source) {
    return htmlspecialchars(task_source_label($source), ENT_QUOTES, 'UTF-8');
}

/**
 * Absolute URL for a task source icon in HTML emails.
 *
 * @param string|null $source
 * @return string
 */
function task_source_email_icon_url($source) {
    $source = task_source_normalize($source);
    $icons = ['admin', 'whatsapp', 'email', 'phone', 'bulk', 'meeting', 'other'];
    $key = in_array($source, $icons, true) ? $source : 'other';
    return base_url('assets/images/task-sources/' . $key . '.png');
}

/**
 * Icon HTML for HTML emails (absolute image URLs).
 *
 * @param string|null $source
 * @return string
 */
function task_source_email_icon_html($source) {
    $label = task_source_label($source);
    if ($label === '—') {
        return '—';
    }

    $iconUrl = task_source_email_icon_url($source);
    $title = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');

    return '<img src="' . htmlspecialchars($iconUrl, ENT_QUOTES, 'UTF-8') . '" alt=""'
        . ' title="' . $title . '" width="16" height="16"'
        . ' style="vertical-align:middle;display:inline-block;border:0;">';
}

/**
 * Icon + label for HTML emails.
 *
 * @param string|null $source
 * @return string
 */
function task_source_email_display_html($source) {
    $label = task_source_label($source);
    if ($label === '—') {
        return '—';
    }

    return task_source_email_icon_html($source)
        . ' <span style="vertical-align:middle;">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';
}

/**
 * Tweezzo app favicon URL for task source display.
 *
 * @return string
 */
function task_source_app_icon_url() {
    return base_url('assets/favicon/favicon-16x16.png');
}

/**
 * Icon HTML for compact displays (e.g. task listing).
 *
 * @param string|null $source
 * @return string
 */
function task_source_icon_html($source) {
    $label = task_source_label($source);
    if ($label === '—') {
        return '<span class="text-muted">—</span>';
    }

    $source = (string) $source;
    if ($source === 'admin') {
        $iconUrl = task_source_app_icon_url();
        $title = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
        return '<img src="' . htmlspecialchars($iconUrl, ENT_QUOTES, 'UTF-8') . '" alt=""'
            . ' title="' . $title . '" aria-label="' . $title . '"'
            . ' width="16" height="16" style="vertical-align:middle;">';
    }

    $icons = [
        'whatsapp' => ['class' => 'fab fa-whatsapp', 'color' => 'text-success'],
        'email'    => ['class' => 'fas fa-envelope', 'color' => 'text-primary'],
        'phone'    => ['class' => 'fas fa-phone', 'color' => 'text-info'],
        'bulk'     => ['class' => 'fas fa-upload', 'color' => 'text-warning'],
        'meeting'  => ['class' => 'fas fa-users', 'color' => 'text-purple'],
        'other'    => ['class' => 'fas fa-question-circle', 'color' => 'text-muted'],
    ];

    $meta = isset($icons[$source]) ? $icons[$source] : ['class' => 'fas fa-circle', 'color' => 'text-muted'];
    $class = trim($meta['class'] . ' ' . $meta['color']);

    return '<i class="' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '" title="' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '" aria-label="' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '"></i>';
}

/**
 * Icon + label for task detail views.
 *
 * @param string|null $source
 * @return string
 */
function task_source_display_html($source) {
    $label = task_source_label($source);
    if ($label === '—') {
        return '<span class="text-muted">—</span>';
    }

    return task_source_icon_html($source) . ' <span class="ms-1">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';
}

/**
 * @param string|null $source
 * @return string
 */
function task_source_normalize($source) {
    $options = task_source_options();
    $source = (string) $source;
    return isset($options[$source]) ? $source : 'admin';
}

/**
 * Field labels for task update notification emails.
 *
 * @return array<string, string>
 */
function task_email_update_field_labels() {
    return [
        'name'                      => 'Task name',
        'description'               => 'Description',
        'task_number'               => 'Task number',
        'section'                   => 'Section',
        'sprint_id'                 => 'Sprint',
        'due_date'                  => 'Due date',
        'estimated_hours'           => 'Estimated hours',
        'work_type'                 => 'Work type',
        'billable'                  => 'Billable',
        'settled'                   => 'Settled',
        'settled_on'                => 'Date settled',
        'ref'                       => 'Ref (Inv / Quote #)',
        'source'                    => 'Source',
        'scope_client_expectation'  => 'Scope — expected',
        'scope_not_included'        => 'Scope — not included',
        'scope_when_done'           => 'Scope — when done',
    ];
}

/**
 * @param string $field
 * @param mixed  $value
 * @param array  $context
 * @return string
 */
function task_email_format_field_value($field, $value, $context = []) {
    if ($value === null || $value === false || $value === '') {
        return '—';
    }

    if ($field === 'source') {
        return task_source_label($value);
    }

    if ($field === 'stage') {
        return task_email_format_stage($value);
    }

    if (in_array($field, ['billable', 'settled'], true)) {
        return ((int) $value === 1) ? 'Yes' : 'No';
    }

    if ($field === 'sprint_id') {
        $id = (int) $value;
        $names = isset($context['sprint_names']) && is_array($context['sprint_names']) ? $context['sprint_names'] : [];
        return isset($names[$id]) ? $names[$id] : ('#' . $id);
    }

    if ($field === 'work_type') {
        return ucfirst(str_replace('_', ' ', (string) $value));
    }

    if (in_array($field, ['due_date', 'settled_on'], true)) {
        return substr((string) $value, 0, 10);
    }

    if ($field === 'progress') {
        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.') . '%';
    }

    if ($field === 'estimated_hours') {
        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
    }

    $text = trim((string) $value);
    return $text === '' ? '—' : $text;
}

/**
 * @param string $field
 * @param mixed  $value
 * @param array  $after POST data for checkbox normalization
 * @return mixed
 */
function task_email_normalize_field_for_compare($field, $value, $after = []) {
    if ($field === 'source') {
        return task_source_normalize($value ?? 'admin');
    }

    if ($field === 'stage') {
        return $value === null || $value === '' ? '' : trim((string) $value);
    }

    if (in_array($field, ['billable', 'settled'], true)) {
        if (!empty($after) && array_key_exists($field, $after)) {
            return !empty($after[$field]) ? 1 : 0;
        }
        return ($value === null || $value === '') ? null : (int) $value;
    }

    if ($field === 'estimated_hours') {
        if ($value === null || $value === '') {
            return null;
        }
        return (string) (float) $value;
    }

    if ($field === 'progress') {
        if ($value === null || $value === '') {
            return null;
        }
        return (string) (float) $value;
    }

    if (in_array($field, ['due_date', 'settled_on'], true)) {
        if ($value === null || $value === '') {
            return null;
        }
        return substr((string) $value, 0, 10);
    }

    if ($field === 'sprint_id') {
        return $value === null || $value === '' ? null : (int) $value;
    }

    if ($value === null) {
        return '';
    }

    return trim((string) $value);
}

/**
 * @param string $text
 * @param int    $max
 * @return string HTML-escaped snippet
 */
function task_email_snippet($text, $max = 280) {
    $text = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $text)));
    if ($text === '') {
        return '—';
    }
    if (function_exists('mb_strlen') && mb_strlen($text) > $max) {
        return htmlspecialchars(mb_substr($text, 0, $max) . '…', ENT_QUOTES, 'UTF-8');
    }
    if (strlen($text) > $max) {
        return htmlspecialchars(substr($text, 0, $max) . '…', ENT_QUOTES, 'UTF-8');
    }
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Build field-level changes for a task update email.
 *
 * @param object|array $before
 * @param array        $after
 * @param array        $context sprint_names, fields (optional subset)
 * @return array<int, array{label:string,old:string,new:string,is_long:bool}>
 */
function task_email_build_update_changes($before, $after, $context = []) {
    $labels = isset($context['fields']) && is_array($context['fields'])
        ? $context['fields']
        : task_email_update_field_labels();
    $changes = [];

    foreach ($labels as $field => $label) {
        $oldRaw = is_object($before) ? ($before->$field ?? null) : ($before[$field] ?? null);
        $newRaw = $after[$field] ?? null;

        $oldNorm = task_email_normalize_field_for_compare($field, $oldRaw, []);
        $newNorm = task_email_normalize_field_for_compare($field, $newRaw, $after);

        if ($oldNorm === $newNorm) {
            continue;
        }

        $isLong = in_array($field, [
            'description',
            'scope_client_expectation',
            'scope_not_included',
            'scope_when_done',
        ], true);

        $changes[] = [
            'label'   => $label,
            'old'     => $isLong
                ? task_email_snippet(task_email_format_field_value($field, $oldRaw, $context))
                : htmlspecialchars(task_email_format_field_value($field, $oldRaw, $context), ENT_QUOTES, 'UTF-8'),
            'new'     => $isLong
                ? task_email_snippet(task_email_format_field_value($field, $newRaw, $context))
                : htmlspecialchars(task_email_format_field_value($field, $newRaw, $context), ENT_QUOTES, 'UTF-8'),
            'is_long' => $isLong,
        ];
    }

    return $changes;
}

/**
 * @param array  $uploadedFiles
 * @param string $uploadFolder
 * @return array<int, array{file_name:string,thumb_name:string,url:string,thumb_url:string}>
 */
function task_email_format_uploaded_files($uploadedFiles, $uploadFolder = 'uploads/tasks/') {
    $files = [];
    foreach ((array) $uploadedFiles as $file) {
        if (empty($file['file_name'])) {
            continue;
        }
        $thumb = !empty($file['image_thumb']) ? $file['image_thumb'] : (!empty($file['thumb_name']) ? $file['thumb_name'] : $file['file_name']);
        $files[] = [
            'file_name'  => $file['file_name'],
            'thumb_name' => $thumb,
            'url'        => base_url($uploadFolder . $file['file_name']),
            'thumb_url'  => base_url($uploadFolder . $thumb),
        ];
    }
    return $files;
}

/**
 * @param array  $deletedFiles
 * @param string $uploadFolder
 * @return array<int, array{file_name:string,thumb_name:string,url:string,thumb_url:string}>
 */
function task_email_format_removed_files($deletedFiles, $uploadFolder = 'uploads/tasks/') {
    $files = [];
    foreach ((array) $deletedFiles as $file) {
        if (is_string($file)) {
            $file = ['file_name' => $file, 'thumb_name' => $file];
        }
        if (empty($file['file_name'])) {
            continue;
        }
        $thumb = !empty($file['thumb_name']) ? $file['thumb_name'] : $file['file_name'];
        $files[] = [
            'file_name'  => $file['file_name'],
            'thumb_name' => $thumb,
            'url'        => base_url($uploadFolder . $file['file_name']),
            'thumb_url'  => base_url($uploadFolder . $thumb),
        ];
    }
    return $files;
}

/**
 * @param array  $changes
 * @param array  $filesAdded
 * @param array  $filesRemoved
 * @return string
 */
function task_email_update_subject_summary($changes, $filesAdded, $filesRemoved) {
    $parts = [];
    if (!empty($changes)) {
        $parts[] = count($changes) . ' field' . (count($changes) === 1 ? '' : 's');
    }
    if (!empty($filesAdded)) {
        $parts[] = count($filesAdded) . ' file' . (count($filesAdded) === 1 ? '' : 's') . ' added';
    }
    if (!empty($filesRemoved)) {
        $parts[] = count($filesRemoved) . ' file' . (count($filesRemoved) === 1 ? '' : 's') . ' removed';
    }
    if (empty($parts)) {
        return 'Task Updated';
    }
    return 'Task Updated (' . implode(', ', $parts) . ')';
}

/**
 * @param string|null $stage
 * @return string
 */
function task_email_format_stage($stage) {
    $stage = (string) $stage;
    return $stage !== '' ? strtoupper(str_replace('_', ' ', $stage)) : '—';
}

/**
 * @param string|null $oldStage
 * @param string|null $newStage
 * @return array<int, array{label:string,old:string,new:string,is_long:bool}>
 */
function task_email_build_stage_change($oldStage, $newStage) {
    return [[
        'label'   => 'Stage',
        'old'     => htmlspecialchars(task_email_format_stage($oldStage), ENT_QUOTES, 'UTF-8'),
        'new'     => htmlspecialchars(task_email_format_stage($newStage), ENT_QUOTES, 'UTF-8'),
        'is_long' => false,
    ]];
}

/**
 * @param string $noteText
 * @return array<int, array{label:string,old:string,new:string,is_long:bool}>
 */
function task_email_build_note_change($noteText) {
    return [[
        'label'   => 'Note added',
        'old'     => '—',
        'new'     => task_email_snippet($noteText),
        'is_long' => true,
    ]];
}

/**
 * @param string $dueDate
 * @return array<int, array{label:string,old:string,new:string,is_long:bool}>
 */
function task_email_build_due_date_change($dueDate) {
    return [[
        'label'   => 'Due date',
        'old'     => '—',
        'new'     => htmlspecialchars(substr((string) $dueDate, 0, 10), ENT_QUOTES, 'UTF-8'),
        'is_long' => false,
    ]];
}

/**
 * @param int $fileCount
 * @return array<int, array{label:string,old:string,new:string,is_long:bool}>
 */
function task_email_build_files_uploaded_change($fileCount) {
    $count = (int) $fileCount;
    $label = $count === 1 ? '1 file attached' : ($count . ' files attached');
    return [[
        'label'   => 'Attachments',
        'old'     => '—',
        'new'     => htmlspecialchars($label, ENT_QUOTES, 'UTF-8'),
        'is_long' => false,
    ]];
}

/**
 * @param int $fileCount
 * @return array<int, array{label:string,old:string,new:string,is_long:bool}>
 */
function task_email_build_files_removed_change($fileCount) {
    $count = (int) $fileCount;
    $label = $count === 1 ? '1 file removed' : ($count . ' files removed');
    return [[
        'label'   => 'Attachments',
        'old'     => htmlspecialchars($label, ENT_QUOTES, 'UTF-8'),
        'new'     => '—',
        'is_long' => false,
    ]];
}

/**
 * Resolve the company logo URL for document headers and emails.
 * Prefers company.logo (uploads/company/), then app param logos.
 *
 * @param object|null $company
 * @return string
 */
function company_logo_url($company = null) {
    $CI =& get_instance();
    $CI->load->model('system_model');

    if ($company === null) {
        $company = $CI->system_model->getCompanyInfo();
    }

    if (!empty($company->logo)) {
        $companyLogoPath = FCPATH . 'uploads/company/' . $company->logo;
        if (is_file($companyLogoPath)) {
            return base_url('uploads/company/' . $company->logo);
        }
    }

    $logoLight = $CI->system_model->getParam('logo-light');
    if (!empty($logoLight) && is_file(FCPATH . 'assets/images/' . $logoLight)) {
        return base_url('assets/images/' . $logoLight);
    }

    $logo = $CI->system_model->getParam('logo');
    if (!empty($logo)) {
        if (is_file(FCPATH . 'assets/images/' . $logo)) {
            return base_url('assets/images/' . $logo);
        }
        if (is_file(FCPATH . 'uploads/logo/' . $logo)) {
            return base_url('uploads/logo/' . $logo);
        }
    }

    return '';
}

/**
 * Build a single-line company address from the company row.
 *
 * @param object|null $company
 * @return string
 */
function company_address_line($company = null) {
    if ($company === null) {
        $CI =& get_instance();
        $CI->load->model('system_model');
        $company = $CI->system_model->getCompanyInfo();
    }

    $parts = array_filter(array(
        isset($company->address1) ? trim((string) $company->address1) : '',
        isset($company->address2) ? trim((string) $company->address2) : '',
        isset($company->city) ? trim((string) $company->city) : '',
        isset($company->country) ? trim((string) $company->country) : '',
    ));

    return implode(', ', $parts);
}
/**
 * Determine whether a menu entry points at the page currently being viewed.
 *
 * Controller names are compared with dashes stripped because the active
 * controller is normalised that way by the controllers themselves, while the
 * menu table stores the raw name. An empty action is treated as "index" to
 * match CodeIgniter's default method.
 *
 * @param string $controller current controller
 * @param string $method current method
 * @param object $item menu entry
 * @param bool $controller_only match on the controller alone, ignoring the action
 * @return bool
 */
function is_active_menu($controller, $method, $item, $controller_only = FALSE) {
    $normalise = function ($value) {
        return strtolower(str_replace('-', '', trim((string) $value)));
    };

    if ($normalise($controller) !== $normalise(isset($item->controller) ? $item->controller : '')) {
        return FALSE;
    }

    if ($controller_only) {
        return TRUE;
    }

    $item_action = $normalise(isset($item->action) ? $item->action : '');
    $item_action = ($item_action === '') ? 'index' : $item_action;

    $current_action = $normalise($method);
    $current_action = ($current_action === '') ? 'index' : $current_action;

    return $item_action === $current_action;
}
