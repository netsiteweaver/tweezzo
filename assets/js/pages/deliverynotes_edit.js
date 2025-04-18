jQuery(function(){
    $('.openCustomerModal').on("click",function(){
        var customer_id = $("#customer_id").val();
        $.ajax({
            url: base_url + "customers/fetch",
            method: "POST",
            dataType: "JSON",
            success: function(response) {
                $('#customersTable tbody').empty();
                var rows = "";
                $(response.customers).each(function(i,j){
                    let rowClass = (customer_id == j.customer_id) ? 'bg-navy' : '';
                    rows += "<tr data-uuid='" + j.uuid + "' class='select-customer cursor-pointer "+rowClass+"'>";
                    rows += "<td>" + j.company_name + "</td>";
                    rows += "<td>" + j.full_name + "</td>";
                    rows += "<td>" + j.phone_number1 + "</td>";
                    rows += "<td>" + j.email + "</td>";
                    rows += "</tr>";
                })
                $('#customersTable tbody').html(rows);
                $('#modalSelectCustomer').modal("show");
            }
        })
        $('#modalSelectCustomer').modal("show");
    })

    $('#customersTable').on("click",".select-customer",function(){
        let uuid = $(this).data("uuid");
        $(this).addClass("bg-navy");
        $.ajax({
            url: base_url + "customers/fetch",
            data:{uuid:uuid},
            method: "POST",
            dataType: "JSON",
            success: function(response) {
                $('input[name=customer_id]').val(response.customers[0].customer_id);
                $('#customer-info td.company').html(response.customers[0].company_name);
                $('#customer-info td.name').html(response.customers[0].full_name);
                $('#customer-info td.address').html(response.customers[0].address);
                $('#customer-info td.phone').html(response.customers[0].phone_number1 + ( ( ( response.customers[0].phone_number1.length>0 ) && ( response.customers[0].phone_number2.length > 0 ) ) ?  ( ', ' + response.customers[0].phone_number2) : '' ) );
                $('#customer-info td.email').html(response.customers[0].email);
                $('#modalSelectCustomer').modal("hide");
                $('#customer-info').closest(".row").removeClass("d-none");
            }
        })
    })

    $('#item-list').on('click', '.addRow', function () {
        var row = $('#item-list tbody tr').eq(0).clone();
        $(row).find('.row_id, .product_id, .stockref, .description, .category, .brand, .order_details_id').val("");
        $(row).find('.cost_price,.qty,.line-total').val("0");
        $('#item-list tbody').append(row)
    })

    $('#item-list').on('click', '.removeRow', function () {
        if ($('tr.item').length == 1) return;

        addToJSON("deleted_rows",$(this).closest("tr").find(".order_details_id").val());
        
        $(this).closest("tr").remove();

        calculate();
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

    $('#dn-form').submit(function(e){
        e.preventDefault();
        var valid = true;
        var error_message = "";
        var rows = 0;
        $('#item-list .item').each(function(i,j){
            if($(this).find('.product_id').val() !== '') rows++;
        })
        if($("input[name=customer_id]").val()==""){
            error_message += "<p>Please select a customer</p>";
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
            url:base_url+"deliverynotes/update",
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            success:function(response){
                if(response.result) {
                    // window.location.href = base_url + "deliverynotes/listing";
                    window.location.reload();
                }else{
                    bootbox.alert(response.msg)
                    return false;
                }
            },
            error:err=>{

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

function addToJSON(field, value){
    let deleted_rows = JSON.parse($('input[name='+field+']').val());
    deleted_rows.push(value);
    $('input[name='+field+']').val(JSON.stringify(deleted_rows))
}