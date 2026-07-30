<?php defined('BASEPATH') or exit('No direct script access allowed');

class Notification_model extends CI_Model
{
    private $table = 'borrowing_items';

    private function _base_query()
    {
        $this->db->select('
            borrowing_items.id,
            borrowing_items.item_status,
            borrowings.id AS borrowing_id,
            borrowings.due_date,
            borrowings.borrower_employee_id AS id_number,
            borrowings.borrower_name,
            borrowings.borrower_position,
            borrowings.borrower_photo,
            items.item_name,
            itemized.unit_no
        ');
    
        $this->db->from($this->table);
    
        $this->db->join(
            'borrowings',
            'borrowings.id = borrowing_items.borrowing_id',
            'left'
        );
    
        $this->db->join(
            'itemized',
            'itemized.id = borrowing_items.unit_id',
            'left'
        );
    
        $this->db->join(
            'items',
            'items.id = itemized.item_id',
            'left'
        );
    
        $this->db->where(
            'borrowing_items.item_status',
            'borrowed'
        );
    }
    // Items overdue right now
    public function get_overdue()
    {
        $this->_base_query();
        $this->db->where('borrowings.due_date <', date('Y-m-d H:i:s'));
        $this->db->order_by('borrowings.due_date', 'asc');
        return $this->db->get()->result();
    }

    // Items due within the next 24 hours (not yet overdue)
    public function get_due_soon()
    {
        $this->_base_query();
        $this->db->where('borrowings.due_date >=', date('Y-m-d H:i:s'));
        $this->db->where('borrowings.due_date <=', date('Y-m-d H:i:s', strtotime('+24 hours')));
        $this->db->order_by('borrowings.due_date', 'asc');
        return $this->db->get()->result();
    }

    // Combined count for the topbar bell badge
    public function get_notification_count()
    {
        $overdue  = count($this->get_overdue());
        $due_soon = count($this->get_due_soon());
        return $overdue + $due_soon;
    }
}