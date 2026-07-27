$(document).ready(function () {

    console.log("users.main.js loaded");

    var currentSearchRequest = null;
    var searchDebounceTimer = null;

    // ─── Open modal via header "Add User" button ───────────
    $('#btnAddItem').off('click').on('click', function () {
        resetModal();
        $('#userModal').modal('show');

        // Auto-focus the search field once the modal is visible
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

        // Debounce — wait 300ms after typing stops before searching
        searchDebounceTimer = setTimeout(function () {
            performSearch(keyword);
        }, 300);
    });

    // ─── Barcode scanners send an Enter keypress after scanning ──
    $('#modalEmployeeSearch').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            clearTimeout(searchDebounceTimer);
            var keyword = $(this).val().trim();
            if (keyword.length >= 2) {
                performSearch(keyword, true); // true = auto-select if exactly one match (typical barcode behavior)
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

                // Barcode scan with exactly one exact match — auto-select immediately
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

    // ─── Click a result row to select that employee ─────────
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

    // Hide the dropdown when clicking elsewhere in the modal
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
                Swal.fire('Success', res.message, 'success');
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }, 'json')
        .fail(function (xhr) {
            console.log('Error:', xhr.responseText);
            Swal.fire('Error', 'Something went wrong.', 'error');
        });
    });

});