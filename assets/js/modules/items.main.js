$(document).ready(function () {

    // Filter variables
    var statusFilter   = '';
    var dateFromFilter = '';
    var dateToFilter   = '';

    // Destroy if already initialized
    if ($.fn.DataTable.isDataTable('#itemsTable')) {
        $('#itemsTable').DataTable().destroy();
    }

    // Init server-side DataTable
    var table = $('#itemsTable').DataTable({
        processing : true,
        serverSide : true,
        lengthMenu : [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],  // ← add this
        pageLength : 5,  // ← default on load
        ajax: {
            url  : BASE_URL + 'items/ajax_list',
            type : 'POST',
            data : function (d) {
                d.status    = statusFilter;
                d.date_from = dateFromFilter;
                d.date_to   = dateToFilter;
            }
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 },
            { data: 7 },
            { data: 8 },
            { data: 9,  orderable: false },
            { data: 10 },
            { data: 11 },
            { data: 12 },
            { data: 13, orderable: false }
        ],
        order: [[0, 'asc']],
        language: {
            emptyTable : 'No items found.',
            processing : '<i class="fas fa-spinner fa-spin"></i> Loading...'
        }
    });

    // Filter form submit
    $('form').on('submit', function (e) {
        e.preventDefault();
        statusFilter   = $('select[name="status"]').val()   || '';
        dateFromFilter = $('input[name="date_from"]').val() || '';
        dateToFilter   = $('input[name="date_to"]').val()   || '';
        table.ajax.reload();
    });

    // Reset filters
    $('#btnReset').on('click', function () {
        $('select[name="status"]').val('');
        $('input[name="date_from"]').val('');
        $('input[name="date_to"]').val('');
        statusFilter   = '';
        dateFromFilter = '';
        dateToFilter   = '';
        table.ajax.reload();
    });

    // Open Add Modal
    $('#btnAddItem').click(function () {
        $('#modalTitle').text('Add Item');
        $('#item_id').val('');
        $('#item_name').val('');
        $('#category').val('');
        $('#brand').val('');
        $('#model').val('');
        $('#serial_number').val('');
        $('#quantity').val('');
        $('#available_quantity').val('');
        $('#borrowed_quantity').val('');
        $('#status').val('available');
        $('#location').val('');
        $('#itemModal').modal('show');
    });

    // Open Edit Modal
    $(document).on('click', '.btnEdit', function () {
        var id = $(this).data('id');
        $.ajax({
            url      : BASE_URL + 'items/get/' + id,
            type     : 'GET',
            dataType : 'json',
            success  : function (res) {
                if (res.success) {
                    $('#modalTitle').text('Edit Item');
                    $('#item_id').val(res.item.id);
                    $('#item_name').val(res.item.item_name);
                    $('#category').val(res.item.category);
                    $('#brand').val(res.item.brand);
                    $('#model').val(res.item.Model);
                    $('#serial_number').val(res.item.serial_number);
                    $('#quantity').val(res.item.quantity);
                    $('#available_quantity').val(res.item.available_quantity);
                    $('#borrowed_quantity').val(res.item.borrowed_quantity);
                    $('#status').val(res.item.status);
                    $('#location').val(res.item.location);
                    $('#itemModal').modal('show');
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            }
        });
    });

    // Save (Add or Edit)
    $('#btnSave').click(function () {
        var id                 = $('#item_id').val();
        var item_name          = $('#item_name').val().trim();
        var category           = $('#category').val().trim();
        var brand              = $('#brand').val().trim();
        var model              = $('#model').val().trim();
        var serial_number      = $('#serial_number').val().trim();
        var quantity           = $('#quantity').val();
        var available_quantity = $('#available_quantity').val();
        var borrowed_quantity  = $('#borrowed_quantity').val();
        var status             = $('#status').val();
        var item_location      = $('#location').val().trim();

        if (!item_name || !category || quantity === '') {
            Swal.fire('Warning', 'Please fill in required fields.', 'warning');
            return;
        }

        var url = id ? BASE_URL + 'items/update/' + id : BASE_URL + 'items/store';

        $.post(url, {
            item_name          : item_name,
            category           : category,
            brand              : brand,
            model              : model,
            serial_number      : serial_number,
            quantity           : quantity,
            available_quantity : available_quantity,
            borrowed_quantity  : borrowed_quantity,
            status             : status,
            location           : item_location
        }, function (res) {
            if (res.success) {
                $('#itemModal').modal('hide');
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

    // Delete
    $(document).on('click', '.btnDelete', function () {
        var id        = $(this).data('id');
        var item_name = $(this).data('name');

        Swal.fire({
            title             : 'Delete ' + item_name + '?',
            text              : 'This action cannot be undone.',
            icon              : 'warning',
            showCancelButton  : true,
            confirmButtonColor: '#d33',
            cancelButtonColor : '#6c757d',
            confirmButtonText : 'Yes, delete it!'
        }).then(function (result) {
            if (result.isConfirmed) {
                $.post(BASE_URL + 'items/delete/' + id, function (res) {
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

});

