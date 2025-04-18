jQuery(function(){

    $('.openSupplierModel').on("click",function(){
        $.ajax({
            url: base_url + "suppliers/fetch",
            method: "POST",
            dataType: "JSON",
            success: function(response) {
                $('#suppliersTable tbody').empty();
                var rows = "";
                $(response.suppliers).each(function(i,j){
                    rows += "<tr data-uuid='" + j.uuid + "' class='select-supplier cursor-pointer'>";
                    rows += "<td>" + j.company_name + "</td>";
                    rows += "<td>" + j.full_name + "</td>";
                    rows += "<td>" + j.phone_number + "</td>";
                    rows += "<td>" + j.email + "</td>";
                    rows += "</tr>";
                })
                $('#suppliersTable tbody').html(rows);
                $('#modalSelectCustomer').modal("show");
            }
        })
        $('#modalSelectSupplier').modal("show");
    })

    $('#suppliersTable').on("click",".select-supplier",function(){
        let uuid = $(this).data("uuid");
        $.ajax({
            url: base_url + "suppliers/fetch",
            data:{uuid:uuid},
            method: "POST",
            dataType: "JSON",
            success: function(response) {
                $('input[name=supplier_id]').val(response.suppliers[0].id);
                $('#supplier-info td.company').html(response.suppliers[0].company_name);
                $('#supplier-info td.name').html(response.suppliers[0].full_name);
                $('#supplier-info td.address').html(response.suppliers[0].address);
                $('#supplier-info td.phone').html(response.suppliers[0].phone_number);
                $('#supplier-info td.email').html(response.suppliers[0].email);
                $('#modalSelectSupplier').modal("hide");
                $('#supplier-info').closest(".row").removeClass("d-none");
            }
        })
    })

    $('#item-list').on('click', '.addRow', function () {
        var row = $('#item-list tbody tr').eq(0).clone();
        $(row).find('.product_id, .stockref, .description, .category, .brand').val("");
        $(row).find('.cost_price,.qty,.line-total').val("0");
        $('#item-list tbody').append(row)
    })

    $('#item-list').on('click', '.removeRow', function () {
        if ($('tr.item').length == 1) return;
        $(this).closest("tr").remove();
    })

    $('#item-list').on("keyup change blur", ".qty", function () {
        calculate();
    })

    $('#item-list').on('click',".stockref",function(){
        $(this).closest('tr').addClass('active');
        Overlay("on")
        fetchProducts();
        // $('#modalSelectProduct').modal("show");
    });

    $('#productsTable').on('click', '.select_product', function () {
        var elem = $(this).closest('tr').data('uuid');
        getItemdetails(elem);
    });

    $('#item-list').on("keypress", ".qty", function (e) {
        var row = $(this);
        if (!e) e = window.event;
        var keyCode = e.keyCode || e.which;
        if (keyCode == '13') {
            e.preventDefault();
            $(row).closest("tr").find(".addRow").trigger("click");
            $(row).closest('tr').next('tr').find(".stockref").trigger("click")
        }
    })

    $('#gr-form').submit(function (e) {
        e.preventDefault();
        var valid = true;
        var error_message = "";
        var rows = 0;
        $('#item-list .item').each(function(i,j){
            if($(this).find('.product_id').val() !== '') rows++;
        })
        if($("input[name=supplier_id]").val()==""){
            error_message += "<p>Please select a supplier</p>";
            valid = false;
        }
        if (rows == 0) {
            error_message += "<p>Please add at least 1 item on the list</p>";
            valid = false;
        }

        if(!valid){
            bootbox.alert(error_message);
            return false;
        }

        $.ajax({
            url: base_url + "goodsreceive/save",
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                if (response.result) {
                    window.location.href = base_url + "/goodsreceive/listing";
                } else {
                    bootbox.alert(response.msg)
                    return false;
                }
            }
        })
    })

})

function fetchProducts()
{
    $.ajax({
        url: base_url + "ajax/misc/getProducts",
        method: "GET",
        dataType: "JSON",
        success: function(response){
            populateProducts(response.products)
        }
    })
}

function populateProducts(products)
{
    $('#productsModal tbody tr').empty();
    let html = "";
    $(products).each(function(i,j){
        html += "<tr class='text-center' data-uuid='"+j.uuid+"'>";
        html += "<td class='select_product cursor-pointer'>"+j.stockref+"</td>";
        html += "<td class='select_product cursor-pointer'>"+j.name+"</td>";
        html += "<td class='select_product cursor-pointer'>"+j.category_name+"</td>";
        html += "<td class='select_product cursor-pointer'>"+j.brand_name+"</td>";
        html += "<td class='select_product cursor-pointer'>"+ parseFloat(j.cost_price).toLocaleString("en-US") +"</td>";
        html += "<td class='select_product cursor-pointer'>"+ parseFloat(j.selling_price).toLocaleString("en-US") +"</td>";
        html += "</tr>";
    })
    $('#productsTable tbody').append(html);
    $('#modalSelectProduct').modal("show");
    Overlay("off")
}

function getItemdetails(uuid) 
{
    Overlay("on");
    $.ajax({
        type: 'GET',
        url: base_url + "products/fetchByUuid",
        method: 'POST',
        data: { uuid: uuid },
        dataType: "JSON",
        success: function (response) {
            console.log(response)
            $('tr.active').find('.product_id').val(response.record.id);
            $('tr.active').find('.stockref').val(response.record.stockref);

            $('tr.active').find('.description').val(response.record.description);

            $('tr.active').find('.cost_price').val(parseFloat(response.record.selling_price));

            $('tr.active .qty').val(1).addClass('targeted');
            calculate();

            $('#modalSelectProduct').modal('hide');
        },
        complete: function () {
            Overlay("off")
        }
    })
}

function calculate() {

    var total_amount = 0
    var total_quantity = 0
    $('#item-list .item').each(function (i, j) {
        var qty = $(this).find(".qty").val();
        if (qty == '') {
            qty = 1;
        }
        var cost_price = $(this).find(".cost_price").val();
        var line_total = parseFloat(qty) * parseFloat(cost_price);
        total_quantity += parseFloat(qty);
        total_amount += line_total;
        $(this).find('.line_total').val(line_total)
    })
    $('.total_quantity').val(total_quantity);
    $('.total_amount').val(total_amount)
}
