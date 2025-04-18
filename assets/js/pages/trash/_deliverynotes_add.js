var measurements = [];
var measurementZones = [];
var step = 0;
var data = new Object;
data.customer_id = data.product_id = data.measurements = data.delivery_date = data.delivery_time = data.delivery_store = data.fabric_reference = data.fabric_color = data.size = null;
data.pictures = [];
data.additional_fields = [];
data.additional_fields_data = [];
var minimumDepositPct = 0;
var orderDetailsNotes = [];

var cart = new Object;
cart.customer = new Object;
cart.orderRows = [];
cart.delivery = new Object;

const url = window.location.href;
const target = url.split('#')[1];

if(typeof target !== 'undefined') {
    $('#'+target+'-tab').trigger("click");
}

$('.nav-link').on("click",function(){
    let target = $(this).data("target");
    
    if(typeof target !== 'undefined') {
        window.history.pushState({ page: 1 }, "New Order", url.split('#')[0]+target);
    }
})

jQuery(function(){

    timer = 0;
    function mySearch (){ 
        var searchTerm = $('#modalSearchClients').val();
        console.log(searchTerm); 
        if(searchTerm.length==0){
            $('#select_customer tbody').empty();
        }else if(searchTerm.length>=2){
            fetchClients(searchTerm,false);
        }else{

        }
    }

    init();

    // region modalAddClient

    $('#modalAddClient').on("shown.bs.modal", function () {
        $('#customer_code').trigger("focus")
    })

    $('#modalAddClient').on("hidden.bs.modal", function () {
        $('#modalAddCustomer input').each(function () {
            $(this).val("")
        })
    })

    // endregion modalAddClient



    // region modalMeasurements

    $('#add_deliverynote').on("click",".get-measurements",function(){
        $('#modalMeasurements').modal({backdrop: 'static', keyboard: false},"show") 
    })

    $('#items').on("click",".enterMeasurements",function() {
        let m = $(this).closest("tr").find(".measurements").val();
        if(m !== '') {
            selectRow($(this));

            let product_id = $(this).closest("tr").find(".product_id").val();
            if(product_id == "") {
                toastr["error"]("Please select a product first");
                unselectRow();
                return;
            }
            var _measurementsJson = $('tr.selected').find('.measurements').val();
            if(_measurementsJson == "") {
                toastr["error"]("No measurements have been recorded yet");
                unselectRow();
                return;
            } else {
                var _measurements = JSON.parse(_measurementsJson);
                console.log(_measurements)

                var lines = "";
                $(_measurements).each(function(i,j){
                    lines += "<tr class='text-center' data-index="+i+">";
                    lines += "<td>"+j.zone+"</td>";
                    lines += "<td><span class='form-control text-center'>"+j.value+"</span><input type='text' class='form-control text-center hidden' value='"+j.value+"'></td>";
                    lines += "<td><span class='form-control text-center'>"+j.remarks+"</span><input type='text' class='form-control text-center hidden' value='"+j.remarks+"'></td>";
                    lines += "<td>";
                    lines += "<div class='btn btn-xs btn-info edit-measurement'><i class='fa fa-edit'></i></div>";
                    lines += " <div class='btn btn-xs btn-success save-measurement hidden'><i class='fa fa-save'></i></div>";
                    lines += "</td>";
                    lines += "</tr>";
                })

                let vetement_size = $('tr.selected').find(".size").val()
                lines += "<tr class='text-center size' data-index='999'>";
                lines += "<td>Size</td>";
                lines += "<td><span class='form-control text-center'>"+vetement_size+"</span><input type='text' class='form-control text-center hidden' value='"+vetement_size+"'></td>";
                lines += "<td></td>";
                lines += "<td>";
                lines += "<div class='btn btn-xs btn-info edit-size'><i class='fa fa-edit'></i></div>";
                lines += "<div class='btn btn-xs btn-success save-size hidden'><i class='fa fa-save'></i></div>";
                lines += "</td>";
                lines += "</tr>";
            }

            $('#viewMeasurementsModal table tbody').empty().append(lines)
            $('#viewMeasurementsModal').modal({backdrop: 'static', keyboard: false},"show") 

            // $(this).closest('tr').find(".viewMeasurements").trigger("click");
            return;
        }
        let product_id = $(this).closest("tr").find(".product_id").val();
        if(product_id == "") {
            toastr["error"]("Please select a product first");
            return;
        }
        selectRow($(this));

        let imageVetement = $(this).closest("tr").find(".image_vetement").val();
        let image_map = $(this).closest("tr").find(".image_map").val();
        
        // console.log(imageVetement, image_map);

        let html = "<div class='row'><div class='col-md-12' style='overflow-y:scroll;'><img class='no-zoom' src='"+base_url+"uploads/vetements/"+imageVetement+"' usemap='#image-map'>";
        html += "<map name='image-map'>"+image_map+"</map>";
        html += "</div></div>";
        $('#vetement_size').val("");
        $('#modalMeasurements .modal-body').empty().append(html);
        $('#modalMeasurements .modal-footer .badge').text("");
        $('#modalMeasurements').modal({backdrop: 'static', keyboard: false},"show") 

    })

    $('#modalSelectProduct').on("shown.bs.modal",function(){});

    $('#modalSelectProduct .close-modal').on("click",function(){
        $('#item_details tr.selected').removeClass("selected");
        $('#modalSelectProduct').modal("hide");
    })
    
    $('#modalSelectProduct').on("hidden.bs.modal",function(){
        // $('#item_details tr.selected').removeClass("selected");
    });

    $('#modalSearchClients').on('keyup', function(e){
        if (timer) {
            clearTimeout(timer);
        }
        timer = setTimeout(mySearch, 400); 
    });

    // $('#add_deliverynote').on("click",".remove-customer",function(ev){
    //     ev.stopPropagation();
    //     removeClient();
    // })

    $('#add_deliverynote').on("click",".select-customer-modal",function(){
        $('#modalSelectClient').modal("show");
    })

    $('#add_deliverynote').on("click",".additionalInfo",function() {
        $('#additionalInfoModal table tbody').empty();
                if(data.additional_fields_data.length > 0){
                    $(data.additional_fields_data).each(function(i,j){
                        console.log(j);
                        var line = "<tr data-id='"+j.id+"'>";
                        line += "<td>" + j.field_name + "</td>";
                        line += "<td><input name='af_"+i+"' id='af_"+i+"' type='text' class='form-control' value='"+j.value+"'></td>";
                        line += "</tr>";
                        $('#additionalInfoModal table tbody').append(line);
                    })
                    $('#clearAdditionalInfo').removeClass("hidden");
                }else{
                    $(data.additional_fields).each(function(i,j){
                        var line = "<tr data-id='"+j.id+"'>";
                        line += "<td>" + j.field_name + "</td>";
                        line += "<td><input name='af_"+i+"' id='af_"+i+"' type='text' class='form-control'></td>";
                        line += "</tr>";
                        $('#additionalInfoModal table tbody').append(line);
                    })
                    $('#clearAdditionalInfo').addClass("hidden");
                }
                
                $('#additionalInfoModal').modal("show");
                $('#additionalInfoModal table tbody tr').eq(0).find("td").eq(1).find("input").select()
    })

    $('#trial_date').on("change",function(){
        let td = $(this).val();
        const date = new Date(td)
        date.setDate(date.getDate() + 2);
        let ddStr = date.toISOString().split("T")[0];
        $('#delivery_date').val(ddStr);
    })

    $('#add_deliverynote').on("click",".uploadPhotos", function() {
        selectRow($(this));

        let product_id = $(this).closest("tr").find(".product_id").val();
        if(product_id == "") {
            unselectRow();
            toastr["error"]("Please select a product first");
            return;
        }

        const imagesJson = $("tr.selected").find(".images").val();
        if(imagesJson == "") imagesJson = "[]";
        const images = JSON.parse(imagesJson);
        if(images.length > 0){
            $.ajax({
                url: base_url + "ajax/misc/getImages",
                data: {images: images},
                method:"POST",
                dataType: "JSON",
                success: function(response) {
                    for(var index = 0; index < response.images.length; index++) {
                        let line = "<div class='preview-image col-sm-2'>"
                        line += "<img class='img-thumbnail' src='"+base_url + "uploads/orders/" + (response.images[index].file_name)+"'>";
                        line += "<i data-id='"+(response.images[index].id)+"' class='fa fa-times delete-image'></i>"
                        line += "</div>"
                        $('#preview').append(line);
                    }                    
                }
            })
        }
        console.log(images)
        $('#modalPhotos').modal("show");
    })

    $('#modalPhotos').on("hide.bs.modal",function(){
        $('#images').val("");
        $('#preview').empty();
        unselectRow()
    })

    $('#add_deliverynote').on("click","#saveOrder", function() {
        saveOrder()
    })

    // $('#add_deliverynote').on("click","#saveDraft", function() {
    //     saveOrder(true)
    // })

    $('#viewMeasurementsModal').on("hide.bs.modal",function(){
        $("tr.selected").removeClass("selected");
    })

    $('#add_deliverynote').on("click",".fetch-products",function(){
        selectRow($(this));
        fetchProducts();
    })

    $('#modalSelectProduct').on("click",".select-product",function(){
        let uuid = $(this).data("uuid");
        $.ajax({
            url: base_url + "products/fetchByUuid",
            data:{uuid:uuid},
            method:"POST",
            dataType:"JSON",
            async:false,
            success:function(response){
                if(response.result){
                    $("tr.selected").find('input.product_id').val(response.record.id)
                    // $("tr.selected").find('input.measurements').val("")
                    // $("tr.selected").find('input.image_map').val(response.record.image_map)
                    // $("tr.selected").find('input.image_vetement').val(response.record.imageVetement)
                    // $("tr.selected").find('input.additional_fields').val(JSON.stringify(response.record.additional_fields));

                    displayItemDetails(response.record);

                    let total_fields = $('area.open-modal').length;
                    // $('#modalMeasurements .modal-footer .badge').text('0 / ' + total_fields);

                    $('#modalSelectProduct').modal("hide");

                    $('tr.selected .quantity').trigger("select")

                    unselectRow($(this))

                    // window.setTimeout(function(){
                    //     $("tr.selected .enterMeasurements").trigger("click");
                    // },500)

                }
            }
        })
    })

    var ms = [];
    

    // region upload photos

    $('#photos').on("change",".photos",function(){
        let elem = $(this).val();
        console.log(elem)
        if(checkPhotos()>=3){

        }else{

        }
    })

    // endregion upload photos

    $('#modalSelectClient').on("show.bs.modal",function(){
        fetchClients("",false);
    })

    $('#modalSelectClient').on("shown.bs.modal",function(){
        $('#modalSearchClients').trigger("focus");
    })

    $('#modalSelectClient').on("hidden.bs.modal",function(){
        $('#modalSearchClients').trigger("focus");
        $('#modalSearchClients').val("");
        $('#select_customer tbody').empty();
    })

    $('#items').on("click",".addRow",function(){
        $(this).closest("tr").removeClass("photo-error");
        var row = $(this).closest("tr").clone();
        // $(row).removeClass("photo-error");
        $(row).find(".product_id").val("");
        $(row).find(".stockref").val("");
        $(row).find(".name").val("");
        $(row).find(".price").val("0");
        $(row).find(".quantity").val("1");
        $(row).find(".amount").val("");
        $('#items tbody').append(row);

    })

    $('#items').on("click",".deleteRow",function(){
        var ct = $('#items tbody tr').length;
        if(ct==1) return false;
        // $(this).closest("tr").next("tr").remove();
        $(this).closest("tr").remove();
    })

    $("#select_customer").on("click",".select-customer",function(){
        let customer_id = $(this).data("customer-id");
        $('input[name=customer_id]').val(customer_id);
        // data.customer_id = customer_id;
        cart.customer = fetchClient(customer_id);
        $('#modalSelectClient').modal("hide");
        $('.select-customer-modal').removeClass("btn-default").addClass("btn-info").html("Customer Selected <i class='fa fa-check'></i>");
    })

    $('#items').on("keyup blur",".quantity, .price",function(){
        reCalculate()
    })

    $('#delivery_store').on("change",function(){
    })

    $('#quantity, #deposit').on("keyup",function(){
    })

    $('input[name=gender]').on("change",function(){
        let gender = $(this).val();
        if(gender=='m'){
            $("input[name=title][value='Mr']").prop("disabled",false);
            $("input[name=title][value='Mrs']").prop("disabled",true);
            $("input[name=title][value='Miss']").prop("disabled",true);
            $("input[name=title][value='Dr']").prop("disabled",false);
            $('input:radio[name=title]')[0].checked = true;
        }else{
            $("input[name=title][value='Mr']").prop("disabled",true);
            $("input[name=title][value='Mrs']").prop("disabled",false);
            $("input[name=title][value='Miss']").prop("disabled",false);
            $("input[name=title][value='Dr']").prop("disabled",false);
            $('input:radio[name=title]')[1].checked = true;
        }
    })

    $('#quick_save_customer').on("click",function(){
        var valid = true;
        var customer = new Object;
        customer.gender = $('#modalAddCustomer input[name=gender]').val();
        customer.title = $('#modalAddCustomer input[name=title]').val();
        customer.gender = $('#modalAddCustomer input[name=gender]').val();
        customer.customer_code = $('#modalAddCustomer input[name=customer_code]').val();
        customer.first_name = $('#modalAddCustomer input[name=first_name]').val();
        customer.last_name = $('#modalAddCustomer input[name=last_name]').val();
        customer.address = $('#modalAddCustomer input[name=address]').val();
        customer.city = $('#modalAddCustomer input[name=city]').val();
        customer.nic = $('#modalAddCustomer input[name=nic]').val();
        customer.vat = $('#modalAddCustomer input[name=vat]').val();
        customer.brn = $('#modalAddCustomer input[name=brn]').val();
        customer.dob = $('#modalAddCustomer input[name=dob]').val();
        customer.nationality = $('#modalAddCustomer select[name=nationality]').val();
        customer.profession = $('#modalAddCustomer input[name=profession]').val();
        customer.marital_status = $('#modalAddCustomer input[name=marital_status]').val();
        customer.shoe_size = $('#modalAddCustomer input[name=shoe_size]').val();
        customer.clothes_size = $('#modalAddCustomer input[name=clothes_size]').val();
        customer.height = $('#modalAddCustomer input[name=height]').val();
        customer.sports = $('#modalAddCustomer input[name=sports]').val();
        customer.fidelity_card = $('#modalAddCustomer input[name=fidelity_card]').val();
        customer.email = $('#modalAddCustomer input[name=email]').val();
        customer.phone_number1 = $('#modalAddCustomer input[name=phone_number1]').val();
        customer.phone_number2 = $('#modalAddCustomer input[name=phone_number2]').val();

        if(customer.first_name.length < 2) {
            $('#modalAddCustomer input[name=first_name]').closest(".form-group").addClass("has-error");
            valid = false;
        }
        if(customer.nic.length<14) {
            $('#modalAddCustomer input[name=nic]').closest(".form-group").addClass("has-error");
            valid = false;
        }

        if(!valid){
            alert("Please correct errors and try again");
            return false;
        }
        $.ajax({
            url: base_url + "ajax/customers/save",
            data: customer,
            method:"POST",
            dataType:"JSON",
            success:function(response){
                if(response.result){
                    $('#modalAddCustomer input').each(function(){
                        $(this).val("")
                    })
                    $('#modalAddClient').modal("hide");
                }else{
                    alert(response.reason);
                }
            }
        })
    })

    $('.add-photo').on("click",function(){
        let elem = $('.proto').clone().removeClass("proto d-none");
        console.log(elem)
        $(".photos-block").append(elem)
    })

    $(".photos-block").on("change",'input[type=file]',function(){
        let filename = $(this).val().split('\\').pop();
        if(filename !== ""){
            data.pictures.push(filename);
        }
    })
    
    $("#add_deliverynote").on("click",'.process',function(){

        if(step == '3b'){

            console.log(data.additional_fields_data);
            
        }else if(step == '4'){
            if($('#photos').hasClass("d-none")){
                $('#photos').removeClass("d-none")
            }else{
                $('#photos').addClass("d-none")
            }
        }else if(step == '6'){
            $('#deposit').trigger("focus");
        }else if(step == '7'){
            $('#delivery_store').trigger("focus");
        }else if(step == '8'){
            $('#delivery_date').trigger("focus");
        }else if(step == '9'){
            $('#delivery_time').trigger("focus");
        }
    })

    $('.photo-proceed').on("click",function(){
        $('#modalPhotos').modal("hide")
    })

    $('#images').on("change",function(){
        $('#submit').trigger("click")
    })

    $('#submit').click(function(){
        var form_data = new FormData();
        var uploadedImages = JSON.parse($('tr.selected .images').val());
        var totalfiles = document.getElementById('images').files.length;
        for (var index = 0; index < totalfiles; index++) {
            form_data.append("images[]", document.getElementById('images').files[index]);
        }
  
        $.ajax({
          url: base_url + 'deliverynotes/uploadImages', 
          type: 'post',
          data: form_data,
          dataType: 'json',
          contentType: false,
          processData: false,
          success: function (response) {
            // $('#preview').empty()
            for(var index = 0; index < response.length; index++) {
                let img = "<div class='preview-image col-sm-2'>"
                img += "<img class='img-thumbnail' src='"+(response[index].image)+"'>";
                img += "<i data-id='"+(response[index].id)+"' class='fa fa-times delete-image'></i>"
                img += "</div>"
                $('#preview').append(img);
                uploadedImages.push(response[index].id);
            }
            console.log(uploadedImages)
            $("tr.selected").find(".images").val(JSON.stringify(uploadedImages));
            // $('#modalPhotos').modal("hide");
          }
        });
  
    })

    $('#preview').on("click",".delete-image", function() {
        let elem = $(this);
        let id = $(this).data("id");
        $.ajax({
            url: base_url + 'deliverynotes/deleteImage', 
            type: 'post',
            data: {id:id},
            dataType: 'json',
            success: function (response) {
                console.log(response);
                let x = $("tr.selected").find(".images").val()
                if(x!==''){
                    xx = JSON.parse(x);
                }

                $(elem).closest(".preview-image").remove();

                for( var i = 0; i < xx.length; i++){ 
    
                    if ( xx[i] === id) { 
                        xx.splice(i, 1); 
                    }
                
                }
                console.log(xx);

                $("tr.selected").find(".images").val(JSON.stringify(xx))

            }
        });
    })

    $('#hasTrialDate').on("click",function(){
        if( $(this).is(":checked") ){

            let headoffice = $(this).data("headoffice");

            var d = new Date(),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear(),
                hours = d.getHours(),
                minutes = d.getMinutes();
        
            console.log(d,day,month.length,year,hours,minutes)

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;
            if (hours < 10) hours = '0' + hours;
            if (minutes < 10) minutes = '0' + minutes;

            console.log(day,month,year,hours,minutes)
        
            let dt1 = [year, month, day].join('-');
            let dt2 = [hours, minutes].join(':')

            console.log(dt1, dt2)

            $('#trial_date').val(dt1).removeAttr("disabled");
            $('#trial_time').val("09:00").removeAttr("disabled");
            $('#trial_store').val(headoffice).removeAttr("disabled");
        } else {
            $('#trial_date').val("").prop("disabled",true);
            $('#trial_time').val("").prop("disabled",true);
            $('#trial_store').val("").prop("disabled",true);
        }
        let state = $(this).is(":checked")

        
    })
})

