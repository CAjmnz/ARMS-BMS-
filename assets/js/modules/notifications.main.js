$(document).ready(function () {

    // Filter pills — only runs on the notifications page
    if ($('#notifList').length > 0) {
        $('.notifPill').on('click', function () {
            $('.notifPill').removeClass('active');
            $(this).addClass('active');

            var filter = $(this).data('filter');

            if (filter === 'all') {
                $('.notifCard').show();
            } else {
                $('.notifCard').hide();
                $('.notifCard[data-type="' + filter + '"]').show();
            }
        });
    }
   // ─── Open Details Modal on card click ───────────────
   $(document).on('click', '.notifCardClickable', function () {
    $('#nd_borrower').text($(this).data('borrower'));
    $('#nd_id_number').text($(this).data('id-number'));
    $('#nd_item').text($(this).data('item') + '#' + $(this).data('unit-no'));
    $('#nd_unit_no').text($(this).data('unit_no'));
    $('#nd_due_date').text($(this).data('due'));
    $('#nd_status').text($(this).data('status'));

    $('#btnReturnFromNotif').data('id',$(this).data('id'));
    $('#notifDetailsModal').modal('show')

   });
       // ─── Open Return Modal from Details Modal ───────────
       $('#btnReturnFromNotif').on('click', function () {
        var id = $(this).data('id');
        $('#notifDetailsModal').modal('hide');

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

    // ─── Confirm Return ──────────────────────────────────
    $('#btnConfirmReturn').on('click', function () {
        var id = $('#return_borrowing_item_id').val();
        var itemStatus = $('#return_item_status').val();
        var conditionAfter = $('#return_condition_after').val();
        var remarks = $('#return_remarks').val().trim();

        $.post(BASE_URL + 'borrowing/mark_returned/' + id, {
            item_status     : itemStatus,
            condition_after : conditionAfter,
            remarks         : remarks
        }, function (res) {
            if (res.success) {
                $('#returnModal').modal('hide');
                Swal.fire('Success', res.message, 'success').then(function () {
                    location.reload(); // refresh the notifications list so the returned item disappears
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
    // (existing bell badge polling code stays below, unchanged)
    function refreshNotifBadge() {
        $.get(BASE_URL + 'notifications/get_count', function (res) {
            if (res.success) {
                var $badge = $('#notifBadge');
                if (res.count > 0) {
                    $badge.text(res.count > 99 ? '99+' : res.count).show();
                } else {
                    $badge.hide();
                }
            }
        }, 'json');
    }

    refreshNotifBadge();
    setInterval(refreshNotifBadge, 60000);

});