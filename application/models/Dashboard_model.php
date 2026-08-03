<?php defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_model extends CI_Model
{
    // ─── Summary Cards ──────────────────────────────────
    public function get_summary()
    {
        $summary = [];

        // Total item types
        $summary['total_item_types'] = $this->db->count_all('items');

        // Total physical units
        $summary['total_units'] = $this->db->count_all('itemized');

        // Available units right now
        $summary['available_units'] = $this->db->where('status', 'available')->count_all_results('itemized');

        // Currently borrowed units
        $summary['borrowed_units'] = $this->db->where('status', 'borrowed')->count_all_results('itemized');

        // Current Reserved units
        $summary['reserved_units'] = $this->db->where('status', 'reserved')->count_all_results('reservation_items');

        // Overdue — borrowed + due_date passed
        $this->db->where('borrowing_items.item_status', 'borrowed');
        $this->db->where('borrowings.due_date <', date('Y-m-d H:i:s'));
        $this->db->join('borrowings', 'borrowings.id = borrowing_items.borrowing_id');
        $summary['overdue_count'] = $this->db->count_all_results('borrowing_items');

        // Returned today
        $this->db->where('DATE(date_returned)', date('Y-m-d'));
        $this->db->where('item_status !=', 'borrowed');
        $summary['returned_today'] = $this->db->count_all_results('borrowing_items');

        // Total active borrowers
        $summary['total_borrowers'] = $this->db->where('status', 'active')->count_all_results('borrowers');

        return $summary;
    }

    // ─── Recent Activity Feed (latest 10 borrow/return actions) ──
    public function get_recent_activity($limit = 10)
    {
        // Recent borrows
        $this->db->select("
            'borrowed' as action_type,
            borrowings.date_released as action_date,
            borrowers.full_name as borrower_name,
            items.item_name,
            itemized.unit_no
        ");
        $this->db->from('borrowing_items');
        $this->db->join('borrowings', 'borrowings.id = borrowing_items.borrowing_id');
        $this->db->join('borrowers', 'borrowers.id = borrowings.borrower_id');
        $this->db->join('itemized', 'itemized.id = borrowing_items.unit_id');
        $this->db->join('items', 'items.id = itemized.item_id');
        $borrows = $this->db->get()->result();

        // Recent returns
        $this->db->select("
            borrowing_items.item_status as action_type,
            borrowing_items.date_returned as action_date,
            borrowers.full_name as borrower_name,
            items.item_name,
            itemized.unit_no
        ");
        $this->db->from('borrowing_items');
        $this->db->join('borrowings', 'borrowings.id = borrowing_items.borrowing_id');
        $this->db->join('borrowers', 'borrowers.id = borrowings.borrower_id');
        $this->db->join('itemized', 'itemized.id = borrowing_items.unit_id');
        $this->db->join('items', 'items.id = itemized.item_id');
        $this->db->where('borrowing_items.item_status !=', 'borrowed');
        $returns = $this->db->get()->result();

        // Merge, sort by date desc, limit
        $all = array_merge($borrows, $returns);
        usort($all, function ($a, $b) {
            return strtotime($b->action_date) - strtotime($a->action_date);
        });

        return array_slice($all, 0, $limit);
    }

    // ─── Chart: Items by Category ──────────────────────
    public function get_category_breakdown()
    {
        $this->db->select('category, SUM(quantity) as total_quantity');
        $this->db->group_by('category');
        return $this->db->get('items')->result();
    }

    // ─── Chart: Borrowing Trend (last 12 month) ──────────
    public function get_borrowing_trend($months = 12)
    {
        $start_date = date('Y-m-01', strtotime("-{$months} months"));

        $this->db->select("DATE_FORMAT(date_released, '%Y-%m') as month, COUNT(*) as total");
        $this->db->where('date_released >=', $start_date);
        $this->db->group_by("DATE_FORMAT(date_released, '%Y-%m')");
        $this->db->order_by('month', 'asc');
        $results = $this->db->get('borrowings')->result();

        // Fill in missing months with 0 so the chart doesn't have gaps
        $trend = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-{$i} months"));
            $trend[$month] = 0;
        }
        foreach ($results as $row) {
            $trend[$row->month] = (int) $row->total;
        }

        return $trend;
    }

    public function get_most_borrowed_items($limit = 5)
    {
        $limit = (int) $limit;

        if ($limit < 1) {
            $limit = 5;
        }

        $this->db->select(
            'items.id,
         items.item_name,
         COUNT(borrowing_items.id) AS borrow_count',
            false
        );

        $this->db->from('borrowing_items');
        $this->db->join(
            'itemized',
            'itemized.id = borrowing_items.unit_id',
            'inner'
        );
        $this->db->join(
            'items',
            'items.id = itemized.item_id',
            'inner'
        );

        $this->db->group_by('items.id');
        $this->db->group_by('items.item_name');
        $this->db->order_by('borrow_count', 'DESC');
        $this->db->order_by('items.item_name', 'ASC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }
    public function get_due_today()
{
    $today_start = date('Y-m-d 00:00:00');
    $tomorrow    = date('Y-m-d 00:00:00', strtotime('+1 day'));

    $this->db->select('
        borrowing_items.id AS borrowing_item_id,
        borrowings.id AS borrowing_id,
        borrowings.due_date,
        borrowings.borrower_employee_id,
        borrowings.borrower_name,
        items.item_name,
        itemized.unit_no
    ');

    $this->db->from('borrowing_items');

    $this->db->join(
        'borrowings',
        'borrowings.id = borrowing_items.borrowing_id',
        'inner'
    );

    $this->db->join(
        'itemized',
        'itemized.id = borrowing_items.unit_id',
        'inner'
    );

    $this->db->join(
        'items',
        'items.id = itemized.item_id',
        'inner'
    );

    $this->db->where(
        'borrowing_items.item_status',
        'borrowed'
    );

    $this->db->where(
        'borrowings.due_date >=',
        $today_start
    );

    $this->db->where(
        'borrowings.due_date <',
        $tomorrow
    );

    $this->db->order_by(
        'borrowings.due_date',
        'ASC'
    );

    return $this->db->get()->result();
}
public function get_low_stock_items($limit = 8)
{
    $limit = (int) $limit;

    if ($limit < 1) {
        $limit = 8;
    }

    $this->db->select(
        "items.id,
         items.item_name,
         SUM(
             CASE
                 WHEN itemized.status = 'available' THEN 1
                 ELSE 0
             END
         ) AS available_units",
        false
    );

    $this->db->from('items');

    $this->db->join(
        'itemized',
        'itemized.item_id = items.id',
        'left'
    );

    $this->db->group_by('items.id');
    $this->db->group_by('items.item_name');

    // Hide items with no available units
    $this->db->having('available_units >', 0);

    // Only show items with 1–4 available units
    $this->db->having('available_units <', 5);

    $this->db->order_by('available_units', 'ASC');
    $this->db->order_by('items.item_name', 'ASC');
    $this->db->limit($limit);

    return $this->db->get()->result();
}
}