function saveOrder()
{
    //clear previous errors
    $('.input-error').removeClass("input-error");
    $('.tab-error').removeClass("tab-error");
    // $('.photo-error').removeClass("photo-error");

    let valid = true;
    let errorMessage = "";
    let customer_id = $("input[name=customer_id]").val();
    if(customer_id==""){
        valid = false;
        $(".select-customer").addClass("input-error");
        errorMessage += "Please select a Customer<br>";
        $('#customer-tab').addClass("tab-error");
    }

    let items = []
    $('#items tbody tr').each(function(i,j){

        if($(this).find(".product_id").val()!=""){
            items.push({
                product_id : $(this).find(".product_id").val(),
                name: $(this).find(".name").val(),
                price : $(this).find(".price").val(),
                quantity : $(this).find(".quantity").val(),
            })
        }

    })

    if(items.length==0){
        valid = false;
        $('#items-tab').addClass("tab-error");
        errorMessage += "Please add at least one Item<br>";
    }

    if(!valid){
        myAlert(errorMessage);
        return false;
    }

    let order = {
        customer_id : $("input[name=customer_id]").val(),
        amount: $('#total_amount').val(),
        items: items
    }

    $.ajax({
        url: base_url + "deliverynotes/save",
        method: "POST",
        data: order,
        dataType: "JSON",
        success: function(response){
            console.log(response)
            if(response.result) {
                window.location.href = base_url + "deliverynotes/listing";
            }
        }
    })
}

