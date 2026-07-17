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