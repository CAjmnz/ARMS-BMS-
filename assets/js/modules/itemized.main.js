$(document).ready(function () {

    var statusFilter    = '';
    var conditionFilter = '';
    var dateFromFilter  = '';
    var dateToFilter    = '';

    // Destroy if already initialized
    if ($.fn.DataTable.isDataTable('#itemizedTable')) {
        $('#itemizedTable').DataTable().destroy();
    }

    // Init server-side DataTable
    var table = $('#itemizedTable').DataTable({
        processing : true,
        serverSide : true,
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
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3, orderable: false },
            { data: 4 },
            { data: 5 },
            { data: 6 },
            { data: 7 },
            { data: 8, orderable: false }
        ],
        order: [[0, 'desc']],
        language: {
            emptyTable : 'No units found.',
            processing : '<i class="fas fa-spinner fa-spin"></i> Loading...'
        }
    });

    // Filter form submit
    $('#filterForm').on('submit', function (e) {
        e.preventDefault();
        statusFilter    = $('select[name="status"]').val()         || '';
        conditionFilter = $('select[name="item_condition"]').val() || '';
        dateFromFilter  = $('input[name="date_from"]').val()       || '';
        dateToFilter    = $('input[name="date_to"]').val()         || '';
        table.ajax.reload();
    });

    // Reset filters
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

 // Open Add Modal
$('#btnAddUnit').click(function () {
    $('#modalTitle').text('Add Unit');
    $('#unit_id').val('');
    $('#unit_item_id').val('').prop('disabled', false);
    $('#unit_count').val(1);
    $('#unit_status').val('available');
    $('#unit_condition').val('new');
    $('#unit_description').val('');
    $('#unitCountGroup').show();  // show unit count on Add
    $('#unitModal').modal('show');
});

// Open Edit Modal
$(document).on('click', '.btnEdit', function () {
    var id = $(this).data('id');
    $.ajax({
        url      : BASE_URL + 'itemized/get/' + id,
        type     : 'GET',
        dataType : 'json',
        success  : function (res) {
            if (res.success) {
                $('#modalTitle').text('Edit Unit');
                $('#unit_id').val(res.item.id);
                $('#unit_item_id').val(res.item.item_id).prop('disabled', true);
                $('#unit_status').val(res.item.status);
                $('#unit_condition').val(res.item.item_condition);
                $('#unit_description').val(res.item.item_description);
                $('#unitCountGroup').hide();  // hide unit count on Edit
                $('#unitModal').modal('show');
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }
    });
});

    // Save (Add or Edit)
    $('#btnSaveUnit').click(function () {
        var id          = $('#unit_id').val();
        var item_id     = $('#unit_item_id').val();
        var unit_no     = $('#unit_no').val();
        var status      = $('#unit_status').val();
        var condition   = $('#unit_condition').val();
        var description = $('#unit_description').val().trim();

        if (!item_id || !unit_no) {
            Swal.fire('Warning', 'Please fill in required fields.', 'warning');
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

    // Delete
    $(document).on('click', '.btnDelete', function () {
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

});