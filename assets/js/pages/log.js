$(document).ready(function(){
    var rows_per_page = window.location.toString().replace(base_url,"").split('/')[2]
    // alert(rows_per_page)
    // alert(isNaN(rows_per_page))
    // var page = window.location.toString().replace(base_url,"").split('/')[3]
    if (typeof rows_per_page == 'undefined') rows_per_page =  25;
    if (isNaN(rows_per_page)){
        rows_per_page =  25;
        window.location.href = base_url + 'log/listing/' + rows_per_page
    }
    // if(typeof page == 'undefined') page =  1;
    // console.log(rows_per_page,page)
    $('#rows_per_page').on('change',function(){
        var rpp = $(this).val()
        window.location.href = base_url + 'log/listing/'+rpp
    })

    $('#filter_user').on('change',function(){
        var user = $(this).val()
        $.ajax({
            url:base_url+"log/filter",
            type:"POST",
            data:{user:user},
            success:function(){
                window.location.href = base_url + 'log/listing/'+rows_per_page
            }
        })
    })

})