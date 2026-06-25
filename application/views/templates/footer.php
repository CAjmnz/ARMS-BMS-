<!-- BASE_URL for JS — uppercase, matches all module files -->
<script>
    var BASE_URL = "<?= base_url() ?>";
</script>

<!-- 1. jQuery FIRST -->
<script src="<?= base_url('assets/js/jquery-3.7.1.min.js') ?>"></script>
<!-- 2. Bootstrap (needs jQuery) -->
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
<!-- 3. DataTables (needs jQuery) -->
<script src="<?= base_url('assets/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/js/dataTables.bootstrap4.min.js') ?>"></script>
<!-- 4. SweetAlert -->
<script src="<?= base_url('assets/js/sweetalert2.all.min.js') ?>"></script>
<!-- 5. Chart.js -->
<script src="<?= base_url('assets/js/chart.umd.min.js') ?>"></script>
<!-- 6. App config (needs BASE_URL) -->
<script src="<?= base_url('assets/js/config.js') ?>"></script>
<!-- 7. App core -->
<script src="<?= base_url('assets/js/app.js') ?>"></script>
<!-- 8. Dashboard module -->
<script src="<?= base_url('assets/js/modules/dashboard.main.js') ?>"></script>
<!-- 9. Users module -->
<script src="<?= base_url('assets/js/modules/users.main.js') ?>"></script>

<?php if (isset($page_scripts)): ?>
    <?php foreach ($page_scripts as $script): ?>
        <script src="<?= base_url('assets/js/' . $script) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>