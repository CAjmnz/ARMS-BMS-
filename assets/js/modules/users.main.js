$(document).ready(function () {
    //Resolve the API's relative photo path into a full, usable URL 
    function resolvePhotoUrl(relativePath){
         if (!relativePath) return null;

         var apiBase = 'http://172.16.161.34/api/rms/monitoring/';

         //Strip leading "../" segments, then rebuild against the API's base path
         var cleanPath = relativePath.replace(/^(\.\.\/)+/, '');

         return apiBase + cleanPath;
    }
    

    console.log("users.main.js loaded");

    // Track the in-flight request so we can cancel stale ones
    var currentSearchRequest = null;

    // Use .off() before .on() to guarantee only ONE handler is ever bound,
    // even if this script somehow runs more than once on the page.
    $('#btnSearchEmployee').off('click').on('click', function (e) {
        e.preventDefault();
        console.log("Search button clicked");

        let keyword = $('#searchEmployee').val().trim();

        if (keyword === '') {
            Swal.fire('Warning', 'Please enter an employee name.', 'warning');
            return;
        }

        searchEmployee(keyword);
    });

    // Also allow pressing Enter in the search box
    $('#searchEmployee').off('keypress').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#btnSearchEmployee').trigger('click');
        }
    });

    function searchEmployee(keyword) {

        // If a previous search is still in flight, cancel it —
        // this is what prevents an old, slow response from overwriting
        // a newer, faster one.
        if (currentSearchRequest !== null) {
            currentSearchRequest.abort();
        }

        var tbody = $('#usersTable tbody');
        tbody.html('<tr><td colspan="9" class="text-center"><i class="fas fa-spinner fa-spin"></i> Searching...</td></tr>');

        currentSearchRequest = $.ajax({
            url: BASE_URL + 'user/search_employee',
            type: 'GET',
            data: { q: keyword },
            dataType: 'json',
            cache: false,   // ← prevents the browser from serving a stale cached GET response

            success: function (response) {
                console.log("API response for '" + keyword + "':", response);

                tbody.empty();

                var employees = (response.data && response.data.employee) ? response.data.employee : [];

                if (employees.length === 0) {
                    tbody.append('<tr><td colspan="9" class="text-center">No employee found.</td></tr>');
                    return;
                }

                $.each(employees, function (index, emp) {
                    tbody.append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>
                                <strong>${emp.employee_name}</strong><br>
                                <small>${emp.employee_position || ''}</small>
                            </td>
                            <td>${emp.employee_id || '-'}</td>
                            <td>${emp.employee_dept || '-'}</td>
                            <td>
                                <span class="badge badge-success">${emp.employee_status || '-'}</span>
                            </td>
                            <td>${emp.employee_bunit || '-'}</td>
                            <td>${emp.employee_type || '-'}</td>
                            <td>
                                <span class="badge badge-primary">User</span>
                            </td>
                            <td>
                                <button class="btn btn-success btn-sm btnAddEmployee" data-emp='${JSON.stringify(emp)}'>
                                    Add
                                </button>
                            </td>
                        </tr>
                    `);
                });
            },

            error: function (xhr, status) {
                // "abort" happens on purpose when we cancel a stale request — don't show an error for that
                if (status !== 'abort') {
                    console.log('Search error:', xhr.responseText);
                    tbody.empty();
                    tbody.append('<tr><td colspan="9" class="text-center text-danger">Search failed. Please try again.</td></tr>');
                }
            },

            complete: function () {
                currentSearchRequest = null;
            }
        });
    }

    // ─── Add button click — open modal, auto-fill fields ────
    $(document).on('click', '.btnAddEmployee', function () {
        var emp = JSON.parse($(this).attr('data-emp'));

        $('#system_user_id').val('');
        $('#employee_id').val(emp.employee_id);
        $('#employee_name').val(emp.employee_name);
        $('#employee_position').val(emp.employee_position);
        $('#employee_dept').val(emp.employee_dept);
        $('#employee_company').val(emp.employee_company || '');
        $('#employee_bunit').val(emp.employee_bunit);
        $('#employee_type').val(emp.employee_type);
        $('#employee_status').val(emp.employee_status);
        $('#role').val('User');
        $('#account_status').val('Active');

        var photoUrl = resolvePhotoUrl(emp.employee_photo);
        if (photoUrl) {
            $('#employee_photo_preview').attr('src',photoUrl).show();
            $('#employee_photo_placeholder').hide();

            //if the image fail to load (broken path ), fall back to placeholder
            $('#employee_photo_preview').off('error').on('error',function (){
                $(this).hide();
                $('#employee_photo_placeholder').show();
            });
        } else {
            $('#employee_photo_preview').hide();

            $('#employee_photo_placeholder').show();
        }

        $('#userModal').modal('show');
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
            role               : $('#role').val(),
            account_status     : $('#account_status').val()
        };

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