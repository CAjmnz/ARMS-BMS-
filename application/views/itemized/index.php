<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<div id="main-content">

    <!-- Page Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h4 style="margin:0; font-weight:700; color:#333;">Item Management &rsaquo; Itemized</h4>
        <div style="display:flex; gap:8px;">

            <!-- Bulk Delete — hidden until select mode -->
            <button class="btn btn-danger btn-sm d-none" id="btnBulkDelete">
                <i class="fas fa-trash"></i> Delete Selected (<span id="selectedCount">0</span>)
            </button>

            <!-- Cancel Select — hidden until select mode -->
            <button class="btn btn-secondary btn-sm d-none" id="btnCancelSelect">
                <i class="fas fa-times"></i> Cancel
            </button>

            <!-- Select Button -->
            <button class="btn btn-warning btn-sm" id="btnSelect">
                <i class="fas fa-check-square"></i> Select
            </button>

            <!-- Add Unit -->
            <button class="btn btn-success btn-sm" id="btnAddUnit">
                <i class="fas fa-plus"></i> Add Unit
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
                        <option value="available">Available</option>
                        <option value="borrowed">Borrowed</option>
                        <option value="reserved">Reserved</option>
                        <option value="returned">Returned</option>
                        <option value="overdue">Overdue</option>
                        <option value="missing">Missing</option>
                        <option value="damaged">Damaged</option>
                        <option value="archived">Archived</option>
                        <option value="under_review">Under Review</option>
                        <option value="disposed">Disposed</option>
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
        <table class="table table-bordered table-hover" id="itemizedTable">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th class="checkbox-col" style="display:none; width:40px;">
                        <input type="checkbox" id="selectAll">
                    </th>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Unit.No</th>
                    <th>Status</th>
                    <th>Condition</th>
                    <th>Description</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

</div>




<!-- Add/Edit Modal -->
<div class="modal fade" id="unitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Unit</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="unit_id">

                <!-- Item Dropdown -->
                <div class="form-group">
                    <label>Item <span class="text-danger">*</span></label>
                    <select class="form-control" id="unit_item_id">
                        <option value="">-- Select Item --</option>
                        <?php foreach ($items as $item): ?>
                            <option value="<?= $item->id ?>">
                                <?= htmlspecialchars($item->item_name) ?>
                                (Current Qty: <?= $item->quantity ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Unit Count — only shown on Add -->
                <div class="form-group" id="unitCountGroup">
                    <label>Number of Units to Add <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="unit_count"
                        placeholder="How many units?" min="1" value="1">
                    <small class="text-muted">
                        Each unit will be auto-numbered sequentially.
                    </small>
                </div>

                <!-- Status -->
                <div class="form-group">
                    <label>Status <span class="text-danger">*</span></label>
                    <select class="form-control" id="unit_status">
                        <option value="available">Available</option>
                        <option value="borrowed">Borrowed</option>
                        <option value="reserved">Reserved</option>
                        <option value="returned">Returned</option>
                        <option value="overdue">Overdue</option>
                        <option value="missing">Missing</option>
                        <option value="damaged">Damaged</option>
                        <option value="archived">Archived</option>
                        <option value="under_review">Under Review</option>
                        <option value="disposed">Disposed</option>
                    </select>
                </div>

                <!-- Condition -->
                <div class="form-group">
                    <label>Condition <span class="text-danger">*</span></label>
                    <select class="form-control" id="unit_condition">
                        <option value="new">New</option>
                        <option value="excellent">Excellent</option>
                        <option value="good">Good</option>
                        <option value="needs repair">Needs Repair</option>
                        <option value="under maintenance">Under Maintenance</option>
                    </select>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" id="unit_description" rows="3"
                        placeholder="e.g. Laptop unit - minor scratches"></textarea>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="btnSaveUnit">Save</button>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('templates/footer'); ?>