const urlSearchParams = new URLSearchParams(window.location.search);
const params = Object.fromEntries(urlSearchParams.entries());
var sd,ed;
var selectedStartDate, selectedEndDate;
getInitialDates();
if(Object.entries(params).length > 0){
    if(typeof params.start_date === 'undefined') {
        startDate = moment().endOf("month");
        sd = startDate.format("YYYY-MM-DD");
    }else{
        startDate = new Date(params.start_date);
        sd = params.start_date;
    }
    if(typeof params.end_date === 'undefined') {
        endDate = moment().endOf("month");
        ed = endDate.format("YYYY-MM-DD");
    }else{
        endDate = new Date(params.end_date);
        ed = params.end_date
    }
}else{
    startDate = moment().startOf("month");
    endDate = moment().endOf("month");
    sd = startDate.format("YYYY-MM-DD");
    ed = endDate.format("YYYY-MM-DD");
}
var hiddenFields = ['customer_id','uuid',];
function display(field,string)
{
    if( (string !== null) && (string.length>0) ){
        field = field.replace("_"," ").toUpperCase();
        var output = '<div class="col-md-6"><div class="form-control">';
        output += "<b>"+field+"</b>: "+string;
        output += '</div></div>';
        return output
    }else{
        return "";
    }

}
jQuery(function(){

    // _autocomplete();

    $('.show-order-details').on("click",function(){
        if($(this).hasClass("fa-plus-square")){
            $("tr.active").removeClass("active");
            $('.order-details').addClass("d-none");
            $('.fa-minus-square').removeClass("fa-minus-square").addClass("fa-plus-square");

            $(this).removeClass("fa-plus-square").addClass("fa-minus-square");
            $(this).closest("tr").addClass("active")
            $(this).closest("tr").next("tr").find("table").removeClass("d-none").find("tr").addClass("active");
        }else{
            $(this).removeClass("fa-minus-square").addClass("fa-plus-square");
            $(this).closest("tr").next("tr").find("table").addClass("d-none");
            $("tr.active").removeClass("active")
        }
    })

    $('.view-customer').on("click",function(){
        let id = $(this).data("customer-id");
        $.ajax({
            url: base_url + "customers/getById",
            method: "POST",
            data:{id:id},
            success: function(response){
                var html = '<div class="row">';
                $.each(response.customer, function(field, data){
                    if( (field !== 'customer_id') && (field !== 'uuid') && (field !== 'created_by') && (field !== 'status') ) {
                        html += display(field,((data!==null)?data:''));
                    }
                })
                html += "</div>";
                $('#customerInfoModal').find(".modal-body").html(html);
                $('.view-customer-history').data("customer-id",response.customer.customer_id)
                $('#customerInfoModal').modal("show");
            }
        })
    })

    $('.view-customer-history').on("click",function(){
        let id = $(this).data("customer-id");
        $.ajax({
            url: base_url + "customers/getHistoryById",
            method: "POST",
            data:{id:id},
            success: function(response){
                $('#customer-history tbody').empty()
                $.each(response.history, function(i,row){
                    var line = "<tr>";
                    line += "<td>"+row.order_date+"</td>";
                    line += "<td>"+row.store+"</td>";

                    line += "<td>"+row.delivery_datetime+"</td>";
                    line += "<td>"+row.delivery_store+"</td>";

                    line += "<td>"+row.document_number+"</td>";

                    line += "<td>"+row.amount+"</td>";
                    line += "<td>"+ ( (row.discount==0) ? '0' : ("("+row.discount_pct+"%)" +row.discount) ) +"</td>";
                    line += "<td>"+(row.amount - row.discount)+"</td>";
                    line += "<td>"+row.amount+"</td>";
                    
                    line += "<td>"+row.deposit+"</td>";
                    line += "<td>"+row.agent+"</td>";
                    line += "<td>"+row.stage+"</td>";
                    line += "</tr>";
                    $('#customer-history tbody').append(line);
                })
                $('#customerHistoryModal').modal("show");
            }
        })
    })
console.log(startDate,endDate)
    $('.daterange').daterangepicker({
        autoApply: true,
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
          'This Year'   : [moment().startOf("year"),moment()],
          'Last Year'   : [moment().subtract(1,'year').startOf("year"), moment().subtract(1,'year').endOf("year")]
        },
        startDate: startDate,
        endDate  : endDate
    }, function (start, end) {
        let search_text = $('#search_text').val();
        let stage = $('#filter_stage :selected').val();
        let qs = base_url + "deliverynotes/listing?start_date="+start.format("YYYY-MM-DD")+"&end_date="+end.format("YYYY-MM-DD")+"&search_text="+search_text+"&stage="+stage;
        selectedStartDate = start.format("YYYY-MM-DD");
        selectedEndDate = end.format("YYYY-MM-DD");
    });

    $('#filter_stage, #filter_channel, select[name=deleted]').on("change",function(){
        // $('#apply-filter').trigger("click");
    })

    $('#optionDeleted').on("click",function(){
        $('#deletedOrders').modal("hide");
        $('#apply-filter').trigger("click");
    })

    $('#search_text').on("focus",function(){
        $('.searchable .fas').removeClass("d-none");
    })

    $('#search_text').on("blur",function(){
        $('.searchable .fas').addClass("d-none")
    })

    $('.clear-search').on("click",function(){
        let elem = $(this).siblings("input").val();
        if(elem=="") return false;
        $(this).siblings("input").val("");
        $('#apply-filter').trigger("click")
    })

    $('#apply-filter').on('click',function(){
        let search_text = $('#search_text').val();
        let rpp = $('#rpp').val();
        let qs = base_url + "deliverynotes/listing?start_date="+selectedStartDate+"&end_date="+selectedEndDate+"&search_text="+search_text+ "&display="+rpp;
        window.location.href = qs;
    })

    $('.wizard').on("click",function(){
        let step = $(this).data("step");
        $('.wizard').removeClass("btn-info").addClass("btn-default")
        $(this).removeClass("btn-default").addClass("btn-info")
        $('.steps').collapse("hide")
        $("#"+step).collapse("show")
    })

    $('.select-category').on("click",function(){
        let id = $(this).data("id");
        $.ajax({
            url : base_url + "products/getByCategory",
            data: {id:id},
            method:"POST",
            dataType: "JSON",
            success: function(response) {
                $('#vetements').empty();
                $(response.products).each(function(i,row){
                    var line = "<div class='btn btn-default select-vetement' data-id='"+row.id+"'>";
                    line += "<img src='"+base_url+"uploads/products/"+row.photo+"' class='img-thumbnail'>"
                    line += "</div>";
                    $('#vetements').append(line);
                })
                $('.wizard').removeClass("btn-info").addClass("btn-default")
                $('.wizard').eq(2).removeClass("btn-default").addClass("btn-info")
                $('#step2').collapse("hide")
                $('#step3').collapse("show")
            }
        })
    })

    $('.getProducts').on("click", function() {
        $.ajax({
            url : base_url + "products/get",
            method:"POST",
            dataType: "JSON",
            success: function(response) {
                $('#vetements').empty();
                $(response.products).each(function(i,row){
                    var line = "<div data-id='"+row.id+"' class='col-md-3 cursor-pointer select-vetement'>"
                    line += "<img src='"+base_url+"uploads/products/"+row.photo+"' class='img-thumbnail' style='vertical-align:top;'>"
                    line += "<div class='order-vetement-item'>"+row.name+"</div>";
                    line += "</div>";
                    $('#vetements').append(line);
                })
                $('.wizard').eq(0).removeClass("btn-info").addClass("btn-default")
                // $('.wizard').removeClass("btn-info").addClass("btn-default")
                // $('.wizard').eq(2).removeClass("btn-default").addClass("btn-info")
                $('#step1').collapse("hide")
                $('#step3').collapse("show")
            }
        })
    })

    $('#vetements').on("click",".select-vetement", function() {
        let id = $(this).data("id");
        $('.wizard').eq(0).removeClass("btn-info").addClass("btn-default")
        $('.wizard').eq(1).removeClass("btn-info").addClass("btn-default")
        $('.wizard').eq(2).removeClass("btn-default").addClass("btn-info")

        $('#step1').collapse("hide")
        $('#step3').collapse("hide")
        $('#step4').collapse("show")
})

    $('.search-option').on('click',function(){
        let option_enabled = $(this).find('span i').hasClass('fa-check');
        if(!option_enabled){
            $(this).find('span i').addClass('fa-check').removeClass('fa-square-o')
        }else{
            $(this).find('span i').removeClass('fa-check').addClass('fa-square-o')
        }
        $('#itemcodes').val('').focus();
        $('#productsModal tbody').empty();
    })

    $('#customer_id').on('change',function(){
		var uuid = this.value;

		$.ajax({
            type: 'GET',
            url:base_url+"ajax/misc/getCustomerDetails", 
            method:'POST',
            data:{uuid:uuid},
            dataType:"JSON",
            success:function(response){
                console.log(response)
                var customer_details = response.customer[0].title + " " + response.customer[0].first_name + " " + response.customer[0].last_name;
                if(response.customer[0].nic.length>0) customer_details += "\nNIC: " + response.customer[0].nic;
                if(response.customer[0].address.length>0) customer_details += "\nAddress: " + response.customer[0].address;
                if(response.customer[0].city.length>0) customer_details += "\nCity: " + response.customer[0].city;
                if(response.customer[0].phone_number1.length>0) customer_details += "\nPhone 1: " + response.customer[0].phone_number1;
                if(response.customer[0].phone_number2.length>0) customer_details += "\nPhone 2: " + response.customer[0].phone_number2;
                $('#customer_details').val(customer_details); 
            },
            complete: function()
            {
                
            }
        })
           
      }); 

    $('#itemcode').on('shown.bs.modal', function (e) {
        $('#itemcodes').focus();
    })

    $('#productsModal').on('click', '.select_product',function(){
        var elem = $(this).closest('tr').data('uuid');
        getItemdetails1(elem);
    }); 

    $('#itemcode').on('hidden.bs.modal', function (e) {
        $('#itemcodes').val('');
        $('#productsModal tbody').empty();
        $('tr').removeClass("active");
      })

    $(".stockref").on('click',function(){
        $(this).closest('tr').addClass('active');
        fetchProducts();
        $('#itemcode').modal("show");
    });

    

    $('#item-list').on('click','.add_row',function(){
        
        var tr = $('#item-clone tr').clone()
        $('#item-list tbody').append(tr)
       
        tr.find('[name="qty[]"],[name="unit_price[]"]').on('input keypress',function(e){
           
            calculate()
        })
       
        tr.find(".stockref").on('click',function(){
            $(this).closest('tr').addClass('active');
            $('#itemcode').modal("show");
        });

 
    })


    if($('#item-list .po-item').length > 0){

        $('#item-list .po-item').each(function(){
            var tr = $(this)
          
            tr.find('[name="qty[]"],[name="unit_price[]"]').on('input keypress',function(e){
                calculate();
            })

            $('#item-list tfoot').find('[name="discount"]').on('input keypress',function(e){
                calculate();
            })
        
            tr.find('[name="qty[]"],[name="unit_price[]"]').trigger('keypress')
        })
    }else{
    $('#add_row').trigger('click')
    }

    $('#add_customer').on("click",function(){
        $('#add_customer_modal').modal("show");
    })

    $('#add_customer_modal').on('shown.bs.modal', function (e) {
        $('#add_customer_form input[name=phone1]').focus();
    })

    $('#add_customer_modal').on('hidden.bs.modal', function (e) {
        emptyForm();
    })

    $("#save_customer").on("click",function(){
        let valid = true;
        let error_message = "<h4>Add Customer</h4>";
        let phone_number1 = $('#add_customer_form input[name=phone_number1]').val();
        let phone_number2 = $('#add_customer_form input[name=phone_number2]').val();
        let first_name = $('#add_customer_form input[name=first_name]').val();
        let last_name = $('#add_customer_form input[name=last_name]').val();
        let email = $('#add_customer_form input[name=email]').val();
        let address = $('#add_customer_form textarea[name=address]').val();
        let city = $('#add_customer_form input[name=city]').val();
        let nic = $('input[name=nic]').val();

        let pattern = /^[0-9]{7,8}$/;
        let check_phone1 = pattern.test(phone_number1);
        let check_phone2 = pattern.test(phone_number2);
        if( ( !check_phone1 ) || (!check_phone2) ){
            valid = false;
            error_message += "<p>Please enter a valid phone or mobile number (7 or 8 digits)</p>";
        }
        if( (first_name.length==0) && (last_name.length == 0)){
            valid = false;
            error_message += "<p>Please enter client's name</p>";
        }

        let nicPattern = /^[A-Z]{1}[0-9]{12}[A-Z0-9]{1}$/;
        let checkNic = nicPattern.test(nic)
        if( !checkNic ){
            valid = false;
            error_message += "<p>Please enter a valid NIC</p>";
        }
        
        if(!valid){
            bootbox.alert(error_message);
            return false;
        }

        $.ajax({
            url: base_url + "customers/quick_save",
            type:"POST",
            dataType:"JSON",
            data:$('form#add_customer_form').serialize(),
            // data:{phone_number1:phone1,phone_number2:phone2,first_name:first_name,last_name:last_name,email:email,address:address,city:city,region:region},
            success:function(response)
            {
                if(response.result){
                    $('#customer_id').append("<option value='"+response.uuid+"'>"+first_name+" "+last_name+" - "+phone_number1+"</option>"); 
                    $('#customer_id').val(response.uuid);
                    $('#customer_id').trigger("change");
                    $('#add_customer_modal').modal("hide");
                }else{
                    toastr["error"](response.error_message) 
                }
            }
        })
        
    })

    $('#add_customer_modal').on("shown.bs.modal",function(){
        $('#add_customer_modal input[name=first_name]').trigger("focus")
    })

    $('.select2').select2({placeholder:"Please Select here",width:"relative"})

    $('#po-form').submit(function(e){
        e.preventDefault();
        let status = true;

        var _this = $(this);
        $('.err-msg').remove();
        $('[name="po_no"]').removeClass('border-danger')

        let basket = 0;
        $('#item-list tbody tr.po-item').each(function(i,row){
            let stockref = $(this).find(".stockref").val();
            if(stockref.length>0) basket++;
        })

        if(basket == 0){
            bootbox.alert("Please add at least 1 item on the list.")
            status = false;
        }

        if( (!status) || (!GlobalFormStatus) )
        {
            return false;
        }

        $.ajax({
            url:base_url+"sales/save_sale",
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            success:function(response){
                console.log(response);
                if(response.result) {
                    let channel_id = $('#channel_id').val();
                    sessionStorage.setItem("sales_channel_id",channel_id);

                    window.location.href = base_url+"sales/add";
                }else{
                    bootbox.alert(response.msg)
                    return false;
                }
            },
            error:err=>{

            }
        })
    })



    $('#po-form_update').submit(function(e){
       
        e.preventDefault();
        var _this = $(this)
        $('.err-msg').remove();
       
        if($('#item-list .po-item').length <= 0){
            bootbox.alert("Please add at least 1 item on the list.")
            return false;
        }
        
        $.ajax({
            url:base_url+"sales/update_sale",
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            error:err=>{
                //console.log(err)
            },
            success:function(response){
                console.log(response);
                if(response.result) {
                    window.location.href = base_url+"/sales/listing";
                   }else{
                    bootbox.alert(response.msg)     
                   }
            }
        })
    })

    let ordersListingCompress = localStorage.getItem("ordersListingCompress");
    if(typeof ordersListingCompress == 'undefined') ordersListingCompress==0;

    if(ordersListingCompress == 1) {
        window.setTimeout(function(){
            $('.compress-table').trigger("click")
        },100)
    } 

    $('.compress-table').on("click", function() {
        if($(this).hasClass("compressed")){
            $(this).removeClass("compressed");
            $(".compress-table .fa-compress").removeClass("hidden");
            $(".compress-table .fa-expand").addClass("hidden");
            $('table .compress').removeClass("hidden");
            localStorage.setItem("ordersListingCompress",0)
        }else{
            $(this).addClass("compressed");
            $(".compress-table .fa-compress").addClass("hidden");
            $(".compress-table .fa-expand").removeClass("hidden");
            $('table .compress').addClass("hidden");
            localStorage.setItem("ordersListingCompress",1)
        }
    })


})

