<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<div id="main-content">

    <!-- Hero Welcome Card -->
    <div style="background:linear-gradient(135deg, #1a6b4f, #0f4a35); border-radius:16px; padding:32px; margin-bottom:24px; position:relative; overflow:hidden; color:#fff;">
        <span style="display:inline-block; background:rgba(255,255,255,0.15); font-size:11px; font-weight:600; letter-spacing:0.5px; padding:4px 12px; border-radius:20px; margin-bottom:16px;">
            ARMS-BMS WORKSPACE
        </span>
        <h3 style="font-weight:700; margin-bottom:6px;">
            Good <?= (date('H') < 12 ? 'morning' : (date('H') < 18 ? 'afternoon' : 'evening')) ?>, <?= htmlspecialchars($this->session->userdata('username') ?? 'Admin') ?>.
        </h3>
        <p style="opacity:0.85; font-size:14px; margin-bottom:16px;">
            Here is a clear overview of your borrowing management workspace.
        </p>
        <div style="display:flex; align-items:center; gap:6px; font-size:13px;">
            <span style="width:8px; height:8px; border-radius:50%; background:#4ade80; display:inline-block;"></span>
            Last login: <strong>First login in this session</strong>
        </div>
    </div>

    <!-- Summary Cards -->
    <div style="font-size:12px; font-weight:600; color:#888; letter-spacing:0.5px; margin-bottom:6px;">LIVE SUMMARY</div>
    <h5 style="font-weight:700; margin-bottom:16px;">Records overview</h5>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <a href="<?= base_url('notifications') ?>" style="text-decoration:none; color:inherit;">
                <div style="background:#fff; border-radius:12px; padding:18px; border:1px solid #e3e6f0; border-top:3px solid #f6ad55; display:flex; justify-content:space-between; align-items:flex-start; height:100%;">
                    <div style="display:flex; gap:12px;">
                        <div style="width:38px; height:38px; border-radius:10px; background:#fef3e2; color:#dd6b20; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <div style="font-size:12px; color:#888;">Pending</div>
                            <div style="font-size:22px; font-weight:700;"><?= $summary['overdue_count'] ?></div>
                            <div style="font-size:11px; color:#aaa;">Overdue items</div>
                        </div>
                    </div>
                    <i class="fas fa-arrow-right" style="color:#ccc; margin-top:6px;"></i>
                </div>
            </a>
        </div>

        <div class="col-md-3 mb-3">
            <a href="<?= base_url('itemized') ?>" style="text-decoration:none; color:inherit;">
                <div style="background:#fff; border-radius:12px; padding:18px; border:1px solid #e3e6f0; border-top:3px solid #4299e1; display:flex; justify-content:space-between; align-items:flex-start; height:100%;">
                    <div style="display:flex; gap:12px;">
                        <div style="width:38px; height:38px; border-radius:10px; background:#e6f3fc; color:#2b6cb0; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-box"></i>
                        </div>
                        <div>
                            <div style="font-size:12px; color:#888;">Units</div>
                            <div style="font-size:22px; font-weight:700;"><?= $summary['total_units'] ?></div>
                            <div style="font-size:11px; color:#aaa;">Total records</div>
                        </div>
                    </div>
                    <i class="fas fa-arrow-right" style="color:#ccc; margin-top:6px;"></i>
                </div>
            </a>
        </div>

        <div class="col-md-3 mb-3">
            <a href="<?= base_url('user') ?>" style="text-decoration:none; color:inherit;">
                <div style="background:#fff; border-radius:12px; padding:18px; border:1px solid #e3e6f0; border-top:3px solid #9f7aea; display:flex; justify-content:space-between; align-items:flex-start; height:100%;">
                    <div style="display:flex; gap:12px;">
                        <div style="width:38px; height:38px; border-radius:10px; background:#f2eafc; color:#6b46c1; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <div style="font-size:12px; color:#888;">Borrowers</div>
                            <div style="font-size:22px; font-weight:700;"><?= $summary['total_borrowers'] ?></div>
                            <div style="font-size:11px; color:#aaa;">Registered</div>
                        </div>
                    </div>
                    <i class="fas fa-arrow-right" style="color:#ccc; margin-top:6px;"></i>
                </div>
            </a>
        </div>

        <div class="col-md-3 mb-3">
            <a href="<?= base_url('borrowing') ?>" style="text-decoration:none; color:inherit;">
                <div style="background:#fff; border-radius:12px; padding:18px; border:1px solid #e3e6f0; border-top:3px solid #48bb78; display:flex; justify-content:space-between; align-items:flex-start; height:100%;">
                    <div style="display:flex; gap:12px;">
                        <div style="width:38px; height:38px; border-radius:10px; background:#e6f9ee; color:#2f855a; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div>
                            <div style="font-size:12px; color:#888;">Borrowed</div>
                            <div style="font-size:22px; font-weight:700;"><?= $summary['borrowed_units'] ?></div>
                            <div style="font-size:11px; color:#aaa;">Currently out</div>
                        </div>
                    </div>
                    <i class="fas fa-arrow-right" style="color:#ccc; margin-top:6px;"></i>
                </div>
            </a>
        </div>
    </div>

    <!-- Charts -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div style="background:#fff; border-radius:12px; padding:20px; border:1px solid #e3e6f0;">
                <h6 style="font-weight:600; margin-bottom:15px;">Items by Category</h6>
                <canvas id="categoryChart" height="220"></canvas>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div style="background:#fff; border-radius:12px; padding:20px; border:1px solid #e3e6f0;">
                <h6 style="font-weight:600; margin-bottom:15px;">Borrowing Trend (Last 12 Months)</h6>
                <canvas id="trendChart" height="220"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div style="background:#fff; border-radius:12px; padding:20px; border:1px solid #e3e6f0;">
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