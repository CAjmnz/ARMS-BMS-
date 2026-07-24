<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<div id="main-content">

    <!-- Page Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h4 style="margin:0; font-weight:700; color:#333;">Employee Management</h4>
        <button class="btn btn-success btn-sm" id="btnAddItem">
            <i class="fas fa-plus"></i> Add User
        </button>
    </div>

    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="card mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-10">
                <input
                    type="text"
                    id="searchEmployee"
                    class="form-control"
                    placeholder="Search employee name or ID...">
            </div>

            <div class="col-md-2">
                <button
                    class="btn btn-primary btn-block"
                    id="btnSearchEmployee">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- Table -->
    <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0;">
    <table class="table table-bordered table-hover" id="usersTable" width="100%">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="30%">Employee</th>
            <th width="12%">Employee ID</th>
            <th width="15%">Department</th>
            <th width="8%">Status</th>
            <th width="8%">B.U.</th>
            <th width="10%">Type</th>
            <th width="7%">Role</th>
            <th width="5%">Action</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="itemModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Employee</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="item_id">
                    <div class="form-row">
                        <div class="form-group col-6 mb-2">
                            <div class="field-wrap">
                                <label>Employee_ID</label>
                                <input type="text" class="form-control" id="item_name" >
                            </div>
                        </div>
                        <div class="form-group col-6 mb-2">
                            <div class="field-wrap">
                                <label>Employee_Name</label>
                                <input type="text" class="form-control" id="item_name" >
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-6 mb-2">
                            <label>Employee_position</label>
                            <input type="text" class="form-control" id="brand" >
                        </div>
                        <div class="form-group col-6 mb-2">
                            <label>Employee_Department</label>
                            <input type="text" class="form-control" id="model" >
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-6 md-2">
                            <label>Employee_company</label>
                            <input type="text" class="form-control" id="serial_number" placeholder="Serial number">
                        </div>
                        <div class="form-group col-6 mb-6">
                            <label>Business Unit</label>
                            <input type="number" class="form-control" id="quantity" placeholder="Quantity" min="0">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-6 md-2">
                            <label>Employee_Type</label>
                            <input type="text" class="form-control" id="serial_number" placeholder="Serial number">
                        </div>
                        <div class="form-group col-6 mb-6">
                            <label>Employee Status</label>
                            <input type="number" class="form-control" id="quantity" placeholder="Quantity" min="0">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6 mb-2">
                            <div class="field-wrap">
                                <label>role</label>
                                <select class="form-control" id="role">
    <option value="User">User</option>
    <option value="Admin">Admin</option>
</select>
                        </div>
                        <div class="form-group col-6 mb-2">
                            <div class="field-wrap">
                                <label>Account Status</label>
                                <select class="form-control" id="account_status">
    <option value="Active">Active</option>
    <option value="Inactive">Inactive</option>
</select>
                            </div>
                        </div>
    </div>

    </div>
                </div> <!-- ← modal-body closes HERE -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="btnSave">Save</button>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('templates/footer'); ?>