jQuery(function(){
console.log('222')
    $('#modalSelectCustomer').on('hidden.bs.modal', function (e) {
        $('#searchCustomer').val('');
        $('#productsTable tbody').empty();
        $('tr').removeClass("active");
        $('.targeted').select().removeClass("targeted");
    })

    $('#modalSelectCustomer').on('shown.bs.modal', function (e) {
        console.log("shown")
        $('#searchCustomer').trigger('focus');
    })
})
