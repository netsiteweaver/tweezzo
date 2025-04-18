jQuery(function(){
    Search("searchText","inventory");

    $('.adjust-inventory').on("click", function() {
        $('#adjustment tbody tr').each(function(i,j) {
            let uuid = $(this).data("uuid");
            let qty = $(this).find(".adjust_to").val();
            console.log(i,uuid, qty);
            
        })
    })
})