<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<div id="main-content">

    <!-- Page Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h4 style="margin:0; font-weight:700; color:#333;">Item Management</h4>
        <button class="btn btn-success btn-sm" id="btnAddItem">
            <i class="fas fa-plus"></i> Add Item
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
    <div style="background:#fff; border-radius:8px; padding:15px; margin-bottom:20px; border:1px solid #e3e6f0;">
        <form method="GET" action="<?= base_url('items') ?>">
            <div class="row">
                <div class="col-md-3">
                    <label style="font-size:13px;">Status</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="">All Status</option>
                        <option value="available" <?= ($this->input->get('status') == 'available')   ? 'selected' : '' ?>>Available</option>
                        <option value="in_use" <?= ($this->input->get('status') == 'in_use')      ? 'selected' : '' ?>>In Use</option>
                        <option value="unavailable" <?= ($this->input->get('status') == 'unavailable') ? 'selected' : '' ?>>Unavailable</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label style="font-size:13px;">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm"
                        value="<?= $this->input->get('date_from') ?>">
                </div>
                <div class="col-md-3">
                    <label style="font-size:13px;">Date To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm"
                        value="<?= $this->input->get('date_to') ?>">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="<?= base_url('items') ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0;">
        <table class="table table-bordered table-hover" id="itemsTable">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>model</th>
                    <th>Serial Num</th>
                    <th>Total Qty</th>
                    <th>Available Qty</th>
                    <th>Borrowed Qty</th>
                    <th>Status</th>
                    <th>Location</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $i => $item): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($item->item_name) ?></td>
                            <td><?= htmlspecialchars($item->category) ?></td>
                            <td><?= htmlspecialchars($item->brand ?? '-') ?></td>
                            <td><?= htmlspecialchars($item->Model ?? '-') ?></td>
                            <td><?= htmlspecialchars($item->serial_number ?? '-') ?></td>
                            <td><?= $item->total_quantity ?? $item->quantity ?></td>
                            <td><?= $item->available_quantity ?? '-' ?></td>
                            <td><?= $item->borrowed_quantity ?? '-' ?></td>
                            <td>
                                <?php if ($item->status === 'available'): ?>
                                    <span class="badge badge-success">Available</span>
                                <?php elseif ($item->status === 'in_use'): ?>
                                    <span class="badge badge-warning">In Use</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Unavailable</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($item->location ?? '-') ?></td>
                            <td><?= date('M d, Y h:i A', strtotime($item->created_at)) ?></td>
                            <td><?= date('M d, Y h:i A', strtotime($item->updated_at)) ?></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <button class="dropdown-item btnEdit" data-id="<?= $item->id ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="dropdown-item btnDelete"
                                            data-id="<?= $item->id ?>"
                                            data-name="<?= htmlspecialchars($item->item_name) ?>">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="itemModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Item</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="item_id">
                    <div class="form-row">
                        <div class="form-group col-6 mb-2">
                            <div class="field-wrap">
                                <label>Item Name</label>
                                <input type="text" class="form-control" id="item_name" placeholder="Item Name">
                            </div>
                        </div>
                        <div class="form-group col-6 mb-2">
                            <div class="field-wrap">
                                <label>Category</label>
                                <input type="text" class="form-control" id="category" placeholder="Category">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-6 mb-2">
                            <label>Brand</label>
                            <input type="text" class="form-control" id="brand" placeholder="Brand">
                        </div>
                        <div class="form-group col-6 mb-2">
                            <label>Model</label>
                            <input type="text" class="form-control" id="model" placeholder="Model">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-6 md-2">
                            <label>Serial Number</label>
                            <input type="text" class="form-control" id="serial_number" placeholder="Serial number">
                        </div>
                        <div class="form-group col-6 mb-6">
                            <label>Quantity</label>
                            <input type="number" class="form-control" id="quantity" placeholder="Quantity" min="0">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-6 md-2">
                            <label>Available Quantity</label>
                            <input type="number" class="form-control" id="available_quantity" placeholder="Available Quantity" min="0">
                        </div>
                        <div class="form-group col-6 mb-2">
                            <label>Borrowed Quantity</label>
                            <input type="number" class="form-control" id="borrowed_quantity" placeholder="Borrowed Quantity" min="0">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-6 mb-2">
                            <div class="field-wrap">
                                <label>Status</label>
                                <select class="form-control" id="status">
                                    <option value="available">Available</option>
                                    <option value="in_use">In Use</option>
                                    <option value="unavailable">Unavailable</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-6 mb-2">
                            <div class="field-wrap">
                                <label>location</label>
                                <input type="text" class="form-control" id="location" placeholder="Location">
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