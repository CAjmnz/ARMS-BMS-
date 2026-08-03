<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<div id="main-content">

    <!-- Hero Welcome Card -->
    <div style="background:linear-gradient(135deg, #1a6b4f, #0f4a35); border-radius:16px; padding:32px; margin-bottom:24px; position:relative; overflow:hidden; color:#fff;">
        <span style="display:inline-block; background:rgba(255,255,255,0.15); font-size:11px; font-weight:600; letter-spacing:0.5px; padding:4px 12px; border-radius:20px; margin-bottom:16px;">
            ARMS-BMS WORKSPACE
        </span>
        <h3 style="font-weight:700; margin-bottom:6px;">
            <?php
            $username = $this->session->userdata('username');
            if (!$username) {
                $username = 'Admin';
            }
            ?>
            Good <?= (date('H') < 12 ? 'morning' : (date('H') < 18 ? 'afternoon' : 'evening')) ?>, <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>.
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
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <a href="<?= base_url('summary/items') ?>" style="text-decoration:none; color:inherit;">
                <div style="background:#fff; border-radius:12px; padding:18px; border:1px solid #e3e6f0; border-top:3px solid #f6ad55; display:flex; justify-content:space-between; align-items:flex-start; height:100%;">
                    <div style="display:flex; gap:12px;">
                        <div style="width:38px; height:38px; border-radius:10px; background:#fef3e2; color:#dd6b20; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-box"></i>
                        </div>
                        <div>
                            <div style="font-size:12px; color:#888;">Total Items</div>
                            <div style="font-size:22px; font-weight:700;"><?= $summary['total_item_types'] ?></div>
                            <div style="font-size:11px; color:#aaa;">View All</div>
                        </div>
                    </div>
                    <i class="fas fa-arrow-right" style="color:#ccc; margin-top:6px;"></i>
                </div>
            </a>
        </div>

        <div class="col-md-3 mb-3">
            <a href="<?= base_url('summary/units') ?>" style="text-decoration:none; color:inherit;">
                <div style="background:#fff; border-radius:12px; padding:18px; border:1px solid #e3e6f0; border-top:3px solid #4299e1; display:flex; justify-content:space-between; align-items:flex-start; height:100%;">
                    <div style="display:flex; gap:12px;">
                        <div style="width:38px; height:38px; border-radius:10px; background:#e6f3fc; color:#2b6cb0; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div>
                            <div style="font-size:12px; color:#888;">Total Units</div>
                            <div style="font-size:22px; font-weight:700;"><?= $summary['total_units'] ?></div>
                            <div style="font-size:11px; color:#aaa;">View All</div>
                        </div>
                    </div>
                    <i class="fas fa-arrow-right" style="color:#ccc; margin-top:6px;"></i>
                </div>
            </a>
        </div>

        <div class="col-md-3 mb-3">
            <a href="<?= base_url('summary/borrowed') ?>" style="text-decoration:none; color:inherit;">
                <div style="background:#fff; border-radius:12px; padding:18px; border:1px solid #e3e6f0; border-top:3px solid #48bb78; display:flex; justify-content:space-between; align-items:flex-start; height:100%;">
                    <div style="display:flex; gap:12px;">
                        <div style="width:38px; height:38px; border-radius:10px; background:#e6f9ee; color:#2f855a; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div>
                            <div style="font-size:12px; color:#888;">Currently Borrowed</div>
                            <div style="font-size:22px; font-weight:700;"><?= $summary['borrowed_units'] ?></div>
                            <div style="font-size:11px; color:#aaa;">View All</div>
                        </div>
                    </div>
                    <i class="fas fa-arrow-right" style="color:#ccc; margin-top:6px;"></i>
                </div>
            </a>
        </div>

        <div class="col-md-3 mb-3">
            <a href="<?= base_url('summary/overdue') ?>" style="text-decoration:none; color:inherit;">
                <div style="background:#fff; border-radius:12px; padding:18px; border:1px solid #e3e6f0; border-top:3px solid #e53e3e; display:flex; justify-content:space-between; align-items:flex-start; height:100%;">
                    <div style="display:flex; gap:12px;">
                        <div style="width:38px; height:38px; border-radius:10px; background:#fde8e8; color:#c53030; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <div style="font-size:12px; color:#888;">Overdue</div>
                            <div style="font-size:22px; font-weight:700;"><?= $summary['overdue_count'] ?></div>
                            <div style="font-size:11px; color:#aaa;">View All</div>
                        </div>
                    </div>
                    <i class="fas fa-arrow-right" style="color:#ccc; margin-top:6px;"></i>
                </div>
            </a>
        </div>
    </div>

    <!-- Charts -->
    <div class="row mb-4 dashboard-charts-row">
        <div class="col-md-6 mb-3">
            <div class="dashboard-chart-card">
                <h6 class="dashboard-card-heading">Items by Category</h6>
                <div class="dashboard-chart-box">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="dashboard-chart-card">
                <h6 class="dashboard-card-heading">Borrowing Trend (Last 12 Months)</h6>
                <div class="dashboard-chart-box">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card dashboard-chart-card mb-4">
        <div class="card-header">
            <div>
                <h5 class="mb-1">Most Borrowed Items</h5>
                <p class="text-muted mb-0">
                    Top five items based on borrowing history
                </p>
            </div>
        </div>

        <div class="card-body">
            <?php if (!empty($most_borrowed_items)): ?>
                <div class="chart-container">
                    <canvas id="mostBorrowedChart"></canvas>
                </div>
            <?php else: ?>
                <div class="text-center text-muted py-5">
                    No borrowing records available.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Due today and low-stock tables -->
    <div class="row dashboard-status-row mb-4">
        <div class="col-lg-7 mb-3 mb-lg-0">
            <div class="dashboard-table-card">
                <div class="dashboard-table-header">
                    <div>
                        <div class="dashboard-table-eyebrow">Borrowing schedule</div>
                        <h5 class="dashboard-table-title">Due Today</h5>
                    </div>
                    <a href="<?= base_url('notifications') ?>" class="dashboard-table-link">
                        View notifications <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table dashboard-status-table mb-0">
                        <thead>
                            <tr>
                                <th>Unit</th>
                                <th>Item</th>
                                <th>Borrower</th>
                                <th>Due time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($due_today)): ?>
                                <tr>
                                    <td colspan="4" class="dashboard-empty-state">
                                        No units are due today.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($due_today as $due): ?>
                                    <tr class="dashboard-clickable-row"
                                        tabindex="0"
                                        role="link"
                                        data-href="<?= base_url('notifications') ?>"
                                        aria-label="Open notifications for <?= htmlspecialchars($due->item_name, ENT_QUOTES, 'UTF-8') ?>">
                                        <td>
                                            <span class="unit-number-badge">
                                                #<?= htmlspecialchars($due->unit_no, ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($due->item_name, ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($due->borrower_name, ENT_QUOTES, 'UTF-8') ?></td>
                                        <td>
                                            <span class="due-time-text">
                                                <?= date('h:i A', strtotime($due->due_date)) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="dashboard-table-card">
                <div class="dashboard-table-header">
                    <div>
                        <div class="dashboard-table-eyebrow">Inventory warning</div>
                        <h5 class="dashboard-table-title">Low Stock Items</h5>
                    </div>
                    <a href="<?= base_url('itemized') ?>" class="dashboard-table-link">
                        View inventory <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table dashboard-status-table mb-0">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-center">Available</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($low_stock_items)): ?>
                                <tr>
                                    <td colspan="3" class="dashboard-empty-state">
                                        No low-stock items.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($low_stock_items as $stock): ?>
                                    <?php $available_units = (int) $stock->available_units; ?>
                                    <tr>
                                        <td><?= htmlspecialchars($stock->item_name, ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="text-center">
                                            <strong class="stock-count <?= $available_units <= 2 ? 'stock-critical' : 'stock-warning' ?>">
                                                <?= $available_units ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <?php if ($available_units <= 2): ?>
                                                <span class="stock-status stock-status-critical">Critical</span>
                                            <?php else: ?>
                                                <span class="stock-status stock-status-warning">Low</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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
                    <tr>
                        <td colspan="4" class="text-center text-muted">No recent activity.</td>
                    </tr>
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
                                echo isset($labels[$log->action_type])
                                    ? $labels[$log->action_type]
                                    : ucfirst($log->action_type);
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