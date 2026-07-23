$(document).ready(function () {

    if ($('#reservationTable').length === 0) {
        return;
    }

    var statusFilter   = '';
    var dateFromFilter = '';
    var dateToFilter   = '';

    if ($.fn.DataTable.isDataTable('#reservationTable')) {
        $('#reservationTable').DataTable().destroy();
    }

    var table = $('#reservationTable').DataTable({
        processing : true,
        serverSide : true,
        responsive : true,
        autoWidth  : false,
        lengthMenu : [[5, 10, 25, 50], [5, 10, 25, 50]],
        pageLength : 10,
        ajax: {
            url  : BASE_URL + 'reservation/ajax_list',
            type : 'POST',
            data : function (d) {
                d.reservation_status = statusFilter;
                d.date_from          = dateFromFilter;
                d.date_to            = dateToFilter;
            }
        },
        columns: [
            { data: 0, orderable: false },  // #
            { data: 1 },                    // Reservation's Id
            { data: 2 },                    // Borrower's Id
            { data: 3 },                    // Borrower's name
            { data: 4 },                    // Unit Id
            { data: 5 },                    // Item Name
            { data: 6 },                    // Reservation Date
            { data: 7 },                    // Pick up date
            { data: 8 },                    // Return Date
            { data: 9 },                    // Purpose
            { data: 10 },                   // Status
            { data: 11 },                   // Reserved by
            { data: 12, orderable: false }  // Action
        ],
        order: [[7, 'asc']],
        language: {
            emptyTable : 'No reservations found.',
            processing : '<i class="fas fa-spinner fa-spin"></i> Loading...'
        }
    });

    // ─── Filters ─────────────────────────────────────────
    $('#filterForm').on('submit', function (e) {
        e.preventDefault();
        statusFilter   = $('select[name="reservation_status"]').val() || '';
        dateFromFilter = $('input[name="date_from"]').val()           || '';
        dateToFilter   = $('input[name="date_to"]').val()             || '';
        table.ajax.reload();
    });

    $('#btnReset').on('click', function () {
        $('select[name="reservation_status"]').val('');
        $('input[name="date_from"]').val('');
        $('input[name="date_to"]').val('');
        statusFilter   = '';
        dateFromFilter = '';
        dateToFilter   = '';
        table.ajax.reload();
    });

// ─── Open Add Modal ──────────────────────────────────
$('#btnAddReservation').click(function () {
    $('#res_borrower_id').val('');
    $('#res_item_id').val('');
    $('#res_reservation_date').val('');
    $('#res_due_date').val('');
    $('#res_purpose').val('');
    $('#resUnitCheckboxList').html('<span class="text-muted">Select an item first.</span>');

    // Prevent selecting a reservation/due date in the past
    var now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset()); // adjust for local time
    var minDateTime = now.toISOString().slice(0, 16); // format: YYYY-MM-DDTHH:MM
    $('#res_reservation_date').attr('min', minDateTime);
    $('#res_due_date').attr('min', minDateTime);

    $('#reservationModal').modal('show');
});

    // ─── Load available units ────────────────────────────
    $(document).on('change', '#res_item_id', function () {
        var itemId = $(this).val();
        var $list = $('#resUnitCheckboxList');

        if (!itemId) {
            $list.html('<span class="text-muted">Select an item first.</span>');
            return;
        }

        $list.html('<span class="text-muted">Loading units...</span>');

        $.get(BASE_URL + 'reservation/get_available_units/' + itemId, function (res) {
            if (res.success && res.units.length > 0) {
                var html = '';
                res.units.forEach(function (unit) {
                    html += '<div class="form-check">' +
                        '<input class="form-check-input resUnitCheckbox" type="checkbox" value="' + unit.id + '" id="res_unit_' + unit.id + '">' +
                        '<label class="form-check-label" for="res_unit_' + unit.id + '">' +
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

    // ─── Save Reservation ─────────────────────────────────
    $('#btnSaveReservation').click(function () {
        var borrowerId = $('#res_borrower_id').val();
        var reservationDate = $('#res_reservation_date').val();
        var dueDate = $('#res_due_date').val();
        var purpose = $('#res_purpose').val().trim();
        var unitIds = [];

        $('.resUnitCheckbox:checked').each(function () {
            unitIds.push($(this).val());
        });

        if (!borrowerId || unitIds.length === 0 || !reservationDate || !dueDate) {
            Swal.fire('Warning', 'Please fill in all required fields and select at least one unit.', 'warning');
            return;
        }

        var now = new Date();
        var resDate = new Date(reservationDate);
        var dueDateObj = new Date(dueDate);

        if(resDate < now){
            Swal.fire('Warning', 'Reservation date cannot be in the past.','warning');
            return;
        }

        if(dueDateObj < resDate){
            Swal.fire('Warning', 'Return date cannot be earlier than the reservation date.',
                'warning');
                return;
        }

        $.post(BASE_URL + 'reservation/store', {
            borrower_id      : borrowerId,
            unit_ids         : unitIds,
            reservation_date : reservationDate,
            due_date         : dueDate,
            purpose          : purpose
        }, function (res) {
            if (res.success) {
                $('#reservationModal').modal('hide');
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

    // ─── Approve ──────────────────────────────────────────
    $('#reservationTable').on('click', '.btnApprove', function () {
        var id = $(this).data('id');
        $.post(BASE_URL + 'reservation/approve/' + id, function (res) {
            if (res.success) {
                Swal.fire('Success', res.message, 'success').then(function () { table.ajax.reload(); });
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }, 'json');
    });

    // ─── Reject ───────────────────────────────────────────
    $('#reservationTable').on('click', '.btnReject', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Reject this reservation?',
            text: 'The reserved unit(s) will be released back to available.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, reject it'
        }).then(function (result) {
            if (result.isConfirmed) {
                $.post(BASE_URL + 'reservation/reject/' + id, function (res) {
                    if (res.success) {
                        Swal.fire('Rejected', res.message, 'success').then(function () { table.ajax.reload(); });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }, 'json');
            }
        });
    });

    // ─── Release ──────────────────────────────────────────
    $('#reservationTable').on('click', '.btnRelease', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Release this reservation?',
            text: 'This will convert it into an active borrowing.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, release it'
        }).then(function (result) {
            if (result.isConfirmed) {
                $.post(BASE_URL + 'reservation/release/' + id, function (res) {
                    if (res.success) {
                        Swal.fire('Released', res.message, 'success').then(function () { table.ajax.reload(); });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }, 'json');
            }
        });
    });

});