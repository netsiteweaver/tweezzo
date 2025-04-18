jQuery(function(){
    
    fetch('');

    $('#apply_filter').on("click",function(){
        fetch('');
    })

    $('.monitor_change').on("change",function(){
        fetch('');
    })

    $('#status_single').on('change',function(){
        let status = $('select[name=status_single]').val();
        let uuid = $('input[name=uuid]').val();
        if(status.length==0) window.location.href = base_url + "reports/deliveries/single/"+uuid;
        window.location.href = base_url + "reports/deliveries/single/"+uuid+"?status="+status
    })

    $('#export').on("click",function(){
        exportToCsv('#tbl_deliveries','Deliveries Report - '+Date.parse(new Date)+'.csv')
    })

    $('#export-single').on("click",function(){
        exportToCsv('#tbl_delivery','Single Delivery Report - '+Date.parse(new Date)+'.csv')
    })

    $('input[name=start_date]').on('change',function(){
        if($('input[name=end_date]').val() < $(this).val()){
            $('input[name=end_date]').val($(this).val())
        }
        $('input[name=end_date]').prop("min",$(this).val())
    })

    $('body').on('click','.view',function(){
        let uuid = $(this).data("uuid");
        viewContent(uuid)
    })
})

function viewContent(uuid)
{
    $.ajax({
        url: base_url + "reports/deliveries/single",
        data:{uuid:uuid},
        method:"POST",
        dataType:"HTML",
        success: function(html){
            $('#modalContent .modal-body').html(html)
            $('#modalContent').modal("show");
        }
    })
}

function fetch(output)
{
    $.ajax({
        url: base_url + "reports/deliveries/fetch/"+output,
        method: "POST",
        data: $('#filter').serialize(),
        dataType: "JSON",
        success: function(response){
            console.log(response)
            $('#tbl_deliveries tbody').empty();
            var lines = [];
            let granTotal = 0;
            $(response.rows).each(function(i,row){
                let line = "<tr class='text-center'>";
                line += "<td>"+row.document_number+"</td>";
                line += "<td class='text-left'>"+row.delivery_date.substring(0,10)+"</td>";
                line += "<td>"+row.delivery_boy+"</td>";
                line += "<td>"+ (row.deliver_status).replace('_',' ').toUpperCase()+"</td>";
                let total = 0;
                $(row.payments[0]).each(function(i,j){
                    total += parseFloat(j.amount);
                    line += "<td class='text-right'>"+ parseFloat(j.amount).toLocaleString("en-US") +"</td>";
                })
                granTotal += total;

                line += "<td class='text-right'>"+ total.toLocaleString("en-US") +"</td>";
                line += "<td>"+ row.Transferred+"</td>";

                // line += "<td><a href='"+base_url+"reports/deliveries/single/"+row.uuid+"' target='_blank'><div class='btn btn-default'><i class='fa fa-eye'></i> View Content</div></a></td>";
                line += "<td><div data-uuid='"+row.uuid+"' class='btn btn-sm btn-info view'><i class='fa fa-eye'></i> View</div></td>";
                lines += line;
            })
            $('#tbl_deliveries tbody').append(lines);

            let tot = "<tr class='text-right'>";
            tot += "<td class='text-center'> Total</td>";
            tot += "<td></td>";
            tot += "<td></td>";
            tot += "<td></td>";
            $(response.totals).each(function(i,row){
                tot += "<td>"+parseFloat(row.amount).toLocaleString("en-US")+"</td>";  
            })
            tot += "<td>"+granTotal.toLocaleString("en-US")+"</td>";  
            $('#tbl_deliveries tbody').append(tot);

        }
    })
}
