jQuery(function(){

    init();

    window.setTimeout(function(){
        let dt = $('.daterange').val();
        let dts = dt.split(' - ');
        selectedStartDate = moment(dts[0],'MM/DD/YYYY').format("YYYY-MM-DD");
        selectedEndDate = moment(dts[1],'MM/DD/YYYY').format("YYYY-MM-DD");
    },500)

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

})

function init()
{
    const urlSearchParams = new URLSearchParams(window.location.search);
    const params = Object.fromEntries(urlSearchParams.entries());
    var sd,ed;
    var selectedStartDate, selectedEndDate;
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
}