function getAdditionalInfo()
{
    data.additional_fields_data = [];
    $('#additionalInfoModal table>tbody>tr').each(function(i,j){
        let fId = $(j).data("id");
        let fName = $(j).find('td').eq(0).text();
        let fValue = $(j).find('td>input').val();
        var line = {"field_id":fId, "field_name":fName,"field_value":fValue};
        data.additional_fields_data.push(line)
    })
}

function validate()
{
    let valid = true;
    let errorMessage = [];
    data.quantity = $('#quantity').val();
    data.price = $('#price').val();
    data.price = parseFloat(data.price.replace(/,/g,''));
    data.amount = $('#amount').val();
    data.amount = parseFloat(data.amount.replace(/,/g,''));
    data.discount_pct = $('#discount_pct').val();
    data.discount = $('#discount').val();
    data.deposit = $('#deposit').val();
    data.delivery_date = $('#delivery_date').val();
    data.delivery_time = $('#delivery_time').val();
    data.delivery_store = $('#delivery_store').val();
    data.fabric_reference = $('#fabric_reference').val();
    data.fabric_color = $('#fabric_color').val();
    data.size = $('#size').val();
    if(data.customer_id === null){
        errorMessage.push(["select or create a customer","#process1"]);
        valid = false;
    }
    if(data.product_id === null) {
        errorMessage.push(["select an item","#process2"]);
        valid = false;
    }
    if(data.measurements === null) {
        errorMessage.push(["enter measurements","#process3"]);
        valid = false;
    }
    if( (data.deposit.length == 0 ) || (isNaN(data.deposit)) || (data.deposit==0) ) {
        errorMessage.push(["enter deposit","#process6"]);
        valid = false;
    }
    if(data.delivery_date.length != 10) {
        errorMessage.push(["enter delivery date","#process8"]);
        valid = false;
    }
    if(data.delivery_time.length != 5) {
        errorMessage.push(["enter delivery time","#process9"]);
        valid = false;
    }
    if(data.delivery_store.length == 0) {
        errorMessage.push(["enter delivery store","#process7"]);
        valid = false;
    }
    if(!valid){
        displayMessage(errorMessage);
        return false;
    }
    clearMessage();
    return true;
}

