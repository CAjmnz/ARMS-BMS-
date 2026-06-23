<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<div id="main-content">

    <!-- Welcome Banner -->
    <div style="background:#4e73df;color:#fff;padding:20px;border-radius:8px;margin-bottom:20px;">
        <h4>Welcome back, <?= htmlspecialchars($firstname . ' ' . $lastname) ?>!</h4>
        <p style="margin:0;"><?= htmlspecialchars($email) ?> &nbsp;|&nbsp; <?= ucfirst($role) ?></p>
    </div>

    <!-- Simple Stat Cards -->
    <div class="row">
        <div class="col-md-3">
            <div style="background:#fff;border:1px solid #e3e6f0;border-radius:8px;padding:20px;text-align:center;">
                <div style="font-size:13px;color:#858796;">Total Users</div>
                <div style="font-size:28px;font-weight:700;color:#4e73df;"><?= $stats->total ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div style="background:#fff;border:1px solid #e3e6f0;border-radius:8px;padding:20px;text-align:center;">
                <div style="font-size:13px;color:#858796;">Active</div>
                <div style="font-size:28px;font-weight:700;color:#1cc88a;"><?= $stats->active ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div style="background:#fff;border:1px solid #e3e6f0;border-radius:8px;padding:20px;text-align:center;">
                <div style="font-size:13px;color:#858796;">Inactive</div>
                <div style="font-size:28px;font-weight:700;color:#e74a3b;"><?= $stats->inactive ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div style="background:#fff;border:1px solid #e3e6f0;border-radius:8px;padding:20px;text-align:center;">
                <div style="font-size:13px;color:#858796;">Admins</div>
                <div style="font-size:28px;font-weight:700;color:#f6c23e;"><?= $stats->admins ?></div>
            </div>
        </div>
    </div>

</div>

<?php $this->load->view('templates/footer'); ?>