<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ping extends CI_Controller
{
    /**
     * Ping endpoint to check if user session is still active
     * Called every 5 seconds by main.js isLoggedIn() function
     * 
     * If user is not logged in, the Auth hook will catch it and return error
     * If we reach here, user is logged in, so return success
     * Also updates online_users table to track active users
     */
    function index()
    {
        // Track online status
        $this->trackOnlineStatus();
        
        // Clean up inactive users (not active in last 2 minutes)
        $this->cleanupInactiveUsers();
        
        echo json_encode(array(
            "result" => true,
            "user_id" => $this->getUserId(),
            "user_type" => $this->getUserType(),
            "timestamp" => date('Y-m-d H:i:s')
        ));
    }
    
    /**
     * Track user's online status in the database
     */
    private function trackOnlineStatus()
    {
        $userId = $this->getUserId();
        $userType = $this->getUserType();
        
        if (!$userId || !$userType) {
            return;
        }
        
        // Get user details based on type
        $userData = $this->getUserData($userId, $userType);
        
        if (!$userData) {
            return;
        }
        
        $data = array(
            'user_id' => $userId,
            'user_type' => $userType,
            'name' => $userData['name'],
            'email' => $userData['email'],
            'photo' => $userData['photo'],
            'session_id' => session_id(),
            'ip_address' => $this->input->ip_address(),
            'user_agent' => substr($this->input->user_agent(), 0, 255),
            'last_activity' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        );
        
        // Check if user already exists in online_users
        $existing = $this->db->where('user_id', $userId)
                            ->where('user_type', $userType)
                            ->get('online_users')
                            ->row();
        
        if ($existing) {
            // Update last_activity
            $this->db->where('user_id', $userId)
                    ->where('user_type', $userType)
                    ->update('online_users', array(
                        'last_activity' => $data['last_activity'],
                        'session_id' => $data['session_id'],
                        'ip_address' => $data['ip_address']
                    ));
        } else {
            // Insert new record
            $this->db->insert('online_users', $data);
        }
    }
    
    /**
     * Get current user ID based on session
     */
    private function getUserId()
    {
        // Check admin user
        if (isset($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        }
        
        // Check customer
        if (isset($_SESSION['customer_access_id'])) {
            return $_SESSION['customer_access_id'];
        }
        
        // Check developer
        if (isset($_SESSION['developer_id'])) {
            return $_SESSION['developer_id'];
        }
        
        return null;
    }
    
    /**
     * Get current user type based on session
     */
    private function getUserType()
    {
        if (isset($_SESSION['user_id'])) {
            return 'admin';
        }
        
        if (isset($_SESSION['customer_access_id'])) {
            return 'customer';
        }
        
        if (isset($_SESSION['developer_id'])) {
            return 'developer';
        }
        
        return null;
    }
    
    /**
     * Get user data from respective table
     */
    private function getUserData($userId, $userType)
    {
        switch ($userType) {
            case 'admin':
                $user = $this->db->select('name, username as email, photo')
                                ->where('id', $userId)
                                ->get('users')
                                ->row_array();
                break;
                
            case 'customer':
                $user = $this->db->select('CONCAT(first_name, " ", last_name) as name, email, "" as photo')
                                ->where('id', $userId)
                                ->get('customer_access')
                                ->row_array();
                break;
                
            case 'developer':
                $user = $this->db->select('COALESCE(display_name, name) as name, email, photo')
                                ->where('id', $userId)
                                ->get('developers')
                                ->row_array();
                break;
                
            default:
                return null;
        }
        
        return $user;
    }
    
    /**
     * Clean up users who haven't been active in last 2 minutes
     */
    private function cleanupInactiveUsers()
    {
        $timeout = date('Y-m-d H:i:s', strtotime('-2 minutes'));
        $this->db->where('last_activity <', $timeout)
                ->delete('online_users');
    }
}
