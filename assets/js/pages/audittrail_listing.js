jQuery(function(){
    var start = $('input[name=start_date]').val();
    let end = $('input[name=end_date]').val();
    $('.date_filter_input').val(start+' - '+end);
    // var start = moment().subtract(29, 'days');
    // var end = moment();

    $('#rows_per_page').on('change',function(){
        let rpp = $(this).val();
        $.ajax({
            url: base_url + 'audittrail/set_rpp/'+rpp,
            success: function(){
                window.location.href = base_url + "audittrail/listing";
            }
        })
    })

    $('.user_filter').on('change',function(){
        let user = $(this).val();
        console.log(user);
    })

    $('.ip_filter').on('change',function(){
        let ip = $(this).val();
        console.log(ip);
    })

    $('.date_filter').daterangepicker({
        startDate: start,
        endDate: end,
        locale: {
            format: 'YYYY-MM-DD'
        },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
         },
        timePicker:false,
        "buttonClasses": "btn btn-warning btn-lg",
        "autoApply": true
    },function(start,end,label){
        $('.date_filter_input').val(start.format('YYYY-MM-DD')+' - '+end.format('YYYY-MM-DD'));
        $('input[name=start_date]').val(start.format('YYYY-MM-DD'));
        $('input[name=end_date]').val(end.format('YYYY-MM-DD'));
        $('.proceed').removeClass("btn-outline");
    });

    $('.monitor').on('change',function(){
        $('.proceed').removeClass("btn-outline");
    })

    $('.proceed').on('click',function(){
        if($(this).hasClass("btn-outline")) return false;
        let start_date = $('input[name=start_date]').val();
        let end_date = $('input[name=end_date]').val()
        let user = $('select[name=user_filter] :selected').val();
        let ip = $('select[name=ip_filter] :selected').val();
        let controller = $('input[name=controller_filter]').val();
        let method = $('input[name=method_filter]').val();

        console.log(start_date,end_date,user,ip)

        $.ajax({
            url: base_url + 'audittrail/filters',
            method:'POST',
            data:{start_date:start_date,end_date:end_date,user:user,ip:ip,controller:controller,method:method},
            success: function(){
                window.location.reload(false);
            }
        })
    })

    $('.reset').on('click',function(){

        $.ajax({
            url: base_url + 'audittrail/clearfilters',
            success: function(){
                window.location.reload(false);
            }
        })
    })

})