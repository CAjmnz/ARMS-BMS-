<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<div id="main-content">

    <h4 style="font-weight:700; color:#333; margin-bottom:20px;">Notifications</h4>

    <!-- Overdue Section -->
    <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0; margin-bottom:20px;">
        <h6 style="font-weight:600; color:#e74a3b; margin-bottom:15px;">
            <i class="fas fa-exclamation-circle"></i> Overdue (<?= count($overdue) ?>)
        </h6>
        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    <th>Borrower</th>
                    <th>Item</th>
                    <th>Due Date</th>
                    <th>Days Overdue</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($overdue)): ?>
                    <tr><td colspan="4" class="text-center text-muted">No overdue items.</td></tr>
                <?php else: ?>
                    <?php foreach ($overdue as $row): ?>
                        <?php $days = floor((time() - strtotime($row->due_date)) / 86400); ?>
                        <tr>
                            <td><?= htmlspecialchars($row->borrower_name) ?> (<?= htmlspecialchars($row->id_number) ?>)</td>
                            <td><?= htmlspecialchars($row->item_name) ?> #<?= $row->unit_no ?></td>
                            <td><?= date('M d, Y h:i A', strtotime($row->due_date)) ?></td>
                            <td><span class="badge badge-danger"><?= $days ?> day(s)</span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Due Soon Section -->
    <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0;">
        <h6 style="font-weight:600; color:#f6c23e; margin-bottom:15px;">
            <i class="fas fa-clock"></i> Due Within 24 Hours (<?= count($due_soon) ?>)
        </h6>
        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    <th>Borrower</th>
                    <th>Item</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($due_soon)): ?>
                    <tr><td colspan="3" class="text-center text-muted">No items due soon.</td></tr>
                <?php else: ?>
                    <?php foreach ($due_soon as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row->borrower_name) ?> (<?= htmlspecialchars($row->id_number) ?>)</td>
                            <td><?= htmlspecialchars($row->item_name) ?> #<?= $row->unit_no ?></td>
                            <td><?= date('M d, Y h:i A', strtotime($row->due_date)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php $this->load->view('templates/footer'); ?>