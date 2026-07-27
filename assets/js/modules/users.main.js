$(document).ready(function () {

    console.log("users.main.js loaded");

    var currentSearchRequest = null;
    var searchDebounceTimer = null;

    // ─── Init DataTable (roster of added system users) ─────
    var statusFilter = '';
    var roleFilter    = '';

    if ($.fn.DataTable.isDataTable('#usersTable')) {
        $('#usersTable').DataTable().destroy();
    }

    var table = $('#usersTable').DataTable({
        processing : true,
        serverSide : true,
        lengthMenu : [[5, 10, 25, 50], [5, 10, 25, 50]],
        pageLength : 10,
        ajax: {
            url  : BASE_URL + 'user/ajax_list',
            type : 'POST',
            data : function (d) {
                d.role           = roleFilter;
                d.account_status = statusFilter;
            }
        },
        columns: [
            { data: 0, orderable: false },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 },
            { data: 7 },
            { data: 8 },
            { data: 9, orderable: false }
        ],
        order: [[1, 'asc']],
        language: {
            emptyTable : 'No system users found.',
            processing : '<i class="fas fa-spinner fa-spin"></i> Loading...'
        }
    });

    $('#filterForm').on('submit', function (e) {
        e.preventDefault();
        roleFilter   = $('select[name="role"]').val()           || '';
        statusFilter = $('select[name="account_status"]').val() || '';
        table.ajax.reload();
    });

    $('#btnReset').on('click', function () {
        $('select[name="role"]').val('');
        $('select[name="account_status"]').val('');
        roleFilter   = '';
        statusFilter = '';
        table.ajax.reload();
    });

    // ─── Open modal via header "Add User" button ───────────
    $('#btnAddItem').off('click').on('click', function () {
        resetModal();
        $('#userModal').modal('show');

        $('#userModal').one('shown.bs.modal', function () {
            $('#modalEmployeeSearch').focus();
        });
    });

    function resetModal() {
        $('#system_user_id').val('');
        $('#employee_id').val('');
        $('#employee_name').val('');
        $('#employee_position').val('');
        $('#employee_dept').val('');
        $('#employee_company').val('');
        $('#employee_bunit').val('');
        $('#employee_type').val('');
        $('#employee_status').val('');
        $('#employee_photo').val('');
        $('#role').val('User');
        $('#account_status').val('Active');
        $('#modalEmployeeSearch').val('');
        $('#employeeSearchResults').hide().empty();
        $('#employee_photo_preview').hide();
        $('#employee_photo_placeholder').show();
        $('#btnSaveUser').prop('disabled', true);
    }

    // ─── Live search inside modal (debounced) ───────────────
    $('#modalEmployeeSearch').on('input', function () {
        var keyword = $(this).val().trim();

        clearTimeout(searchDebounceTimer);

        if (keyword.length < 2) {
            $('#employeeSearchResults').hide().empty();
            return;
        }

        searchDebounceTimer = setTimeout(function () {
            performSearch(keyword);
        }, 300);
    });

    $('#modalEmployeeSearch').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            clearTimeout(searchDebounceTimer);
            var keyword = $(this).val().trim();
            if (keyword.length >= 2) {
                performSearch(keyword, true);
            }
        }
    });

    function performSearch(keyword, autoSelectIfSingle) {
        if (currentSearchRequest !== null) {
            currentSearchRequest.abort();
        }

        var $results = $('#employeeSearchResults');
        $results.html('<div class="p-2 text-muted"><i class="fas fa-spinner fa-spin"></i> Searching...</div>').show();

        currentSearchRequest = $.ajax({
            url: BASE_URL + 'user/search_employee',
            type: 'GET',
            data: { q: keyword },
            dataType: 'json',
            cache: false,

            success: function (response) {
                var employees = (response.data && response.data.employee) ? response.data.employee : [];

                if (employees.length === 0) {
                    $results.html('<div class="p-2 text-muted">No employee found.</div>').show();
                    return;
                }

                if (autoSelectIfSingle && employees.length === 1) {
                    selectEmployee(employees[0]);
                    return;
                }

                var html = '';
                employees.forEach(function (emp) {
                    html += '<div class="employeeResultRow p-2" style="cursor:pointer; border-bottom:1px solid #f0f0f0;" data-emp=\'' + JSON.stringify(emp).replace(/'/g, "&#39;") + '\'>' +
                        '<strong>' + emp.employee_name + '</strong><br>' +
                        '<small class="text-muted">' + (emp.employee_id || '') + ' &middot; ' + (emp.employee_position || '') + '</small>' +
                        '</div>';
                });
                $results.html(html).show();
            },

            error: function (xhr, status) {
                if (status !== 'abort') {
                    $results.html('<div class="p-2 text-danger">Search failed. Please try again.</div>').show();
                }
            },

            complete: function () {
                currentSearchRequest = null;
            }
        });
    }

    $(document).on('click', '.employeeResultRow', function () {
        var emp = JSON.parse($(this).attr('data-emp').replace(/&#39;/g, "'"));
        selectEmployee(emp);
    });

    function selectEmployee(emp) {
        $('#employee_id').val(emp.employee_id);
        $('#employee_name').val(emp.employee_name);
        $('#employee_position').val(emp.employee_position);
        $('#employee_dept').val(emp.employee_dept);
        $('#employee_company').val(emp.employee_company || '');
        $('#employee_bunit').val(emp.employee_bunit);
        $('#employee_type').val(emp.employee_type);
        $('#employee_status').val(emp.employee_status);
        $('#employee_photo').val(emp.employee_photo || '');

        $('#modalEmployeeSearch').val(emp.employee_name);
        $('#employeeSearchResults').hide().empty();

        var photoUrl = resolvePhotoUrl(emp.employee_photo);
        if (photoUrl) {
            $('#employee_photo_preview').attr('src', photoUrl).show();
            $('#employee_photo_placeholder').hide();
            $('#employee_photo_preview').off('error').on('error', function () {
                $(this).hide();
                $('#employee_photo_placeholder').show();
            });
        } else {
            $('#employee_photo_preview').hide();
            $('#employee_photo_placeholder').show();
        }

        $('#btnSaveUser').prop('disabled', false);
    }

    function resolvePhotoUrl(relativePath) {
        if (!relativePath) return null;
        var apiBase = 'http://172.16.161.34/api/rms/monitoring/';
        var cleanPath = relativePath.replace(/^(\.\.\/)+/, '');
        return apiBase + cleanPath;
    }

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#modalEmployeeSearch, #employeeSearchResults').length) {
            $('#employeeSearchResults').hide();
        }
    });

    // ─── Save User ────────────────────────────────────────────
    $('#btnSaveUser').off('click').on('click', function () {
        var payload = {
            employee_id       : $('#employee_id').val(),
            employee_name     : $('#employee_name').val(),
            employee_position : $('#employee_position').val(),
            employee_dept     : $('#employee_dept').val(),
            employee_company  : $('#employee_company').val(),
            employee_bunit    : $('#employee_bunit').val(),
            employee_type     : $('#employee_type').val(),
            employee_status   : $('#employee_status').val(),
            employee_photo    : $('#employee_photo').val(),
            role              : $('#role').val(),
            account_status    : $('#account_status').val()
        };

        if (!payload.employee_id) {
            Swal.fire('Warning', 'Please select an employee first.', 'warning');
            return;
        }

        $.post(BASE_URL + 'user/save_user', payload, function (res) {
            if (res.success) {
                $('#userModal').modal('hide');
                Swal.fire('Success', res.message, 'success').then(function () {
                    table.ajax.reload();
                });
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }, 'json')
        .fail(function (xhr) {
            console.log('Error:', xhr.responseText);
            Swal.fire('Error', 'Something went wrong.', 'error');
        });
    });

    // ─── Edit ────────────────────────────────────────────
    $('#usersTable').on('click', '.btnEditUser', function () {
        var id = $(this).data('id');
        $.get(BASE_URL + 'user/get/' + id, function (res) {
            if (res.success) {
                $('#edit_user_id').val(id);
                $('#edit_employee_name').val(res.item.employee_name);
                $('#edit_role').val(res.item.role);
                $('#edit_account_status').val(res.item.account_status);
                $('#editUserModal').modal('show');
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }, 'json');
    });

    $('#btnUpdateUser').on('click', function () {
        var id = $('#edit_user_id').val();
        $.post(BASE_URL + 'user/update_user/' + id, {
            role           : $('#edit_role').val(),
            account_status : $('#edit_account_status').val()
        }, function (res) {
            if (res.success) {
                $('#editUserModal').modal('hide');
                Swal.fire('Success', res.message, 'success').then(function () {
                    table.ajax.reload();
                });
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }, 'json');
    });

    // ─── Delete ──────────────────────────────────────────
    $('#usersTable').on('click', '.btnDeleteUser', function () {
        var id   = $(this).data('id');
        var name = $(this).data('name');

        Swal.fire({
            title: 'Delete ' + name + '?',
            text: 'This will remove their system access.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it'
        }).then(function (result) {
            if (result.isConfirmed) {
                $.post(BASE_URL + 'user/delete/' + id, function (res) {
                    if (res.success) {
                        Swal.fire('Deleted!', res.message, 'success').then(function () {
                            table.ajax.reload();
                        });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }, 'json');
            }
        });
    });

});