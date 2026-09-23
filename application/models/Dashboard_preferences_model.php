<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Per-user dashboard block layout: position, visibility and row breaks.
 *
 * Rows are keyed on users.id (the primary key), so the shared users table
 * needs no user_type filter here - the back office dashboard is the only
 * consumer and $_SESSION['user_id'] is always a back office user.
 */
class Dashboard_preferences_model extends CI_Model
{
    /** Blocks never seen before sort after every saved one, still visible. */
    const UNSAVED_OFFSET = 1000;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return array block_key => ['position'=>int,'visible'=>bool,'row_break'=>bool]
     */
    public function getForUser($user_id)
    {
        if(empty($user_id)) return array();

        $rows = $this->db->select("block_key, position, visible, row_break")
                         ->from("dashboard_preferences")
                         ->where("user_id", $user_id)
                         ->get()->result();

        $prefs = array();
        foreach($rows as $row){
            $prefs[$row->block_key] = array(
                "position"  =>  (int)$row->position,
                "visible"   =>  (bool)$row->visible,
                "row_break" =>  (bool)$row->row_break
            );
        }
        return $prefs;
    }

    /**
     * Order $blocks by the user's saved layout and flag each one's visibility.
     * Blocks with no saved row keep their default order at the end and stay visible.
     *
     * @param array $blocks list of blocks, each with a 'key'
     * @return array same blocks, reordered, each with 'visible' and 'row_break' bools
     */
    public function applyTo(array $blocks, $user_id)
    {
        $prefs = $this->getForUser($user_id);

        $ordered = array();
        foreach(array_values($blocks) as $index => $block){
            $key = $block['key'];
            $block['visible']   = isset($prefs[$key]) ? $prefs[$key]['visible'] : true;
            $block['row_break'] = isset($prefs[$key]) ? $prefs[$key]['row_break'] : false;
            $block['position'] = isset($prefs[$key])
                                    ? $prefs[$key]['position']
                                    : ($index + self::UNSAVED_OFFSET);
            $ordered[] = $block;
        }

        usort($ordered, function($a, $b){
            if($a['position'] == $b['position']) return 0;
            return ($a['position'] < $b['position']) ? -1 : 1;
        });

        return $ordered;
    }

    /**
     * Replace the user's whole layout in one go.
     *
     * @param array $ordered_keys block keys in the order they should render
     * @param array $hidden_keys  block keys the user has hidden
     * @param array $break_keys   block keys after which a new row starts
     */
    public function save($user_id, array $ordered_keys, array $hidden_keys, array $break_keys = array())
    {
        if(empty($user_id) || empty($ordered_keys)) return false;

        $hidden = array_flip($hidden_keys);
        $breaks = array_flip($break_keys);
        $now = date("Y-m-d H:i:s");

        $rows = array();
        foreach(array_values($ordered_keys) as $position => $key){
            $rows[] = array(
                "user_id"       =>  $user_id,
                "block_key"     =>  $key,
                "position"      =>  $position,
                "visible"       =>  isset($hidden[$key]) ? 0 : 1,
                "row_break"     =>  isset($breaks[$key]) ? 1 : 0,
                "updated_on"    =>  $now
            );
        }

        // One statement, so a re-save overwrites the previous layout atomically.
        $values = array();
        foreach($rows as $row){
            $values[] = "(" . $this->db->escape($row['user_id']) . ","
                            . $this->db->escape($row['block_key']) . ","
                            . $this->db->escape($row['position']) . ","
                            . $this->db->escape($row['visible']) . ","
                            . $this->db->escape($row['row_break']) . ","
                            . $this->db->escape($row['updated_on']) . ")";
        }

        $this->db->query("INSERT INTO `dashboard_preferences`
                            (`user_id`,`block_key`,`position`,`visible`,`row_break`,`updated_on`)
                          VALUES " . implode(",", $values) . "
                          ON DUPLICATE KEY UPDATE
                            `position`   = VALUES(`position`),
                            `visible`    = VALUES(`visible`),
                            `row_break`  = VALUES(`row_break`),
                            `updated_on` = VALUES(`updated_on`)");

        return true;
    }

    /** Drop the user's layout so the dashboard falls back to defaults. */
    public function reset($user_id)
    {
        if(empty($user_id)) return false;
        $this->db->where("user_id", $user_id)->delete("dashboard_preferences");
        return true;
    }
}
