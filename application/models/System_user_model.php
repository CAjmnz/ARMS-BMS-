<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class System_user_model extends CI_Model
{
    protected $table = 'system_users';

    private $column =[
        0 => 'employee_name',
        1 => 'employee_id',
        2 => 'employee_dept',
        3 => 'employee_status',
        4 => 'employee_bunit',
        5 => 'employee_type',
        6 => 'role',
        7 => 'account_status',
    ];
    /**
     * Get all system users
     */
    public function get_all()
    {
        return $this->db->order_by('employee_name', 'ASC')
                        ->get($this->table)
                        ->result();
    }


    public function get_by_id($id)
    {
        return $this->db->where('id',$id)->get($this->table)->row();
    }
    /**
     * Get user by employee ID
     */
    public function get_by_employee_id($employee_id)
    {
        return $this->db
                    ->where('employee_id', $employee_id)
                    ->get($this->table) > 0;
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
    // ─── DataTables support ─────────────────────────────
    public function count_total()
    {
        return $this->db->count_all($this->table);
    }

    public function count_filtered($search = '',$filters = [])
    {
        $this->_apply_filters($filters);
        $this->_apply_search($search);
        return $this->db->count_all_results($this->table);
    }
    
    public function get_datatables($limit,$start,$search = '', $order_col = 0,
    $order_dir = 'asc', $filters=[])
    {
        $this->_apply_filters($filters);
        $this->_apply_search($search);

        $col = isset($this->columns[$order_col]) ? $this->columns[$order_col]:'employee_name';
        $this->db->order_by($col,$order_dir);
        $this->db->limit($limit, $start);

        return $this->db->get($this->table)->result();
    }

    private function _apply_filters($filters = [])
    {
        if(!empty($filters['role'])){
            $this->db->where('role',$filter['role']);
        }
        if(!empty($filters['account_status'])) {
            $this->db->where('account_status', $filters['account_status']);
        }
    }

    private function _apply_search($search = '')
    {
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('employee_name', $search);
            $this->db->or_like('employee_id', $search);
            $this->db->or_like('employee_dept', $search);
            $this->db->group_end();
        }
    }
}

