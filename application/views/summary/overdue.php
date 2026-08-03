<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<div id="main-content">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h4 style="margin:0; font-weight:700; color:#333;">Summary &rsaquo; Overdue</h4>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Metric Card -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div style="background:#fff; border-radius:12px; padding:18px; border:1px solid #e3e6f0; border-top:3px solid #e53e3e;">
                <div style="font-size:12px; color:#888;">Total Overdue</div>
                <div style="font-size:28px; font-weight:700;"><?= $total_overdue ?></div>
            </div>
        </div>
    </div>

    <!-- Overdue Items Table -->
    <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0; margin-bottom:20px;">
        <h6 style="font-weight:600; margin-bottom:15px;">Overdue Items Table</h6>
        <table class="table table-bordered table-hover table-sm">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th>Borrower Id</th>
                    <th>Borrower Name</th>
                    <th>Item Name</th>
                    <th>Borrowed Date</th>
                    <th>Due Date</th>
                    <th>Days Overdue</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($overdue_list)): ?>
                    <tr><td colspan="6" class="text-center text-muted">No overdue items. 🎉</td></tr>
                <?php else: ?>
                    <?php foreach ($overdue_list as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row->borrower_employee_id ?? '-') ?></td>
                            <td><?= htmlspecialchars($row->borrower_name ?? '-') ?></td>
                            <td><?= htmlspecialchars($row->item_name) ?> #<?= $row->unit_no ?></td>
                            <td><?= date('M d, Y h:i A', strtotime($row->date_released)) ?></td>
                            <td><?= date('M d, Y h:i A', strtotime($row->due_date)) ?></td>
                            <td><span class="badge badge-danger"><?= $row->days_overdue ?> day(s)</span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="row">
        <!-- Days Late Summary -->
        <div class="col-md-6 mb-3">
            <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0; height:100%;">
                <h6 style="font-weight:600; margin-bottom:15px;">Days Late Summary</h6>
                <table class="table table-sm">
                    <thead>
                        <tr><th>Status</th><th>Count</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($days_buckets as $label => $count): ?>
                            <tr>
                                <td><?= $label ?></td>
                                <td><strong><?= $count ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Overdue by Category -->
        <div class="col-md-6 mb-3">
            <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0; height:100%;">
                <h6 style="font-weight:600; margin-bottom:15px;">Overdue by Category</h6>
                <table class="table table-sm">
                    <thead>
                        <tr><th>Category</th><th>Overdue Count</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($by_category)): ?>
                            <tr><td colspan="2" class="text-center text-muted">No data.</td></tr>
                        <?php else: ?>
                            <?php foreach ($by_category as $cat => $count): ?>
                                <tr>
                                    <td><?= htmlspecialchars($cat) ?></td>
                                    <td><strong><?= $count ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('templates/footer'); ?>