# ARMS-BMS dashboard update

This update adds:

- A **Due Today** table that lists currently borrowed units whose due date is
  today. Clicking a row opens the Notifications page.
- A **Low Stock Items** table that lists item types with 1 to 4 available
  units. Counts 1-2 are critical/red; counts 3-4 are low/yellow. Zero-stock
  items are intentionally excluded.
- A **Quick Actions** dropdown in the topbar for Add Item, Borrow Item, Return
  Item, Manage Users, and Reports.

The code is compatible with PHP 5.4, CodeIgniter 3, Bootstrap 4.6, jQuery, and
the Chart.js 2.9.4 dashboard setup from the previous graph fix.

## Installation

1. Copy `application/controllers/Dashboard.php` into your project.
2. Open your existing `application/models/Dashboard_model.php`. Copy both
   methods from `integration/Dashboard_model-methods.php.txt` and paste them
   inside the `Dashboard_model` class, immediately before the final `}`.
   Do not replace your full model because it contains your existing summary,
   activity, and chart methods.
3. Copy `application/views/dashboard/index.php` into your project.
4. Copy `assets/js/modules/dashboard.main.js` into your project.
5. Copy `assets/css/modules/10-dashboard.css` and
   `assets/css/modules/70-topbar-modern.css` into your project.
6. Copy `application/views/templates/_quick_actions.php` into your project.
7. Open `application/views/templates/topbar.php`. Inside the existing
   `<div class="topbar-right">`, place this line before the notification bell:

   `<?php $this->load->view('templates/_quick_actions'); ?>`

8. Restart Apache and hard-refresh the browser with `Ctrl + F5`.

## Existing database fields used

The Due Today query uses the structure already used by the notification model:

- `borrowing_items.borrowing_id`
- `borrowing_items.unit_id`
- `borrowing_items.item_status`
- `borrowings.id`
- `borrowings.due_date`
- `borrowings.borrower_name`
- `itemized.id`, `itemized.item_id`, `itemized.unit_no`
- `items.id`, `items.item_name`

The Low Stock query counts rows where `itemized.status = 'available'` and does
not change the database.

## Route note

The existing routes confirmed from the project are `items/create`, `borrowing`,
`returns`, `users`, and `notifications`. The Reports controller route was not
shown in the supplied files, so the partial uses `reports`. If your controller
is singular, change only this line:

`<?= base_url('reports') ?>`

to:

`<?= base_url('report') ?>`

No workflow or database changes are included.
