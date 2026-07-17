<?php defined('BASEPATH') or exit('No direct script access allowed');

class Borrowing_model extends CI_Model
{
    private $table = 'borrowing_items';

    // Searchable/sortable columns for DataTables
    private $columns = [
        0  => 'borrowers.id_number',
        1  => 'borrowers.full_name',
        2  => 'items.item_name',
        3  => 'items.category',
        4  => 'borrowing_items.condition_before',
        5  => 'borrowings.date_released',
        6  => 'borrowings.due_date',
        7  => 'borrowing_items.item_status',
        8  => 'users.full_name',
    ];

    private function _base_query()
{
    $this->db->select('
        borrowing_items.id,
        borrowing_items.unit_id,
        borrowing_items.condition_before,
        borrowing_items.item_status,
        borrowings.id as borrowing_id,
        borrowings.date_released,
        borrowings.due_date,
        borrowings.status as borrowing_status,
        borrowers.id_number,
        borrowers.full_name as borrower_name,
        items.item_name,
        items.category,
        itemized.unit_no,
        users.username as released_by_name
    ');
    $this->db->from($this->table);
    $this->db->join('borrowings', 'borrowings.id = borrowing_items.borrowing_id', 'left');
    $this->db->join('borrowers', 'borrowers.id = borrowings.borrower_id', 'left');
    $this->db->join('itemized', 'itemized.id = borrowing_items.unit_id', 'left');
    $this->db->join('items', 'items.id = itemized.item_id', 'left');
    $this->db->join('users', 'users.id = borrowings.released_by', 'left');
    $this->db->where('borrowing_items.item_status', 'borrowed');   // ← only show active borrowings
}
    // Get single borrowing_item by ID
    public function get_by_id($id)
    {
        $this->_base_query();
        $this->db->where('borrowing_items.id', $id);
        return $this->db->get()->row();
    }

    // Count total records
    public function count_total()
    {
        return $this->db->count_all($this->table);
    }

    // Count filtered records
    public function count_filtered($search = '', $filters = [])
    {
        $this->_base_query();
        $this->_apply_filters($filters);
        $this->_apply_search($search);
        return $this->db->count_all_results();
    }

    // Get paginated rows (with search + filters + sort)
    public function get_datatables($limit, $start, $search = '', $order_col = 0, $order_dir = 'asc', $filters = [])
    {
        $this->_base_query();
        $this->_apply_filters($filters);
        $this->_apply_search($search);

        $col = isset($this->columns[$order_col]) ? $this->columns[$order_col] : 'borrowings.date_released';
        $this->db->order_by($col, $order_dir);
        $this->db->limit($limit, $start);

        return $this->db->get()->result();
    }

    // Private — apply filters
    private function _apply_filters($filters = [])
    {
        if (!empty($filters['item_status'])) {
            if ($filters['item_status'] === 'overdue') {
                $this->db->where('borrowing_items.item_status', 'borrowed');
                $this->db->where('borrowings.due_date <', date('Y-m-d H:i:s'));
            } elseif ($filters['item_status'] === 'due_soon') {
                $this->db->where('borrowing_items.item_status', 'borrowed');
                $this->db->where('borrowings.due_date >=', date('Y-m-d H:i:s'));
                $this->db->where('borrowings.due_date <=', date('Y-m-d H:i:s', strtotime('+24 hours')));
            } else {
                $this->db->where('borrowing_items.item_status', $filters['item_status']);
            }
        }
        if (!empty($filters['borrowing_status'])) {
            $this->db->where('borrowings.status', $filters['borrowing_status']);
        }
        if (!empty($filters['date_from'])) {
            $this->db->where('DATE(borrowings.date_released) >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('DATE(borrowings.date_released) <=', $filters['date_to']);
        }
    }
    // Private — apply search
    private function _apply_search($search = '')
    {
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('borrowers.id_number', $search);
            $this->db->or_like('borrowers.full_name', $search);
            $this->db->or_like('items.item_name', $search);
            $this->db->or_like('items.category', $search);
            $this->db->group_end();
        }
    }

    // Update item status (e.g. mark returned/damaged/lost)
    public function update_item($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('borrowing_items', $data);
    }

    // Delete a borrowing item row
    public function delete_item($id)
    {
        return $this->db->delete('borrowing_items', ['id' => $id]);
    }
    // Get available units for a given item (for the Add Borrowing form)
    public function get_available_units($item_id)
    {
        $this->db->select('id, unit_no, item_condition');
        $this->db->where('item_id', $item_id);
        $this->db->where('status', 'available');
        $this->db->order_by('unit_no', 'asc');
        return $this->db->get('itemized')->result();
    }

    // Create a borrowing transaction + its items
    public function create_borrowing($header, $unit_ids)
    {
        $this->db->trans_start();

        $this->db->insert('borrowings', $header);
        $borrowing_id = $this->db->insert_id();

        foreach ($unit_ids as $unit_id) {
            $unit = $this->db->get_where('itemized', ['id' => $unit_id])->row();

            if ($unit) {
                $this->db->insert('borrowing_items', [
                    'borrowing_id'     => $borrowing_id,
                    'unit_id'          => $unit_id,
                    'condition_before' => $unit->item_condition,
                    'item_status'      => 'borrowed',
                ]);

                // Flip unit to borrowed
                $this->db->where('id', $unit_id);
                $this->db->update('itemized', ['status' => 'borrowed']);
            }
        }

        $this->db->trans_complete();

        return $this->db->trans_status() ? $borrowing_id : false;
    }

    //Check if a unit has any borrowing history (blocks unit deletion if  true)
    public function unit_has_borrowing_history($unit_id)
    {
        $this->db->where('unit_id',$unit_id);
        return $this->db->count_all_results('borrowing_items') > 0;
    }
    // Get a single borrowing_item (for the Mark Returned modal)
public function get_item_by_id($id)
{
    $this->_base_query();
    $this->db->where('borrowing_items.id', $id);
    return $this->db->get()->row();
}

// Mark a specific borrowing_item as returned/damaged/lost
public function mark_returned($id, $data)
{
    $this->db->where('id', $id);
    return $this->db->update('borrowing_items', $data);
}

// Check if every item in a borrowing transaction has been returned
public function all_items_returned($borrowing_id)
{
    $this->db->where('borrowing_id', $borrowing_id);
    $this->db->where('item_status', 'borrowed');
    $still_out = $this->db->count_all_results('borrowing_items');
    return $still_out === 0;
}

// Close out the borrowing transaction once everything is back
public function close_borrowing($borrowing_id)
{
    $this->db->where('id', $borrowing_id);
    return $this->db->update('borrowings', [
        'status'        => 'returned',
        'date_returned' => date('Y-m-d H:i:s'),
    ]);
}
}