function rem_item(_this){
    if($('#item-list .po-item').length > 1){
        _this.closest('tr').remove();   
    }  
}

function rem_item1(_this){
    _this.closest('tr').add()
}

function getItemdetails(id,tr)
{
   $.ajax({
    type: 'GET',
    url:base_url+"sales/getProductDetailsbyId", 
    method:'POST',
    data:{q:id},
    dataType:"JSON",
    success:function(response){
        $('tr.active').find('.unit_price').val(parseFloat(response.response[0].selling_price)+parseFloat(response.response[0].delivery_fee).toLocaleString("en-US"));
        $('tr.active').find('.category').text(response.response[0].category_name); 
        $('tr.active').find('.description').val(response.response[0].product_name); 
    },
    complete: function()
    {
        
    }
})

}


function getSalesDetails(id){
   
   $.ajax({
    type: 'GET',
    url:base_url+"sales/getSalesDetails", 
    method:'POST',
    data:{q:id},
    dataType:"JSON",
    success:function(response){
  
     $("#saledetails_table tbody").empty();      
    $.each(response, function (i, item) {
        
        $('<tr data-id="'+item.id+'">').append(
        $('<td class="select_sales_details">').text(item.description),
        $('<td class="select_sales_details">').text(item.quantity),
        $('<td class="select_sales_details">').text(item.price),
        $('<td class="select_sales_details">').text(item.total)
        ).appendTo('#saledetails_table tbody');       
    
    });
    },
    complete: function()
    {
        $('#salesdetails').modal("show");
    }
})

}


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