function clearMessage()
{
    $('#message-box').html("");
}

function displayMessage(message)
{
    if( (typeof message === 'undefined') || (message.length==0) ) return false;

    if(Array.isArray(message)) {
        var list = '<ul class="list-group">';
        list += '<li class="list-group-item">Please correct the following errors:</li>';
        $(message).each(function(i,j){
            list += '<li data-error="'+j[1]+'" class="list-group-item red cursor-pointer fetch-error"><i class="fas fa-circle"></i> '+j[0]+'</li>';
        })
        list += "</ul>";
        $('#message-box').html(list);
    }else{
        $('#message-box').html(message);
    }
}

function init()
{
    $.ajax({
        url : base_url + "ajax/misc/getParam",
        method:"GET",
        dataType:"JSON",
        data:{param:'minimum_deposit_pct'},
        success:function(response){
            minimumDepositPct = response.param
        }
    })
    // $('#modalSelectClient').modal("show");
}

function fetchProducts()
{
    $.ajax({
        url: base_url + "products/fetchAll",
        method:"POST",
        dataType:"JSON",
        success:function(response){
            if(response.result){
                $('#products-table tbody').empty();
                let rows = "";
                $(response.rows).each(function(i,j){
                    rows += "<tr class='text-center cursor-pointer select-product' data-uuid='"+j.uuid+"'>";
                    rows += "<td>" + j.stockref	+ "</td>";
                    rows += "<td>" + j.name	+ "</td>";
                    rows += "<td>Rs " + parseFloat(j.selling_price).toLocaleString("en-US")	+ "</td>";
                    if(j.photo !== ""){
                        rows += "<td>";
                        rows += "<img style='height:50px;' src='./uploads/products/"+j.photo+"'>"
                        rows += "</td>";
                    }else{
                        rows += "<td><img style='height:50px;' src='./assets/images/image-placeholder-500x500.jpg'></td>";
                    }
                    
                    rows += "</tr>"
                })
                $('#products-table tbody').append(rows)
                $('#modalSelectProduct').modal("show");
            }
            
        }
    })
}

