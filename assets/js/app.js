(function () {

    // ── Clock ─────────────────────────────────────────────────────

    function updateClock() {
        var el = document.getElementById('topbarClock');
        if (!el) return;
        el.textContent = new Date().toLocaleTimeString([], {
            hour:   '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }
    updateClock();
    setInterval(updateClock, 1000);
    // ── Sidebar toggle ────────────────────────────────────────────
    var sidebar     = document.getElementById('sidebar');
    var topbar      = document.getElementById('topbar');
    var mainContent = document.getElementById('main-content');
    var toggleBtn   = document.getElementById('sidebarToggle');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            if (topbar)      topbar.classList.toggle('expanded');
            if (mainContent) mainContent.classList.toggle('expanded');
        });
    }

    // ── Auto-dismiss alerts ───────────────────────────────────────
    setTimeout(function () {
        if (typeof $ !== 'undefined') {
            $('.alert').fadeOut('slow');
        }
    }, 4000);

})();
$(document).ready(function () {

    // Sidebar Toggle
    $('#sidebarToggle').on('click', function () {
        $('#sidebar').toggleClass('sidebar-collapsed');
        $('#topbar').toggleClass('topbar-expanded');
        $('#main-content').toggleClass('main-expanded');
    });

});