function getItemdetails1(uuid)
{
    // Overlay("on");
   $.ajax({
    url:base_url+"ajax/misc/getProduct", 
    method:'POST',
    data:{uuid:uuid},
    dataType:"JSON",
    success:function(response){
    
        $('tr.active').find('.stockref').val(response.product[0].stockref); 
        $('tr.active').find('.description').val(response.product[0].description); 
        $('tr.active').find('.category').val(response.product[0].category_name); 
        $('tr.active').find('.price').val(parseFloat(response.product[0].selling_price));
        // let productName = response.response[0].product_name;
        // if(response.response[0].color_name !== null) productName += " | Color: "+response.response[0].color_name;
        // if( (response.response[0].size !== null) && (response.response[0].size.length >0) ) productName += " | Size: "+response.response[0].size;
        
        // $('tr.active').find('.description').val(productName); 
        // $('tr.active').find('.stockref').val(response.response[0].stockref); 
        // $('tr.active').find('.item_id').val(response.response[0].product_id); 
        // $('tr.active .qty').val(1)
        
        // calculate();

        $('#itemcode').modal('hide');
    },
    complete: function()
    {
        Overlay("off");
    }
})

}

function calculate(){
    var _total = 0
    $('#item-list .po-item').each(function(i,j){
        var qty = $(this).find("[name='qty[]']").val();
        var unit_price = $(this).find("[name='unit_price[]']").val();
        //if(qty > 0 && unit_price > 0){
            var row_total = parseFloat(qty) * parseFloat(unit_price);
            _total += row_total;
        //}
        $(this).find('#total-price').val(parseFloat(row_total).toLocaleString('en-US'))
    })
    var discount_amount = $('#discount').val();
    $('#sub_total').val(parseFloat(_total).toLocaleString("en-US"))
    $('#total').val(parseFloat(_total-discount_amount).toLocaleString("en-US"))
}


