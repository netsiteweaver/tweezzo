<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Files_model extends CI_Model{

    public function uploadImage($fieldName="photos",$destinationFolder="photos",$resize=false,$allowedFileTypes=array('jpg','jpeg','png','gif','mp4','mov'))
    {
        $base_folder = realpath('.');
        // $base_folder = substr($base_folder,0,strlen($base_folder)-5);
        $upload_folder = $base_folder . DIRECTORY_SEPARATOR . $destinationFolder;

        $config['upload_path']          = $upload_folder;
        $config['allowed_types']        = $allowedFileTypes;

        $this->upload->initialize($config);
        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload($fieldName))
        {
            return $this->upload->display_errors();
        }
        else
        {
            $data = $this->upload->data();
            
            if( (isset($resize)) && is_array($resize) ){
                $width = isset($resize['width']) ? $resize['width'] : 100;
                $height = isset($resize['height']) ? $resize['height'] : 100;
                $thumb_name = isset($resize['thumb_name']) ? $resize['thumb_name'] : "resized";

                $this->resizeImage($upload_folder.'/'.$data['file_name'], $width, $height, $thumb_name);
                $data['image_'.$thumb_name] = $data['raw_name'] . "_" . $thumb_name . $data['file_ext'];

            }
            return $data;
        }


    }

    public function uploadPDF($fieldName="document",$destinationFolder="photos")
    {
        $base_folder = realpath('.');
        $base_folder = substr($base_folder,0,strlen($base_folder)-5);
        $upload_folder = $base_folder . 'uploads/brochures/';
        
        $config['upload_path']          = $upload_folder;
        $config['allowed_types']        = 'pdf';

        $this->upload->initialize($config);
        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload($fieldName))
        {
            return $this->upload->display_errors();
        }else{
            $data = $this->upload->data();
            return $data;
        }


    }

    public function uploadCSV($fieldName="document")
    {
        $base_folder = realpath('.');
        // $base_folder = substr($base_folder,0,strlen($base_folder)-5);
        $upload_folder = $base_folder . '/uploads/';
        
        $config['upload_path']          = $upload_folder;
        $config['allowed_types']        = 'csv';

        $this->upload->initialize($config);
        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload($fieldName))
        {
            return $this->upload->display_errors();
        }else{
            $data = $this->upload->data();
            return $data;
        }


    }
    public function uploadImages($fieldName="photos",$destinationFolder="photos",$resize=false)
    {
        $uploadFolder = realpath('.');//$this->config->item("upload_folder");
        $uploadFolder = $uploadFolder . DIRECTORY_SEPARATOR.$destinationFolder;
        if(!file_exists($uploadFolder)){
            mkdir($uploadFolder,0777);
        }
        $config['upload_path']          = $uploadFolder;
        $config['allowed_types']        = array('gif','jpg','jpeg','png');
        $config['encrypt_name']         = true;
        $config['file_ext_tolower']     = true;

        // $count          = count($_FILES[$fieldName]['size']);
        $uploadedFile   = $_FILES[$fieldName];
        $filesUploaded  = array();
        $errors          = array();

        // for($s=0; $s<$count; $s++) {
        foreach($_FILES[$fieldName]['name'] as $id => $image){
            $_FILES[$fieldName]['name']     =$uploadedFile['name'][$id];
            $_FILES[$fieldName]['type']     = $uploadedFile['type'][$id];
            $_FILES[$fieldName]['tmp_name'] = $uploadedFile['tmp_name'][$id];
            $_FILES[$fieldName]['error']    = $uploadedFile['error'][$id];
            $_FILES[$fieldName]['size']     = $uploadedFile['size'][$id];
            // $uploadFolder   = $this->getUploadFolder($destinationFolder);
            // $uploadFolder = str_replace('/',DIRECTORY_SEPARATOR,$uploadFolder);
            $config['upload_path'] = $uploadFolder;
            $config['allowed_types'] = 'gif|jpg|png';

            if(!empty($_FILES[$fieldName]['name'])) {
                $this->upload->initialize($config);
                $this->load->library('upload', $config);
                if(! $this->upload->do_upload($fieldName) ){
                    $errors[$id] = strip_tags($this->upload->display_errors());
                }else{
                    $data = $this->upload->data();
                    $filesUploaded[$id] = $data['file_name'];
                }
            }
        }

        return array('filesUploaded'=>$filesUploaded,'errors'=>$errors);

    }

    public function uploadMultipleImages($fieldname="photos",$destinationFolder="photos") {

        $uploadFolder   = $this->getUploadFolder($destinationFolder);
        $count          = count($_FILES[$fieldname]['size']);
        $uploadedFile   = $_FILES[$fieldname];
        $filesUploaded  = array();
        $errors          = array();

        for($s=0; $s<$count; $s++) {

            $_FILES[$fieldname]['name']     =$uploadedFile['name'][$s];
            $_FILES[$fieldname]['type']     = $uploadedFile['type'][$s];
            $_FILES[$fieldname]['tmp_name'] = $uploadedFile['tmp_name'][$s];
            $_FILES[$fieldname]['error']    = $uploadedFile['error'][$s];
            $_FILES[$fieldname]['size']     = $uploadedFile['size'][$s];
            $uploadFolder   = $this->getUploadFolder($destinationFolder);
            $uploadFolder = str_replace('/',DIRECTORY_SEPARATOR,$uploadFolder);
            $config['upload_path'] = $uploadFolder;
            $config['allowed_types'] = 'gif|jpg|png';

            $this->upload->initialize($config);
            $this->load->library('upload', $config);
            if(! $this->upload->do_upload($fieldname) ){
                $errors[] = strip_tags($this->upload->display_errors());
            }else{
                $data = $this->upload->data();
                $filesUploaded[] = $data['file_name'];
            }

        }

        return array('filesUploaded'=>$filesUploaded,'errors'=>$errors);
    }

    public function resizeImage($image, $width=100,$height=100, $thumb_name="resized")
    {
        $config['image_library'] = 'gd2';
        $config['source_image'] = $image;
        $config['create_thumb'] = TRUE;
        $config['thumb_marker'] = '_' . $thumb_name;
        $config['maintain_ratio'] = TRUE;
        $config['width']         = $width;
        $config['height']       = $height;
        $config['quality']      = 90;

        $this->load->library('image_lib', $config);

        $this->image_lib->initialize($config);

        if ( ! $this->image_lib->resize())
        {
            echo $this->image_lib->display_errors();
        }

    }

    public function uploadImages2($fieldName="photos",$destinationFolder="photos",$resize=false)
    {
        $base_folder = realpath('.');
        $upload_folder = $base_folder . DIRECTORY_SEPARATOR . $destinationFolder;

        if(!file_exists($upload_folder)){
            mkdir($upload_folder,0777);
        }

        $uploadedFile   =   $_FILES[$fieldName];
        $filesUploaded  =   array();
        $errors         =   array();

        foreach($_FILES[$fieldName]['name'] as $id => $image){
            $_FILES[$fieldName]['name']     =$uploadedFile['name'][$id];
            $_FILES[$fieldName]['type']     = $uploadedFile['type'][$id];
            $_FILES[$fieldName]['tmp_name'] = $uploadedFile['tmp_name'][$id];
            $_FILES[$fieldName]['error']    = $uploadedFile['error'][$id];
            $_FILES[$fieldName]['size']     = $uploadedFile['size'][$id];
            $config['upload_path'] = $upload_folder;
            $config['allowed_types'] = array('jpg','jpeg','png');
            $config['encrypt_name']         = true;
            $config['file_ext_tolower']     = true;

            if(!empty($_FILES[$fieldName]['name'])) {
                $this->upload->initialize($config);
                $this->load->library('upload', $config);
                if(! $this->upload->do_upload($fieldName) ){
                    $errors[$id] = strip_tags($this->upload->display_errors());
                }else{
                    $filesUploaded[$id] = $this->upload->data();
                    file_put_contents('./debug/logfile.txt', "[" . date('Y-m-d H:i:s') . "] " . $filesUploaded[0]['full_path'] . "\r\n", FILE_APPEND);
                }
            }
        }

        return array('filesUploaded'=>$filesUploaded,'errors'=>$errors);

    }

    public function watermarkImage($image='', $text="LEMONYELLOW",$font_size="32")
    {
        $this->load->model('system_model');
        $watermarkJSON = $this->system_model->getParam('watermark');
        $watermark = json_decode($watermarkJSON);
        // debug($watermark);
        if(!empty($text)){
            $config['image_library'] = 'gd2';
            $config['source_image'] = $image;
            $config['wm_text'] = $text;
            $config['wm_type'] = 'text';
            $config['wm_font_path'] = './system/fonts/'.$watermark->font.'.ttf';
            $config['wm_font_size'] = (!empty($font_size))?$font_size:$watermark->wm_font_size;
            $config['wm_font_color'] = $watermark->wm_font_color;
            $config['wm_vrt_alignment'] = $watermark->wm_vrt_alignment;
            $config['wm_hor_alignment'] = $watermark->wm_hor_alignment;
            // $config['wm_padding'] = ($watermark->verticalAlignment=='bottom')?($watermark->padding*-1):$watermark->padding;
            // $config['wm_hor_offset'] = -50;//$watermark->verticalAlignment;
            // $config['wm_vrt_offset'] = 50;//$watermark->verticalAlignment;

            $this->load->library('image_lib', $config);
            $this->image_lib->clear(); 
            $this->image_lib->initialize($config);
            $this->image_lib->watermark();
            if(!empty($this->image_lib->display_errors())){
                echo $this->image_lib->display_errors();
            }
        }

        return;

    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function getUploadFolder($folder='photos')
    {

        return substr( realpath('.'), 0, strrpos(realpath('.'), DIRECTORY_SEPARATOR) ) . DIRECTORY_SEPARATOR . $folder;

    }

    public function deleteFile($file_id)
    {
        $file = $this->db->select('file_name, thumb_name')->from('task_images')->where(array('id'=>$file_id))->get()->row();
        if(!empty($file)){
            if(file_exists('uploads/tasks/'.$file->file_name)) {
                unlink('uploads/tasks/'.$file->file_name);
            }
            if(file_exists('uploads/tasks/'.$file->thumb_name)) {
                unlink('uploads/tasks/'.$file->thumb_name);
            }
        }
    }

}