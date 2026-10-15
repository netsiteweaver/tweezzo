function isValidEmail(email) {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
}

jQuery(function(){
    // Auto-submit form when active filter changes
    $('#active_filter').on("change", function(){
        $(this).closest('form').submit();
    });

    // Auto-submit form when display dropdown changes
    $('#rpp').on("change", function(){
        if($(this).val() !== '') {
            $(this).closest('form').submit();
        }
    });

    // Auto-submit form when hide_completed checkbox changes
    $('#hide_completed').on("change", function(){
        $(this).closest('form').submit();
    });

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
                        url: base_url + "/customers/toggleActive",
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
            url: base_url + "/customers/info",
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
            url: base_url + "/customers/delete",
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

    // ---------- Portal users modal (list, add/edit, reset password, remove) ----------
    var MAX_PORTAL_USERS = 5;
    var $pu = $('#modalPortalUsers');
    var portalUsers = [];

    function esc(value) {
        return $('<div>').text(value == null ? '' : String(value)).html();
    }

    function currentPortalUuid() {
        return $pu.find('input[name=portal_customer_uuid]').val();
    }

    function showPortalPanel(panel) {
        $pu.find('.portal-users-list-panel').toggleClass('d-none', panel !== 'list');
        $pu.find('.portal-user-form-panel').toggleClass('d-none', panel !== 'form');
    }

    function updateListingCount(uuid, count) {
        var $badge = $('#customers_listing tr[data-uuid="' + uuid + '"] .portal-user-count');
        $badge.text(count).toggleClass('badge-info', count > 0).toggleClass('badge-secondary', count === 0);
    }

    function renderPortalUsers() {
        var check = '<i class="fa fa-check-circle text-success" title="Yes"></i>';
        var count = portalUsers.length;
        $pu.find('.users-count-label').text(count + ' user' + (count === 1 ? '' : 's') + ' (max ' + MAX_PORTAL_USERS + ')');
        $pu.find('.add-portal-user').toggle(count < MAX_PORTAL_USERS);
        updateListingCount(currentPortalUuid(), count);

        if (count === 0) {
            $pu.find('.portal-users-list').html('<div class="alert alert-info">No portal access users found for this customer.</div>');
            return;
        }
        var html = '<div class="table-responsive"><table class="table table-striped table-bordered table-sm">';
        html += '<thead><tr>' +
            '<th><i class="fa fa-user mr-1"></i>Name</th>' +
            '<th><i class="fa fa-envelope mr-1"></i>Email</th>' +
            '<th><i class="fa fa-phone mr-1"></i>Phone</th>' +
            '<th><i class="fa fa-briefcase mr-1"></i>Job Description</th>' +
            '<th><i class="fa fa-globe mr-1"></i>Country</th>' +
            '<th class="text-center"><i class="fa fa-user-shield mr-1"></i>Admin</th>' +
            '<th class="text-center"><i class="fa fa-check-double mr-1"></i>Validator</th>' +
            '<th style="width:1%;white-space:nowrap;">Actions</th>' +
            '</tr></thead><tbody>';
        portalUsers.forEach(function(user){
            html += '<tr data-id="' + esc(user.id) + '">' +
                '<td>' + esc(user.name) + '</td>' +
                '<td>' + esc(user.email) + '</td>' +
                '<td>' + (esc(user.phone_number1) || '-') + '</td>' +
                '<td>' + (esc(user.job_description) || '-') + '</td>' +
                '<td>' + (user.country_code ? '<i class="flag flag-' + esc(user.country_code) + '"></i>' : '') + '</td>' +
                '<td class="text-center">' + (user.admin == '1' ? check : '') + '</td>' +
                '<td class="text-center">' + (user.can_validate == '1' ? check : '') + '</td>' +
                '<td style="white-space:nowrap;">' +
                    '<button type="button" class="btn btn-info btn-sm edit-portal-user" title="Edit"><i class="fa fa-edit"></i></button> ' +
                    '<button type="button" class="btn btn-warning btn-sm reset-portal-password" title="Reset password"><i class="fa fa-key"></i></button> ' +
                    '<button type="button" class="btn btn-danger btn-sm remove-portal-user" title="Remove access"><i class="fa fa-trash"></i></button>' +
                '</td>' +
                '</tr>';
        });
        html += '</tbody></table></div>';
        $pu.find('.portal-users-list').html(html);
    }

    function loadPortalUsers() {
        $pu.find('.portal-users-list').html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</p>');
        $.ajax({
            url: base_url + "/customers/get_portal_access_users",
            type: "POST",
            dataType: "JSON",
            data: {uuid: currentPortalUuid()},
            success: function(response){
                if(response.result){
                    portalUsers = response.users || [];
                    renderPortalUsers();
                } else {
                    $pu.find('.portal-users-list').html('<div class="alert alert-danger">Error: ' + esc(response.reason) + '</div>');
                }
            },
            error: function(){
                $pu.find('.portal-users-list').html('<div class="alert alert-danger">Error loading portal access users.</div>');
            }
        });
    }

    function findPortalUser($btn) {
        var id = $btn.closest('tr').data('id');
        return portalUsers.filter(function(u){ return u.id == id; })[0];
    }

    function togglePasswordInput() {
        $pu.find('.password').toggleClass('d-none', $pu.find('.generate_password').is(':checked'));
    }

    function openPortalUserForm(user) {
        var $f = $pu.find('.portal-user-form-panel');
        $f.find('.access_id').val(user ? user.id : '');
        $f.find('.name').val(user ? user.name : '');
        $f.find('.email').val(user ? user.email : '');
        $f.find('.phone').val(user ? (user.phone_number1 || '') : '');
        $f.find('.country_code').val(user && user.country_code ? user.country_code : 'mu');
        $f.find('.admin').prop('checked', !!user && user.admin == '1');
        $f.find('.can_validate').prop('checked', !!user && user.can_validate == '1');
        $f.find('.generate_password').prop('checked', true);
        $f.find('.password').val('');
        togglePasswordInput();
        $f.find('.add-mode-only').toggle(!user);
        $f.find('.form-title').html(user
            ? '<i class="fa fa-edit"></i> Edit ' + esc(user.name)
            : '<i class="fa fa-unlock"></i> Grant User Access');
        $pu.find('.new-password-panel').addClass('d-none');
        showPortalPanel('form');
        $f.find('.name').trigger('focus');
    }

    $('.managePortalUsers').on('click', function(){
        var $tr = $(this).closest('tr');
        $pu.find('input[name=portal_customer_uuid]').val($tr.data('uuid'));
        $pu.find('.customer-name').text($tr.find('td:first').text());
        $pu.find('.new-password-panel').addClass('d-none');
        showPortalPanel('list');
        portalUsers = [];
        $pu.modal('show');
        loadPortalUsers();
    });

    $pu.on('click', '.add-portal-user', function(){ openPortalUserForm(null); });
    $pu.on('click', '.edit-portal-user', function(){ openPortalUserForm(findPortalUser($(this))); });
    $pu.on('click', '.cancel-portal-user', function(){ showPortalPanel('list'); });
    $pu.on('change', '.generate_password', togglePasswordInput);
    $pu.on('click', '.close-new-password', function(){ $pu.find('.new-password-panel').addClass('d-none'); });

    $pu.on('click', '.save-portal-user', function(){
        var $f = $pu.find('.portal-user-form-panel');
        var accessId = $f.find('.access_id').val();
        var data = {
            name: $f.find('.name').val().trim(),
            email: $f.find('.email').val().trim(),
            phone: $f.find('.phone').val().trim(),
            country_code: $f.find('.country_code').val(),
            admin: $f.find('.admin').is(':checked') ? 1 : 0,
            can_validate: $f.find('.can_validate').is(':checked') ? 1 : 0
        };
        var generatePassword = $f.find('.generate_password').is(':checked');
        var errorMessage = "";
        if (data.name.length < 4) errorMessage += "- a name of at least 4 chars\r\n";
        if (!isValidEmail(data.email)) errorMessage += "- a valid email\r\n";
        if (!accessId && !generatePassword && $f.find('.password').val().length < 4) {
            errorMessage += "- a password of at least 4 chars (or tick 'Generate password')\r\n";
        }
        if (errorMessage.length > 0) {
            alert("Please correct the following error(s):\r\n" + errorMessage);
            return false;
        }

        var url;
        if (accessId) {
            url = base_url + "portal/customers/updateUserAccess";
            data.access_id = accessId;
        } else {
            url = base_url + "portal/customers/addUserAccess";
            data.uuid = currentPortalUuid();
            data.generate_password = generatePassword ? 1 : 0;
            data.password = generatePassword ? '' : $f.find('.password').val();
        }

        var $btn = $(this).prop('disabled', true);
        $.ajax({
            url: url,
            method: "POST",
            dataType: "JSON",
            data: data,
            success: function(response){
                if (response.result === false) {
                    alertify.alert(response.reason || 'Save failed');
                    return;
                }
                alertify.success(accessId ? "User updated" : "User added");
                showPortalPanel('list');
                loadPortalUsers();
            },
            error: function(){
                alertify.error("Error saving user. Please try again.");
            },
            complete: function(){
                $btn.prop('disabled', false);
            }
        });
    });

    $pu.on('click', '.remove-portal-user', function(){
        var user = findPortalUser($(this));
        if (!user) return;
        bootbox.confirm({
            message: "Are you sure you want to remove access to user <b>" + esc(user.name) + "</b>?",
            buttons: {
                confirm: { label: 'Remove Access', className: 'btn-danger' },
                cancel: { label: 'Cancel', className: 'btn-primary' }
            },
            callback: function (result) {
                if (!result) return;
                $.ajax({
                    url: base_url + "portal/customers/removeAccess",
                    data: {user_id: user.id, name: user.name},
                    method: "POST",
                    dataType: "JSON",
                    success: function(response){
                        if (response.result) {
                            alertify.success("Access removed");
                            loadPortalUsers();
                        } else {
                            bootbox.alert({ title: "Error", message: "Failed to remove selected user" });
                        }
                    }
                });
            }
        });
    });

    $pu.on('click', '.reset-portal-password', function(){
        var user = findPortalUser($(this));
        if (!user) return;
        var $btn = $(this);
        bootbox.confirm({
            message: "Are you sure you want to reset the password for <b>" + esc(user.name) + "</b>?<br><br>A new password will be generated and emailed to <b>" + esc(user.email) + "</b>.",
            buttons: {
                confirm: { label: 'Yes, Reset Password', className: 'btn-warning' },
                cancel: { label: 'Cancel', className: 'btn-default' }
            },
            callback: function (result) {
                if (!result) return;
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
                $.ajax({
                    url: base_url + "/customers/reset_portal_password",
                    type: "POST",
                    dataType: "JSON",
                    data: {access_id: user.id},
                    success: function(response){
                        if (response.result) {
                            var $np = $pu.find('.new-password-panel');
                            $np.find('.reset-user-name').text(response.user.name);
                            $np.find('.reset-user-email').text(response.user.email);
                            $np.find('.new-password-display').val(response.password);
                            $np.removeClass('d-none');
                            alertify.success(response.message);
                        } else {
                            alertify.error("Error: " + response.reason);
                        }
                    },
                    error: function(){
                        alertify.error("Error resetting password. Please try again.");
                    },
                    complete: function(){
                        $btn.prop('disabled', false).html('<i class="fa fa-key"></i>');
                    }
                });
            }
        });
    });

    $pu.on('click', '.copy-password-btn', function(){
        $pu.find('.new-password-display').trigger('select');
        document.execCommand('copy');
        var $btn = $(this);
        var originalHtml = $btn.html();
        $btn.html('<i class="fa fa-check"></i> Copied!');
        setTimeout(function(){ $btn.html(originalHtml); }, 2000);
        alertify.success('Password copied to clipboard!');
    });
})
