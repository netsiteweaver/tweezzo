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
        alert(district);
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
})