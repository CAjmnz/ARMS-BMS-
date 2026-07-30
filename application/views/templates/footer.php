<!-- BASE_URL for JS — uppercase, matches all module files -->



<!-- 1. jQuery FIRST -->
<script src="<?= base_url('assets/js/jquery-3.7.1.min.js') ?>"></script>
<!-- 2. Bootstrap (needs jQuery) -->
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
<!-- 3. DataTables (needs jQuery) -->
<script src="<?= base_url('assets/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('assets/js/dataTables.responsive.min.js') ?>"></script>
<script src="<?= base_url('assets/js/responsive.bootstrap4.min.js') ?>"></script>
<!-- 4. SweetAlert -->
<script src="<?= base_url('assets/js/sweetalert2.all.min.js') ?>"></script>
<!-- 5. Chart.js -->
<script src="<?= base_url('assets/js/chart.umd.min.js') ?>"></script>
<!-- Select2 (needs jQuery) -->
<script src="<?= base_url('assets/js/select2.min.js')?>"></script>

<script>
    var BASE_URL = "<?= base_url() ?>";
</script>

<!-- 6. App config (needs BASE_URL) -->
<script src="<?= base_url('assets/js/config.js') ?>"></script>
<!-- 7. App core -->
<script src="<?= base_url('assets/js/app.js') ?>"></script>
<!-- 8. Dashboard module -->
<script src="<?= base_url('assets/js/modules/dashboard.main.js') ?>"></script>
<!-- 9. items module-->
<script src="<?= base_url('assets/js/modules/items.main.js') ?>"></script>
<!-- 10. itemized module-->
<script src="<?= base_url('assets/js/modules/itemized.main.js') ?>"></script>
<!-- 11. borrowing module-->
<script src="<?= base_url('assets/js/modules/borrowing.main.js') ?>"></script>
<!-- 12. Returns module -->
<script src="<?= base_url('assets/js/modules/returns.main.js') ?>"></script>
<!-- 13. Users module -->
<script src="<?= base_url('assets/js/modules/users.main.js') ?>"></script>
<!-- 14. Notification module(global  — runs on every page)-->
<script src="<?= base_url('assets/js/modules/notifications.main.js') ?>"></script>
<!-- 15. Reservation module -->
<script src="<?= base_url('assets/js/modules/reservation.main.js') ?>"></script>



<?php if (isset($page_scripts)): ?>
    <?php foreach ($page_scripts as $script): ?>
        <script src="<?= base_url('assets/js/' . $script) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>