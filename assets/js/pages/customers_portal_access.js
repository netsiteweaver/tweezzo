jQuery(function(){
    function isValidEmail(email) {
        var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    function setAddUserModalMode(mode) {
        var $m = $('#addUserAccessModal');
        if (mode === 'add') {
            $m.find('.access_id').val('');
            $m.find('.name, .email, .phone, .password').val('');
            $m.find('.country_code').val('mu');
            $m.find('.admin').prop('checked', false);
            $('#addUserAccessModalTitle').html('<i class="fa fa-unlock"></i> Grant User Access');
            $m.find('.password-group .form-control').attr('placeholder', 'Enter password');
            $m.find('.add-mode-hint').show();
        } else {
            $('#addUserAccessModalTitle').html('<i class="fa fa-edit"></i> Edit User Access');
            $m.find('.password-group .form-control').attr('placeholder', 'Leave blank to keep current password');
            $m.find('.add-mode-hint').hide();
        }
    }

    function loadPortalUsers(uuid) {
        $('#portal-users-list-standalone').html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</p>');
        $.ajax({
            url: base_url + "/customers/get_portal_access_users",
            type: "POST",
            dataType: "JSON",
            data: { uuid: uuid },
            success: function(response){
                if(response.result){
                    if(response.users && response.users.length > 0){
                        var html = '<div class="table-responsive"><table class="table table-striped table-bordered">';
                        html += '<thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Job Description</th><th>Admin</th><th>Action</th></tr></thead>';
                        html += '<tbody>';
                        response.users.forEach(function(user){
                            html += '<tr>';
                            html += '<td>' + user.name + '</td>';
                            html += '<td>' + user.email + '</td>';
                            html += '<td>' + (user.phone_number1 || '-') + '</td>';
                            html += '<td>' + (user.job_description || '-') + '</td>';
                            html += '<td>' + (user.admin == '1' ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>') + '</td>';
                            html += '<td><button class="btn btn-warning btn-sm reset-password-btn-standalone" data-access-id="' + user.id + '" data-name="' + user.name + '" data-email="' + user.email + '"><i class="fa fa-key"></i> Reset Password</button></td>';
                            html += '</tr>';
                        });
                        html += '</tbody></table></div>';
                        $('#portal-users-list-standalone').html(html);
                    } else {
                        $('#portal-users-list-standalone').html('<div class="alert alert-info">No portal access users found for this customer.</div>');
                    }
                } else {
                    $('#portal-users-list-standalone').html('<div class="alert alert-danger">Error: ' + response.reason + '</div>');
                }
            },
            error: function(){
                $('#portal-users-list-standalone').html('<div class="alert alert-danger">Error loading portal access users.</div>');
            }
        });
    }

    $('#manage-portal-password-standalone').on('click', function(){
        var uuid = $('input[name=uuid]').val();
        $('#modalPortalPasswordStandalone').modal('show');
        loadPortalUsers(uuid);
    });

    $('#add-user-access').on("click", function(){
        setAddUserModalMode('add');
        $('#addUserAccessModal').modal("show");
        $('#addUserAccessModal').one("shown.bs.modal", function(){
            $("#addUserAccessModal .name").trigger("focus");
        });
    });

    $('#addUserAccessModal').on("hidden.bs.modal", function(){
        $('#addUserAccessModal .access_id').val('');
    });

    $('#existing_users').on("click", ".editUser", function(){
        var $tr = $(this).closest("tr");
        var id = $tr.data("id");
        var name = $tr.find(".userName").val();
        var phone = $tr.find(".userPhone").val();
        var email = $tr.find(".userEmail").val();
        var flagClass = $tr.find("td:eq(3) .flag").attr("class") || "";
        var countryCode = (flagClass.match(/flag-(\w+)/) || [null, "mu"])[1];
        var isAdmin = $tr.find("td:eq(4) .badge").length > 0;
        $('#addUserAccessModal .access_id').val(id);
        $('#addUserAccessModal .name').val(name);
        $('#addUserAccessModal .phone').val(phone);
        $('#addUserAccessModal .email').val(email);
        $('#addUserAccessModal .country_code').val(countryCode);
        $('#addUserAccessModal .admin').prop('checked', isAdmin);
        $('#addUserAccessModal .password').val('');
        setAddUserModalMode('edit');
        $('#addUserAccessModal').modal("show");
        $('#addUserAccessModal').one("shown.bs.modal", function(){
            $("#addUserAccessModal .name").trigger("focus");
        });
    });

    $('#addUserAccessModal .save').on("click", function(){
        var uuid = $("input[name=uuid]").val();
        var accessId = $('#addUserAccessModal .access_id').val();
        var name = $('#addUserAccessModal .name').val().trim();
        var email = $('#addUserAccessModal .email').val();
        var phone = $('#addUserAccessModal .phone').val();
        var password = $('#addUserAccessModal .password').val();
        var countryCode = $('#addUserAccessModal .country_code').val();
        var admin = $('#addUserAccessModal .admin').is(':checked') ? 1 : 0;
        var errorMessage = "";

        if (name.length < 4) {
            errorMessage += "- a name of at least 4 chars\r\n";
        }
        if (!isValidEmail(email)) {
            errorMessage += "- a valid email\r\n";
        }
        if (!accessId && password.length < 4) {
            errorMessage += "- a valid password of at least 4 chars (required for new user)\r\n";
        }
        if (errorMessage.length > 0) {
            alert("Please correct the following error(s):\r\n" + errorMessage);
            return false;
        }

        if (accessId) {
            $.ajax({
                url: base_url + "portal/customers/updateUserAccess",
                method: "POST",
                dataType: "JSON",
                data: { access_id: accessId, name: name, email: email, phone: phone, country_code: countryCode, admin: admin, password: password },
                success: function(response){
                    if (response.result === false) {
                        alertify.alert(response.reason || 'Update failed');
                    } else {
                        var adminBadge = admin ? '<span class="badge badge-info">Yes</span>' : 'No';
                        var $row = $("#existing_users tbody tr[data-id='" + accessId + "']");
                        $row.find(".userName").val(name);
                        $row.find(".userPhone").val(phone);
                        $row.find(".userEmail").val(email);
                        $row.find("td:eq(3)").html('<i class="flag flag-' + countryCode + '"></i><input type="text" class="form-control d-none" placeholder="' + countryCode + '?">');
                        $row.find("td:eq(4)").html(adminBadge);
                        $("#addUserAccessModal input, #addUserAccessModal textarea").val("");
                        $("#addUserAccessModal .access_id").val("");
                        $("#addUserAccessModal .admin").prop("checked", false);
                        $('#addUserAccessModal').modal("hide");
                    }
                }
            });
        } else {
            $.ajax({
                url: base_url + "portal/customers/addUserAccess",
                method: "POST",
                dataType: "JSON",
                data: { uuid: uuid, name: name, email: email, phone: phone, password: password, country_code: countryCode, admin: admin },
                success: function(response){
                    if (response.result === false) {
                        alertify.alert(response.reason);
                    } else {
                        $("#addUserAccessModal input, #addUserAccessModal textarea").val("");
                        $("#addUserAccessModal select").val("mu");
                        $("#addUserAccessModal .admin").prop("checked", false);
                        $("#addUserAccessModal .access_id").val("");
                        $('#addUserAccessModal').modal("hide");
                        var adminBadge = admin ? '<span class="badge badge-info">Yes</span>' : 'No';
                        var row = '<tr data-id="' + response.user_id + '">' +
                            '<td><input type="text" class="form-control userName" placeholder="Enter Name" value="' + name + '" readonly=""></td>' +
                            '<td><input type="text" class="form-control userPhone" placeholder="Enter Phone" value="' + phone + '" readonly=""></td>' +
                            '<td><input type="text" class="form-control userEmail" placeholder="Enter Email" value="' + email + '" readonly=""></td>' +
                            '<td><i class="flag flag-' + countryCode + '"></i><input type="text" class="form-control d-none" placeholder="' + countryCode + '?"></td>' +
                            '<td>' + adminBadge + '</td>' +
                            '<td><div class="btn btn-info btn-sm editUser" title="Edit"><i class="fa fa-edit"></i></div> <div class="btn btn-danger btn-sm deleteUser" title="Remove access"><i class="fa fa-trash"></i></div></td>' +
                            '</tr>';
                        $("#existing_users tbody").append(row);
                    }
                }
            });
        }
    });

    $('#existing_users').on("click", ".deleteUser", function(){
        var row = $(this);
        var id = row.closest("tr").data("id");
        var name = row.closest("tr").find(".userName").val();
        bootbox.confirm({
            message: "Are you sure you want to remove access to user <b>" + name + "</b>?",
            buttons: {
                confirm: {
                    label: 'Remove Access',
                    className: 'btn-danger'
                },
                cancel: {
                    label: 'Cancel',
                    className: 'btn-primary'
                }
            },
            callback: function (result) {
                if (result === true) {
                    $.ajax({
                        url: base_url + "portal/customers/removeAccess",
                        data: { user_id: id, name: name },
                        method: "POST",
                        dataType: "JSON",
                        success: function(response){
                            if (response.result) {
                                row.closest("tr").remove();
                            } else {
                                bootbox.alert({
                                    title: "Error",
                                    message: "Failed to remove selected user"
                                });
                            }
                        }
                    });
                }
            }
        });
    });

    $('#portal-users-list-standalone').on('click', '.reset-password-btn-standalone', function(){
        var accessId = $(this).data('access-id');
        var userName = $(this).data('name');
        var userEmail = $(this).data('email');
        var btn = $(this);

        bootbox.confirm({
            message: "Are you sure you want to reset the password for <b>" + userName + "</b>?<br><br>A new password will be generated and emailed to <b>" + userEmail + "</b>.",
            buttons: {
                confirm: {
                    label: 'Yes, Reset Password',
                    className: 'btn-warning'
                },
                cancel: {
                    label: 'Cancel',
                    className: 'btn-default'
                }
            },
            callback: function (result) {
                if (result) {
                    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Resetting...');
                    $.ajax({
                        url: base_url + "/customers/reset_portal_password",
                        type: "POST",
                        dataType: "JSON",
                        data: { access_id: accessId },
                        success: function(response){
                            if(response.result){
                                $('#modalPortalPasswordStandalone').modal('hide');
                                $('#reset-user-name-standalone').text(response.user.name);
                                $('#reset-user-email-standalone').text(response.user.email);
                                $('#new-password-display-standalone').val(response.password);
                                $('#modalNewPasswordStandalone').modal('show');
                                alertify.success(response.message);
                            } else {
                                alertify.error("Error: " + response.reason);
                                btn.prop('disabled', false).html('<i class="fa fa-key"></i> Reset Password');
                            }
                        },
                        error: function(){
                            alertify.error("Error resetting password. Please try again.");
                            btn.prop('disabled', false).html('<i class="fa fa-key"></i> Reset Password');
                        }
                    });
                }
            }
        });
    });

    $('#copy-password-btn-standalone').on('click', function(){
        var passwordField = $('#new-password-display-standalone');
        passwordField.select();
        document.execCommand('copy');

        var btn = $(this);
        var originalHtml = btn.html();
        btn.html('<i class="fa fa-check"></i> Copied!');
        setTimeout(function(){
            btn.html(originalHtml);
        }, 2000);
        alertify.success('Password copied to clipboard!');
    });
});
