jQuery(function(){
    
    $(".monitor").on("change", function(){
        let active_filter = $('#active_filter').length ? $('#active_filter').val() : 'active';
        let customer_id = $('#customer_id').val();
        let order_by = $('#order_by').val();
        let order_dir = $('#order_dir').val();
        let display = $('#display').val();

        window.location.href = base_url + '/sprints/listing?active_filter='+encodeURIComponent(active_filter)+'&order_by='+order_by+"&order_dir="+order_dir+"&display="+display+"&customer_id="+customer_id;
    });

    $('body').on('click', '.sprint-toggle-active', function (e) {
        e.preventDefault();
        var t = $(this);
        var url = t.data('url');
        var uuid = t.data('uuid');
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            data: { uuid: uuid },
            success: function (d) {
                if (d.result && d.active !== undefined) {
                    var on = (String(d.active) === '1');
                    t.removeClass('btn-info btn-danger').addClass(on ? 'btn-info' : 'btn-danger');
                    t.find('i').removeClass('fa-check fa-times').addClass(on ? 'fa-check' : 'fa-times');
                    toastr['success'](on ? 'Sprint is active' : 'Sprint is inactive');
                } else {
                    toastr['error'](d.reason || 'Could not update sprint');
                }
            },
            error: function () {
                toastr['error']('Request failed');
            }
        });
    });

})