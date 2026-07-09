<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Borrowing_model extends CI_Model
{
    protected $table = 'borrowing';

    public function __construct()
    {
        parent::__construct();
    }

    // ==========================
    // Basic CRUD
    // ==========================

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function get_by_id($id)
    {
        return $this->db
            ->where('id', $id)
            ->get($this->table)
            ->row();
    }

    public function update($id, $data)
    {
        return $this->db
            ->where('id', $id)
            ->update($this->table, $data);
    }

    public function delete($id)
    {
        return $this->db
            ->where('id', $id)
            ->delete($this->table);
    }

    // ==========================
    // DataTables
    // ==========================

    public function count_total()
    {
        return $this->db->count_all($this->table);
    }

    public function count_filtered($search = '', $filters = [])
    {
        $this->apply_filters($search, $filters);

        return $this->db
            ->from($this->table)
            ->count_all_results();
    }

    public function get_datatables(
        $limit,
        $start,
        $search,
        $order_col,
        $order_dir,
        $filters = []
    ) {

        $columns = [
            'id',
            'borrow_no',
            'borrower_id',
            'borrower_name',
            'borrow_date',
            'due_date',
            'status',
            'released_by',
            'created_at'
        ];

        $this->db->from($this->table);

        $this->apply_filters($search, $filters);

        if (isset($columns[$order_col])) {
            $this->db->order_by($columns[$order_col], $order_dir);
        } else {
            $this->db->order_by('id', 'DESC');
        }

        $this->db->limit($limit, $start);

        return $this->db->get()->result();
    }

    // ==========================
    // Filters
    // ==========================

    private function apply_filters($search = '', $filters = [])
    {
        if (!empty($search)) {

            $this->db->group_start();

            $this->db->like('borrow_no', $search);
            $this->db->or_like('borrower_id', $search);
            $this->db->or_like('borrower_name', $search);
            $this->db->or_like('department', $search);

            $this->db->group_end();
        }

        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('borrow_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('borrow_date <=', $filters['date_to']);
        }
    }
}