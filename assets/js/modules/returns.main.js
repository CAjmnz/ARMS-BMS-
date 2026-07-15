$(document).ready(function () {

    if ($('#returnsTable').length === 0) {
        return;
    }

    var statusFilter   = '';
    var dateFromFilter = '';
    var dateToFilter   = '';

    if ($.fn.DataTable.isDataTable('#returnsTable')) {
        $('#returnsTable').DataTable().destroy();
    }

    var table = $('#returnsTable').DataTable({
        processing : true,
        serverSide : true,
        lengthMenu : [[5, 10, 25, 50], [5, 10, 25, 50]],
        pageLength : 10,
        ajax: {
            url  : BASE_URL + 'returns/ajax_list',
            type : 'POST',
            data : function (d) {
                d.item_status = statusFilter;
                d.date_from   = dateFromFilter;
                d.date_to     = dateToFilter;
            }
        },
        columns: [
            { data: 0, orderable: false },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6, orderable: false },
            { data: 7 },
            { data: 8 },
            { data: 9 },
            { data: 10 },
            { data: 11, orderable: false },
            { data: 12 },
            { data: 13 },
            { data: 14 },
            { data: 15, orderable: false }
        ],
        order: [[10, 'desc']],
        language: {
            emptyTable : 'No returned items found.',
            processing : '<i class="fas fa-spinner fa-spin"></i> Loading...'
        }
    });

    $('#filterForm').on('submit', function (e) {
        e.preventDefault();
        statusFilter   = $('select[name="item_status"]').val() || '';
        dateFromFilter = $('input[name="date_from"]').val()    || '';
        dateToFilter   = $('input[name="date_to"]').val()      || '';
        table.ajax.reload();
    });

    $('#btnReset').on('click', function () {
        $('select[name="item_status"]').val('');
        $('input[name="date_from"]').val('');
        $('input[name="date_to"]').val('');
        statusFilter   = '';
        dateFromFilter = '';
        dateToFilter   = '';
        table.ajax.reload();
    });

});