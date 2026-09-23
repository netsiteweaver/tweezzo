<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Per-user dashboard layout. The Auth hook already guarantees a live session
 * by the time we get here, so we only need the user id off it.
 */
class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("dashboard_preferences_model");
    }

    public function savePreferences()
    {
        $user_id = $this->currentUserId();
        if(empty($user_id)) return $this->respond(false, "Not logged in.");

        $order  = json_decode($this->input->post('order'), true);
        $hidden = json_decode($this->input->post('hidden'), true);
        $breaks = json_decode($this->input->post('breaks'), true);

        if(!is_array($order))  $order  = array();
        if(!is_array($hidden)) $hidden = array();
        if(!is_array($breaks)) $breaks = array();

        $order  = $this->cleanKeys($order);
        $hidden = $this->cleanKeys($hidden);
        $breaks = $this->cleanKeys($breaks);

        if(empty($order)) return $this->respond(false, "Nothing to save.");

        $this->dashboard_preferences_model->save($user_id, $order, $hidden, $breaks);

        return $this->respond(true, "Dashboard layout saved.");
    }

    public function resetPreferences()
    {
        $user_id = $this->currentUserId();
        if(empty($user_id)) return $this->respond(false, "Not logged in.");

        $this->dashboard_preferences_model->reset($user_id);

        return $this->respond(true, "Dashboard reset to default.");
    }

    /** Keep only well-formed block keys, de-duplicated and order-preserving. */
    private function cleanKeys(array $keys)
    {
        $clean = array();
        foreach($keys as $key){
            if(!is_string($key)) continue;
            if(!preg_match('/^[a-z0-9_]{1,64}$/', $key)) continue;
            $clean[$key] = true;
        }
        return array_keys($clean);
    }

    private function currentUserId()
    {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }

    private function respond($result, $message)
    {
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode(array("result"=>$result, "message"=>$message)));
    }
}
