<?php
$username        = $this->session->userdata('username') ?? '';
$firstname       = $this->session->userdata('firstname') ?? '';
$lastname        = $this->session->userdata('lastname') ?? '';
$profile_picture = $this->session->userdata('profile_picture') ?? '';

$full_name = trim($firstname . ' ' . $lastname);
$full_name = $full_name !== '' ? $full_name : $username;

$initials = strtoupper(
    substr($firstname, 0, 1) .
    substr($lastname, 0, 1)
);

$initials = $initials !== '' ? $initials : 'U';

$page_label = isset($page_label) && $page_label !== ''
    ? $page_label
    : 'Dashboard';
?>

<div id="topbar">

    <!-- Left Area -->
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
                Admin Control Panel
            </span>

            <h1 class="topbar-page-title">
                <?= htmlspecialchars($page_label, ENT_QUOTES, 'UTF-8') ?>
            </h1>
        </div>

    </div>

    <!-- Right Area -->
    <div class="topbar-right">

        <!-- Optional Action Button -->

        <!-- Notifications -->
        <a
            href="<?= base_url('notifications') ?>"
            id="notifBell"
            class="topbar-icon-button"
            title="Notifications"
            aria-label="Notifications"
        >
            <i class="fas fa-bell"></i>

            <span id="notifBadge" class="notification-badge">
                0
            </span>
        </a>

        <!-- Clock -->
        <span id="topbarClock"></span>

        <!-- User Dropdown -->
        <div class="dropdown topbar-profile-dropdown">

            <a
                href="#"
                class="topbar-profile"
                id="avatarDropdown"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
            >

                <?php if (
                    !empty($profile_picture) &&
                    file_exists(FCPATH . $profile_picture)
                ): ?>

                    <img
                        src="<?= base_url($profile_picture) ?>?v=<?= time() ?>"
                        alt="<?= htmlspecialchars($full_name, ENT_QUOTES, 'UTF-8') ?>"
                        class="topbar-avatar-image"
                    >

                <?php else: ?>

                    <div class="topbar-avatar">
                        <?= htmlspecialchars($initials, ENT_QUOTES, 'UTF-8') ?>
                    </div>

                <?php endif; ?>

                <div class="topbar-profile-text">
                    <span class="topbar-profile-name">
                        <?= htmlspecialchars($full_name, ENT_QUOTES, 'UTF-8') ?>
                    </span>

                    <span class="topbar-profile-role">
                        Administrator
                    </span>
                </div>

                <i class="fas fa-chevron-down topbar-profile-chevron"></i>

            </a>

            <!-- Dropdown Menu -->
            <div
                class="dropdown-menu dropdown-menu-right topbar-dropdown-menu"
                aria-labelledby="avatarDropdown"
            >

                <div class="topbar-dropdown-header">

                    <div class="topbar-dropdown-name">
                        <?= htmlspecialchars($full_name, ENT_QUOTES, 'UTF-8') ?>
                    </div>

                    <div class="topbar-dropdown-username">
                        <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>
                    </div>

                </div>

                <a
                    class="dropdown-item topbar-dropdown-item"
                    href="<?= base_url('myprofile') ?>"
                >
                    <i class="fas fa-user-circle"></i>
                    <span>My Profile</span>
                </a>

                <a
                    class="dropdown-item topbar-dropdown-item"
                    href="<?= base_url('settings') ?>"
                >
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>

                <div class="dropdown-divider"></div>

                <a
                    class="dropdown-item topbar-dropdown-item"
                    href="<?= base_url('about_us') ?>"
                >
                    <i class="fas fa-info-circle"></i>
                    <span>About Us</span>
                </a>

                <a
                    class="dropdown-item topbar-dropdown-item"
                    href="<?= base_url('privacy') ?>"
                >
                    <i class="fas fa-shield-alt"></i>
                    <span>Privacy Policy</span>
                </a>

                <div class="dropdown-divider"></div>

                <a
                    class="dropdown-item topbar-dropdown-item topbar-signout"
                    href="<?= base_url('auth/logout') ?>"
                >
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Sign out</span>
                </a>

            </div>

        </div>

    </div>

</div>