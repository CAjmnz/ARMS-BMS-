<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<div id="main-content">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h4 style="margin:0; font-weight:700; color:#333;">Summary &rsaquo; Currently Borrowed</h4>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Metric Card -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div style="background:#fff; border-radius:12px; padding:18px; border:1px solid #e3e6f0; border-top:3px solid #48bb78;">
                <div style="font-size:12px; color:#888;">Total Borrowed</div>
                <div style="font-size:28px; font-weight:700;"><?= $total_borrowed ?></div>
            </div>
        </div>
    </div>

    <!-- Borrowed Item Table -->
    <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0; margin-bottom:20px;">
        <h6 style="font-weight:600; margin-bottom:15px;">Borrowed Item Table</h6>
        <table class="table table-bordered table-hover" id="summaryBorrowedTable">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th>#</th>
                    <th>Borrower's Id</th>
                    <th>Borrower's name</th>
                    <th>Position</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Condition Before Borrowing</th>
                    <th>Borrowed Date</th>
                    <th>Due date</th>
                    <th>Borrowing status</th>
                    <th>Released by</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Status Breakdown mini-table -->
    <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0;">
        <h6 style="font-weight:600; margin-bottom:15px;">Status Summary</h6>
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Number of Items</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($status_breakdown)): ?>
                    <tr><td colspan="2" class="text-center text-muted">No records found.</td></tr>
                <?php else: ?>
                    <?php foreach ($status_breakdown as $row): ?>
                        <tr>
                            <td><?= ucfirst($row->item_status) ?></td>
                            <td><strong><?= $row->total ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $this->load->view('templates/footer'); ?>