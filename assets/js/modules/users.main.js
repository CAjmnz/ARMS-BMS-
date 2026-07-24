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
}