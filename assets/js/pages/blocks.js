jQuery(function () {

    tableSort('#blocks','blocks','id','display_order');

    Search('searchText','blocks');

    $('body').on('click', '.addRow', function () {
        let newRow = $('.proto').clone();
        $('.table').append(newRow);
        $('.table tr:last').removeClass('proto hidden')
        $(this).closest('tr').find('.deleteRow').removeClass('hidden')
        $(this).addClass('hidden');
    })

    $('body').on('click', '.deleteRow', function () {
        $(this).closest('tr').remove();
    })


    $('input[name=name]').on('blur',function(){
        let block = $(this).val();
        let template = $('input[name=template]').val();
        if(template=='') {
            template = block.trim().toLowerCase().replace(/\s/g,"_")+'.php';
            $('input[name=template]').val(template)
        }
    })
})