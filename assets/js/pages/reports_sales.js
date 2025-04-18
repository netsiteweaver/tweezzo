jQuery(function(){
    
    fetch();

    $('#apply_filter').on("click",function(){
        fetch();
    })

    $('#export').on("click",function(){
        exportToCsv('#tbl_orders','Orders Report - '+Date.parse(new Date)+'.csv')
    })

    $('input[name=start_date]').on('change',function(){
        $('input[name=end_date]').val($(this).val())
        $('input[name=end_date]').prop("min",$(this).val())
    })

    $('body').on('click','.view-order', function(){
        let uuid = $(this).closest("tr").data("uuid");
        window.location.href = base_url + "sales/view/" + uuid + "?referer=reports/sales/";
    })
})

function fetch()
{
    var stages = {draft:'default',delivery:'warning',completed:'success',cancel:'danger'}
    $.ajax({
        url: base_url + "reports/sales/fetch/",
        method: "POST",
        data: $('#filter').serialize(),
        dataType: "JSON",
        success: function(response){
            $('#tbl_orders tbody').empty();
            var lines = [];
            $(response.rows).each(function(i,row){
                let line = "<tr class='text-center' data-uuid='"+row.uuid+"'>";
                line += "<td>"+row.invoice_date+"</td>";
                // line += "<td class='text-left'>"+(row.customer_details).replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1::$2')+"</td>";
                line += "<td class='text-left'>"+"<a href='tel:"+row.phone_number1+"'>"+row.phone_number1+"</a>";
                line += "<br>"+row.first_name+' '+row.last_name+"</td>";
                line += "<td class='view-order cursor-pointer'>"+row.document_number+"</td>";
                line += "<td>"+row.stockref+"</td>";
                line += "<td class='text-left'>"+row.description+"</td>";
                line += "<td>"+row.color_name+"</td>";
                line += "<td>"+row.category_name+"</td>";
                line += "<td>"+row.channel_name.toUpperCase()+"</td>";
                line += "<td>"+row.region.toUpperCase()+"</td>";
                line += "<td>"+row.route.toUpperCase()+"</td>";
                if(row.deliver_status == 'cancelled'){
                    line += "<td><span class='label label-danger'>"+row.deliver_status.toUpperCase()+"</span></td>";
                }else{
                    line += "<td><span class='label label-"+stages[row.stage]+"'>"+row.stage.toUpperCase()+"</span></td>";
                }
                line += "<td>"+row.quantity+"</td>";
                line += "<td class='text-right'>"+parseFloat(row.price).toLocaleString('en-US')+"</td>";
                line += "<td class='text-right'>"+parseFloat(row.total).toLocaleString('en-US')+"</td>";
                lines += line;
            })
            $('#tbl_orders tbody').append(lines);
        }
    })
}
