<!-- 1. jQuery -->
<script src="<?= base_url('assets/js/jquery-3.7.1.min.js') ?>"></script>
<!-- 2. Bootstrap -->
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
<!-- 3. DataTables -->
<script src="<?= base_url('assets/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('assets/js/dataTables.responsive.min.js') ?>"></script>
<script src="<?= base_url('assets/js/responsive.bootstrap4.min.js') ?>"></script>
<!-- 4. SweetAlert -->
<script src="<?= base_url('assets/js/sweetalert2.all.min.js') ?>"></script>
<!-- 5. Chart.js: load one version only (dashboard.main.js uses 2.9.4 syntax) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<!-- 6. Select2 -->
<script src="<?= base_url('assets/js/select2.min.js') ?>"></script>

<script>
    var BASE_URL = <?= json_encode(base_url()) ?>;
</script>

<!-- Application scripts -->
<script src="<?= base_url('assets/js/config.js') ?>"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
<script src="<?= base_url('assets/js/modules/dashboard.main.js') ?>"></script>
<script src="<?= base_url('assets/js/modules/items.main.js') ?>"></script>
<script src="<?= base_url('assets/js/modules/itemized.main.js') ?>"></script>
<script src="<?= base_url('assets/js/modules/borrowing.main.js') ?>"></script>
<script src="<?= base_url('assets/js/modules/returns.main.js') ?>"></script>
<script src="<?= base_url('assets/js/modules/users.main.js') ?>"></script>
<script src="<?= base_url('assets/js/modules/notifications.main.js') ?>"></script>
<script src="<?= base_url('assets/js/modules/reservation.main.js') ?>"></script>
<script src="<?= base_url('assets/js/modules/summary_items.main.js') ?>"></script>
<script src="<?= base_url('assets/js/modules/summary_units.main.js') ?>"></script>
<script src="<?= base_url('assets/js/modules/summary_borrowed.main.js') ?>"></script>
<script src="<?= base_url('assets/js/modules/summary_overdue.main.js') ?>"></script>
<script src="<?= base_url('assets/js/modules/dashboard_activity.main.js') ?>"></script>
<script src="<?= base_url('assets/js/modules/profile.main.js') ?>"></script>


<?php if (isset($page_scripts)): ?>
    <?php foreach ($page_scripts as $script): ?>
        <script src="<?= base_url('assets/js/' . $script) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
