<?php
$username = $this->session->userdata('username') ?? '';
$page_label = isset($page_label) ? $page_label : 'Dashboard';
?>

<div id="topbar">
    <div style="font-size:15px;font-weight:600;color:#333;">
        ARMS-BMS &rsaquo; <?= htmlspecialchars($page_label) ?>
    </div>
    <div class="topbar-user">
        <i class="fas fa-user-circle"></i>
        <?= htmlspecialchars(trim($username)) ?>
    </div>
</div>