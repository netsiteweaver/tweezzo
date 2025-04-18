jQuery(function(){

    let buttons = [
            {type:'dismiss',label:'Cancel',icon:'fa fa-times',buttonClass:'btn btn-info'},
            {type:'button',label:'Save',icon:'fa fa-save',buttonClass:'btn bg-navy',id:'save001'},
            {type:'button',label:'Cancel',icon:'fa fa-undo',buttonClass:'btn bg-yellow',id:'cancel001'}
    ]
    // createModal("modalForm","Hello");
    // $('#modalForm').modal("show");


    // $('body').on("click","#modal-save",function(){
        
    // })


    // const textInput = document.getElementById('textInput');
    // textInput.addEventListener('keydown', (event) => {
    //   if (event.key === 'Enter') {
    //     console.log('Enter key pressed!');
    //     // Perform desired actions here
    //     alert();
    //   }
    // });

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
        let goodsreceive_details_id = $(this).closest("tr").find(".goodsreceive_details_id").val();
        let product_id = $(this).closest("tr").find(".product_id").val();
        let goodsreceive_id = $("input[name=goodsreceive_id]").val();

        $(this).closest("tr").addClass("bg-navy");

        $.ajax({
            url: base_url + "goodsreceive/getserialnumbers",
            data:{goodsreceive_id:goodsreceive_id,goodsreceive_details_id:goodsreceive_details_id, product_id:product_id},
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

        if(serialnumbers.length>0){
            let goodsreceive_details_id = $('tr.bg-navy .goodsreceive_details_id').val();
            $.ajax({
                url: base_url + 'goodsreceive/saveSerialNumbers',
                data: {serialnumbers:JSON.stringify(serialnumbers),goodsreceive_details_id,goodsreceive_details_id},
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