function fetchClients(searchTerm,random)
{
    if( (typeof (random) === 'undefined') || (random===null) ){
        random = false;
    }
    $('#modal-client-overlay').removeClass("hidden");
    $.ajax({
        url: base_url + "ajax/customers/get",
        method: "POST",
        dataType: "JSON",
        data:{searchTerm:searchTerm,random:random},
        success: function(response){
            $('#select_customer tbody').empty();
            if(response.result){
                $(response.customers).each(function(i,row){
                    $('#select_customers').append("<option value='"+row.customer_id+"'>"+row.title+" "+row.last_name+" "+row.first_name+" | "+row.phone_number1+"</option>")
                    let line = "<tr class='select-customer cursor-pointer' data-customer-id='"+row.customer_id+"'>";
                    line += "<td>"+row.full_name+"</td>";
                    line += "<td>"+row.phone_number1+"</td>";
                    line += "<td>"+row.phone_number2+"</td>";
                    line += "<td>"+row.email+"</td>";
                    line += "<td>"+row.address+"</td>";
                    line += "<td>"+row.certificate_issue_date+"</td>";
                    line += "</tr>";
                    $('#select_customer tbody').append(line);
                })
            }else{
                window.location.href = base_url +"users/signin";
            }
        },
        complete: function(){
            window.setTimeout(function(){
                $('#modal-client-overlay').addClass("hidden");
            },250)
            
        }
    })
}

