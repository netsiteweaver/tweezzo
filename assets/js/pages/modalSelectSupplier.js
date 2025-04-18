jQuery(function(){

    $('#modalSelectSupplier').on('hidden.bs.modal', function (e) {
        $('#searchSupplier').val('');
        $('#productsTable tbody').empty();
        $('tr').removeClass("active");
        $('.targeted').select().removeClass("targeted");
    })

    $('#modalSelectSupplier').on('shown.bs.modal', function (e) {
        console.log("shown")
        $('#searchSupplier').trigger('focus');
    })
})
