<?php

class Quotes_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getRandomQuote()
    {
        $query = "SELECT * FROM quotes WHERE deleted_on IS NULL ORDER BY RAND() LIMIT 1";
        $quote = $this->db->query($query)->row();
        return $quote;
    }

}