$(document).ready(function () {
    if ($('#summaryItemsTable').length === 0) return;

    $('#summaryItemsTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ajax: { url: BASE_URL + 'items/ajax_list', type: 'POST' },
        columns: [
            { data: 0, orderable: false }, { data: 1 }, { data: 2 }, { data: 3 },
            { data: 4 }, { data: 5 }, { data: 6 }, { data: 7 }, { data: 8 },
            { data: 9 }, { data: 10 }, { data: 11 }, { data: 12 }, { data: 13 }
        ],
        columnDefs: [{ targets: 13, visible: false }], // hide Action column
        order: [[0, 'desc']],
        language: { emptyTable: 'No items found.' }
    });
});