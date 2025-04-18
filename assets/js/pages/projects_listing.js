jQuery(function(){
    
    $(".monitor").on("change", function(){
        let order_by = $('#order_by').val();
        let order_dir = $('#order_dir').val();
        let display = $('#display').val();

        window.location.href = "/projects/listing?order_by="+order_by+"&order_dir="+order_dir+"&display="+display;
    })

})