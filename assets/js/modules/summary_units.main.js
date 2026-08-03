$(document).ready(function () {
    if ($('#summaryUnitsTable').length === 0) return;

    $('#summaryUnitsTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ajax: { url: BASE_URL + 'itemized/ajax_list', type: 'POST' },
        columns: [
            { data: 0, orderable: false, visible: false }, { data: 1 }, { data: 2 },
            { data: 3 }, { data: 4 }, { data: 5 }, { data: 6 }, { data: 7 }, { data: 8 }, { data: 9 }
        ],
        columnDefs: [{ targets: 9, visible: false }], // hide Action column
        order: [[1, 'asc']],
        language: { emptyTable: 'No units found.' }
    });
});