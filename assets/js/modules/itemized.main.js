$(document).ready(function () {

    var statusFilter    = '';
    var conditionFilter = '';
    var dateFromFilter  = '';
    var dateToFilter    = '';
    var selectMode      = false;

    // Destroy if already initialized
    if ($.fn.DataTable.isDataTable('#itemizedTable')) {
        $('#itemizedTable').DataTable().destroy();
    }

    // Init server-side DataTable
    var table = $('#itemizedTable').DataTable({
        processing : true,
        serverSide : true,
        responsive: true,
        autoWidth: false,
        lengthMenu : [[5, 10, 25, 50], [5, 10, 25, 50]],
        pageLength : 10,
        ajax: {
            url  : BASE_URL + 'itemized/ajax_list',
            type : 'POST',
            data : function (d) {
                d.status         = statusFilter;
                d.item_condition = conditionFilter;
                d.date_from      = dateFromFilter;
                d.date_to        = dateToFilter;
            }
        },
        columns: [
            { data: 0, orderable: false, visible: false },  // checkbox
            { data: 1 },                                     // #
            { data: 2 },                                     // Item Name
            { data: 3 },                                     // Unit No
            { data: 4, orderable: false },                   // Status
            { data: 5 },                                     // Condition
            { data: 6 },                                     // Description
            { data: 7 },                                     // Created At
            { data: 8 },                                     // Updated At
            { data: 9, orderable: false }                    // Action
        ],
        order: [[1, 'asc']],
        language: {
            emptyTable : 'No units found.',
            processing : '<i class="fas fa-spinner fa-spin"></i> Loading...'
        }
    });

    // ─── Filters ─────────────────────────────────────────
    $('#filterForm').on('submit', function (e) {
        e.preventDefault();
        statusFilter    = $('select[name="status"]').val()         || '';
        conditionFilter = $('select[name="item_condition"]').val() || '';
        dateFromFilter  = $('input[name="date_from"]').val()       || '';
        dateToFilter    = $('input[name="date_to"]').val()         || '';
        table.ajax.reload();
    });

    $('#btnReset').on('click', function () {
        $('select[name="status"]').val('');
        $('select[name="item_condition"]').val('');
        $('input[name="date_from"]').val('');
        $('input[name="date_to"]').val('');
        statusFilter    = '';
        conditionFilter = '';
        dateFromFilter  = '';
        dateToFilter    = '';
        table.ajax.reload();
    });

    // ─── Select Mode ──────────────────────────────────────
    $('#btnSelect').on('click', function () {
        selectMode = true;
        table.column(0).visible(true);
        $('.checkbox-col').show();
        $('#btnSelect').addClass('d-none');
        $('#btnCancelSelect').removeClass('d-none');
        $('#btnBulkDelete').removeClass('d-none');
    });

    $('#btnCancelSelect').on('click', function () {
        selectMode = false;
        table.column(0).visible(false);
        $('.checkbox-col').hide();
        $('#selectAll').prop('checked', false);
        $('#btnCancelSelect').addClass('d-none');
        $('#btnBulkDelete').addClass('d-none');
        $('#btnSelect').removeClass('d-none');
        $('#selectedCount').text(0);
    });

    // ─── Select All ───────────────────────────────────────
    $('#selectAll').on('click', function () {
        $('.rowCheckbox').prop('checked', $(this).prop('checked'));
        updateCount();
    });

    $(document).on('change', '.rowCheckbox', function () {
        var total   = $('.rowCheckbox').length;
        var checked = $('.rowCheckbox:checked').length;
        $('#selectAll').prop('checked', total === checked);
        updateCount();
    });

    function updateCount() {
        var checked = $('.rowCheckbox:checked').length;
        $('#selectedCount').text(checked);
    }

    // ─── DataTable Redraw ─────────────────────────────────
    table.on('draw', function () {
        $('#selectAll').prop('checked', false);
        $('#selectedCount').text(0);
        if (!selectMode) {
            table.column(0).visible(false);
        }
    });

    // ─── Add Modal ────────────────────────────────────────
    $('#btnAddUnit').click(function () {
        $('#modalTitle').text('Add Unit');
        $('#unit_id').val('');
        $('#unit_item_id').val('').prop('disabled', false);
        $('#unit_count').val(1);
        $('#unit_status').val('available');
        $('#unit_condition').val('new');
        $('#unit_description').val('');
        $('#unitCountGroup').show();
        $('#unitModal').modal('show');
    });

    // ─── Edit Modal ───────────────────────────────────────
    $(document).on('click', '.btnEdit', function () {
        var id = $(this).data('id');   // encoded id from data-id attribute
        $.ajax({
            url      : BASE_URL + 'itemized/get/' + id,
            type     : 'GET',
            dataType : 'json',
            success  : function (res) {
                if (res.success) {
                    $('#modalTitle').text('Edit Unit');
                    $('#unit_id').val(id);   // ← use the encoded id, NOT res.item.id
                    $('#unit_item_id').val(res.item.item_id).prop('disabled', true);
                    $('#unit_status').val(res.item.status);
                    $('#unit_condition').val(res.item.item_condition);
                    $('#unit_description').val(res.item.item_description);
                    $('#unitCountGroup').hide();
                    $('#unitModal').modal('show');
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            }
        });
    });
    // ─── Save ─────────────────────────────────────────────
    $('#btnSaveUnit').click(function () {
        var id          = $('#unit_id').val();
        $('#unit_item_id').prop('disabled', false);
        var item_id     = $('#unit_item_id').val();
        var unit_count  = parseInt($('#unit_count').val()) || 1;
        var status      = $('#unit_status').val();
        var condition   = $('#unit_condition').val();
        var description = $('#unit_description').val().trim();

        if (!item_id) {
            Swal.fire('Warning', 'Please select an item.', 'warning');
            return;
        }

        if (!id && (isNaN(unit_count) || unit_count < 1)) {
            Swal.fire('Warning', 'Please enter number of units.', 'warning');
            return;
        }

        var url = id ? BASE_URL + 'itemized/update/' + id : BASE_URL + 'itemized/store';

        $.post(url, {
            item_id          : item_id,
            unit_count       : unit_count,
            status           : status,
            item_condition   : condition,
            item_description : description
        }, function (res) {
            if (res.success) {
                $('#unitModal').modal('hide');
                Swal.fire('Success', res.message, 'success').then(function () {
                    table.ajax.reload();
                });
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }, 'json')
        .fail(function (xhr) {
            console.log('Error:', xhr.responseText);
            Swal.fire('Error', 'Something went wrong.', 'error');
        });
    });

    // ─── Single Delete ────────────────────────────────────
    $('#itemizedTable').on('click', '.btnDelete', function () {
        var id   = $(this).data('id');
        var name = $(this).data('name');

        Swal.fire({
            title             : 'Delete ' + name + '?',
            text              : 'This action cannot be undone.',
            icon              : 'warning',
            showCancelButton  : true,
            confirmButtonColor: '#d33',
            cancelButtonColor : '#6c757d',
            confirmButtonText : 'Yes, delete it!'
        }).then(function (result) {
            if (result.isConfirmed) {
                $.post(BASE_URL + 'itemized/delete/' + id, function (res) {
                    if (res.success) {
                        Swal.fire('Deleted!', res.message, 'success').then(function () {
                            table.ajax.reload();
                        });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }, 'json');
            }
        });
    });

    // ─── Bulk Delete ──────────────────────────────────────
    $('#btnBulkDelete').on('click', function () {
        var ids = [];
        $('.rowCheckbox:checked').each(function () {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            Swal.fire('Warning', 'No units selected.', 'warning');
            return;
        }

        Swal.fire({
            title             : 'Delete ' + ids.length + ' unit(s)?',
            text              : 'This action cannot be undone.',
            icon              : 'warning',
            showCancelButton  : true,
            confirmButtonColor: '#d33',
            cancelButtonColor : '#6c757d',
            confirmButtonText : 'Yes, delete all!'
        }).then(function (result) {
            if (result.isConfirmed) {
                $.post(BASE_URL + 'itemized/bulk_delete', {
                    ids: ids
                }, function (res) {
                    if (res.success) {
                        Swal.fire('Deleted!', res.message, 'success').then(function () {
                            $('#btnCancelSelect').trigger('click');
                        });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }, 'json');
            }
        });
    });

    // Fix aria-hidden focus warning
    $('#unitModal').on('hidden.bs.modal', function () {
        $('body').focus();
        $(this).find('select, input, textarea').blur();
    });

});