function fetchClient(customer_id)
{
    let customer = null;
    $.ajax({
        url: base_url + "ajax/customers/get",
        data:{customer_id:(customer_id==null)?0:customer_id},
        method: "POST",
        dataType: "JSON",
        async: false,
        success: function(response){
            if(response.result){
                customer = response.customers[0];
                let row = response.customers[0];
                $('#customer_info .customer_info').empty();
                // $('#customer_info .code').removeClass("cursor-pointer select-customer-modal").html(row.customer_code + "<div class='remove-customer text-bold red cursor-pointer' style='float:right'>X</div>");
                $('#customer_info .full_name').html(row.full_name)
                $('#customer_info .address').html(row.address)
                $('#customer_info .phone_1').html(row.phone_number1)
                $('#customer_info .phone_2').html(row.phone_number2)
                $('#customer_info .email').html(row.email)

                $('#customer_info .certificate_issue_date').html(row.certificate_issue_date)
                $('#customer_info .certificate_holder_email').html(row.certificate_holder_email)
                $('#customer_info .certificate_holder_name').html(row.certificate_holder_name)
                $('#customer_info .certificate_holder_mobile').html(row.certificate_holder_mobile)
                $('#customer_info .certificate_number').html(row.certificate_number)
                
                reCalculate();
            }else{
                window.location.href = base_url +"users/signin";
            }
            
        }
    })

    return customer;
}

