$(document).ready(function () {

    var itemStatusFilter      = '';
    var borrowingStatusFilter = '';
    var dateFromFilter        = '';
    var dateToFilter          = '';

    // Destroy if already initialized
    if ($.fn.DataTable.isDataTable('#borrowingTable')) {
        $('#borrowingTable').DataTable().destroy();
    }

    // Init server-side DataTable
    var table = $('#borrowingTable').DataTable({
        processing : true,
        serverSide : true,
        responsive : true,
        autoWidth  : false,
        lengthMenu : [[5, 10, 25, 50], [5, 10, 25, 50]],
        pageLength : 10,
        ajax: {
            url  : BASE_URL + 'borrowing/ajax_list',
            type : 'POST',
            data : function (d) {
                d.item_status      = itemStatusFilter;
                d.borrowing_status = borrowingStatusFilter;
                d.date_from        = dateFromFilter;
                d.date_to          = dateToFilter;
            }
        },
        columns: [
            { data: 0, orderable: false },  // #
            { data: 1 },                    // Reservation's Id
            { data: 2 },                    // Borrower's Id
            { data: 3 },                    // Borrower's name
            { data: 4 },                    // Unit id
            { data: 5 },                    // Item Name
            { data: 6 },                    // Reservation Date 
            { data: 7 },                    // Pick up Date
            { data: 8 },                    // Return date
            { data: 9 },                    // Purpose
            { data: 10 },                   // Status 
            { data: 11 },                   // Reserved by
            { data: 12, orderable: false }  // Action
        ],
        order: [[7, 'desc']],
        language: {
            emptyTable : 'No borrowing records found.',
            processing : '<i class="fas fa-spinner fa-spin"></i> Loading...'
        }
    });
 // ─── Auto-apply filter from URL (e.g. from a notification click) ──
 var urlParams = new URLSearchParams(window.location.search);
 var initialFilter = urlParams.get('filter');

 if (initialFilter) {
     itemStatusFilter = initialFilter;
     $('select[name="status"]').val(initialFilter);
     table.ajax.reload();
 }
    // ─── Filters ─────────────────────────────────────────
    $('#filterForm').on('submit', function (e) {
        e.preventDefault();
        itemStatusFilter      = $('select[name="status"]').val()   || '';
        borrowingStatusFilter = $('select[name="condition"]').val()|| '';
        dateFromFilter         = $('input[name="date_from"]').val() || '';
        dateToFilter           = $('input[name="date_to"]').val()   || '';
        table.ajax.reload();
    });

    $('#btnReset').on('click', function () {
        $('select[name="status"]').val('');
        $('select[name="condition"]').val('');
        $('input[name="date_from"]').val('');
        $('input[name="date_to"]').val('');
        itemStatusFilter      = '';
        borrowingStatusFilter = '';
        dateFromFilter         = '';
        dateToFilter           = '';
        table.ajax.reload();
    });
    
         // ─── Add Borrowing Modal ───────────────────────────────
$('#btnAddBorrowing').click(function () {
    $('#borrowing_borrower_id').val('');
    $('#borrowing_item_id').val('');
    $('#borrowing_purpose').val('');
    $('#borrowing_due_date').val('');
    $('#unitCheckboxList').html('<span class="text-muted">Select an item first.</span>');
    $('#borrowingModal').modal('show');
});

// ─── Load available units when item changes ────────────
$(document).on('change', '#borrowing_item_id', function () {
    var itemId = $(this).val();
    var $list = $('#unitCheckboxList');

    if (!itemId) {
        $list.html('<span class="text-muted">Select an item first.</span>');
        return;
    }

    $list.html('<span class="text-muted">Loading units...</span>');

    $.get(BASE_URL + 'borrowing/get_available_units/' + itemId, function (res) {
        if (res.success && res.units.length > 0) {
            var html = '';
            res.units.forEach(function (unit) {
                html += '<div class="form-check">' +
                    '<input class="form-check-input unitCheckbox" type="checkbox" value="' + unit.id + '" id="unit_' + unit.id + '">' +
                    '<label class="form-check-label" for="unit_' + unit.id + '">' +
                    'Unit #' + unit.unit_no + ' (' + unit.item_condition + ')' +
                    '</label>' +
                    '</div>';
            });
            $list.html(html);
        } else {
            $list.html('<span class="text-danger">No available units for this item.</span>');
        }
    }, 'json');
});

// ─── Save Borrowing ─────────────────────────────────────
$('#btnSaveBorrowing').click(function () {
    var borrowerId = $('#borrowing_borrower_id').val();
    var purpose    = $('#borrowing_purpose').val().trim();
    var dueDate    = $('#borrowing_due_date').val();
    var unitIds    = [];

    $('.unitCheckbox:checked').each(function () {
        unitIds.push($(this).val());
    });

    if (!borrowerId) {
        Swal.fire('Warning', 'Please select a borrower.', 'warning');
        return;
    }
    if (unitIds.length === 0) {
        Swal.fire('Warning', 'Please select at least one unit.', 'warning');
        return;
    }
    if (!dueDate) {
        Swal.fire('Warning', 'Please set a due date.', 'warning');
        return;
    }

    $.post(BASE_URL + 'borrowing/store', {
        borrower_id : borrowerId,
        unit_ids    : unitIds,
        purpose     : purpose,
        due_date    : dueDate
    }, function (res) {
        if (res.success) {
            $('#borrowingModal').modal('hide');
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
// ─── Open Mark Returned Modal ───────────────────────────
$('#borrowingTable').on('click', '.btnReturn', function () {
    var id = $(this).data('id');
    $('#return_borrowing_item_id').val(id);
    $('#return_item_status').val('returned');
    $('#return_condition_after').val('good');
    $('#return_remarks').val('');
    $('#returnModal').modal('show');
});

// Toggle condition dropdown based on return status
$(document).on('change', '#return_item_status', function () {
    if ($(this).val() === 'returned') {
        $('#conditionAfterGroup').show();
    } else {
        $('#conditionAfterGroup').hide();
    }
});

// ─── Confirm Return ──────────────────────────────────────
$('#btnConfirmReturn').click(function () {
    var id = $('#return_borrowing_item_id').val();
    var itemStatus = $('#return_item_status').val();
    var conditionAfter = $('#return_condition_after').val();
    var remarks = $('#return_remarks').val().trim();

    $.post(BASE_URL + 'borrowing/mark_returned/' + id, {
        item_status      : itemStatus,
        condition_after   : conditionAfter,
        remarks           : remarks
    }, function (res) {
        if (res.success) {
            $('#returnModal').modal('hide');
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
});