(function () {

    // ── Clock ──────────────────────────────────────────────────────
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

    // ── Sidebar Toggle ─────────────────────────────────────────────
    var sidebar     = document.getElementById('sidebar');
    var topbar      = document.getElementById('topbar');
    var mainContent = document.getElementById('main-content');
    var toggleBtn   = document.getElementById('sidebarToggle');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function () {
            var isCollapsed = sidebar.classList.toggle('sidebar-collapsed');

            if (isCollapsed) {
                // Collapsed — icon only (60px)
                if (topbar)      topbar.style.left            = '60px';
                if (mainContent) mainContent.style.marginLeft = '60px';
            } else {
                // Expanded — full sidebar (230px)
                if (topbar)      topbar.style.left            = '230px';
                if (mainContent) mainContent.style.marginLeft = '230px';
            }
        });
    }

    // ── Auto-dismiss alerts ────────────────────────────────────────
    setTimeout(function () {
        if (typeof $ !== 'undefined') {
            $('.alert').fadeOut('slow');
        }
    }, 4000);

})();