<?php
//userdata
$username = $this->session->userdata('username') ?? '';
$profile_picture = $this->session->userdata('profile_picture') ?? '';
$intials = strtoupper(substr($username,0,1))?:'U';
$page_label = isset($page_label) ? $page_label : 'Dashboard';
?>


<div id="topbar">
    <div style="font-size:20px;font-weight:600;color:#333;">
    <button class="topbar-toggle" id="sidebarToggle" title="Toggle sidebar">
        <i class="fas fa-bars"></i>
    </button>
        A.R.M.S - Borrower's Monitoring System &rsaquo; <?= htmlspecialchars($page_label) ?>
    </div>

    <span id="topbarClock"></span>
<!--AVATAR DROPDOWN-->
<?php
$username        = $this->session->userdata('username') ?? '';
$firstname       = $this->session->userdata('firstname') ?? '';
$lastname        = $this->session->userdata('lastname') ?? '';
$profile_picture = $this->session->userdata('profile_picture') ?? '';
$initials        = strtoupper(substr($firstname, 0, 1) . substr($lastname, 0, 1)) ?: 'U';
$page_label      = isset($page_label) ? $page_label : 'Dashboard';
?>

<div id="topbar">
    <div style="font-size:15px;font-weight:600;color:#333;">
         <?= htmlspecialchars($username) ?>
    </div>

    <!-- Avatar Dropdown -->
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-decoration-none"
           id="avatarDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

            <!-- Avatar -->
            <?php if (!empty($profile_picture) && file_exists(FCPATH . $profile_picture)): ?>
                <img src="<?= base_url($profile_picture) ?>?v=<?= time() ?>"
                     style="width:38px;height:38px;border-radius:50%;
                            object-fit:cover;border:2px solid #dee2e6;">
            <?php else: ?>
                <div style="width:38px;height:38px;border-radius:50%;
                            background:#4e73df;color:#fff;font-weight:700;
                            display:flex;align-items:center;justify-content:center;
                            font-size:14px;border:2px solid #dee2e6;">
                    <?= $initials ?>
                </div>
            <?php endif; ?>

            <!-- Name -->
            <span style="margin-left:8px;font-size:14px;color:#333;font-weight:500;">
                <?= htmlspecialchars(trim($firstname . ' ' . $lastname)) ?>
            </span>
            <i class="fas fa-chevron-down ml-2" style="font-size:11px;color:#888;"></i>
        </a>

        <!-- Dropdown Menu -->
        <div class="dropdown-menu dropdown-menu-right shadow"
             aria-labelledby="avatarDropdown"
             style="min-width:200px;border:none;border-radius:8px;padding:8px 0;">

            <!-- User Info Header -->
            <div style="padding:10px 16px;border-bottom:1px solid #f0f0f0;margin-bottom:4px;">
                <div style="font-weight:600;font-size:14px;color:#333;">
                    <?= htmlspecialchars(trim($firstname . ' ' . $lastname)) ?>
                </div>
                <div style="font-size:12px;color:#888;">
                    <?= htmlspecialchars($username) ?>
                </div>
            </div>

            <!-- My Profile -->
            <a class="dropdown-item d-flex align-items-center gap-2"
               href="<?= base_url('myprofile') ?>"
               style="padding:8px 16px;font-size:14px;">
                <i class="fas fa-user-circle" style="width:16px;color:#4e73df;"></i>
                <span>My Profile</span>
            </a>

            <!-- Settings -->
            <a class="dropdown-item d-flex align-items-center gap-2"
               href="<?= base_url('settings') ?>"
               style="padding:8px 16px;font-size:14px;">
                <i class="fas fa-cog" style="width:16px;color:#6c757d;"></i>
                <span>Settings</span>
            </a>

            <div class="dropdown-divider"></div>

            <!-- Privacy Policy -->
            <a class="dropdown-item d-flex align-items-center gap-2"
               href="<?= base_url('privacy') ?>"
               style="padding:8px 16px;font-size:14px;">
                <i class="fas fa-shield-alt" style="width:16px;color:#6c757d;"></i>
                <span>Privacy Policy</span>
            </a>

            <div class="dropdown-divider"></div>

            <!-- Logout -->
            <a class="dropdown-item d-flex align-items-center gap-2"
               href="<?= base_url('dashboard/logout') ?>"
               style="padding:8px 16px;font-size:14px;color:#e74a3b;">
                <i class="fas fa-sign-out-alt" style="width:16px;color:#e74a3b;"></i>
                <span>Sign out</span>
            </a>

        </div>
    </div>
</div>
</div>