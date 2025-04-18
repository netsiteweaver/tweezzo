jQuery(function(){

    $('#modalSelectProduct').on('hidden.bs.modal', function (e) {
        $('#searchText').val('');
        $('#productsTable tbody').empty();
        $('tr').removeClass("active");
        $('.targeted').select().removeClass("targeted");
    })

    $('#modalSelectProduct').on('shown.bs.modal', function (e) {
        $('#searchText').trigger('focus');
    })
})
