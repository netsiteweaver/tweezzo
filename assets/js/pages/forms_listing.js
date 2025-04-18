jQuery(function(){
    $('#export').on('click',function(){
        $('#export-options').modal("show");
    })
    $('#export-form-submissions').on('click',function(){
        let format = $('.select-export-type.btn-info').data('type');

        const params = new Proxy(new URLSearchParams(window.location.search), {
            get: (searchParams, prop) => searchParams.get(prop),
          });
        let type = params.type; // "some_value"
        if(type == null) {
            window.location.href = base_url + 'forms/export';
        }else{
            window.location.href = base_url + 'forms/export?type='+type+'&format='+format;
        }
        $('#export-options').modal("hide");
    })

    $('.select-export-type').on('click',function(){
        $('.select-export-type').removeClass("btn-info").addClass("btn-default");
        $(this).addClass('btn-info');
    })

    $('.select-type').on('click',function(){
        let type = $(this).data('type');

        var pathArray = window.location.pathname.split('/');
        var pageIndex = pathArray.indexOf('listing') + 1;
        if(pathArray[pageIndex] == 'undefined') pathArray[pageIndex] = 1;
        if(pathArray.length == pageIndex) pathArray[pageIndex] = 1
        // console.log(pathArray.length,pathArray,pageIndex,pathArray[pageIndex]);
        if(type.length==0){
            window.location.href = base_url+'forms/listing/'+pathArray[pageIndex];
        }else{
            window.location.href = base_url+'forms/listing/'+pathArray[pageIndex]+'?type='+type;
        }
        
    })

    $('.view-modal').on('click',function(){
        Overlay('on');
        let uuid = $(this).data('uuid');
        $.ajax({
            url: base_url + 'forms/fetch?uuid='+uuid,
            dataType:'JSON',
            success: function(data) {
                if(data.result==true){
                    $('#content').html(data.data);
                    $('#view-enquiry').modal('show')
                }
            },
            complete: function() {
                Overlay("off");
            }
        })
    })
})
