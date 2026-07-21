<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<div id="main-content">

    <h4 style="font-weight:700; color:#333; margin-bottom:20px;">Dashboard</h4>
    

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0; border-left:12px solid #4e73df;">
                <div style="font-size:13px; color:#888;">Total Item Types</div>
                <div style="font-size:28px; font-weight:700;"><?= $summary['total_item_types'] ?></div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0; border-left:12px solid #1cc88a;">
                <div style="font-size:13px; color:#888;">Available Units</div>
                <div style="font-size:28px; font-weight:700;"><?= $summary['available_units'] ?></div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0; border-left:12px solid #f6c23e;">
                <div style="font-size:13px; color:#888;">Currently Borrowed</div>
                <div style="font-size:28px; font-weight:700;"><?= $summary['borrowed_units'] ?></div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0; border-left:12px solid #e74a3b;">
                <div style="font-size:13px; color:#888;">Overdue</div>
                <div style="font-size:28px; font-weight:700;"><?= $summary['overdue_count'] ?></div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0;">
                <div style="font-size:13px; color:#888;">Total Units</div>
                <div style="font-size:22px; font-weight:700;"><?= $summary['total_units'] ?></div>
                <a href="itemized">units</a>
                
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0;">
                <div style="font-size:13px; color:#888;">Returned Today</div>
                <div style="font-size:22px; font-weight:700;"><?= $summary['returned_today'] ?></div>
                <a href="returns">Return</a>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0;">
                <div style="font-size:13px; color:#888;">Active Borrowers</div>
                <div style="font-size:22px; font-weight:700;"><?= $summary['total_borrowers'] ?></div>
                <a href="borrowing">borrowers</a>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0;">
                <h6 style="font-weight:600; margin-bottom:15px;">Items by Category</h6>
                <canvas id="categoryChart" height="220"></canvas>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0;">
                <h6 style="font-weight:600; margin-bottom:15px;">Borrowing Trend (Last 7 Days)</h6>
                <canvas id="trendChart" height="220"></canvas>
                
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0;">
        <h6 style="font-weight:600; margin-bottom:15px;">Recent Activity</h6>
        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Action</th>
                    <th>Borrower</th>
                    <th>Item</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($activity)): ?>
                    <tr><td colspan="4" class="text-center text-muted">No recent activity.</td></tr>
                <?php else: ?>
                    <?php foreach ($activity as $log): ?>
                        <tr>
                            <td><?= date('M d, Y h:i A', strtotime($log->action_date)) ?></td>
                            <td>
                                <?php
                                $labels = [
                                    'borrowed' => '<span class="badge badge-warning">Borrowed</span>',
                                    'returned' => '<span class="badge badge-primary">Returned</span>',
                                    'damaged'  => '<span class="badge badge-danger">Damaged</span>',
                                    'lost'     => '<span class="badge badge-dark">Lost</span>',
                                ];
                                echo $labels[$log->action_type] ?? ucfirst($log->action_type);
                                ?>
                            </td>
                            <td><?= htmlspecialchars($log->borrower_name) ?></td>
                            <td><?= htmlspecialchars($log->item_name) ?> #<?= $log->unit_no ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php $this->load->view('templates/footer'); ?>