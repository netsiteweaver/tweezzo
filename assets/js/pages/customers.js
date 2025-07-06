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
                    $("#addUserAccessModal input, #addUserAccessModal select, #addUserAccessModal textarea").val("")
                    $('#addUserAccessModal').modal("hide");
                    let row = `<tr>
                            <td><input type="text" class="form-control" placeholder="Enter Name" value="${name}" readonly=""></td>
                            <td><input type="text" class="form-control" placeholder="Enter Phone" value="${phone}" readonly=""></td>
                            <td><input type="text" class="form-control" placeholder="Enter Email" value="${email}" readonly=""></td>
                            <td>
                                <i class="flag flag-${country_code}"></i>
                                <input type="text" class="form-control d-none" placeholder="${country_code}?">
                            </td>
                            <td>
                                <div class="btn btn-danger"><i class="fa fa-trash"></i></div>
                            </td>
                        </tr>`;
                    $("#existing_users tbody").append(row);
                }
            }
        })

    })
})