<?php defined('BASEPATH') or exit('No direct script access allowed');

class Borrower_model extends CI_Model
{
    private $table = 'borrowers';

    public function get_all()
    {
        $this->db->where('status', 'active');
        $this->db->order_by('full_name', 'asc');
        return $this->db->get($this->table)->result();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }
}