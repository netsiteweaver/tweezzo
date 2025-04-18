jQuery(function(){

    // const textInput = document.getElementById('textInput');
    // textInput.addEventListener('keydown', (event) => {
    //   if (event.key === 'Enter') {
    //     console.log('Enter key pressed!');
    //     // Perform desired actions here
    //     alert();
    //   }
    // });

    $('body').on('click','.remove-sn',function() {
        $(this).parent().find("input").val("").addClass("added").removeAttr("readonly").focus();
        let id = $(this).parent().data("id");
        let removed_sns_json = $('input[name=removed_sns]').val();
        if(removed_sns_json == "") removed_sns_json = "[]";
        let removed_sns = JSON.parse(removed_sns_json);
        removed_sns.push(id);
        $('input[name=removed_sns]').val(JSON.stringify(removed_sns));
        $(this).remove();
    })
    $('#modalSerialNumbers').on("hidden.bs.modal",function(){
        $('#item-list tr').removeClass("bg-navy");
        $('#modalSerialNumbers .modal-body').html("");
    })

    $('#modalSerialNumbers').on("hidden.bs.modal",function(){
        $('#save_serialnumbers').removeClass("d-none");
    })

    $('body').on('keydown', '.sn', function(e) {
        
        if ((e.key == 'Enter') || (e.keyCode == 13)) {

            let index = $(".sn").index($(this)); 
            let totalInputs = $('.sn').length;
            if(index == totalInputs -1){
                $('#save_serialnumbers').trigger("click")
                return;
            }
            
            $('.sn').removeClass("active-sn");
            $('.sn').eq(index+1).addClass("active-sn").find('input').focus();
        }
    })

    $('#item-list').on("click",".open-serialnumber-modal",function(){
        let deliverynote_details_id = $(this).closest("tr").find(".deliverynote_details_id").val();
        let product_id = $(this).closest("tr").find(".product_id").val();
        let deliverynote_id = $("input[name=deliverynote_id]").val();

        $(this).closest("tr").addClass("bg-navy");

        $.ajax({
            url: base_url + "deliverynotes/getserialnumbers",
            data:{deliverynote_id:deliverynote_id,deliverynote_details_id:deliverynote_details_id, product_id:product_id},
            method: "POST",
            dataType:"JSON",
            success: function(response){
                let total_rows = response.serial_numbers[0].quantity;
                let items = "<div id='sns' class='row'>";
                if(response.serial_numbers[0].serial_number===null){
                    for(var x = 0; x < (total_rows); x++) {
                        items += "<div class='col-md-3 mt-2 sn'>";
                        items += "<input class='added form-control text-center' name='serialnumber[]' value='' placeholder='.Enter / Scan Serial Number'>";
                        // items += "<div class='remove-sn' style='position: absolute;top: 3px;right: 15px;font-size: 10px;color: red;cursor: pointer;'><i class='fa fa-times'></i></div>";
                        items += "</div>"
                    }
                }else{
                    let current_row = 0;
                    $('#modalSerialNumbers .modal-body').html("");
                    $(response.serial_numbers).each(function(i,j){
                        console.log(i,j)
                        current_row = i;
                        items += "<div data-id="+j.id+" class='empty col-md-3 mt-2 sn'>";
                        items += "<input class='form-control text-center' name='serialnumber[]' value='"+j.serial_number+"' readonly>";
                        items += "<div class='remove-sn' style='position: absolute;top: 3px;right: 15px;font-size: 10px;color: red;cursor: pointer;'><i class='fa fa-times'></i></div>";
                        items += "</div>"
                    })
                    if(current_row < total_rows){
                        for(var x = current_row; x < (total_rows - 1); x++) {
                            items += "<div class='col-md-3 mt-2 sn'>";
                            items += "<input class='added form-control text-center' name='serialnumber[]' value='' placeholder='Enter / Scan Serial Number'>";
                            items += "</div>"
                        }
                    }
                }
                items += "</div>";
                $('#modalSerialNumbers .modal-body').html(items);
                window.setTimeout(function(){
                    $('.sn').eq(0).addClass('active-sn').find('input').focus();
                },500)
                
            }
        })
        $('#modalSerialNumbers').modal("show")
    })

    $('#save_serialnumbers').on("click", function(){
        var serialnumbers = [];
        $('#modalSerialNumbers input.added').each(function(i,j){
            let elem = $(this).val();
            if(elem!="") serialnumbers.push(elem);
        })

        let deliverynote_details_id = $('tr.bg-navy .deliverynote_details_id').val();
        let removed_sns = JSON.parse($('input[name=removed_sns]').val());
        if(removed_sns.length >0 ) {
            $.ajax({
                url: base_url + 'deliverynotes/removeSerialNumbers',
                data: {deliverynote_details_id,deliverynote_details_id,removed_sns:$('input[name=removed_sns]').val()},
                method:"POST",
                dataType:"JSON",
                success: function(response) {
                    // if(response.result){
                    //     $('#modalSerialNumbers').modal("hide")
                    // }else{
                    //     $('#modalSerialNumbers .modal-body').html("<div class='alert alert-danger'>"+response.reason+"</div>");
                    //     $('#save_serialnumbers').addClass("d-none");
                    // }
                }
            })
        }

        if(serialnumbers.length>0){
            
            $.ajax({
                url: base_url + 'deliverynotes/saveSerialNumbers',
                data: {serialnumbers:JSON.stringify(serialnumbers),deliverynote_details_id:deliverynote_details_id},
                method:"POST",
                dataType:"JSON",
                success: function(response) {
                    if(response.result){
                        $('#modalSerialNumbers').modal("hide")
                    }else{
                        $('#modalSerialNumbers .modal-body').html("<div class='alert alert-danger'>"+response.reason+"</div>");
                        $('#save_serialnumbers').addClass("d-none");
                    }
                }
            })
        }else{
            $('#modalSerialNumbers').modal("hide")
        }
    })

    $("body").on("click",".remove-sn", function() {
        let id = $(this).parent().data("id");
        let removed_sn_json = $('input[name=removed_sn]').val();
        let removed_sn = [];
        removed_sn = removed_sn_json.length > 0 ? JSON.parse(removed_sn_json) : removed_sn;
        removed_sn.push(id)
        console.log(removed_sn)
    })
    
})