// function removeClient()
// {
//     $('#customer_info .customer_info').empty();
//     $('#customer_info .full_name').addClass("cursor-pointer select-customer-modal").html("Click to Select Customer");
//     $('input[name=customer_id]').val("");
// }

function displayItemDetails(item)
{
    $('#item_details tr.selected .product_id').val(item.id);
    $('#item_details tr.selected .stockref').val(item.stockref);
    $('#item_details tr.selected .name').val(item.name);
    
    $('#item_details tr.selected .price').val( parseFloat(item.selling_price).toLocaleString("en-US"));
    $('#item_details tr.selected .quantity').val(1);
    $('#item_details tr.selected .amount').val( parseFloat(item.selling_price).toLocaleString("en-US") );

    reCalculate()
}

function reCalculate()
{
    console.log(new Date)
    var total_amount = 0;
    var total_quantity = 0;
    $('#items tbody tr').each(function(i,j) {
        let p = $(this).find(".price").val();
        let price = p.replace(/,/g, '')
        let quantity = $(this).find(".quantity").val();
        total_quantity += parseFloat(quantity);
        let amount = parseFloat(price) * parseFloat(quantity);
        $(this).find('.amount').val(amount.toLocaleString("en-US"));
        total_amount += amount;
    })
    $('#total_amount').val(formatNumber(total_amount));
    $('#total_quantity').val(formatNumber(total_quantity));

}

