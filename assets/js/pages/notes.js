jQuery(function(){
    $('#notes_list .active-filter').on('click', function() {
        let startDate = $('input[name=start_date]').val();
        let forPeriod = $('input[name=for]').val();
        let period = $('select[name=period]').val();
        let display = $('select[name=display]').val();
        let customerId = '';
        let projectId = '';
        let sprintId = '';
        if($(this).data('type') == 'customer') {
            customerId = $(this).data('customer-id');
        }
        if($(this).data('type') == 'project') {
            projectId = $(this).data('project-id');
        }
        if($(this).data('type') == 'sprint') {
            sprintId = $(this).data('sprint-id');
        }
        let qs = `?start_date=${startDate}&for=${forPeriod}&period=${period}&customer_id=${customerId}&project_id=${projectId}&sprint_id=${sprintId}&display=${display}`;
        // console.log(qs);
        window.location.href = base_url + "notes/listing" + qs;
    })

    $('.resetFilter').on('click', function(){
        window.location.href = base_url + "notes/listing";
    })
})