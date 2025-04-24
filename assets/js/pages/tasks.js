jQuery(function(){

    $('#saveNote').on('click', function(e){
        e.preventDefault();
        let task_id = $('#task_notes input[name=task_id]').val();
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
                    alertify.success('Note saved successfully');
                    loadNotes(task_id);
                    $('#task_notes textarea[name=notes]').val('');
                }else{
                    alertify.error(response.reason);
                    $('#task_notes textarea[name=notes]').val('');
                    loadNotes(task_id);
                }
            },
            complete: function(){
                Overlay("off")
            }
        })

    })

    $('#previousNotes').on('click', '.deleteNote', function(e){
        e.preventDefault();
        let task_id = $('input[name=task_id]').val();
        let note_id = $(this).data('note-id');

        alertify.confirm('Delete Note', 'Are you sure you want to delete this note?', function(){
            Overlay("on")
            $.ajax({
                url: base_url + "tasks/deleteNote",
                method: "POST",
                dataType: "JSON",
                data: {note_id:note_id},
                success: function(response)
                {
                    if(response.result){
                        Overlay("off")
                        alertify.success('Note deleted successfully');
                        loadNotes(task_id);
                    }
                },
                complete: function(){
                    Overlay("off")
                }
            })
        }
        , function(){
            alertify.error('Cancel');
        });
    })

    $('#previousNotes').on('click', '.outOfScope', function(e){
        e.preventDefault();
        let task_id = $('input[name=task_id]').val();
        let note_id = $(this).data('note-id');

        alertify.confirm('Out Of Scope', 'Are you sure this note is out of scope?', function(){
            Overlay("on")
            $.ajax({
                url: base_url + "tasks/outOfScope",
                method: "POST",
                dataType: "JSON",
                data: {note_id:note_id},
                success: function(response)
                {
                    if(response.result){
                        Overlay("off")
                        alertify.success('Note marked out of scope');
                        loadNotes(task_id);
                    }
                },
                complete: function(){
                    Overlay("off")
                }
            })
        }
        , function(){
            alertify.error('Cancel');
        });
    })

    $('select[name=stage]').on("change", function(){
        let stage = $(this).val();
        if(stage == 'new'){
            $('input[name=progress]').val(0 + " %");
        }else if(stage == 'in_progress'){
            $('input[name=progress]').val(20 + " %");
        }else if(stage == 'testing'){
            $('input[name=progress]').val(40 + " %");
        }else if(stage == 'staging'){
            $('input[name=progress]').val(60 + " %");
        }else if(stage == 'validated'){
            $('input[name=progress]').val(80 + " %");
        }else if(stage == 'completed'){
            $('input[name=progress]').val(100 + " %");
        }
    })

    $('select[name=customer_id]').on('change', function(){
        let customer_id = $(this).val();
        $('select[name=project_id]').val("");
        $('select[name=project_id] option').attr("disabled",true);

        $('select[name=sprint_id]').val("");
        $('select[name=sprint_id] option').attr("disabled",true);

        $('select[name=project_id] option').each(function(i,j){
            if($(this).data("customer-id") == customer_id){
                $(this).removeAttr('disabled')
            }else{
                $(this).attr("disabled",true)
            }
        })
    })


    $('.delete_file').on('click', function(){
        let fileId = $(this).data('file-id');
        let deleted_images_json = $('input[name=deleted_images]').val();
        if(deleted_images_json == ""){
            deleted_images_json = "[]";
        }
        let deleted_images = JSON.parse(deleted_images_json);
        deleted_images.push(fileId);
        $('input[name=deleted_images]').val(JSON.stringify(deleted_images));
        $(this).closest('.col-md-2').remove();
    })


    $('select[name=project_id]').on('change', function(){
        let project_id = $(this).val();

        $('select[name=sprint_id]').val("");
        $('select[name=sprint_id] option').attr("disabled",true);

        $('select[name=sprint_id] option').each(function(i,j){
            if($(this).data("project-id") == project_id){
                $(this).removeAttr('disabled')
            }else{
                $(this).attr("disabled",true)
            }
        })
    })

    $('#sprint_id').on('change', function(){
        let sprint_id = $(this).val();
        if( sprint_id.length > 0){
            $('.ready').removeClass('d-none');
        }else{
            $('.ready').addClass('d-none');   
        }
    })

    $(".select-user").on("click", function(){
        let taskId = $('input[name=id]').val();
        let userId = $(this).data("id");
        if($(this).hasClass("assigned")){
            $(this).removeClass("assigned");
            removeUser( taskId, userId);
        }else{
            $(this).addClass("assigned");
            assignUser( taskId, userId);
        }
    })

    $('#project_id').on('change', function(){
        let project_id = $(this).val();
        getByProjectId(project_id);
        
    })

    $("#sprint_id").on("change",function(){
        let sprint_id = $(this).val();
        // alert(sprint_id);
        getMaxTaskNumberBySprintId(sprint_id);
    })


    $('#customer_id').on('change', function(){
        let customer_id = $(this).val();
        // console.log(customer_id)
        if(customer_id == ""){
            $('#project_id').empty();
            $('#sprint_id').empty();
            $('.ready').addClass('d-none');
            return false;
        }
        getByCustomerId(customer_id);
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
                    console.log(j)
                    let row = `<tr>`
                    row += `<td>${i+1}</td>`
                    row += `<td>${j.notes}<br><span class='float-right' style='color:#4c4c4c; padding:3px 8px; font-size:0.8em; font-style:italic;'>`;
                    if(j.name !== null) row += j.name;
                    if(j.customer !== null) row += j.customer;
                    row += ` - ${j.created_on}</span></td>`
                    if(response.user_id == j.created_by){
                        row += `<td><div class="btn btn-xs btn-danger deleteNote" data-note-id='${j.id}'><i class="fa fa-trash"></i></div></td>`
                    }else{
                        row += `<td></td>`
                    }
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

function getByCustomerId(customer_id)
{
    Overlay("on")
    $.ajax({
        url: '/projects/getByCustomerId',
        type: 'POST',
        data: {customer_id: customer_id},
        dataType: 'json',
        success: function(response){
            
            $('#project_id').empty();
            if(response.result) {
                $('.message').empty()
                $('#project_id').append('<option value="">Select Project</option>');
                $(response.data).each(function(index, item){    
                    $('#project_id').append('<option class="select-project" value="'+item.id+'">'+item.name+'</option>');
                });
                if(response.rows == 1){
                    window.setTimeout(function(){
                        $('#project_id').val(response.data[0].id);
                        getByProjectId(response.data[0].id);
                    }
                    , 100);
                }
                Overlay("off")
            }else{
                $('.message').text('No project found for customer');
                $('#project_id').empty();
                $('#sprint_id').empty();
                $('.ready').addClass('d-none');
                Overlay("off")
            }
        }
    })
}

function getByProjectId(project_id)
{
    Overlay("on")
    $.ajax({
        url: '/sprints/getByProjectId',
        type: 'POST',
        data: {project_id: project_id},
        dataType: 'json',
        success: function(response){
            console.log(response)
            $('#sprint_id').empty();
            if(response.result) {
                $('#sprint_id').append('<option value="">Select Sprint</option>');
                $(response.data).each(function(index, item){    
                    $('#sprint_id').append('<option class="select-project" value="'+item.id+'">'+item.name+'</option>');
                });
                // if(response.rows == 1){
                //     window.setTimeout(function(){
                //         $('#sprint_id').val(response.data[0].id);
                //         $('.ready').removeClass('d-none');
                //         $('input[name=section]').trigger('focus');
                //     }
                //     , 100);
                    $("input[name='section']").trigger('focus');
                // }
                Overlay("off")
            }else{
                $("#sprint_id").empty();
                $('.ready').addClass('d-none');
                Overlay("off")
            }
        }
    })
}

function getMaxTaskNumberBySprintId(sprint_id)
{
    Overlay("on")
    $.ajax({
        url: '/tasks/getMaxTaskNumberBySprintId',
        type: 'POST',
        data: {sprint_id: sprint_id},
        dataType: 'json',
        success: function(response){
            console.log(response)
            // $('#sprint_id').empty();
            if(response.result) {
                // $('#sprint_id').append('<option value="">Select Sprint</option>');
                // $(response.data).each(function(index, item){    
                    // $('#sprint_id').append('<option class="select-project" value="'+item.id+'">'+item.name+'</option>');
                // });
                // if(response.rows == 1){
                //     window.setTimeout(function(){
                //         $('#sprint_id').val(response.data[0].id);
                //         $('.ready').removeClass('d-none');
                //         $('input[name=section]').trigger('focus');
                //     }
                //     , 100);
                $("input[name='task_number']").attr('placeholder', response.maxTaskNumber);
                window.setTimeout(function(){
                    $("input[name='task_number']").attr('value', response.maxTaskNumber);
                },1500)
                // }
                Overlay("off")
            }else{
                $("#sprint_id").empty();
                $('.ready').addClass('d-none');
                Overlay("off")
            }
        }
    })
}

function assignUser(taskId, userId)
{
    Overlay("on")
    $.ajax({
        url: base_url + "tasks/assignUser",
        method: "POST",
        dataType: "JSON",
        data: {taskId:taskId, userId:userId},
        success: function(response)
        {
            if(response.result){
                Overlay("off")
            }else{
                Overlay("off")
                alertify.alert('Error',response.reason)
            }
        }
    })
}

function removeUser(taskId, userId)
{
    Overlay("on")
    $.ajax({
        url: base_url + "tasks/removeUser",
        method: "POST",
        dataType: "JSON",
        data: {taskId:taskId, userId:userId},
        success: function(response)
        {
            if(response.result){
                Overlay("off")
            }else{
                Overlay("off")
                alertify.alert('Error',response.reason)
            }
        }
    })
}