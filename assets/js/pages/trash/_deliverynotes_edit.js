var measurementZones = [];
var orderDetailsNotes = [];

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

    // init()

    // totalSupplement();
    // reCalculate();

    $('.order_details_notes').each(function(i,j){
        let rw = $(this).closest("tr").index();
        orderDetailsNotes[rw] = $(this).val();
    })

    $('#items').on("click",".rowComment",function() {

        let product_id = $(this).closest("tr").find(".product_id").val();
        if(product_id == "") {
            toastr["error"]("Please select a product first");
            return;
        }

        selectRow($(this));

        let rw = $('#item_details tr.selected').index();
        let notes = (orderDetailsNotes[rw] !== 'undefined') ? orderDetailsNotes[rw] : '';
        // let notes = $(this).closest("tr").find(".order_details_notes").val();
        $('#rowCommentModal textarea').val(notes)

        $('#rowCommentModal').modal("show");

    })

    $('#rowCommentModal').on("hidden.bs.modal",function(){
        $('#item_details tr.selected').removeClass("selected");
    })

    $('#saveOrderDetailsNotes').on("click",function(){
        let notes = $('#rowCommentModal textarea').val();
        let rw = $('#item_details tr.selected').index();
        console.log(rw/2)
        orderDetailsNotes[rw] = notes;
        $('#item_details tbody tr.selected').find(".order_details_notes").val(notes)

        $('#rowCommentModal').modal("hide");
    })

    $('textarea#new_comment').on("keydown",function(e){
        if(e.key == 'Enter'){
            if(e.shiftKey) return;
            $('#saveComment').trigger("click");
        }
    })

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

    $('#edit_order').on("click",".get-measurements",function(){
        $('#modalMeasurements').modal("show");
    })

    $('#items').on("click",".enterMeasurements",function() {
        let m = $(this).closest("tr").find(".measurements").val();
        let rowId = $(this).closest("tr").data("id");
        if(m=='!!'){
            if(rowId!==""){
                selectRow($(this));
                $.ajax({
                    url : base_url + "orders/getSingleRowDetails",
                    method:"POST",
                    dataType:"JSON",
                    data:{id:rowId},
                    success:function(response){
                        // console.log(response.row.orderAdditionalFields)
                        $('tr.selected').find(".measurements").val(response.row.orderMeasurements);
                        $('tr.selected').find(".additional_fields").val(response.row.orderAdditionalFields);
                        $('tr.selected').find(".enterMeasurements").trigger("click")
                    },
                    complete:function(){
                        // unselectRow();
                    }
                })
                return
            }
        }
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
            $('#viewMeasurementsModal').modal("show")

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
        $('#modalMeasurements').modal("show");

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

    $('#edit_order').on("click",".remove-customer",function(ev){
        ev.stopPropagation();
        removeClient();
    })

    $('#edit_order').on("click",".select-customer",function(){
        $('#modalSelectClient').modal("show");
    })

    $('#edit_order').on("click",".additionalInfo",function() {
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

    $('#edit_order').on("click",".uploadPhotos", function() {
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
                        line += "<img style='cursor:zoom-in;' class='img-thumbnail' src='"+base_url + "uploads/orders/" + (response.images[index].file_name)+"'>";
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

    $('#edit_order').on("click","#updateOrder", function() {
        updateOrder()
    })

    $('#edit_order').on("click","#updateOrderDraft", function() {
        updateOrder(true)
    })

    $('#viewMeasurementsModal').on("hide.bs.modal",function(){
        $("tr.selected").removeClass("selected");
    })

    $('#edit_order').on("click",".fetch-products",function(){
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
                    $("tr.selected").find('input.measurements').val("")
                    $("tr.selected").find('input.image_map').val(response.record.image_map)
                    $("tr.selected").find('input.image_vetement').val(response.record.imageVetement)
                    $("tr.selected").find('input.additional_fields').val(JSON.stringify(response.record.additional_fields));

                    

                    displayItemDetails(response.record);

                    let total_fields = $('area.open-modal').length;
                    $('#modalMeasurements .modal-footer .badge').text('0 / ' + total_fields);

                    $('#modalSelectProduct').modal("hide");

                    window.setTimeout(function(){
                        $("tr.selected .enterMeasurements").trigger("click");
                    },500)

                   

                }
            }
        })
    })

    $('#modalMeasurements').on("click",".open-modal",function(e){
        e.preventDefault();
        let zone = $(this).attr("title");
        let info = $(this).attr("href");

        // if(measurementZones.indexOf(zone)!==-1) {
        //     myAlert("Measurement for zone "+zone+" has already been recorded. To re-enter, please click on View Measurements and delete it first")
        //     return false;
        // }

        let label = zone;
        if(info!=="") label = info + " ["+zone+"]"
        $('#zone').html(label)
        
        $('#measurementModal').modal("show");
        return false;
    })

    var ms = [];
    $("#saveMeasurement").on("click",function(e){
        e.preventDefault();
        let m = $('#measurementModal input[name=measurement]').val().trim();
        let remarks = $('#measurementModal textarea[name=measurements_remarks]').val();

        if(m=='') {
            $('#measurementModal input[name=measurement]').parent().addClass("has-error");
            $('#measurementModal input[name=measurement]').trigger('focus')
            return false;
        }
        let zone = $('#zone').text();

        let rS = $('tr.selected').find(".measurements").val();

        let ck = ms.find( (o,i) => {
            if(o.zone === zone) {
                ms[i] = {
                    zone: zone,
                    value: m,
                    remarks: remarks
                };
                return true;
            }
        })
        if(typeof ck === 'undefined') {
            ms.push({
                zone: zone,
                value: m,
                remarks: remarks
            })
        }

        $('#measurementModal input[name=measurement]').val('')
        $("#measurementModal").modal("hide");
        
    })

    $('#saveMeasurements').on("click",function(){
        let valid = true;
        let errorMessage = "";
        let total_fields = $('area.open-modal').length;
        let vetement_size = $('#vetement_size').val();
        if(ms.length==0){
            errorMessage += "No measurements have been recorded yet<br>";
            valid = false;
        }else if(ms.length < total_fields){
            let used = "<ul>";
            $(ms).each(function(i,j){
                used += "<li>"+j.zone + "</li>";
            })
            used += "</ul>";
            errorMessage += "Some measurements are missing. Currently only "+ms.length+" of "+total_fields+" measurements have been recorded, as shown below:";
            errorMessage += used;
            valid = false;
        }
        if(vetement_size==""){
            errorMessage += "Please enter Size";
            valid = false;
        }

        if(!valid){
            myAlert(errorMessage)
        }else{
            $('tr.selected').find(".measurements").val(JSON.stringify(ms))
            $('tr.selected').find(".size").val(vetement_size);
            $('#modalMeasurements').modal("hide");
            ms=[];
        }
    })

    $('#viewMeasurementsModal').on("click",".edit-measurement",function(){
        if($('#viewMeasurementsModal').hasClass("editing")) {
            alertify.alert("Please save your previous edit before proceeding")
            return false;
        }
        $('#viewMeasurementsModal').addClass("editing");
        $(this).addClass("hidden");
        $(this).closest('tr').find(".save-measurement").removeClass("hidden");

        $(this).closest('tr').find("span").addClass("hidden");
        $(this).closest('tr').find("input").removeClass("hidden");
    })

    $('#viewMeasurementsModal').on("click",".edit-size",function(){
        $('#viewMeasurementsModal').addClass("editing");
        $(this).addClass("hidden");
        $(this).closest('tr').find(".save-size").removeClass("hidden");

        $(this).closest('tr').find("span").addClass("hidden");
        $(this).closest('tr').find("input").removeClass("hidden");
    })

    $('#viewMeasurementsModal').on("click",".save-measurement",function(){
        $(this).removeClass("editing");
        $(this).addClass("hidden");
        $(this).closest('tr').find(".edit-measurement").removeClass("hidden");

        let value = $(this).closest('tr').find("input").eq(0).val()
        $(this).closest('tr').find("span").eq(0).text(value).removeClass("hidden");

        let remarks = $(this).closest('tr').find("input").eq(1).val()
        $(this).closest('tr').find("span").eq(1).text(remarks).removeClass("hidden");

        $(this).closest('tr').find("input").addClass("hidden");

        var updates = [];
        $(this).closest("tbody").find("tr").each(function(){
            // let zone = $(this).find('td').eq(0).text();
            // let v = $(this).find('input').eq(0).val();
            // let r = $(this).find('input').eq(1).val();
            if(!$(this).hasClass("size")){
                updates.push({
                    zone: $(this).find('td').eq(0).text(),
                    value: $(this).find('input').eq(0).val(),
                    remarks: $(this).find('input').eq(1).val()
                });
            }

        })
        console.log(updates);
        $('#viewMeasurementsModal').removeClass("editing");
        $('tr.selected').find(".measurements").val(JSON.stringify(updates))
    })

    $('#closeViewMeasurements').on("click", function() {
        if($('#viewMeasurementsModal').hasClass("editing")) {
            alertify.confirm(
                'Changes Alert', 
                'Would you like to discard your changes?', 
                function(){ 
                    $('#viewMeasurementsModal').removeClass("editing");
                    $('#viewMeasurementsModal').modal("hide");
                }
                , function(){ 
                    // alertify.error('Cancel')
            });
        }else{
            $('#viewMeasurementsModal').modal("hide");
        }

    })

    $('#viewMeasurementsModal').on("click",".save-size",function(){
        $('#viewMeasurementsModal').removeClass("editing");
        $(this).addClass("hidden");
        $(this).closest('tr').find(".edit-measurement").removeClass("hidden");

        let value = $(this).closest('tr').find("input").eq(0).val()
        $(this).closest('tr').find("span").eq(0).text(value).removeClass("hidden");

        let remarks = $(this).closest('tr').find("input").eq(1).val()
        $(this).closest('tr').find("span").eq(1).text(remarks).removeClass("hidden");

        $(this).closest('tr').find("input").addClass("hidden");

        $('tr.selected').find(".size").val(value)

        // var updates = [];
        // $(this).closest("tbody").find("tr").each(function(){
        //     // let zone = $(this).find('td').eq(0).text();
        //     // let v = $(this).find('input').eq(0).val();
        //     // let r = $(this).find('input').eq(1).val();
        //     updates.push({
        //         zone: $(this).find('td').eq(0).text(),
        //         value: $(this).find('input').eq(0).val(),
        //         remarks: $(this).find('input').eq(1).val()
        //     });
        // })
        // console.log(updates)
        // $('tr.selected').find(".measurements").val(JSON.stringify(updates))
    })

    // endregion modalMeasurements


    // region additionInfo

    $('#items').on("click",".additionalFields",function() {
        let product_id = $(this).closest("tr").find(".product_id").val();
        if(product_id == "") {
            toastr["error"]("Please select a product first");
            return;
        }

        selectRow($(this));

        $('#additionalInfoModal table tbody').empty();

        let additionalFieldsJson = $(this).closest("tr.selected").find(".additional_fields").val();
        let additionalFields = [];
        if(additionalFieldsJson !== ''){
            additionalFields = JSON.parse(additionalFieldsJson);
        }
        if(additionalFields.length > 0){
            $(additionalFields).each(function(i,j){
                console.log(j);
                var line = "<tr data-id='"+j.id+"'>";
                line += "<td>" + j.field_name + "</td>";
                line += "<td><input name='af_"+i+"' id='af_"+i+"' type='text' class='form-control' value='"+( (typeof j.value !== 'undefined') ? j.value : '')+"'></td>";
                line += "</tr>";
                $('#additionalInfoModal table tbody').append(line);
            })
            $('#clearAdditionalInfo').removeClass("hidden");
        }
        // else{
            // $(data.additional_fields).each(function(i,j){
            //     var line = "<tr data-id='"+j.id+"'>";
            //     line += "<td>" + j.field_name + "</td>";
            //     line += "<td><input name='af_"+i+"' id='af_"+i+"' type='text' class='form-control'></td>";
            //     line += "</tr>";
            //     $('#additionalInfoModal table tbody').append(line);
            // })
            // $('#clearAdditionalInfo').addClass("hidden");
        // }
        
        $('#additionalInfoModal').modal("show");
        $('#additionalInfoModal table tbody tr').eq(0).find("td").eq(1).find("input").select()
    })

    $('#saveAdditionalInfo').on("click",function(){
        var data = [];
        $('#additionalInfoModal table tbody tr').each(function(i,j){
            let id = $(this).data("id");
            let field_name = $(this).find("td").eq(0).text();
            let value = $(this).find("input").val();
            data.push({id:id,field_name:field_name,value:value})
        })
        $("tr.selected").find(".additional_fields").val(JSON.stringify(data))
        $('#additionalInfoModal').modal("hide");
    })

    $('#clearAdditionalInfo').on("click",function(){
        $('#additionalInfoModal table input').val("");
        $('#clearAdditionalInfo').addClass("hidden");
        $('#additionalInfoModal table tbody tr').eq(0).find("td").eq(1).find("input").select()
    })

    $('#additionalInfoModal').on("show.bs.modal",function(){
        $('#additionalInfoModal table tbody tr').eq(0).find("td").eq(1).find("input").select()
    })

    $('#additionalInfoModal').on("hide.bs.modal",function(){
        $('#item_details tr.selected').removeClass("selected");
    })

    // endregion additionInfo    


    // region upload photos

    $('#photos').on("change",".photos",function(){
        let elem = $(this).val();
        console.log(elem)
        if(checkPhotos()>=3){

        }else{

        }
    })

    // endregion upload photos

    $('#modalSelectClient').on("shown.bs.modal",function(){
        fetchClients("",false);
        $('#modalSearchClients').trigger("focus");
    })

    $('#modalSelectClient').on("hidden.bs.modal",function(){
        $('#modalSearchClients').trigger("focus");
        $('#modalSearchClients').val("");
        $('#select_customer tbody').empty();
    })

    $('#saveComment').on("click", function(){
        if($(this).hasClass("running")) return false;
        
        let comment = $('#new_comment').val();
        let id = $(this).data("id");
        if(comment.length==0) return

        let currentState = $('#saveComment').html();
        $(this).addClass("running");
        $('#saveComment').html('<i class="fas fa-sync"></i> Saving ...')
        $.ajax({
            url: base_url + "orders/saveComment",
            method:"POST",
            data:{id:id,comment:comment},
            dataType:"JSON",
            success: function(response) {
                let html = "<li class='list-group-item'>";
                    html += "<span class='author'>On " + response.comments[0].created_on + " " + response.comments[0].agent + " commented:</span><br>";
                    html += nl2br(comment)
                    html += "</li>";
                    $('ul#comments').prepend(html)
                $('#new_comment').val("");
                $('#saveComment').removeClass("running");
                $('#saveComment').html(currentState)
            }
        })
    })

    $('#items').on("click",".addRow",function(){
        $(this).closest("tr").removeClass("photo-error");
        var row = $(this).closest("tr").clone();
        $(row).attr("data-id","0")
        $(row).find(".product_id").val("");
        $(row).find(".measurements").val("");
        $(row).find(".images").val("[]");
        $(row).find(".image_map").val("");
        $(row).find(".image_vetement").val("");
        $(row).find(".additional_fields").val("");
        $(row).find(".itemcode").val("");
        $(row).find(".description").val("");
        $(row).find(".price").val("0");
        $(row).find(".quantity").val("1");

        $(row).find(".enterMeasurements, .uploadPhotos, .additionalFields").removeClass("hidden")
        $('#items tbody').append(row);

        var row2 = $(this).closest("tr").next("tr").clone();
        $(row2).find(".fabric_reference").val("");
        $(row2).find(".fabric_color").val("");
        $(row2).find(".size").val("0");
        $('#items tbody').append(row2);
    })

    $('#items').on("click",".deleteRow",function(){
        var ct = $('#items tbody tr').length;
        if(ct==2) return false;
        var order_details_id = $(this).closest("tr").data("id");
        var deleted_rows_json = $("input[name=deleted_rows]").val();
        var deleted_rows = JSON.parse(deleted_rows_json);
        deleted_rows.push(order_details_id)
        $("input[name=deleted_rows]").val(JSON.stringify(deleted_rows));
        $(this).closest("tr").next("tr").remove();
        $(this).closest("tr").remove();
        reCalculate();
    })

    $("#select_customer").on("click",".select-customer",function(){
        let customer_id = $(this).data("customer-id");
        $('input[name=customer_id]').val(customer_id);
        // data.customer_id = customer_id;
        fetchClient(customer_id);
        $('#modalSelectClient').modal("hide");
    })

    $('.balance-left').on("click", function() {
        alertify.alert("Sorry, cannot proceed to Final Stage because this order has a balance of " + $('#balance').val())
    })
    
    $('#item_details .price, #item_details .quantity, #totals #deposit').on("keyup blur change",function(){
        reCalculate()
    })

    $('#measurementModal').on("shown.bs.modal",function(){
        $('#measurementModal input[name=measurement]').trigger("focus");
    })

    $('#modalMeasurements').on("hide.bs.modal",function(){
        ms = [];
        $("tr.selected").removeClass("selected")
    })

    $('#measurementModal').on("hidden.bs.modal",function(){
        $('#measurementModal input[name=measurement]').parent().removeClass("has-error");
        $('#measurementModal textarea[name=measurements_remarks]').val("");
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
    
    $('#message-box').on("click",".fetch-error",function(){
        let elem = $(this).data("error");
        console.log(elem)
        $(elem).trigger('click')
    })

    $("#edit_order").on("click",'.process',function(){

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
          url: base_url + 'orders/uploadImages', 
          type: 'post',
          data: form_data,
          dataType: 'json',
          contentType: false,
          processData: false,
          success: function (response) {
            // $('#preview').empty()
            for(var index = 0; index < response.length; index++) {
                let line = "<div class='preview-image col-sm-2'>"
                line += "<img style='cursor:zoom-in;' class='img-thumbnail' src='"+(response[index].image)+"'>";
                line += "<i data-id='"+(response[index].id)+"' class='fa fa-times delete-image'></i>"
                line += "</div>"
                $('#preview').append(line);
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
            url: base_url + 'orders/deleteImage', 
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

    $('#nextStage').on("click",function(){
        let uuid = $('#edit_order').find("input[name=uuid]").val();
        let nextStageId = $(this).data("stage");
        let page = $(this).attr("page");
        let nextStageName = $(this).text();
        // let remarks = "";
        bootbox.prompt({
            title: 'Notes for <strong>' + nextStageName + "</strong>",
            inputType: 'textarea',
            callback: function (result) {
                // console.log(result);
                // remarks = result;
                if(result !== null){
                    $.ajax({
                        url: base_url + "orders/nextStage",
                        method:"POST",
                        data:{uuid:uuid,nextStageId:nextStageId,nextStageName:nextStageName,remarks:result},
                        dataType:"JSON",
                        success: function(response) {
                            console.log(response)
                            if(response.result == true){
                                window.location.href = base_url + "orders/listing/"+page;
                            }else{
                                alertify.alert("Stages",response.reason)
                            }
                        }
                    })
                }else{

                }
                
            }
        });
        

    })

    $('.nextStageSingle').on("click",function(){
        let uuid = $("input[name=uuid]").val();
        let orderDetailsId = $(this).data("order-details-id");
        let nextStageId = $(this).data("stage");
        let nextStageName = $(this).data("next-stage-name");
        let remarks = "";
        bootbox.prompt({
            title: 'Notes for <strong>' + nextStageName + "</strong>",
            inputType: 'textarea',
            callback: function (result) {
                remarks = result;
                if(result !== null){
                    $.ajax({
                        url: base_url + "orders/nextStageSingle",
                        method:"POST",
                        data:{orderDetailsId:orderDetailsId, nextStageId:nextStageId, nextStageName:nextStageName, remarks:remarks},
                        dataType:"JSON",
                        success: function(response) {
                            console.log(response)
                            if(response.result == true){
                                window.location.href = base_url + "orders/edit/" + uuid;
                            }else{
                                alertify.alert("Stages",response.reason)
                            }
                        }
                    })
                }else{

                }
                
            }
        });
        

    })

    /** SUPPLEMENTS */

    $('#edit_order').on("click",".supplements",function(){

        // let product_id = $(this).closest("tr").find(".product_id").val();
        // if(product_id == "") {
        //     toastr["error"]("Please select a product first");
        //     unselectRow();
        //     return;
        // }

        $('#rowSupplementsModal').modal("show");
        $(this).closest("tr").addClass("selected");
        let supplements = JSON.parse($("tr.selected").find(".supplements").val());

        //clear previous data
        $('#rowSupplementsModal table tbody tr').each(function(i,row){
            if(i==0){
                $(this).find(".supplement_type").val("");
                $(this).find(".supplement_name").val("");
                $(this).find(".supplement_amount").val(0);
            } else {
                $(this).remove();
            }
        })
        $('#rowSupplementsModal .supplement_total').val('0');

        $(supplements).each(function(i,row){
            let x = $('#rowSupplementsModal table tbody tr').eq(0).clone();
            $(x).find(".supplement_type").val(row.id)
            $(x).find(".supplement_name").val(row.name)
            $(x).find(".supplement_amount").val(row.amount)
            $('#rowSupplementsModal table tbody').append(x)
        })
        if($('#rowSupplementsModal table tbody tr').length > 1) $('#rowSupplementsModal table tbody tr').eq(0).remove()
        totalSupplement();
    })

    $('#rowSupplementsModal').on("change",".supplement_type",function(){
        let amount = $(this).closest("tr").find(".supplement_type :selected").data("amount");
        let name = $(this).closest("tr").find(".supplement_type :selected").text();
        $(this).closest("tr").find(".supplement_amount").val(amount);
        $(this).closest("tr").find(".supplement_name").val(name);
        totalSupplement();
    })

    $('#rowSupplementsModal').on("click",".addRow", function() {
        let row = $(this).closest("tr").clone();
        $(row).find(".supplement_type").val("").removeClass("input-error");
        $(row).find(".supplement_amount").val("0");
        $('#rowSupplementsModal table tbody').append(row);
        totalSupplement();
    })

    $('#rowSupplementsModal').on("click",".deleteRow", function() {
        let ct = $('#rowSupplementsModal table tbody tr').length;
        if(ct == 1) return;
        $(this).closest('tr').remove();
        totalSupplement();
    })

    function totalSupplement()
    {
        let total = 0;
        let orderSupplementTotal = 0;
        $('#rowSupplementsModal table tbody tr').each(function(){
            let amount = parseFloat($(this).find('.supplement_amount').val());
            if(isNaN(amount)) amount = 0;
            total += amount
        })
        $('#rowSupplementsModal .supplement_total').val(total);

        $('#items table tbody tr.master').each(function(i,j){
            let product_id = $(this).find(".product_id").val();
            // if(product_id!=''){
                let supplements = JSON.parse($(this).find(".supplements").val());
                $(supplements).each(function(i,j){
                    orderSupplementTotal += j.amount;
                })
            // }
        })
        // console.log("grand total",orderSupplementTotal)
        $('#supplement_total').val(orderSupplementTotal)
        reCalculate();
    }

    $('#saveOrderSupplements').on("click", function() {
        let data = [];
        $('#rowSupplementsModal table tbody tr').each(function(){
            let id = $(this).find('.supplement_type').val();
            if(id!==''){
                let amount = parseFloat($(this).find('.supplement_amount').val());
                let name = $(this).find('.supplement_name').val();
                data.push({id:id, amount:amount, name:name})
            }
        })
        if(data.length == 0){
            $('.supplement_type').addClass("input-error");
            return;
        }
        $("tr.selected").find(".supplements").val(JSON.stringify(data));
        $('#rowSupplementsModal').modal("hide");
        unselectRow()
        totalSupplement();
    })

    $('#rowSupplementsModal').on("hidden.bs.modal",function(){
        unselectRow();
    })

    /** END OF SUPPLEMENTS */


})

function updateOrder( draft )
{
    //clear previous errors
    $('.input-error').removeClass("input-error");
    $('.tab-error').removeClass("tab-error");
    $('.photo-error').removeClass("photo-error");

    draft = ( (typeof draft == 'undefined') || (draft == null) ) ? false : draft;

    let valid = true;
    let errorMessage = "";
    let customer_id = $("input[name=customer_id]").val();
    let uuid = $("#edit_order [name=uuid]").val();
    let id = $("#edit_order [name=id]").val();

    if(customer_id==""){
        valid = false;
        $(".select-customer").addClass("input-error");
        errorMessage += "Please select a Customer<br>";
        $('#customer-tab').addClass("tab-error");
    }

    if(!draft) {
        let delivery_date = $('#delivery_date').val();
        if(delivery_date == ""){
            valid = false;
            $('#delivery_date').addClass("input-error");
            errorMessage += "Please enter Delivery Date<br>";
            $('#delivery-tab').addClass("tab-error");
        }

        let delivery_time = $('#delivery_time').val();
        if(delivery_time == ""){
            valid = false;
            $('#delivery_time').addClass("input-error");
            errorMessage += "Please enter Delivery Time<br>";
            $('#delivery-tab').addClass("tab-error");
        }

        let delivery_store = $('#delivery_store').val();
        if(delivery_store == ""){
            valid = false;
            errorMessage += "Please select Delivery Store<br>";
            $('#delivery_store').addClass("input-error");
            $('#delivery-tab').addClass("tab-error");
        }
    }

    let items = []
    $('.photo-error').removeClass("photo-error");
    $('#items tbody tr.master').each(function(i,j){

        let orderDetailsId = (typeof ($(this).data("id")) === 'undefined' ) ? 0 : $(this).data("id");

        if($(this).find(".product_id").val()!=""){

            let imagesJson = $(this).find(".images").val();
            let imageCount = JSON.parse(imagesJson).length;

            if(!draft) {

                if($(this).find(".measurements").val() == "") {
                    valid = false;
                    $('#items-tab').addClass("tab-error");
                    errorMessage += "Please enter measurements <i class='fa fa-ruler'></i> for #"+(i+1)+" - " + $(this).find(".fetch-products").val()+"<br>"
                    return;
                }

                if($(this).next("tr").find(".fabric_color").val() == ''){
                    valid = false;
                    $(this).next("tr").find(".fabric_color").addClass("input-error");
                    $('#items-tab').addClass("tab-error");
                    errorMessage += "#" + (i+1) + " - " + $(this).find(".fetch-products").val() + "  Fabric Color is mandatory<br>";
                }

                if($(this).next("tr").find(".fabric_reference").val() == '') {
                    valid = false;
                    errorMessage += "#" + (i+1) + " - " + $(this).find(".fetch-products").val() + " Fabric Reference is mandatory<br>";
                    $(this).next("tr").find(".fabric_reference").addClass("input-error");
                    $('#items-tab').addClass("tab-error");
                }

                console.log(imagesJson)
                if(imageCount < 3) {
                    $(this).find(".uploadPhotos").addClass("photo-error");
                    $('#items-tab').addClass("tab-error");
                    valid = false;
                    errorMessage += (i+1) + " - " + $(this).find(".fetch-products").val() + " has only "+imageCount+" images uploaded but requires 3<br>";
                }
            }

            items.push({
                order_details_id : orderDetailsId,
                product_id : $(this).find(".product_id").val(),
                measurements : $(this).find(".measurements").val(),
                additional_fields : $(this).find(".additional_fields").val(),
                notes: $(this).find(".order_details_notes").val(),
                supplements: $(this).find(".supplements").val(),
                price : $(this).find(".price").val(),
                quantity : $(this).find(".quantity").val(),
                fabric_reference : $(this).next("tr").find(".fabric_reference").val(),
                fabric_color : $(this).next("tr").find(".fabric_color").val(),
                size : $(this).next("tr").find(".size").val(),

                images: JSON.parse(imagesJson)
            })
        }

    })

    if(items.length==0){
        valid = false;
        $('#items-tab').addClass("tab-error");
        errorMessage += "Please add at least one Item<br>";
    }

    if(!valid){
        // alert(errorMessage)
        myAlert(errorMessage);
        return false;
    }

    let order = {
        uuid: uuid,
        id:id,
        customer_id : $("input[name=customer_id]").val(),
        discount_pct : $('#discount_pct').val(), 
        discount : $('#discount').val(),
        deposit : $('#deposit').val(),
        amount: $('#amount').val(),
        supplements: $('#supplement_total').val(),
        stage_id : 1,

        delivery_date : $('#delivery_date').val(),
        delivery_time : $('#delivery_time').val(),

        delivery_store_id : $('#delivery_store').val(),
        department_id : 1,

        trial_date : $('#trial_date').val(),
        trial_time : $('#trial_time').val(),
        trial_store_id : $('#trial_store').val(),

        complete : 0,

        draft: (draft) ? 1 : 0,

        deleted_rows : $("input[name=deleted_rows]").val(),
        items: items
    }
    console.log(items);

    $.ajax({
        url: base_url + "orders/update",
        method: "POST",
        data: order,
        dataType: "JSON",
        success: function(response){
            console.log(response)
            if(response.result) {
                window.location.href = base_url + "orders/listing";
            }
        }
    })
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

function fetchProducts()
{
    $.ajax({
        url: base_url + "products/fetchAll",
        method:"POST",
        dataType:"JSON",
        success:function(response){
            if(response.result){
                var data = "<div class='row'>";
                $(response.rows).each(function(i,j){
                    data += "<div data-uuid="+j.uuid+" class='col-md-4 select-product cursor-pointer'>";
                    data += "<img src='"+base_url+"uploads/products/"+j.photo+"' class='img-thumbnail no-zoom'>"
                    data += "<h6>"+j.name+"</h6>";
                    data += "</div>";
                })
                data += "</div>";
                $('#modalSelectProduct .modal-body').empty().append(data);
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
                    line += "<td>"+row.customer_code+"</td>";
                    line += "<td>"+row.title+" "+row.last_name+" "+row.first_name+"</td>";
                    line += "<td>"+row.fidelity_card+"</td>";
                    line += "<td>"+row.address+", "+row.city+"</td>";
                    line += "<td>";
                    if(row.phone_number1 !== null) line += row.phone_number1;
                    if( (row.phone_number2 !== null) || (row.phone_number2 == "") ) {
                        line += ","+row.phone_number2;
                    }
                    line += "</td>";
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
                $('#customer_info .code').removeClass("cursor-pointer select-customer").html(row.customer_code + "<div class='remove-customer text-bold red cursor-pointer' style='float:right'>X</div>");
                $('#customer_info .name').html(row.first_name+' '+row.last_name)
                $('#customer_info .address').html(row.address+', '+row.city)
                $('#customer_info .phones').html(row.phone_number1 + ', ' + row.phone_number2)
                $('#customer_info .nationality').html(row.nationality)
                $('#customer_info .fidelity').html(row.fidelity_card)
                $('#customer_info .discount').html(row.discount+"%")
                $('#item_details .discount').val();

                $('#discount_pct').val(row.discount);

                reCalculate();
            }else{
                window.location.href = base_url +"users/signin";
            }
            
        }
    })

    return customer;
}

function removeClient()
{
    $('#customer_info .customer_info').empty();
    $('#customer_info .code').addClass("cursor-pointer select-customer").html("Click to Select Customer");
    $('input[name=customer_id]').val("");
}

function displayItemDetails(item)
{
    $('#item_details tr.selected .itemcode').val(item.stockref);
    $('#item_details tr.selected .description').val(item.name);
    
    $('#item_details tr.selected .price').val( parseFloat(item.selling_price).toLocaleString("en-US"));
    $('#item_details tr.selected .quantity').val(1);
    $('#item_details tr.selected .amount').val( parseFloat(item.selling_price).toLocaleString("en-US") );

    reCalculate()
}

function reCalculate()
{
    var total = 0;
    $('#items tbody tr.master').each(function(i,j) {
        let t = $(this);
        let p = $(this).find(".price").val();
        let price = p.replace(/,/g, '')
        let quantity = parseInt($(this).find(".quantity").val().trim());
        if(isNaN(quantity)) {
            quantity = 1;
            window.setTimeout(function(){
                $(t).find(".quantity").val(quantity);
            },750)
        }
        let amount = parseFloat(price) * parseFloat(quantity);
        $(this).find('.amount').val(amount);
        total += amount;
    })

    let st = $('#supplement_total').val();
    let supplement_total = parseFloat(st.replace(/,/g,''));
    console.log(supplement_total);

    let sub_total = total + supplement_total
    let discount_pct = $('#discount_pct').val();
    let deposit = $('#deposit').val();
    let discount_amount = sub_total * discount_pct / 100;
    let net_amount = sub_total - discount_amount;
    let balance = net_amount - deposit;

    $('#amount').val(formatNumber(total));
    $('#sub_total').val(formatNumber(sub_total));
    $('#discount').val(formatNumber(discount_amount));
    $('#net_amount').val(formatNumber(net_amount));
    $('#balance').val(formatNumber(balance));

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
    $(elem).closest("tr").next("tr").addClass("selected");
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

function init()
{
    $('#items tr.master').each(function(){
        let row = $(this);
        let rowId = $(this).data("id");

        $.ajax({
            url : base_url + "orders/getSingleRowDetails",
            method:"POST",
            dataType:"JSON",
            data:{id:rowId},
            success:function(response){
                $(row).find(".measurements").val(response.row.orderMeasurements);
                $(row).find(".additional_fields").val(response.row.orderAdditionalFields);
                $(row).find(".supplements").val(response.row.orderSupplements);
                $(row).find(".image_map").val(response.row.vetementImageMap);
            }
        })

    })
    
}