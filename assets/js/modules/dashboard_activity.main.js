$(document).ready(function () {
    if ($('#activityTable').length === 0) return;

    $('#activityTable').DataTable({
        processing: true,
        serverSide: true,
        lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
        pageLength: 10,
        ajax: {
            url: BASE_URL + 'dashboard/activity_ajax_list',
            type: 'POST'
        },
        columns: [
            { data: 0 },
            { data: 1, orderable: false },
            { data: 2 },
            { data: 3, orderable: false }
        ],
        order: [[0, 'desc']],
        language: {
            emptyTable: 'No recent activity.',
            processing: '<i class="fas fa-spinner fa-spin"></i> Loading...'
        }
    });
});