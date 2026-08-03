$(document).ready(function () {
    if ($('#summaryBorrowedTable').length === 0) return;

    $('#summaryBorrowedTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ajax: { url: BASE_URL + 'borrowing/ajax_list', type: 'POST' },
        columns: [
            { data: 0, orderable: false }, { data: 1 }, { data: 2 }, { data: 3 },
            { data: 4 }, { data: 5 }, { data: 6, orderable: false }, { data: 7 },
            { data: 8 }, { data: 9 }, { data: 10 }, { data: 11 }, { data: 12 }
        ],
        columnDefs: [{ targets: 12, visible: false }], // hide Action column
        order: [[8, 'desc']],
        language: { emptyTable: 'Nothing is currently borrowed.' }
    });
});