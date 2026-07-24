$(document).ready(function () {

    console.log("users.main.js loaded");

    $('#btnSearchEmployee').on('click', function (e) {

        e.preventDefault();

        console.log("Search button clicked");

        let keyword = $('#searchEmployee').val().trim();

        if (keyword === '') {
            Swal.fire('Warning', 'Please enter an employee name.', 'warning');
            return;
        }

        searchEmployee(keyword);

    });

});

function searchEmployee(keyword)
{
    $.ajax({
        url: BASE_URL + 'user/search_employee',
        type: 'GET',
        data: {
            q: keyword
        },
        dataType: 'json',

        success: function(response) {

            let tbody = $('#usersTable tbody');
        
            tbody.empty();
        
            let employees = response.data.employee;
        
            if (employees.length === 0) {
        
                tbody.append(`
                    <tr>
                        <td colspan="9" class="text-center">
                            No employee found.
                        </td>
                    </tr>
                `);
        
                return;
            }
        
            $.each(employees, function(index, emp){
        
                tbody.append(`
                    <tr>
        
                        <td>${index + 1}</td>
        
                        <td>
                            <strong>${emp.employee_name}</strong><br>
                            <small>${emp.employee_position}</small>
                        </td>
        
                        <td>${emp.employee_id}</td>
        
                        <td>${emp.employee_dept}</td>
        
                        <td>
                            <span class="badge badge-success">
                                ${emp.employee_status}
                            </span>
                        </td>
        
                        <td>${emp.employee_bunit}</td>
        
                        <td>${emp.employee_type}</td>
        
                        <td>
                            <span class="badge badge-primary">
                                User
                            </span>
                        </td>
        
                        <td>
                            <button class="btn btn-success btn-sm">
                                Add
                            </button>
                        </td>
        
                    </tr>
                `);
        
            });
        
        }
       
    });
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

    $('#userModal').modal('show');
});

// ─── Save User ────────────────────────────────────────────
$('#btnSaveUser').on('click', function () {
    var payload = {
        employee_id       : $('#employee_id').val(),
        employee_name      : $('#employee_name').val(),
        employee_position  : $('#employee_position').val(),
        employee_dept      : $('#employee_dept').val(),
        employee_company   : $('#employee_company').val(),
        employee_bunit     : $('#employee_bunit').val(),
        employee_type      : $('#employee_type').val(),
        employee_status    : $('#employee_status').val(),
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
}