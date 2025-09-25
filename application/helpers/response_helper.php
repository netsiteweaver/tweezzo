<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('json_response')) {
    /**
     * Send JSON response
     *
     * @param mixed $data      Data to send (array/object/string)
     * @param int   $status    HTTP status code
     * @param bool  $error     Whether it's an error response
     */
    function json_response($data="", $status = 200, $error = false)
    {
        $CI =& get_instance();

        // Prepare response format
        $response = [
            'result'  => $error ? false : true,
            'message' => is_string($data) ? $data : null,
            'data'    => is_array($data) || is_object($data) ? $data : null
        ];

        // Send response
        return $CI->output
            ->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode($response, JSON_UNESCAPED_UNICODE));
    }
}
