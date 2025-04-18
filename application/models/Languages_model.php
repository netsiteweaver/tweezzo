<?php

class Languages_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        return $this->db->select('id,name,abbr,flag')->from('languages')->where('status','1')->get()->result();
    }

    public function getAll2()
    {
        return $this->db->select('id,name,abbr,flag,status')->from('languages')->get()->result();
    }

    public function get($id)
    {
        return $this->db->select('id,name,abbr,flag')->from('languages')->where(array('status'=>'1','id'=>$id))->get()->row();
    }

    public function update()
    {
        $id = $this->input->post('id');
        $this->db->set('name',$this->input->post('name'));
        $this->db->set('abbr',$this->input->post('abbr'));
        if(!empty($_FILES['flag']['tmp_name'])){
            $flag = $this->upload_image();
            $this->db->set('flag',$flag);
        }
        $this->db->where('id',$id);
        $this->db->update('languages');
    }

    public function save()
    {
        $this->db->set('name',$this->input->post('name'));
        $this->db->set('abbr',strtolower($this->input->post('abbr')));
        if(!empty($_FILES['flag']['tmp_name'])){
            $flag = $this->upload_image();
            $this->db->set('flag',$flag);
        }
        $this->db->insert('languages');
        $this->add_columns(strtolower($this->input->post('abbr')));
    }

    private function upload_image()
    {
        if (!move_uploaded_file($_FILES['flag']['tmp_name'],'../uploads/flags/'.$_FILES['flag']['name'])){
            throw new RuntimeException('Failed to move uploaded file.');
        }
        return $_FILES['flag']['name'];
    }

    private function add_columns($lang)
    {
        $this->db->query("ALTER TABLE `pages` ADD COLUMN `name_$lang` TEXT NOT NULL AFTER `icon`");
        $this->db->query("UPDATE `pages` SET `name_$lang` = `name_en`");

        $this->db->query("ALTER TABLE `pages` ADD COLUMN `slug_$lang` TEXT NOT NULL AFTER `link`");
        $this->db->query("UPDATE `pages` SET `slug_$lang` = `slug_en`");

        $this->db->query("ALTER TABLE `page_block` ADD COLUMN `content_$lang` TEXT NOT NULL AFTER `block_order`");
        $this->db->query("UPDATE `page_block` SET `content_$lang` = `content_en`");

        $this->db->query("ALTER TABLE `translations` ADD COLUMN `text_$lang` TEXT NOT NULL AFTER `id`");
        $this->db->query("UPDATE `translations` SET `text_$lang` = `text_en`");
    }

    private function drop_columns($lang)
    {
        $this->db->query("ALTER TABLE `pages` DROP COLUMN `name_$lang`");
        $this->db->query("ALTER TABLE `pages` DROP COLUMN `slug_$lang`");
        $this->db->query("ALTER TABLE `page_block` DROP COLUMN `content_$lang`");
        $this->db->query("ALTER TABLE `translations` DROP COLUMN `text_$lang`");
    }

}