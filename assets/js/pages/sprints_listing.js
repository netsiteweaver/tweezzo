jQuery(function(){
    
    $(".monitor").on("change", function(){
        let active_filter = $('#active_filter').length ? $('#active_filter').val() : 'active';
        let customer_id = $('#customer_id').val();
        let order_by = $('#order_by').val();
        let order_dir = $('#order_dir').val();
        let display = $('#display').val();

        window.location.href = base_url + '/sprints/listing?active_filter='+encodeURIComponent(active_filter)+'&order_by='+order_by+"&order_dir="+order_dir+"&display="+display+"&customer_id="+customer_id;
    })

})