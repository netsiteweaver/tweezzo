jQuery(function(){
    $('#add').on('click',function(){
        let row = $('#dashboard-access div').eq(1);
        let clone = $(row).clone();
        $(clone).find('input').val('');
        $('#dashboard-access .box-body').eq(0).append(clone)
    })

    $('#dashboard-access').on('click','.remove',function(){
        $(this).closest(".row").remove();
    })

    $('#update').on("click",function(){
        let valid = true;
        var rows = [];
        $('#dashboard-access .box-body .row').each(function(i,row){
            let lbl = $(row).find(".lbl").val();
            let url = $(row).find(".url").val();
            let cls = $(row).find(".cls").val();
            let icon = $(row).find(".icon").val();
            let width = $(row).find(".width :selected").val();

            if( (typeof lbl !== 'undefined') && (lbl.length>0) ){
                let line = {
                    label:lbl,
                    url:url,
                    class:cls,
                    icon:icon,
                    width:width
                };

                rows.push(line);
            }
        })

        $.ajax({
            url: base_url+"settings/update_dashboard",
            method:"POST",
            data:{rows:rows},
            success:function(response){
                window.location.reload();
            }
        })
    })
})