function formatNumber(number)
{
    return parseFloat(number).toLocaleString("en-US",{maximumFractionDigits:2,minimumFractionDigits:2});
}

function fetchClientHistory(customer_id)
{
    $.ajax({
        url: base_url + "ajax/customers/getHistory",
        data:{customer_id:customer_id},
        method:"POST",
        dataType:"JSON",
        success:function(response)
        {
            console.log(response)
            if( (response.result) && (response.orders.length>0) ){
                $('#customer_history tbody').empty();
                var rows = "";
                $(response.orders).each(function(i,j){
                    rows += '<tr class="text-center">';
                    rows += '<td>'+j.order_date+"</td>";
                    rows += '<td>'+j.document_number+"</td>";
                    rows += '<td>'+j.productName+"</td>";
                    rows += '<td>'+(parseFloat(j.amount) - parseFloat(j.discount)).toLocaleString("en-US")+"</td>";
                    rows += '<td>'+j.stageName+"</td>";
                    rows += "</tr>";
                })
                // console.log(rows)
                $('#customer_history tbody').append(rows);
                $('#customer_history table').removeClass("d-none");
            }else{
                $('#customer_history').find("table").addClass("d-none");
            }
        }
    })
}

function checkPhotos()
{
    let ct = 0;
    $('#photos .photos').each(function(){
        if($(this).val().length>0) ct++
    });
    console.log(ct);
    if(ct>0) {
        $('#process5 span').text("("+ct+")")
    }else{
        $('#process5 span').text("")
    }
    return ct;
}

function selectRow(elem)
{
    $(elem).closest("tr").addClass("selected");
    // $(elem).closest("tr").next("tr").addClass("selected");
}

function unselectRow()
{
    $("tr.selected").removeClass("selected");
}

function uploadFormData(i) {
    const form = document.getElementById('uploadForm'+i);
    const formData = new FormData(form);

    fetch(base_url + 'images/upload', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok) {
            return response.text();
        }
        throw new Error('Network response was not ok.');
    })
    .then(data => {
        console.log(data); // Handle successful response
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function uploadFormDatax() {
    const form = document.getElementById('uploadForm1');
    console.log(form);
    return
    const formData = new FormData(form);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', base_url + 'images/upload', true);

    xhr.onload = function() {
        if (xhr.status === 200) {
            // Request was successful
            console.log(xhr.responseText);
        } else {
            // Error occurred
            console.error(xhr.statusText);
        }
    };

    xhr.onerror = function() {
        // Network error
        console.error('Network error occurred');
    };

    xhr.send(formData);
}

$('#items tbody').on('click', 'td', function() {
    // Find the closest parent <tr> element
    var clickedRow = $(this).closest('tr');
    
    // Get the index of the clicked row within the tbody
    var rowIndex = clickedRow.index();

    // if( (rowIndex%2) == 1)  // console.log("********")
    // console.log('Even: ',isEven(rowIndex));
    // // console.log('Odd: ',isOdd(rowIndex));
    
    // Output the index of the clicked row (zero-based index)
    // console.log('Clicked row index:', rowIndex);
    
    // You can also access specific data within the clicked row if needed
    // var cells = clickedRow.find('td');
    // var name = $(cells[0]).text(); // First <td> in the row
    // var age = $(cells[1]).text(); // Second <td> in the row
    // // console.log('Name:', name, 'Age:', age);
});


function isEven(number){
    return ( (number%2) == 0) ? true : false
}

// function isOdd(number){
//     return ( (number%2) == 0) ? false : true
// }