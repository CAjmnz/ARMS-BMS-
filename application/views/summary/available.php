<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<div id="main-content">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h4 style="margin:0; font-weight:700; color:#333;">Summary &rsaquo; Available Items</h4>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Metric Card -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div style="background:#fff; border-radius:12px; padding:18px; border:1px solid #e3e6f0; border-top:3px solid #48bb78;">
                <div style="font-size:12px; color:#888;">Total Available Items</div>
                <div style="font-size:28px; font-weight:700;"><?= $total_available ?></div>
            </div>
        </div>
    </div>

    <!-- Borrowed Item Table (Item Catalog) -->
    <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0; margin-bottom:20px;">
        <h6 style="font-weight:600; margin-bottom:15px;">Borrowed Item Table</h6>
        <table class="table table-bordered table-hover table-sm">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th>Item Id</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Available</th>
                    <th>Location</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr><td colspan="7" class="text-center text-muted">No items found.</td></tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= $item->id ?></td>
                            <td><?= htmlspecialchars($item->item_name) ?></td>
                            <td><?= htmlspecialchars($item->category) ?></td>
                            <td><?= $item->quantity ?></td>
                            <td><?= $item->available_quantity ?></td>
                            <td><?= htmlspecialchars($item->location ?? '-') ?></td>
                            <td>
                                <?php
                                $badge_class = $item->status === 'available' ? 'badge-success'
                                    : ($item->status === 'in-use' ? 'badge-warning' : 'badge-danger');
                                ?>
                                <span class="badge <?= $badge_class ?>"><?= ucfirst($item->status) ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="row">
        <!-- Available Items by Category -->
        <div class="col-md-6 mb-3">
            <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0; height:100%;">
                <h6 style="font-weight:600; margin-bottom:15px;">Available Items by Category</h6>
                <table class="table table-sm">
                    <thead>
                        <tr><th>Categories</th><th>Available Items</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($category_availability)): ?>
                            <tr><td colspan="2" class="text-center text-muted">No data.</td></tr>
                        <?php else: ?>
                            <?php foreach ($category_availability as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row->category) ?></td>
                                    <td><strong><?= (int) $row->total_available ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Status Summary -->
        <div class="col-md-6 mb-3">
            <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0; height:100%;">
                <h6 style="font-weight:600; margin-bottom:15px;">Status Summary</h6>
                <table class="table table-sm">
                    <thead>
                        <tr><th>Status</th><th>Number of Items</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($status_breakdown)): ?>
                            <tr><td colspan="2" class="text-center text-muted">No data.</td></tr>
                        <?php else: ?>
                            <?php foreach ($status_breakdown as $row): ?>
                                <tr>
                                    <td><?= ucfirst($row->status) ?></td>
                                    <td><strong><?= $row->total ?></strong></td>
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