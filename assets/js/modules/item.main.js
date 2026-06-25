var BASE_URL = "<?= base_url() ?>";

$(document).ready(function () {

    // Init DataTable
    $('#itemsTable').DataTable({
        responsive: true,
        order: [[0, 'asc']]
    });

    // Open Add Modal
    $('#btnAddItem').click(function () {
        $('#modalTitle').text('Add Item');
        $('#item_id').val('');
        $('#barcode').val('');
        $('#name').val('');
        $('#category').val('');
        $('#quantity').val('');
        $('#status').val('available');
        $('#itemModal').modal('show');
    });

    // Open Edit Modal
    $(document).on('click', '.btnEdit', function () {
        var id = $(this).data('id');
        $.get(BASE_URL + 'items/get/' + id, function (res) {
            if (res.success) {
                $('#modalTitle').text('Edit Item');
                $('#item_id').val(res.item.id);
                $('#barcode').val(res.item.barcode);
                $('#name').val(res.item.name);
                $('#category').val(res.item.category);
                $('#quantity').val(res.item.quantity);
                $('#status').val(res.item.status);
                $('#itemModal').modal('show');
            }
        }, 'json');
    });

    // Save (Add or Edit)
    $('#btnSave').click(function () {
        var id       = $('#item_id').val();
        var barcode  = $('#barcode').val().trim();
        var name     = $('#name').val().trim();
        var category = $('#category').val().trim();
        var quantity = $('#quantity').val();
        var status   = $('#status').val();

        // Basic validation
        if (!barcode || !name || !category || quantity === '') {
            Swal.fire('Warning', 'Please fill in all fields.', 'warning');
            return;
        }

        var url = id ? BASE_URL + 'items/update/' + id : BASE_URL + 'items/store';

        $.post(url, {
            barcode  : barcode,
            name     : name,
            category : category,
            quantity : quantity,
            status   : status
        }, function (res) {
            if (res.success) {
                $('#itemModal').modal('hide');
                Swal.fire('Success', res.message, 'success').then(function () {
                    location.reload();
                });
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }, 'json');
    });

    // Delete
    $(document).on('click', '.btnDelete', function () {
        var id   = $(this).data('id');
        var name = $(this).data('name');

        Swal.fire({
            title: 'Delete ' + name + '?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then(function (result) {
            if (result.isConfirmed) {
                $.post(BASE_URL + 'items/delete/' + id, function (res) {
                    if (res.success) {
                        Swal.fire('Deleted!', res.message, 'success').then(function () {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }, 'json');
            }
        });
    });

});
