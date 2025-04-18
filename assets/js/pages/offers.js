jQuery(function(){

    tableSort('#offers_listing','offers','id','display_order');

    var isAMG = false;

    $('#delete_line').on("click",function(){
        $(this).closest('.row').remove();
        $('#select_model_block').removeClass("hidden");
        $('input[name=saved_line_id]').val('');
    })

    $('select[name=line_id]').on('change',function(){
        let elem = $(this).val();
        $('input[name=saved_line_id]').val(elem);
    })

    $('#save_offers').on('submit',function(){
        let valid = true;
        let errorMessage = new Array();
        let uuid = $('input[name=uuid]').val();
        let line_id = $('select[name=line_id]').val();
        let saved_line_id = $('input[name=saved_line_id]').val();
        let offer_starts = $('input[name=offer_starts]').val();
        let offer_ends = $('input[name=offer_ends]').val();
        let duty_free_price = $('input[name=duty_free_price]').val();
        let duty_paid_price = $('input[name=duty_paid_price]').val();

        if(uuid==""){
            if(line_id==""){
                errorMessage.push("Please select a line before proceeding");
                valid = false;
            }
        }else{
            if(saved_line_id==""){
                errorMessage.push("Please select a line before proceeding");
                valid = false;
            }
        }

        if(offer_starts > offer_ends ){
            errorMessage.push("Offer Ends Should be Greater than Offer Starts");
            valid = false;
        }

        if(parseFloat(duty_free_price) > parseFloat(duty_paid_price) ){
            errorMessage.push("Duty Paid Price should be greater than Duty Free Price")
            valid = false;
        }

        if(!valid){
            var msg = "<b>Please correct the following errors:</b><br>";
            $(errorMessage).each(function(i,j){
                msg += (i+1)+" - "+j+"<br>";
            })
            toastr["error"](msg)
            return false;
        }
    })

    $('body').on('change','#select_model',function(){
        let elem = $(this).val();
        if(elem=="") {
            clearDropDown("submodel");
            clearDropDown("version");
            clearDropDown("line");
            return false;
        }

        $.ajax({
            url: base_url + "ajax/misc/getSubmodels",
            data: {model_id:elem},
            method:"POST",
            dataType:"JSON",
            success:function(response){
                if(response.result){
                    clearDropDown("submodel");
                    clearDropDown("version");
                    clearDropDown("line");
                    $('#select_submodel').append("<option value=''>Select Sub Model</option>")
                    $(response.data).each(function(i,j){
                        $('#select_submodel').append("<option data-amg='"+j.isAMG+"' value='"+j.id+"'>"+j.sub_model+"</option>")
                    })
                    $('#select_submodel_block').val('').removeClass("hidden");
                }else{
                    clearDropDown("submodel");
                    clearDropDown("version");
                    clearDropDown("line");
                }
            },
            complete: function(){

            }
        })
        
    })

    $('body').on('change','#select_submodel',function(){
        let elem = $(this).val();
        if(elem=="") {
            clearDropDown("version");
            clearDropDown("line");
            return false;
        }

        isAMG = $('#select_submodel :selected').data('amg');
        $('input[name=amg]').val(isAMG)

        $.ajax({
            url: base_url + "ajax/misc/getVersions",
            data: {submodel_id:elem},
            method:"POST",
            dataType:"JSON",
            success:function(response){
                if(response.result){
                    clearDropDown("version");
                    clearDropDown("line");
                    $('#select_version').append("<option value=''>Select Version</option>")
                    $(response.data).each(function(i,j){
                        $('#select_version').append("<option value='"+j.id+"'>"+j.version+"</option>")
                    })
                    $('#select_version_block').val('').removeClass("hidden");
                }else{
                    clearDropDown("version");
                    clearDropDown("line");
                }
            },
            complete: function(){

            }
        })
        
    })

    $('body').on('change','#select_version',function(){
        let elem = $(this).val();
        if(elem=="") {
            clearDropDown("line");
            return false;
        }

        let amg = $('#select_submodel :selected').data('amg');
        if(amg=='1') {
            $('input[name=label]').prop("readonly",true).removeClass("required");
            $('.label_block').addClass("hidden");
        }else{
            $('input[name=label]').prop("readonly",false).addClass("required");
            $('.label_block').removeClass("hidden");
        }

        $.ajax({
            url: base_url + "ajax/misc/getLines",
            data: {version_id:elem},
            method:"POST",
            dataType:"JSON",
            success:function(response){
                if(response.result){
                    if(isAMG) $('#line_id').val(response.data[0].id)
                    $('#select_line').find("option").remove();
                    $('#select_line').append("<option value=''>Select Version</option>")
                    $(response.data).each(function(i,j){
                        $('#select_line').append("<option value='"+j.id+"'>"+j.line+"</option>")
                        $('#select_line_block').val('').removeClass("hidden");
                    })
                    if(!isAMG) {
                        // $('#select_line_block').val('').removeClass("hidden");
                    }else{
                        alert(j.id)
                        $('input[name=saved_line_id]').val(j.id)
                    }
                }else{
                    clearDropDown("line");
                    toastr["error"]("The version you have selected has no line attached to it")
                }
            },
            complete: function(){

            }
        })
        
    })

})

function clearDropDown(element)
{
    console.log(element)
    $("#select_"+element).find("option").remove();
    $("#select_"+element+"_block").addClass("hidden");
    $("#select_"+element+"_block").find("select").val("");
}


var showPreview = function(event) {
    var output = document.getElementById('preview');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
      URL.revokeObjectURL(output.src) // free memory
    }
};
