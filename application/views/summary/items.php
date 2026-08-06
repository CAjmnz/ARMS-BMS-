<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<div id="main-content">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h4 style="margin:0; font-weight:700; color:#333;">Summary &rsaquo; Total Items</h4>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Metric Card -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div style="background:#fff; border-radius:12px; padding:18px; border:1px solid #e3e6f0; border-top:3px solid #f6ad55;">
                <div style="font-size:12px; color:#888;">Total Items</div>
                <div style="font-size:28px; font-weight:700;"><?= $total_items ?></div>
            </div>
        </div>
    </div>

    <!-- Status Breakdown mini-table -->
    <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0; margin-bottom:20px;">
        <h6 style="font-weight:600; margin-bottom:15px;">Status Breakdown</h6>
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($status_breakdown)): ?>
                    <tr><td colspan="2" class="text-center text-muted">No units found.</td></tr>
                <?php else: ?>
                    <?php foreach ($status_breakdown as $row): ?>
                        <tr>
                            <td><?= ucfirst(str_replace('_', ' ', $row->status)) ?></td>
                            <td><strong><?= $row->total ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Inventory Summary Table -->
    <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0;">
        <h6 style="font-weight:600; margin-bottom:15px;">Inventory Summary</h6>
        <table class="table table-bordered table-hover" id="summaryItemsTable" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Serial Number</th>
                    <th>Total Qty</th>
                    <th>Available Qty</th>
                    <th>Borrowed Qty</th>
                    <th>Status</th>
                    <th>Location</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<?php $this->load->view('templates/footer'); ?>