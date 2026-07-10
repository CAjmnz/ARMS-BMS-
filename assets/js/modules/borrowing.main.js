$(document).ready(function () {

    var itemStatusFilter      = '';
    var borrowingStatusFilter = '';
    var dateFromFilter        = '';
    var dateToFilter          = '';

    // Destroy if already initialized
    if ($.fn.DataTable.isDataTable('#borrowingTable')) {
        $('#borrowingTable').DataTable().destroy();
    }

    // Init server-side DataTable
    var table = $('#borrowingTable').DataTable({
        processing : true,
        serverSide : true,
        lengthMenu : [[5, 10, 25, 50], [5, 10, 25, 50]],
        pageLength : 10,
        ajax: {
            url  : BASE_URL + 'borrowing/ajax_list',
            type : 'POST',
            data : function (d) {
                d.item_status      = itemStatusFilter;
                d.borrowing_status = borrowingStatusFilter;
                d.date_from        = dateFromFilter;
                d.date_to          = dateToFilter;
            }
        },
        columns: [
            { data: 0, orderable: false },  // #
            { data: 1 },                    // Borrower's Id
            { data: 2 },                    // Borrower's name
            { data: 3 },                    // Item Name
            { data: 4 },                    // Category
            { data: 5, orderable: false },  // Quantity
            { data: 6 },                    // Condition Before Borrowing
            { data: 7 },                    // Borrowed Date
            { data: 8 },                    // Due date
            { data: 9, orderable: false },  // Borrowing status
            { data: 10 },                   // Released by
            { data: 11, orderable: false }  // Action
        ],
        order: [[7, 'desc']],
        language: {
            emptyTable : 'No borrowing records found.',
            processing : '<i class="fas fa-spinner fa-spin"></i> Loading...'
        }
    });

    // ─── Filters ─────────────────────────────────────────
    $('#filterForm').on('submit', function (e) {
        e.preventDefault();
        itemStatusFilter      = $('select[name="status"]').val()   || '';
        borrowingStatusFilter = $('select[name="condition"]').val()|| '';
        dateFromFilter         = $('input[name="date_from"]').val() || '';
        dateToFilter           = $('input[name="date_to"]').val()   || '';
        table.ajax.reload();
    });

    $('#btnReset').on('click', function () {
        $('select[name="status"]').val('');
        $('select[name="condition"]').val('');
        $('input[name="date_from"]').val('');
        $('input[name="date_to"]').val('');
        itemStatusFilter      = '';
        borrowingStatusFilter = '';
        dateFromFilter         = '';
        dateToFilter           = '';
        table.ajax.reload();
    });

});