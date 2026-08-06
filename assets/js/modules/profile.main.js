$(document).ready(function () {

    if ($('#profile_username').length === 0) return;

    // ─── Update Username ────────────────────────────────
    $('#btnSaveUsername').on('click', function () {
        var username = $('#profile_username').val().trim();

        if (!username) {
            Swal.fire('Warning', 'Username cannot be empty.', 'warning');
            return;
        }

        $.post(BASE_URL + 'myprofile/update_username', { username: username }, function (res) {
            if (res.success) {
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

    // ─── Update Password ────────────────────────────────
    $('#btnSavePassword').on('click', function () {
        var payload = {
            current_password: $('#current_password').val(),
            new_password: $('#new_password').val(),
            confirm_password: $('#confirm_password').val()
        };

        if (!payload.current_password || !payload.new_password || !payload.confirm_password) {
            Swal.fire('Warning', 'Please fill in all password fields.', 'warning');
            return;
        }

        $.post(BASE_URL + 'myprofile/update_password', payload, function (res) {
            if (res.success) {
                Swal.fire('Success', res.message, 'success').then(function () {
                    $('#current_password').val('');
                    $('#new_password').val('');
                    $('#confirm_password').val('');
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

});