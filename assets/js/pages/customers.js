function isValidEmail(email) {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
}

jQuery(function(){

    $('.input-group-text.clear-search').on("click",function(){
        let elem = $(this).siblings("input").val();
        if(elem=="") return false;
        $(this).siblings("input").val("");
    })

    $('input[name=gender]').on("change",function(){
        let gender = $(this).val();
        if(gender=='m'){
            $("input[name=title][value='Mr']").prop("disabled",false);
            $("input[name=title][value='Mrs']").prop("disabled",true);
            $("input[name=title][value='Miss']").prop("disabled",true);
            $("input[name=title][value='Dr']").prop("disabled",false);
            $('input:radio[name=title]')[0].checked = true;
        }else{
            $("input[name=title][value='Mr']").prop("disabled",true);
            $("input[name=title][value='Mrs']").prop("disabled",false);
            $("input[name=title][value='Miss']").prop("disabled",false);
            $("input[name=title][value='Dr']").prop("disabled",false);
            $('input:radio[name=title]')[1].checked = true;
        }
    })

    $('#edit-code').on("click",function(){
        if($('#customer_code').attr("readonly")=="readonly") {
            $('#customer_code').removeAttr("readonly")
        }else{
            $('#customer_code').attr("readonly","readonly")
        }
        
    })

    $('.select-district').on("click",function(e){
        e.preventDefault();
        let district = $(this).attr("title");
        // alert(district);
    })

    // $('.resetPassword').on("click", function(){
    //     let uuid = $(this).closest("tr").data("uuid");
    //     alert(uuid)
    // })

    $(".toggleActive").on("click", function(){
        let uuid = $(this).closest("tr").data("uuid");
        let row = $(this).closest("tr");
        let activeIndicator = row.find(".activeOrNot div");
        let toggleBtn = $(this);
        
        bootbox.confirm({
            message: "Are you sure you want to toggle this customer's active status? This will also affect all related projects, sprints, and tasks.",
            buttons: {
                confirm: {
                    label: 'Yes, Proceed',
                    className: 'btn-warning'
                },
                cancel: {
                    label: 'Cancel',
                    className: 'btn-default'
                }
            },
            callback: function (result) {
                if(result==true){
                    Overlay("on");
                    $.ajax({
                        url: "/customers/toggleActive",
                        type: "POST",
                        dataType: "JSON",
                        data: {uuid: uuid},
                        success: function(response){
                            if(response.result){
                                alertify.success(response.message + ". Affected: " + response.affectedProjects + " projects, " + response.affectedSprints + " sprints");
                                
                                // Update the active indicator
                                if(response.newStatus == '1'){
                                    activeIndicator.removeClass('btn-danger').addClass('btn-info');
                                    activeIndicator.find('i').removeClass('fa-times').addClass('fa-check');
                                    toggleBtn.removeClass('btn-success').addClass('btn-warning');
                                    toggleBtn.find('i').removeClass('fa-eye').addClass('fa-eye-slash');
                                    toggleBtn.attr('title', 'Set Inactive');
                                }else{
                                    activeIndicator.removeClass('btn-info').addClass('btn-danger');
                                    activeIndicator.find('i').removeClass('fa-check').addClass('fa-times');
                                    toggleBtn.removeClass('btn-warning').addClass('btn-success');
                                    toggleBtn.find('i').removeClass('fa-eye-slash').addClass('fa-eye');
                                    toggleBtn.attr('title', 'Set Active');
                                }
                            }else{
                                alertify.error("Error: " + response.reason);
                            }
                        },
                        complete: function(){
                            Overlay("off")
                        }
                    });
                }
            }
        });
    });

    $(".deleteCustomer").on("click", function(){
        let uuid = $(this).closest("tr").data("uuid");
        $(this).closest("tr").addClass("active");
        Overlay("on");
        $.ajax({
            url: "/customers/info",
            type: "POST",
            dataType: "JSON",
            data: {uuid: uuid},
            success: function(response){
                if(response.result){
                    $('#modalCustomerInfo .customer').text(response.info.customer.company_name)
                    $('#modalCustomerInfo .projects').text(response.info.projectCount)
                    $('#modalCustomerInfo .sprints').text(response.info.sprintCount)
                    $('#modalCustomerInfo .tasks').text(response.info.taskCount)
                    $('#modalCustomerInfo input[name=customer_uuid]').val(response.info.customer.uuid)
                    $('#modalCustomerInfo').modal('show');
                }else{
                    // alertify.error("Error deleting customer.");
                }
            },
            complete: function(){
                Overlay("off")
            }
        });
    })

    $(".deleteConfirm").on("click", function(){
        let uuid = $('#modalCustomerInfo input[name=customer_uuid]').val()
        Overlay("on");
        $.ajax({
            url: "/customers/delete",
            type: "POST",
            dataType: "JSON",
            data: {uuid: uuid},
            success: function(response){
                if(response.result){
                    alertify.success("Customer deleted successfully.");
                    // location.reload();
                    $('#customers_listing tbody tr.active').remove();
                    $('#modalCustomerInfo').modal('hide');
                }else{
                    alertify.error("Error deleting customer.");
                }
            },
            complete: function(){
                Overlay("off")
            }
        });
    })

    $('#modalCustomerInfo').on('hidden.bs.modal', function () {
        $('#customers_listing tbody tr.active').removeClass("active");
    })

    $('#add-user-access').on("click", function(){
        $('#addUserAccessModal').modal("show");
        $('#addUserAccessModal').on("shown.bs.modal",function(){
            $("#addUserAccessModal .name").trigger("focus")
        })
    })

    $('#addUserAccessModal').on("hidden.bs.modal",function(){
        // alert();
    })

    $('#addUserAccessModal .save').on("click", function(){
        let uuid = $("input[name=uuid]").val();
        // console.log(uuid)
        let name = $('#addUserAccessModal .name').val().trim();
        let email = $('#addUserAccessModal .email').val();
        let phone = $('#addUserAccessModal .phone').val();
        let password = $('#addUserAccessModal .password').val();
        let country_code = $('#addUserAccessModal .country_code').val();
        let errorMessage = "";

        if(name.length < 4){
            errorMessage += "- a name of at least 4 chars\r\n";
        }

        if(!isValidEmail(email)){
            errorMessage += "- a valid email\r\n";
        }

        if(password.length < 4){
            errorMessage += "- a valid password of at least 4 chars\r\n";
        }

        if(errorMessage.length > 0){
            errorMessage = "Please correct the following error(s):\r\n" + errorMessage;
            alert(errorMessage)
            return false;
        }

        $.ajax({
            url: base_url + "portal/customers/addUserAccess",
            method: "POST",
            dataType: "JSON",
            data:{uuid:uuid,name:name,email:email,phone:phone,password:password,country_code:country_code},
            success:function(response){
                if(response.result == false){
                    alertify.alert(response.reason);
                }else{
                    $("#addUserAccessModal input, #addUserAccessModal textarea").val("");
                    $("#addUserAccessModal select").val("mu");
                    $('#addUserAccessModal').modal("hide");
                    let row = `<tr data-id="${response.user_id}">
                            <td><input type="text" class="form-control" placeholder="Enter Name" value="${name}" readonly=""></td>
                            <td><input type="text" class="form-control" placeholder="Enter Phone" value="${phone}" readonly=""></td>
                            <td><input type="text" class="form-control" placeholder="Enter Email" value="${email}" readonly=""></td>
                            <td>
                                <i class="flag flag-${country_code}"></i>
                                <input type="text" class="form-control d-none" placeholder="${country_code}?">
                            </td>
                            <td>
                                <div class="btn btn-danger deleteUser"><i class="fa fa-trash"></i></div>
                            </td>
                        </tr>`;
                    $("#existing_users tbody").append(row);
                }
            }
        })

    })

    $('#existing_users').on("click",".deleteUser",function(){
        let row = $(this);
        let id = $(this).closest("tr").data("id");
        let name = $(this).closest("tr").find(".userName").val();
        bootbox.confirm({
	        message: `Are you sure you want to remove access to user <b>${name}</b>?`,
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
	        	if(result==true){
                    $.ajax({
                        url: base_url + "portal/customers/removeAccess",
                        data: {user_id:id,name:name},
                        method:"POST",
                        dataType:"JSON",
                        success:function(response){
                            if(response.result){
                                $(row).closest("tr").remove();
                            }else{
                                bootbox.alert({
                                    title: "Error",
                                    message: "Failed to remove selected user"
                                })
                            }
                        }
                    })	        	
                }
	            
	        }
	    });	

    })

    // Manage Portal Password functionality
    $(".managePortalPassword").on("click", function(){
        let uuid = $(this).closest("tr").data("uuid");
        let companyName = $(this).closest("tr").find("td:first").text();
        
        $('#portal-customer-name').text(companyName);
        $('input[name=portal_customer_uuid]').val(uuid);
        $('#portal-users-list').html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</p>');
        $('#modalPortalPassword').modal('show');
        
        // Load portal access users
        $.ajax({
            url: "/customers/get_portal_access_users",
            type: "POST",
            dataType: "JSON",
            data: {uuid: uuid},
            success: function(response){
                if(response.result){
                    if(response.users && response.users.length > 0){
                        let html = '<div class="table-responsive"><table class="table table-striped table-bordered">';
                        html += '<thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Job Description</th><th>Admin</th><th>Action</th></tr></thead>';
                        html += '<tbody>';
                        
                        response.users.forEach(function(user){
                            html += '<tr>';
                            html += '<td>' + user.name + '</td>';
                            html += '<td>' + user.email + '</td>';
                            html += '<td>' + (user.phone_number1 || '-') + '</td>';
                            html += '<td>' + (user.job_description || '-') + '</td>';
                            html += '<td>' + (user.admin == '1' ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>') + '</td>';
                            html += '<td><button class="btn btn-warning btn-sm reset-password-btn" data-access-id="' + user.id + '" data-name="' + user.name + '" data-email="' + user.email + '">';
                            html += '<i class="fa fa-key"></i> Reset Password</button></td>';
                            html += '</tr>';
                        });
                        
                        html += '</tbody></table></div>';
                        $('#portal-users-list').html(html);
                    } else {
                        $('#portal-users-list').html('<div class="alert alert-info">No portal access users found for this customer.</div>');
                    }
                } else {
                    $('#portal-users-list').html('<div class="alert alert-danger">Error: ' + response.reason + '</div>');
                }
            },
            error: function(){
                $('#portal-users-list').html('<div class="alert alert-danger">Error loading portal access users.</div>');
            }
        });
    });

    // Reset password button click handler (using event delegation)
    $('#portal-users-list').on('click', '.reset-password-btn', function(){
        let accessId = $(this).data('access-id');
        let userName = $(this).data('name');
        let userEmail = $(this).data('email');
        let btn = $(this);
        
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
                if(result){
                    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Resetting...');
                    
                    $.ajax({
                        url: "/customers/reset_portal_password",
                        type: "POST",
                        dataType: "JSON",
                        data: {access_id: accessId},
                        success: function(response){
                            if(response.result){
                                // Close the portal password modal
                                $('#modalPortalPassword').modal('hide');
                                
                                // Show the new password modal
                                $('#reset-user-name').text(response.user.name);
                                $('#reset-user-email').text(response.user.email);
                                $('#new-password-display').val(response.password);
                                $('#modalNewPassword').modal('show');
                                
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

    // Copy password to clipboard
    $('#copy-password-btn').on('click', function(){
        let passwordField = $('#new-password-display');
        passwordField.select();
        document.execCommand('copy');
        
        let btn = $(this);
        let originalHtml = btn.html();
        btn.html('<i class="fa fa-check"></i> Copied!');
        
        setTimeout(function(){
            btn.html(originalHtml);
        }, 2000);
        
        alertify.success('Password copied to clipboard!');
    });

    // When new password modal is hidden, show the portal password modal again
    $('#modalNewPassword').on('hidden.bs.modal', function () {
        let uuid = $('input[name=portal_customer_uuid]').val();
        if(uuid){
            // Optionally reload the portal password modal
            // $('#modalPortalPassword').modal('show');
        }
    });
})