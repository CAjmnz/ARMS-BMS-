<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<div id="main-content">

    <h4 style="font-weight:700; color:#333; margin-bottom:4px;">Notifications</h4>
    <div style="color:#888; font-size:13px; margin-bottom:20px;">Dashboard &rsaquo; Notifications</div>

    <!-- Filter Pills -->
    <div style="display:flex; gap:10px; margin-bottom:24px; flex-wrap:wrap;">
        <button class="notifPill active" data-filter="all">All</button>
        <button class="notifPill" data-filter="overdue">Overdue</button>
        <button class="notifPill" data-filter="due_soon">Due Soon</button>
    </div>

    <!-- Notification Cards -->
    <div id="notifList">

        <?php foreach ($overdue as $row): ?>
            <?php $days = floor((time() - strtotime($row->due_date)) / 86400); ?>
            <div class="notifCard" data-type="overdue" style="border-left:4px solid #e74a3b;">
                <div>
                    <div class="notifTitle">Overdue</div>
                    <div class="notifSub"><?= htmlspecialchars($row->borrower_name) ?> (<?= htmlspecialchars($row->id_number) ?>)</div>
                    <div class="notifSub"><?= htmlspecialchars($row->item_name) ?> #<?= $row->unit_no ?></div>
                    <div class="notifDate">Due: <?= date('M d, Y h:i A', strtotime($row->due_date)) ?></div>
                </div>
                <div class="notifValue" style="color:#e74a3b;">
                    <?= $days ?> day<?= $days != 1 ? 's' : '' ?> late
                </div>
            </div>
        <?php endforeach; ?>

        <?php foreach ($due_soon as $row): ?>
            <div class="notifCard" data-type="due_soon" style="border-left:4px solid #f6c23e;">
                <div>
                    <div class="notifTitle">Due Soon</div>
                    <div class="notifSub"><?= htmlspecialchars($row->borrower_name) ?> (<?= htmlspecialchars($row->id_number) ?>)</div>
                    <div class="notifSub"><?= htmlspecialchars($row->item_name) ?> #<?= $row->unit_no ?></div>
                    <div class="notifDate">Due: <?= date('M d, Y h:i A', strtotime($row->due_date)) ?></div>
                </div>
                <div class="notifValue" style="color:#f6c23e;">
                    Due within 24h
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($overdue) && empty($due_soon)): ?>
            <div style="text-align:center; color:#999; padding:60px 0;">
                <i class="fas fa-bell-slash" style="font-size:32px; margin-bottom:10px; display:block;"></i>
                No notifications right now.
            </div>
        <?php endif; ?>

    </div>

</div>



<?php $this->load->view('templates/footer'); ?>