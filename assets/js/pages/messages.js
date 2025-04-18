jQuery(function(){
    
    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });

    $('#select_read_messages').on("click",function(){
        let checked = $(this).is(":checked");
        $('#customers_listing tbody tr').each(function(){
            if(checked) {
                $(this).find(".select_read_message").removeClass("d-none").attr("checked",true)
            }else{
                $(this).find(".select_read_message").removeAttr("checked")
            }
            
        })
    })

    $('#delete_read_messages').on("click",function(){
        alertify.confirm(
            'Are you sure you want to delete all read messages?', 
            function(){ 
                $.ajax({
                    url:base_url+"messages/deleteReadMessages",
                    method:"GET",
                    dataType: "JSON",
                    success:function(response){
                        if(response.result){
                            $('#customers_listing tbody tr').each(function(){
                                let uuid = $(this).data("id");
                                if(response.deletedRecords.indexOf(uuid) >= 0){
                                    $(this).remove();
                                }
                            })
                            window.setTimeout(function(){
                                window.location.href = base_url + "messages/listing/1";
                            },750)
                            
                        }
                    }
                })
                
            }, 
            function(){ 
                // alertify.error('Cancel')
            });
    })

    $('#submit_comments').on("submit",function(e){
        e.preventDefault();
        let comments = $('input[name=comments]').val();
        let id = $('input[name=id]').val();
        if(comments=="") return false;

        $.ajax({
            url: base_url + "orders/saveComments",
            data:{comments:comments,id:id},
            method:"POST",
            dataType:"JSON",
            success:function(response){
                if(response.result){
                    $('#comments-block-list').empty();
                    $('#comments-block .badge').removeClass('d-none').text(response.comments.length);
                    $(response.comments).each(function(i,j){
                        var line = '<div class="comment '+((i==response.comments.length-1)?'fade-in-text':'')+'">';
                        line += '<span class="author">'+j.created_on.substr(0,10)+' '+ j.userName + '</span> ';
                        line += '<span>'+j.comments+'</span>';
                        line += '</div>';
                        $('#comments-block-list').append(line);
                    })
                    $('#comments-block-list').scrollTop($('#comments-block-list')[0].scrollHeight);
                    $('input[name=comments]').val("").focus();
                }
            }
        })

        return false;
    })

    $('#submit_remarks').on("submit",function(e){
        e.preventDefault();
        if($('#save_remarks').hasClass("processing")) return false;

        let remarks = $('textarea[name=remarks]').val();
        let id = $('input[name=id]').val();
        if(remarks=="") return false;

        $('#save_remarks').addClass("processing disabled").removeClass("btn-outline-info").addClass("btn-warning");
        $.ajax({
            url: base_url + "orders/saveRemarks",
            data:{remarks:remarks,id:id},
            method:"POST",
            dataType:"JSON",
            success:function(response){
                if(response.result){
                    $('#save_remarks').removeClass("disabled processing").addClass("btn-outline-info").removeClass("btn-warning");
                }
            }
        })

        return false;
    })
})