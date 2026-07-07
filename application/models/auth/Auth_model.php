<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {

    protected $table = 'users';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Fetch active, non-deleted user by email.
     * Used by Login controller.
     */
    public function get_by_email($username)
    {
        $this->db->where('username', $username);
        $this->db->where('deleted_at', NULL);   // exclude soft-deleted users
        $this->db->where('is_active', 1);       // exclude deactivated users
        $query = $this->db->get($this->table);
        return $query->row();
    }

    /**
     * Fetch user by ID.
     * Used by RMS_Controller to load current user into session context.
     */
    public function get_by_id($id)
    {
        $this->db->where('id', $id);
        $this->db->where('deleted_at', NULL);
        $query = $this->db->get($this->table);
        return $query->row();
    }
}