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
    <div class="notifCard notifCardClickable" data-type="overdue" style="border-left:4px solid #e74a3b; cursor:pointer;"
         data-id="<?= encode_id($row->id) ?>"
         data-borrower="<?= htmlspecialchars($row->borrower_name) ?>"
         data-id-number="<?= htmlspecialchars($row->id_number) ?>"
         data-item="<?= htmlspecialchars($row->item_name) ?>"
         data-unit-no="<?= $row->unit_no ?>"
         data-due="<?= date('M d, Y h:i A', strtotime($row->due_date)) ?>"
         data-status="Overdue by <?= $days ?> day<?= $days != 1 ? 's' : '' ?>">
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
    <div class="notifCard notifCardClickable" data-type="due_soon" style="border-left:6px solid #f6c23e; cursor:pointer;"
         data-id="<?= encode_id($row->id) ?>"
         data-borrower="<?= htmlspecialchars($row->borrower_name) ?>"
         data-id-number="<?= htmlspecialchars($row->id_number) ?>"
         data-item="<?= htmlspecialchars($row->item_name) ?>"
         data-unit-no="<?= $row->unit_no ?>"
         data-due="<?= date('M d, Y h:i A', strtotime($row->due_date)) ?>"
         data-status="Due within 24 hours">
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
<!-- Notification Details Modal -->
<div class="modal fade" id="notifDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notifModalTitle">Item Details</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row mb-2">
                    <div class="col-6"><strong>Borrower:</strong> <span id="nd_borrower">-</span></div>
                    <div class="col-6"><strong>ID No:</strong> <span id="nd_id_number">-</span></div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>Item:</strong> <span id="nd_item">-</span></div>
                    <div class="col-6"><strong>Unit No:</strong> <span id="nd_unit_no">-</span></div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>Due Date:</strong> <span id="nd_due_date">-</span></div>
                    <div class="col-6"><strong>Status:</strong> <span id="nd_status">-</span></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="btnReturnFromNotif">
                    <i class="fas fa-undo"></i> Mark Returned
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reuse the same Mark Returned modal from Borrowing page -->
<div class="modal fade" id="returnModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark as Returned</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="return_borrowing_item_id">
                <div class="form-group">
                    <label>Return Status <span class="text-danger">*</span></label>
                    <select class="form-control" id="return_item_status">
                        <option value="returned">Returned (good condition)</option>
                        <option value="damaged">Returned Damaged</option>
                        <option value="lost">Lost / Not Returned</option>
                    </select>
                </div>
                <div class="form-group" id="conditionAfterGroup">
                    <label>Condition</label>
                    <select class="form-control" id="return_condition_after">
                        <option value="new">New</option>
                        <option value="excellent">Excellent</option>
                        <option value="good">Good</option>
                        <option value="needs repair">Needs Repair</option>
                        <option value="under maintenance">Under Maintenance</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Remarks</label>
                    <textarea class="form-control" id="return_remarks" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="btnConfirmReturn">Confirm</button>
            </div>
        </div>
    </div>
</div>
</div>



<?php $this->load->view('templates/footer'); ?>