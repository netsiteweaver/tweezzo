jQuery(function () {

    Search('searchText', 'productsTable');
    // Search('searchText','tbl1');


    $('#supplier_id').on('change', function () {
        var supplier_id = this.value;
        $.ajax({
            type: 'GET',
            url: base_url + "suppliers/get",
            method: 'POST',
            data: { supplier_id: supplier_id },
            dataType: "JSON",
            success: function (response) {
                let item = response.result;
                let data = item.company_name + "<br>" + item.address + "<br>Email: " + item.email + "<br>Phone: " + item.phone_number
                console.log(data)
                $('#supplier-info .company_name').val(response.result.company_name);
                $('#supplier-info .contact_person').val(response.result.full_name);
                $('#supplier-info .address').val(response.result.address);
                $('#supplier-info .phone').val(response.result.phone_number);
                $('#supplier-info .email').val(response.result.email);
                $('#supplier-info').removeClass("d-none");
            },
            complete: function () {

            }
        })

    });

    $('#selectProductModal').on('shown.bs.modal', function (e) {
        // $('#itemcodes').focus();

    })


    $('#productsTable').on('click', '.select_product', function () {
        var elem = $(this).closest('tr').data('uuid');
        getItemdetails(elem);
        // $("#itemcode").removeData('modal');
        // window.setTimeout(function(){
        // $('#itemcode').modal('hide');
        // },500)
        // $('tr.active .addRow').trigger("click");
        // $('table#item-list tbody tr:last-child').find(".item_id").focus();
    });


    $("body").on("click", ".stockref", function () {
        $(this).closest('tr').addClass('active');

        productModal();
    });


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
        console.log($(this).val())
        calculate();
    })

    $('.select2').select2({ placeholder: "Please Select here", width: "relative" })

    $('#gr-form').submit(function (e) {
        e.preventDefault();
        console.log(new FormData)
        var _this = $(this)
        $('.err-msg').remove();
        $('[name="po_no"]').removeClass('border-danger')
        if ($('#item-list .item').length <= 0) {
            bootbox.alert("Please add atleast 1 item on the list");
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

    $('.delete_purchase').on("click", function (e) {
        let number = $(this).closest("tr").data("number");
        let url = $(this).attr("href")
        let row = $(this).closest("tr");

        e.preventDefault();
        bootbox.confirm({
            message: "Are you sure you want to delete Purchase no. " + number + "?",
            buttons: {
                confirm: {
                    label: 'Yes, Delete It !',
                    className: 'btn-danger'
                },
                cancel: {
                    label: 'No',
                    className: 'btn-primary'
                }
            },
            callback: function (result) {
                if (result == true) {


                    console.log(url)
                    $.ajax({
                        url: url,
                        method: "POST",
                        dataType: "JSON",
                        success: function (response) {
                            toastr.info("Purchase " + number + " has been deleted");
                            $(row).remove();
                        }
                    })
                }
            }
        });

        return false;
    })

    $('#po-form_update').submit(function (e) {
        e.preventDefault();
        var _this = $(this)
        $('.err-msg').remove();
        $('[name="po_no"]').removeClass('border-danger')
        if ($('#item-list .item').length <= 0) {
            bootbox.alert("Please add atleast 1 item on the list");
            return false;
        }

        $.ajax({
            url: base_url + "purchases/update_purchase",
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                if (response.result) {
                    window.location.href = base_url + "/purchases/listing";
                } else {
                    bootbox.alert(response.msg)
                    return false;
                }
            }
        })
    })

    $('#item-list').on("keypress", ".qty", function (e) {
        var row = $(this);
        if (!e) e = window.event;
        var keyCode = e.keyCode || e.which;
        if (keyCode == '13') {
            e.preventDefault();
            $(row).closest("tr").find(".addRow").trigger("click");
            console.log("ENTER")
            $(row).closest('tr').next('tr').find(".stockref").trigger("click")
        }
    })

})

function isNumberKey(evt, element) {
    var charCode = (evt.which) ? evt.which : evt.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57) && !(charCode == 46 || charCode == 8))
        return false;
    else {
        var len = $(element).val().length;
        var index = $(element).val().indexOf('.');
        if (index > 0 && charCode == 46) {
            return false;
        }
        if (index > 0) {
            var CharAfterdot = (len + 1) - index;
            if (CharAfterdot > 3) {
                return false;
            }
        }

    }
    return true;
}

function getItemdetails(uuid) {
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

            $('tr.active').find('.category').val(response.record.category_name);
            $('tr.active').find('.brand').val(response.record.brand_name);

            $('tr.active').find('.cost_price').val(parseFloat(response.record.cost_price));

            $('tr.active .qty').val(1).addClass('targeted');
            calculate();

            $('#selectProductModal').modal('hide');
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



