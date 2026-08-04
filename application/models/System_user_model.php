<?php
defined('BASEPATH') or exit('No direct script access allowed');

class System_user_model extends CI_Model
{
    protected $table = 'system_users';

    private $column = array(
        0 => 'employee_name',
        1 => 'employee_id',
        2 => 'employee_dept',
        3 => 'employee_status',
        4 => 'employee_bunit',
        5 => 'employee_type',
        6 => 'role',
        7 => 'account_status'
    );

    public function get_all()
    {
        return $this->db
            ->order_by('employee_name', 'ASC')
            ->get($this->table)
            ->result();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->where('id', (int) $id)
            ->get($this->table)
            ->row();
    }

    public function get_by_employee_id($employee_id)
    {
        return $this->db
            ->where('employee_id', $employee_id)
            ->get($this->table)
            ->row();
    }

    public function exists($employee_id)
    {
        return $this->db
            ->where('employee_id', $employee_id)
            ->count_all_results($this->table) > 0;
    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        return $this->db
            ->where('id', (int) $id)
            ->update($this->table, $data);
    }

    public function delete($id)
    {
        return $this->db
            ->where('id', (int) $id)
            ->delete($this->table);
    }

    public function count_total()
    {
        return $this->db->count_all($this->table);
    }

    public function count_filtered($search = '', $filters = array())
    {
        $this->_apply_filters($filters);
        $this->_apply_search($search);
        return $this->db->count_all_results($this->table);
    }

    public function get_datatables(
        $limit,
        $start,
        $search = '',
        $order_col = 0,
        $order_dir = 'asc',
        $filters = array()
    ) {
        $this->_apply_filters($filters);
        $this->_apply_search($search);

        $column = isset($this->column[$order_col])
            ? $this->column[$order_col]
            : 'employee_name';

        $direction = strtolower($order_dir) === 'desc' ? 'DESC' : 'ASC';

        $this->db->order_by($column, $direction);
        $this->db->limit((int) $limit, (int) $start);

        return $this->db->get($this->table)->result();
    }

    private function _apply_filters($filters = array())
    {
        if (!empty($filters['role'])) {
            $this->db->where('role', $filters['role']);
        }

        if (!empty($filters['account_status'])) {
            $this->db->where('account_status', $filters['account_status']);
        }
    }

    private function _apply_search($search = '')
    {
        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('employee_name', $search);
            $this->db->or_like('employee_id', $search);
            $this->db->or_like('employee_dept', $search);
            $this->db->group_end();
        }
    }

    public function reset_password($id)
    {
        return $this->set_password($id, 'bms-2026');
    }

    public function set_password($id, $plain_password)
    {
        $user = $this->get_by_id($id);

        if (!$user) {
            return FALSE;
        }

        return $this->db
            ->where('id', (int) $id)
            ->update($this->table, array(
                'password'              => password_hash($plain_password, PASSWORD_DEFAULT),
                'password_change_count' => (int) $user->password_change_count + 1,
                'last_password_change'  => date('Y-m-d H:i:s')
            ));
    }
}