function getCurrentDate()
{
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; 
    var yyyy = today.getFullYear();
    var date = "2011-09-29";
    if(dd<10) 
    {
        dd='0'+dd;
    } 

    if(mm<10) 
    {
        mm='0'+mm;
    } 
    var date1 = yyyy+'-'+mm+'-'+dd;

    return date1;
}

// function _autocomplete(){
//     $( "#itemcodes" ).autocomplete({
//         source: function (request, response) {
//             let options = [];
//             $('.search-option').each(function(i,row){
//                 let option = $(this).data("option");
//                 let check = $(this).find('span i').hasClass("fa-check");
//                 if(check){
//                     options.push(option)
//                 }
//             })
//             if(options.length == 0) {
//                 bootbox.alert("No columns selected");
//                 return false;
//             }
//             console.log(options.length)
//             $.ajax({
//                 url: base_url+"ajax/misc/search_items",
//                 type: "POST",
//                 data: {data:request,search_in:options},
//                 dataType: 'json',
//                 success: function (response) {
//                     populateProducts(response.products)
//                 }
//             });
//             }
//         });
// }



function emptyForm()
{
    $('#add_customer_form input[name=phone1]').val('');
    $('#add_customer_form input[name=phone2]').val('');
    $('#add_customer_form input[name=first_name]').val('');
    $('#add_customer_form input[name=last_name]').val('');
    $('#add_customer_form input[name=email]').val('');
    $('#add_customer_form input[name=city]').val('');
    $('#add_customer_form textarea[name=address]').val('');
}

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
        html += "<td class='select_product cursor-pointer'>"+j.description+"</td>";
        html += "<td class='select_product cursor-pointer'>"+j.selling_price+"</td>";
        html += "</tr>";
    })
    $('#productsModal tbody').append(html);
}

function getInitialDates()
{
    window.setTimeout(function(){
        let dt = $('.daterange').val();
        let dts = dt.split(' - ');
        selectedStartDate = moment(dts[0],'MM/DD/YYYY').format("YYYY-MM-DD");
        selectedEndDate = moment(dts[1],'MM/DD/YYYY').format("YYYY-MM-DD");
    },500)
}