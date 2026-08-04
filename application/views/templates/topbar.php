<?php
$username        = (string) $this->session->userdata('username');
$employee_id     = (string) $this->session->userdata('employee_id');
$firstname       = (string) $this->session->userdata('firstname');
$lastname        = (string) $this->session->userdata('lastname');
$session_name    = (string) $this->session->userdata('full_name');
$role            = (string) $this->session->userdata('role');
$account_source  = (string) $this->session->userdata('account_source');
$profile_picture = (string) $this->session->userdata('profile_picture');

$full_name = trim($session_name);
if ($full_name === '') {
    $full_name = trim($firstname . ' ' . $lastname);
}
if ($full_name === '') {
    $full_name = $username;
}

$name_parts = preg_split('/[\s,]+/', $full_name, -1, PREG_SPLIT_NO_EMPTY);
$initials = '';
foreach (array_slice($name_parts, 0, 2) as $name_part) {
    $initials .= strtoupper(substr($name_part, 0, 1));
}
$initials = $initials !== '' ? $initials : 'U';

$role_label = in_array($role, array('Super-admin', 'Admin', 'User'), TRUE)
    ? $role
    : 'User';

$identity_label = $employee_id !== '' ? $employee_id : $username;
$page_label = isset($page_label) && $page_label !== ''
    ? $page_label
    : 'Dashboard';

$avatar_url = '';
if ($profile_picture !== '') {
    if ($account_source === 'system_users') {
        $avatar_url = base_url('user/photo_proxy?path=' . rawurlencode($profile_picture));
    } elseif (file_exists(FCPATH . ltrim($profile_picture, '/'))) {
        $avatar_url = base_url(ltrim($profile_picture, '/')) . '?v=' . time();
    }
}
?>

<div id="topbar">
    <div class="topbar-left">
        <button
            type="button"
            class="topbar-toggle"
            id="sidebarToggle"
            title="Toggle sidebar"
            aria-label="Toggle sidebar"
        >
            <i class="fas fa-bars"></i>
        </button>

        <div class="topbar-page-info">
            <span class="topbar-eyebrow">
                <?= $role_label === 'User' ? 'Borrowing Management System' : 'Admin Control Panel' ?>
            </span>
            <h1 class="topbar-page-title">
                <?= html_escape($page_label) ?>
            </h1>
        </div>
    </div>

    <div class="topbar-right">
        <a
            href="<?= base_url('notifications') ?>"
            id="notifBell"
            class="topbar-icon-button"
            title="Notifications"
            aria-label="Notifications"
        >
            <i class="fas fa-bell"></i>
            <span id="notifBadge" class="notification-badge">0</span>
        </a>

        <span id="topbarClock"></span>

        <div class="dropdown topbar-profile-dropdown">
            <a
                href="#"
                class="topbar-profile"
                id="avatarDropdown"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
            >
                <?php if ($avatar_url !== ''): ?>
                    <img
                        src="<?= html_escape($avatar_url) ?>"
                        alt="<?= html_escape($full_name) ?>"
                        class="topbar-avatar-image"
                    >
                <?php else: ?>
                    <div class="topbar-avatar"><?= html_escape($initials) ?></div>
                <?php endif; ?>

                <div class="topbar-profile-text">
                    <span class="topbar-profile-name"><?= html_escape($full_name) ?></span>
                    <span class="topbar-profile-role"><?= html_escape($role_label) ?></span>
                </div>

                <i class="fas fa-chevron-down topbar-profile-chevron"></i>
            </a>

            <div
                class="dropdown-menu dropdown-menu-right topbar-dropdown-menu"
                aria-labelledby="avatarDropdown"
            >
                <div class="topbar-dropdown-header">
                    <div class="topbar-dropdown-name"><?= html_escape($full_name) ?></div>
                    <div class="topbar-dropdown-username"><?= html_escape($identity_label) ?></div>
                </div>

                <a class="dropdown-item topbar-dropdown-item" href="<?= base_url('myprofile') ?>">
                    <i class="fas fa-user-circle"></i>
                    <span>My Profile</span>
                </a>

                <a class="dropdown-item topbar-dropdown-item" href="<?= base_url('settings') ?>">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item topbar-dropdown-item" href="<?= base_url('about_us') ?>">
                    <i class="fas fa-info-circle"></i>
                    <span>About Us</span>
                </a>

                <a class="dropdown-item topbar-dropdown-item" href="<?= base_url('privacy') ?>">
                    <i class="fas fa-shield-alt"></i>
                    <span>Privacy Policy</span>
                </a>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item topbar-dropdown-item topbar-signout" href="<?= site_url('auth/logout') ?>">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Sign out</span>
                </a>
            </div>
        </div>
    </div>
</div>
