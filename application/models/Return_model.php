<?php defined('BASEPATH') or exit('No direct script access allowed');

class Return_model extends CI_Model
{
    private $table = 'borrowing_items';

    private $columns = [
        0  => 'borrowings.id',
        1  => 'borrowings.borrower_employee_id',
        2  => 'borrowings.borrower_name',
        3  => 'items.item_name',
        4  => 'items.category',
        5  => 'borrowing_items.condition_after',
        6  => 'borrowings.date_released',
        7  => 'borrowings.due_date',
        8  => 'borrowing_items.date_returned',
        9  => 'borrowing_items.item_status',
        10 => 'received_user.username',
    ];

    private function _base_query()
    {
        $this->db->select('
            borrowing_items.id,
            borrowing_items.condition_after,
            borrowing_items.item_status,
            borrowing_items.date_returned,
            borrowing_items.remarks,
            borrowings.id as borrowing_id,
            borrowings.date_released,
            borrowings.due_date,
            borrowings.status as borrowing_status
            borrowings.borrower_employee_id,
            borrowings.borrower_name,
            borrowings.borrower_position,
            borrowings.borrower_dept,
            borrowings.borrower_photo,
            items.item_name,
            items.category,
            itemized.unit_no,
            received_user.username as received_by_name
        ');
        $this->db->from($this->table);
        $this->db->join('borrowings', 'borrowings.id = borrowing_items.borrowing_id', 'left');
        $this->db->join('itemized', 'itemized.id = borrowing_items.unit_id', 'left');
        $this->db->join('items', 'items.id = itemized.item_id', 'left');
        $this->db->join('users as received_user', 'received_user.id = borrowing_items.received_by', 'left');
        $this->db->where('borrowing_items.item_status !=', 'borrowed');   // only show completed returns
    }

    public function count_total()
    {
        $this->_base_query();
        return $this->db->count_all_results();
    }

    public function count_filtered($search = '', $filters = [])
    {
        $this->_base_query();
        $this->_apply_filters($filters);
        $this->_apply_search($search);
        return $this->db->count_all_results();
    }

    public function get_datatables($limit, $start, $search = '', $order_col = 0, $order_dir = 'asc', $filters = [])
    {
        $this->_base_query();
        $this->_apply_filters($filters);
        $this->_apply_search($search);

        $col = isset($this->columns[$order_col]) ? $this->columns[$order_col] : 'borrowing_items.date_returned';
        $this->db->order_by($col, $order_dir);
        $this->db->limit($limit, $start);

        return $this->db->get()->result();
    }

    private function _apply_filters($filters = [])
    {
        if (!empty($filters['item_status'])) {
            $this->db->where('borrowing_items.item_status', $filters['item_status']);
        }

        // Determine which date column to filter on
        $date_field = 'borrowing_items.date_returned'; // default
        if (!empty($filters['date_type'])) {
            switch ($filters['date_type']) {
                case 'borrowed_date':
                    $date_field = 'borrowings.date_released';
                    break;
                case 'due_date':
                    $date_field = 'borrowings.due_date';
                    break;
                case 'return_date':
                    $date_field = 'borrowing_items.date_returned';
                    break;
            }
        }

        if (!empty($filters['date_from'])) {
            $this->db->where("DATE({$date_field}) >=", $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where("DATE({$date_field}) <=", $filters['date_to']);
        }
    }

    private function _apply_search($search = '')
    {
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('borrowing.borrower_employee_id', $search);
            $this->db->or_like('borrowings.borrower_name', $search);
            $this->db->or_like('items.item_name', $search);
            $this->db->or_like('items.category', $search);
            $this->db->group_end();
        }
    }
    // Get full detail for one borrowing_item record (borrow + return info combined)
    public function get_full_details($id)
    {
        $this->db->select('
        borrowing_items.id,
        borrowing_items.condition_before,
        borrowing_items.condition_after,
        borrowing_items.item_status,
        borrowing_items.date_returned,
        borrowing_items.remarks,
        borrowings.id as borrowing_id,
        borrowings.purpose,
        borrowings.status as borrowing_status,
        borrowings.date_requested,
        borrowings.date_released,
        borrowings.due_date,
        borrowings.borrower_employee_id,
        borrowings.borrower_name,
        borrowings.borrower_position,
        borrowings.borrower_dept,
        borrowings.borrower_photo,
        items.item_name,
        items.category,
        items.brand,
        items.Model,
        items.serial_number,
        itemized.unit_no,
        released_user.username as released_by_name,
        received_user.username as received_by_name
    ');
        $this->db->from($this->table);
        $this->db->join('borrowings', 'borrowings.id = borrowing_items.borrowing_id', 'left');
        $this->db->join('itemized', 'itemized.id = borrowing_items.unit_id', 'left');
        $this->db->join('items', 'items.id = itemized.item_id', 'left');
        $this->db->join('users as released_user', 'released_user.id = borrowings.released_by', 'left');
        $this->db->join('users as received_user', 'received_user.id = borrowing_items.received_by', 'left');
        $this->db->where('borrowing_items.id', $id);

        return $this->db->get()->row();
    }
}
