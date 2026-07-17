<?php
$username        = $this->session->userdata('username')        ?? '';
$firstname       = $this->session->userdata('firstname')       ?? '';
$lastname        = $this->session->userdata('lastname')        ?? '';
$profile_picture = $this->session->userdata('profile_picture') ?? '';
$initials        = strtoupper(substr($firstname, 0, 1) . substr($lastname, 0, 1)) ?: 'U';
$page_label      = isset($page_label) ? $page_label : 'Dashboard';
?>

<div id="topbar">

    <!-- Left — Toggle + Breadcrumb -->
    <div style="display:flex; align-items:center; gap:12px;">
        <button class="topbar-toggle" id="sidebarToggle" title="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </button>
        <div style="font-size:15px; font-weight:600; color:#1B3A6B;">
            A.R.M.S &rsaquo; <?= htmlspecialchars($page_label) ?>
        </div>
    </div>

    <!-- Right — Clock + Avatar -->
    <div class="topbar-right">

        <!-- Notification Bell-->
         <a href="<?=  base_url('notifications') ?>" id="notifBell"
         style="position:relative; margin-right:16px; color:#1B3A6B; font-size:18px;">
        <i class="fas fa-bell"></i> <span id="notifBadge" style="
            display:none;
            position:absolute;
            top:-6px;
            right:-8px;
            background:#e74a3b;
            color:#fff;
            font-size:10px;
            font-weight:700;
            min-width:16px;
            height:16px;
            border-radius:8px;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:0 4px;
        ">0</span>
        </a>

        <!-- Clock -->
        <span id="topbarClock"></span>
        
        <!-- Avatar Dropdown -->
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none"
               id="avatarDropdown" data-toggle="dropdown"
               aria-haspopup="true" aria-expanded="false">

                <?php if (!empty($profile_picture) && file_exists(FCPATH . $profile_picture)): ?>
                    <img src="<?= base_url($profile_picture) ?>?v=<?= time() ?>"
                         style="width:38px;height:38px;border-radius:50%;
                                object-fit:cover;border:2px solid #2563B8;">
                <?php else: ?>
                    <div class="topbar-avatar"><?= $initials ?></div>
                <?php endif; ?>

                <span style="margin-left:8px;font-size:14px;color:#1B3A6B;font-weight:500;">
                    <?= htmlspecialchars(trim($firstname . ' ' . $lastname)) ?>
                </span>
                <i class="fas fa-chevron-down ml-2" style="font-size:11px;color:#888;"></i>
            </a>

            <!-- Dropdown Menu -->
            <div class="dropdown-menu dropdown-menu-right shadow"
                 aria-labelledby="avatarDropdown"
                 style="min-width:200px;border:none;border-radius:8px;padding:8px 0;">

                <!-- User Info -->
                <div style="padding:10px 16px;border-bottom:1px solid #f0f0f0;margin-bottom:4px;">
                    <div style="font-weight:600;font-size:14px;color:#1B3A6B;">
                        <?= htmlspecialchars(trim($firstname . ' ' . $lastname)) ?>
                    </div>
                    <div style="font-size:12px;color:#888;">
                        <?= htmlspecialchars($username) ?>
                    </div>
                </div>

                <a class="dropdown-item d-flex align-items-center"
                   href="<?= base_url('myprofile') ?>"
                   style="padding:8px 16px;font-size:14px;gap:10px;">
                    <i class="fas fa-user-circle" style="color:#2563B8;width:16px;"></i>
                    My Profile
                </a>

                <a class="dropdown-item d-flex align-items-center"
                   href="<?= base_url('settings') ?>"
                   style="padding:8px 16px;font-size:14px;gap:10px;">
                    <i class="fas fa-cog" style="color:#6c757d;width:16px;"></i>
                    Settings
                </a>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item d-flex align-items-center"
                   href="<?= base_url('privacy') ?>"
                   style="padding:8px 16px;font-size:14px;gap:10px;">
                    <i class="fas fa-shield-alt" style="color:#6c757d;width:16px;"></i>
                    Privacy Policy
                </a>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item d-flex align-items-center"
                   href="<?= base_url('dashboard/logout') ?>"
                   style="padding:8px 16px;font-size:14px;gap:10px;color:#e74a3b;">
                    <i class="fas fa-sign-out-alt" style="color:#e74a3b;width:16px;"></i>
                    Sign out
                </a>

            </div>
        </div>

    </div>
</div>