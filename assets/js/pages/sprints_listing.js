jQuery(function(){
    
    $(".monitor").on("change", function(){
        let customer_id = $('#customer_id').val();
        let order_by = $('#order_by').val();
        let order_dir = $('#order_dir').val();
        let display = $('#display').val();

        window.location.href = '/sprints/listing?order_by='+order_by+"&order_dir="+order_dir+"&display="+display+"&customer_id="+customer_id;
    })

})