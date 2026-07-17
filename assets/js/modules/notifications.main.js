$(document).ready(function(){

    function refreshNotifBadge(){
        $.get(BASE_URL + 'notifications/get_count',function(res)) {
            if(res.success)
        }
    }
})$(document).ready(function () {

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

    // Run immediately on page load
    refreshNotifBadge();

    // Poll every 60 seconds
    setInterval(refreshNotifBadge, 60000);

});