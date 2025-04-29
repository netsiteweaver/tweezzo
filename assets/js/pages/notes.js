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

    $('#saveNote').on('click', function(e){
        // e.preventDefault();
        let task_id = $(this).data('task-id');
        let notes = $('#task_notes textarea[name=notes]').val();
        if(notes == ""){
            alertify.alert('Error', 'Please enter a note');
            return false;
        }

        Overlay("on")
        $.ajax({
            url: base_url + "tasks/saveNote",
            method: "POST",
            dataType: "JSON",
            data: {task_id:task_id, notes:notes},
            success: function(response)
            {
                if(response.result){
                    $('.summernote').summernote('code', '');
                    $('#task_notes textarea[name=notes]').val('');
                    alertify.success('Note saved successfully');
                    loadNotes(task_id);
                }else{
                    // $('.summernote').summernote('code', '');
                    // $('#task_notes textarea[name=notes]').val('');
                    alertify.error(response.reason);
                    // loadNotes(task_id);
                }
            },
            complete: function(){
                Overlay("off")
            }
        })

    })
})

function loadNotes(task_id)
{
    Overlay("on")
    $.ajax({
        url: base_url + "tasks/loadNotes",
        method: "POST",
        dataType: "JSON",
        data: {task_id:task_id},
        success: function(response)
        {
            if(response.result){
                Overlay("off")
                $('#previousNotes').empty();
                $(response.notes).each(function(i,j){
                    let row = `<tr class='`
                    if(j.out_of_scope == '1') row += 'alert alert-danger'
                    row += `'>`
                    row += `<td>${i+1}</td>`
                    row += `<td>${j.notes}<span class='float-right' style='color:#4c4c4c; padding:3px 8px; font-size:0.8em; font-style:italic;'>`;
                    if(j.name !== null) row += j.name;
                    if(j.customer !== null) row += j.customer;
                    row += ` - ${j.created_on}</span></td>`
                    // if(response.user_id == j.created_by){
                    //     row += `<td><div class="btn btn-xs btn-danger deleteNote" data-note-id='${j.id}'><i class="fa fa-trash"></i></div></td>`
                    // }else{
                    //     row += `<td></td>`
                    // }
                    row += `</tr>`
                    $('#previousNotes').append(row);
                })
            }
        },
        complete: function(){
            Overlay("off")
        }
    })
}