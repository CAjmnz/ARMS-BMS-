<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class System_user_model extends CI_Model
{
    protected $table = 'system_users';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all system users
     */
    public function get_all()
    {
        return $this->db->order_by('employee_name', 'ASC')
                        ->get($this->table)
                        ->result();
    }

    /**
     * Get user by employee ID
     */
    public function get_by_employee_id($employee_id)
    {
        return $this->db
                    ->where('employee_id', $employee_id)
                    ->get($this->table)
                    ->row();
    }

    /**
     * Insert new system user
     */
    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update system user
     */
    public function update($id, $data)
    {
        return $this->db
                    ->where('id', $id)
                    ->update($this->table, $data);
    }

    /**
     * Delete system user
     */
    public function delete($id)
    {
        return $this->db
                    ->where('id', $id)
                    ->delete($this->table);
    }
}