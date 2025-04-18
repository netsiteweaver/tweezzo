var te;
var dndState = false;

jQuery(function(){
    // te = window.setInterval(function(){ 
    //     getNotifications() 
    // },1000)

    // if(localStorage.getItem("dnd")!==null){
    //     dndState = true;
    //     $('#dnd2').prop("checked",true);
    // }

    // $('#discountApprovalModal').on("hidden.bs.modal",function(){
    //     window.setTimeout(function(){
    //         te = window.setInterval(function(){
    //             getNotifications()
    //         },1000)
    //     },5000)
        
    // })

    $('#discountApprovalModal').on("shown.bs.modal",function(){
        Notify("notify-bell");
    })

    $('#discount_request_settings').on("click",function(){
        $('#discountRequestSettingsModal').modal("show");
    })

    $('.dnd').on("change",function(){
        let dnd = $(this).is(":checked");
        
        if(dnd){
            $(".dnd").prop("checked",true);
            dndState = true;
            localStorage.setItem("dnd",true);
        }else{
            $(".dnd").prop("checked",false);
            dndState = false;
            localStorage.removeItem("dnd");
            te = window.setInterval(function(){
                getNotifications()
            },1000)
        }
    })

    $('#discountApprovalModal').on("click",".approve-discount",function(){
        $(this).closest('tr').find("input").attr("readonly",false).select();
        $(this).closest("tr").find(".save-discount").removeClass("hidden")
        $(this).closest("tr").find(".approve-discount").addClass("hidden")
    })

    $('#discountApprovalModal').on("click",".reject-discount",function(){
        $(this).closest("tr").addClass("active");
        let id = $(this).closest("tr").data("id");

        $.ajax({
            url: base_url + "ajax/discount/reject",
            type:"POST",
            dataType:"JSON",
            data:{id:id},
            complete: function(){
                $('#discount_request').find("tr.active").remove();
                if(($('#discount_request tbody tr').length)==0){
                    $('#discountApprovalModal').modal("hide")
                }
            }
        })
    })

    $('#discountApprovalModal').on("click",".save-discount",function(){
        $(this).closest("tr").addClass("active");
        let id = $(this).closest("tr").data("id");
        let amountApproved = $(this).closest("tr").find('input').val()
        let amountRequested = $(this).closest("tr").data("amount");
        console.log(amountApproved,amountRequested)
        if(amountApproved>amountRequested){
            bootbox.alert("Please enter an amount less than Rs "+amountRequested)
            return false;
        }

        $.ajax({
            url: base_url + "ajax/discount/approve",
            type:"POST",
            dataType:"JSON",
            data:{id:id,amountApproved:amountApproved},
            complete: function(){
                $('#discount_request').find("tr.active").remove();
                if(($('#discount_request tbody tr').length)==0){
                    $('#discountApprovalModal').modal("hide")
                }
            }
        })
    })

    $('#discountApprovalModal').on("keypress",".amount-approved",function(e){
        console.log(e.keyCode)
        if( (e.charCode == 46) || ( (e.charCode>=48)&&(e.charCode<=57) ) ){
            
        }else{
            bootbox.alert("Invalid character entered")
            e.preventDefault();
        }
        
    })

})

function getNotifications()
{
    $.ajax({
        url: base_url + "ajax/misc/monitor",
        type: "POST",
        data: {timestamp:''},
        dataType: "JSON",
        success: function(response) {
            if(response.result){
                if(response.requests.length>0)
                {
                    $('#discount_request tbody').empty();
                    $('#notif').removeClass("hidden")
                    $('#notif .badge').text(response.requests.length)
                    $(response.requests).each(function(i,row){
                        let line = "<tr data-id='"+row.id+"' data-amount='"+row.discount_requested+"'>";
                        line += "<td>"+row.created_on.substring(0,16)+"</td>";
                        line += "<td>"+row.delivery_boy+"</td>";
                        line += "<td>"+row.document_number+"</td>";
                        line += "<td>"+row.customer_details+"</td>";
                        line += "<td><input class='form-control text-right amount-approved' value='"+row.discount_requested+"' readonly>";
                        line += "</td>";
                        line += "<td>";
                        line += "<div class='btn btn-md btn-success approve-discount'><i class='fa fa-check'></i></div>";
                        line += "<div class='hidden btn btn-md btn-info save-discount'><i class='fa fa-save'></i></div>";
                        line += "<div class='btn btn-md btn-danger reject-discount'><i class='fa fa-times'></i></div>";
                        line += "</td>";
                        line += "</tr>";
                        $('#discount_request tbody').append(line);
                    })
                    if(!dndState) {
                        $('#discountApprovalModal').modal("show");
                        $('#notif .fa-microphone-slash').addClass("hidden")
                    }else{
                        $('#notif .fa-microphone-slash').removeClass("hidden")
                    }
                    clearInterval(te)
                }
                else
                {
                    $('#notif .badge').text('');
                    $('#notif').addClass("hidden")
                }
            }
        }
    })
}