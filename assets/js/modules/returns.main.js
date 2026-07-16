$(document).ready(function () {

    if ($('#returnsTable').length === 0) {
        return;
    }

    var statusFilter   = '';
    var dateFromFilter = '';
    var dateToFilter   = '';

    if ($.fn.DataTable.isDataTable('#returnsTable')) {
        $('#returnsTable').DataTable().destroy();
    }

    var table = $('#returnsTable').DataTable({
        processing : true,
        serverSide : true,
        lengthMenu : [[5, 10, 25, 50], [5, 10, 25, 50]],
        pageLength : 10,
        ajax: {
            url  : BASE_URL + 'returns/ajax_list',
            type : 'POST',
            data : function (d) {
                d.item_status = statusFilter;
                d.date_from   = dateFromFilter;
                d.date_to     = dateToFilter;
            }
        },
        columns: [
            { data: 0, orderable: false },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6, orderable: false },
            { data: 7 },
            { data: 8 },
            { data: 9 },
            { data: 10 },
            { data: 11, orderable: false },
            { data: 12 },
            { data: 13 },
            { data: 14 },
            { data: 15, orderable: false }
        ],
        order: [[10, 'desc']],
        language: {
            emptyTable : 'No returned items found.',
            processing : '<i class="fas fa-spinner fa-spin"></i> Loading...'
        }
    });

    $('#filterForm').on('submit', function (e) {
        e.preventDefault();
        statusFilter   = $('select[name="item_status"]').val() || '';
        dateFromFilter = $('input[name="date_from"]').val()    || '';
        dateToFilter   = $('input[name="date_to"]').val()      || '';
        table.ajax.reload();
    });

    $('#btnReset').on('click', function () {
        $('select[name="item_status"]').val('');
        $('input[name="date_from"]').val('');
        $('input[name="date_to"]').val('');
        statusFilter   = '';
        dateFromFilter = '';
        dateToFilter   = '';
        table.ajax.reload();
    });

    $('#returnsTable').on('click', '.btnView', function () {
        var id = $(this).data('id');
    
        $.get(BASE_URL + 'returns/get_details/' + id, function (res) {
            if (res.success) {
                var d = res.item;
    
                $('#d_borrower_name').text(d.borrower_name || '-');
                $('#d_id_number').text(d.id_number || '-');
                $('#d_borrower_type').text(d.borrower_type || '-');
                $('#d_contact').text(d.contact_number || '-');
                $('#d_email').text(d.email || '-');
    
                $('#d_item_name').text(d.item_name || '-');
                $('#d_category').text(d.category || '-');
                $('#d_unit_no').text(d.unit_no || '-');
                $('#d_brand_model').text((d.brand || '-') + ' / ' + (d.Model || '-'));
                $('#d_serial').text(d.serial_number || '-');
    
                $('#d_txn').text('TXN-' + String(d.borrowing_id).padStart(5, '0'));
                $('#d_purpose').text(d.purpose || '-');
                $('#d_date_requested').text(d.date_requested ? new Date(d.date_requested).toLocaleString() : '-');
                $('#d_date_released').text(d.date_released ? new Date(d.date_released).toLocaleString() : '-');
                $('#d_due_date').text(d.due_date ? new Date(d.due_date).toLocaleString() : '-');
                $('#d_released_by').text(d.released_by_name || '-');
                $('#d_condition_before').text(d.condition_before || '-');
    
                $('#d_item_status').text(d.item_status || '-');
                $('#d_date_returned').text(d.date_returned ? new Date(d.date_returned).toLocaleString() : '-');
                $('#d_condition_after').text(d.condition_after || '-');
                $('#d_received_by').text(d.received_by_name || '-');
                $('#d_remarks').text(d.remarks || '-');
    
                $('#detailsModal').modal('show');
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }, 'json');
    });
});