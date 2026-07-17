<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<div id="main-content">

    <!-- Page Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h4 style="margin:0; font-weight:700; color:#333;">Borrowing Management &rsaquo; Borrowing</h4>
        <div style="display:flex; gap:8px;">
            <button class="btn btn-success btn-sm" id="btnAddBorrowing">
                <i class="fas fa-plus"></i> Borrowing
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
    <?php endif; ?>

    <!-- Filters -->
    <div style="background:#fff; border-radius:8px; padding:15px; margin-bottom:20px; border:1px solid #e3e6f0;">
        <form id="filterForm">
            <div class="row">
                <div class="col-md-3">
                    <label style="font-size:13px;">Status</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="">All Status</option>
                        <option value="borrowed">Borrowed</option>
                        <option value="returned">Returned</option>
                        <option value="damaged">Damaged</option>
                        <option value="lost">Lost</option>
                        <option value="overdue">Overdue</option>
                        <option value="due_soon">Due Soon</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label style="font-size:13px;">Condition</label>
                    <select name="item_condition" class="form-control form-control-sm">
                        <option value="">All Conditions</option>
                        <option value="new">New</option>
                        <option value="excellent">Excellent</option>
                        <option value="good">Good</option>
                        <option value="needs repair">Needs Repair</option>
                        <option value="under maintenance">Under Maintenance</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label style="font-size:13px;">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label style="font-size:13px;">Date To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm mr-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <button type="button" id="btnReset" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times"></i> Reset
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0;">
        <table class="table table-bordered table-hover" id="borrowingTable">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th>#</th>
                    <th>Borrower's Id</th>
                    <th>Borrower's name</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Condition Before Borrowing</th>
                    <th>Borrowed Date</th>
                    <th>Due date</th>
                    <th>Borrowing status</th>
                    <th>Released by</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <!-- Add/Edit Modal -->
    <!-- Add Borrowing Modal -->
    <div class="modal fade" id="borrowingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Release Borrowing</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <!-- Borrower -->
                    <div class="form-group">
                        <label>Borrower <span class="text-danger">*</span></label>
                        <select class="form-control" id="borrowing_borrower_id">
                            <option value="">-- Select Borrower --</option>
                            <?php foreach ($borrowers as $b): ?>
                                <option value="<?= $b->id ?>">
                                    <?= htmlspecialchars($b->full_name) ?> (<?= htmlspecialchars($b->id_number) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Item -->
                    <div class="form-group">
                        <label>Item <span class="text-danger">*</span></label>
                        <select class="form-control" id="borrowing_item_id">
                            <option value="">-- Select Item --</option>
                            <?php foreach ($items as $item): ?>
                                <option value="<?= $item->id ?>">
                                    <?= htmlspecialchars($item->item_name) ?>
                                    (Available: <?= $item->available_quantity ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Units — populated via AJAX -->
                    <div class="form-group">
                        <label>Available Units <span class="text-danger">*</span></label>
                        <div id="unitCheckboxList" style="max-height:150px; overflow-y:auto; border:1px solid #ddd; border-radius:4px; padding:8px;">
                            <span class="text-muted">Select an item first.</span>
                        </div>
                    </div>

                    <!-- Purpose -->
                    <div class="form-group">
                        <label>Purpose</label>
                        <input type="text" class="form-control" id="borrowing_purpose" placeholder="e.g. Thesis defense">
                    </div>

                    <!-- Due Date -->
                    <div class="form-group">
                        <label>Due Date <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" id="borrowing_due_date">
                    </div>

                    <!-- released by -->
                    <div class="form-group">
                        <label>released by <span class="text-danger">*</span></label>
                        <select class="form-control" id="borrowing_borrower_id">
                            <option value="">-- Select Borrower --</option>
                            <?php foreach ($borrowers as $b): ?>
                                <option value="<?= $b->id ?>">
                                    <?= htmlspecialchars($b->full_name) ?> (<?= htmlspecialchars($b->id_number) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="btnSaveBorrowing">Release</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mark Returned Modal -->
    <div class="modal fade" id="returnModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mark as Returned</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
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
                        <textarea class="form-control" id="return_remarks" rows="2" placeholder="Optional notes"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="btnConfirmReturn">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('templates/footer'); ?>