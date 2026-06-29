
<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<link rel="stylesheet" href="<?= base_url('assets/css/users.css') ?>">

<!-- Chart data -->
<input type="hidden" id="chart_status_data" value='<?= $chart_status_data ?>'>
<input type="hidden" id="chart_role_data"   value='<?= $chart_role_data ?>'>
<input type="hidden" id="chart_log_labels"  value='<?= $chart_log_labels ?>'>
<input type="hidden" id="chart_log_counts"  value='<?= $chart_log_counts ?>'>
<input type="hidden" id="chart_birth_data"  value='<?= $chart_birth_data ?>'>

<div id="main-content">

    <?php if (!empty($flash_error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash_error) ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($flash_success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash_success) ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div class="wb-text">
            <h2>
                Welcome back,
                <?= htmlspecialchars($username) ?>!
            </h2>
        </div>

        <!-- Avatar — photo or initials -->
        <div class="wb-avatar">
            <?php
                $pp      = $profile_picture ?? null;
                $pp_path = $pp ? FCPATH . $pp : null;
            ?>
            <?php if (!empty($pp) && file_exists($pp_path)): ?>
                <img src="<?= base_url($pp) ?>?v=<?= time() ?>"
                     alt="Avatar"
                     style="width:60px;height:60px;border-radius:50%;
                            object-fit:cover;border:3px solid #fff;">
            <?php else: ?>
                <?= strtoupper(substr($username, 0, 1)) ?>
            <?php endif; ?>
        </div>
    </div>

    

<!-- Page JS -->
<script src="<?= base_url('assets/js/modules/dashboard.main.js') ?>"></script>

<?php $this->load->view('templates/footer'); ?>

