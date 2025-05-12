jQuery(function(){

    // tableSort("#task-list","tasks");

    $('.view-notes').on("click", function() {
        let taskId = $(this).closest("tr").data("id");
        let taskNumber = $(this).closest("tr").find("td.task-number").html();
        let taskSection = $(this).closest("tr").find("td.task-section").html();
        let taskName = $(this).closest("tr").find("td.task-name").text();

        Overlay("on");
        $.ajax({
            url: base_url + "tasks/loadNotes",
            method: "POST",
            dataType: "JSON",
            data: {task_id:taskId},
            success: function(response)
            {
                if(response.result){
                    if(response.notes.length == 0){
                        toastr.error("No notes found for this task.")
                    }else{
                        $('#modalNotes .modal-body tbody').empty();
                        $('#modalNotes .modal-title').html(`<b>Notes for</b>: ${taskNumber} / ${taskSection} / ${taskName}`);
                        $(response.notes).each(function(i,j){
                            let html = `<tr><td style='font-size:10px; color:#ccc; '>${i+1}</td><td>${nl2br(j.notes)}<span class='float-right' style='font-size:12px; margin-top: 20px; color:#999;'>by `
                            if(j.customer !== null){
                                html += j.customer;
                            }else if(j.name !== null){
                                html += j.name;
                            }
                            html += ` on ${j.created_on.substring(0,16)}</span></td></tr>`;
                            $('#modalNotes .modal-body tbody').append(html);
                        })
                        $('#modalNotes').modal("show");
                    }
                    Overlay("off");
                }else{
                    alertify.alert('Error',response.reason)
                }
            },
            complete: function(response) {
                Overlay("off");
            }
        })
    })

    $('.email').on("click",function(){
        let btn = $(this);
        let customerEmail = $(this).data("email");

        if($(this).hasClass("disabled")) return false;
        if($(this).hasClass("running")) return false;
        
        $(this).addClass("running");

        alertify.prompt('Email','Do you want to email this task list to the customer? <br>Below is the email we have for the selected customer, but it may happen that want to send it to an alternate email.', customerEmail,
            function(evt, email){

                let customer_id = $('#customer_id').val();
                let project_id = $('#project_id').val();
                let sprint_id = $('#sprint_id').val();
                let stage = $('#stage').val();
                let assigned_to = $('#assigned_to').val();
                let order_by = $('#order_by').val();
                let order_dir = $('#order_dir').val();
                let display = $('#display').val();

                params = '?customer_id='+customer_id+"&project_id="+project_id+"&sprint_id="+sprint_id+"&stage="+stage+"&assigned_to="+assigned_to+"&order_by="+order_by+"&order_dir="+order_dir+"&display="+display+"&customer_email="+email+"&type=customer&output=email";
                // console.log(params, customerEmail, email)
                $.ajax({
                    url: 'tasks/email'+params,
                    method:"GET",
                    // dataType:"JSON",
                    complete: function(response) {
                        $(btn).removeClass('running');
                        alertify.alert("Confirmation","Email has been queued and will be sent shortly.")
                    }
                })
            },
            function(){
                $(btn).removeClass('running');
                // alertify.error("Cancelled.")
            }
        )
    })

    $('.email-developer').on("click",function(){
        let btn = $(this);
        // let customerEmail = $(this).data("email");

        if($(this).hasClass("disabled")) return false;
        if($(this).hasClass("running")) return false;
        
        $(this).addClass("running");

        alertify.prompt('Email','Do you want to email this task list to a developer or any other person? <br>Simply enter the destination email below.', '',
            function(evt, email){

                if( (email=='') || (!valid(email)) ){
                    toastr.error("Please enter a valid email")
                    $(btn).removeClass('running');
                    return
                }

                let customer_id = $('#customer_id').val();
                let project_id = $('#project_id').val();
                let sprint_id = $('#sprint_id').val();
                let stage = $('#stage').val();
                let assigned_to = $('#assigned_to').val();
                let order_by = $('#order_by').val();
                let order_dir = $('#order_dir').val();
                let display = $('#display').val();

                params = '?customer_id='+customer_id+"&project_id="+project_id+"&sprint_id="+sprint_id+"&stage="+stage+"&assigned_to="+assigned_to+"&order_by="+order_by+"&order_dir="+order_dir+"&display="+display+"&customer_email="+email+"&output=email&type=developer";
                console.log(params, email)
                $.ajax({
                    url: 'tasks/email'+params,
                    method:"GET",
                    // dataType:"JSON",
                    complete: function(response) {
                        $(btn).removeClass('running');
                        alertify.alert("Confirmation","Email has been queued and will be sent shortly.")
                    }
                })
            },
            function(){
                $(btn).removeClass('running');
                // alertify.error("Cancelled.")
            }
        )
    })

    $('.select_task').on("click", function() {
        let selected = $('#task-list tbody tr td input.select_task:checked').length
        if(selected>0){
            $('#withSelectedBtn').removeClass("disabled");
        }else{
            $('#withSelectedBtn').addClass("disabled");
        }
    })

    $('.select_all_tasks').on("click", function() {
        if(!$(this).is(":checked")) {
            $('#task-list tbody tr td input.select_task').prop("checked",false);
            $('#withSelectedBtn').addClass("disabled");
        }else{
            $('#task-list tbody tr td input.select_task').prop("checked",true);
            $('#withSelectedBtn').removeClass("disabled");
        }
    })

    $(".select-user").on("click", function(){
        let taskId = $('input[name=id]').val();
        let userId = $(this).data("id");
        if($(this).hasClass("assigned")){
            $(this).removeClass("assigned");
        }else{
            $(this).addClass("assigned");
        }
    })

    $(".select-sprint").on("click", function(){
        $(".select-sprint").removeClass("assigned");
        if($(this).hasClass("assigned")){
            $(this).removeClass("assigned");
        }else{
            $(this).addClass("assigned");
        }
    })

    $('.delete-multiple').on("click", function(){
        $('#modalDeleteConfirmation .modal-body').empty();
        let html = "<ul class='list-group'>"
        $(":checkbox.select_task:checked").each(function(i,j){
            let taskNumber = $(this).closest("tr").find("td.task-number").html();
            let taskSection = $(this).closest("tr").find("td.task-section").html();
            let taskName = $(this).closest("tr").find("td.task-name").html();
            html += `<li class='list-group-item list-group-item-danger'>[${taskNumber}] ${taskSection} / ${taskName}</li>`
        })
        html += "</ul>"
        console.log(html);
        $('#modalDeleteConfirmation .modal-body').html(html);
        $('#modalDeleteConfirmation').modal("show")
    })

    $('.proceedWithDeletion').on("click", function(){
        let taskIds = [];
        $(":checkbox.select_task:checked").each(function(i,j){
            taskIds.push($(this).closest("tr").data("id"));
        })
        deleteTasks(taskIds);
    })

    $('.assign-multiple').on("click", function(){
        $('#modalAssignUsers').modal("show")
        
    })

    $('.stage-multiple').on("click", function(){
        $('#modalSetStage').modal("show")
        
    })

    $('.due-date-multiple').on("click", function(){
        $('#modalDueDate').modal("show")
        
    })

    $('.move-sprint-multiple').on("click", function(){
        $('#modalChangeSprint').modal("show")
    })

    $('#modalChangeSprint .changeSprint').on("click", function(){
        let taskIds = [];
        let sprint = $('#modalChangeSprint .select-sprint.assigned').data("sprint");

        if(sprint == "") return false;

        $(":checkbox.select_task:checked").each(function(i,j){
            taskIds.push($(this).closest("tr").data("id"));
        })

        changeSprint(taskIds,sprint);
    })

    $(".select-stage").on("click", function(){
        $('#modalSetStage .select-stage').removeClass("assigned");
        $(this).addClass("assigned");
    })

    $('#modalSetStage .changeStage').on("click", function(){
        let taskIds = [];
        let stage = $('#modalSetStage .select-stage.assigned').data("stage");

        if(stage == "") return false;

        $(":checkbox.select_task:checked").each(function(i,j){
            taskIds.push($(this).closest("tr").data("id"));
        })

        changeStage(taskIds,stage);
    })

    $('.proceed').on("click", function(){
        let taskIds = [];
        let userIds = [];
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const customerId = urlParams.get('customer_id');
        const projectId = urlParams.get('project_id');
        const sprintId = urlParams.get('sprint_id');

        $(":checkbox.select_task:checked").each(function(i,j){
            taskIds.push($(this).closest("tr").data("id"));
        })
        $('ul#users-list li.select-user.assigned').each(function(i,j){
            userIds.push($(this).data("id"));
        })
        assignUsers(taskIds,userIds,customerId,projectId,sprintId);
    })

    $('.setDueDate').on("click", function(){
        let taskIds = [];
        let dueDate = $('#modalDueDate input[name=due_date]').val();

        $(":checkbox.select_task:checked").each(function(i,j){
            taskIds.push($(this).closest("tr").data("id"));
        })

        setDueDate(taskIds,dueDate);
    })

    $('#customer_id').on("change",function(){
        $('#project_id').val('');
        $('#sprint_id').val('');
    })
    
    $(".monitor").on("change", function(){
        let customer_id = $('#customer_id').val();
        let project_id = $('#project_id').val();
        let sprint_id = $('#sprint_id').val();
        let stage = $('#stage').val();
        let order_by = $('#order_by').val();
        let order_dir = $('#order_dir').val();
        let display = $('#display').val();
        let assigned_to = $('#assigned_to').val();
        let notes_only = $('#notes_only').val();
        let search_text = $('#search_text').val();

        Overlay("on");
        setTimeout(function(){
            window.location.href = '/tasks/listing?customer_id='+customer_id+"&project_id="+project_id+"&sprint_id="+sprint_id+"&stage="+stage+"&order_by="+order_by+"&order_dir="+order_dir+"&display="+display+"&assigned_to="+assigned_to+"&notes_only="+notes_only+"&search_text="+search_text;
        },100)
    })

    $('.search').on("click", function(){
        Overlay("on");
        $('.monitor').trigger("change");
    })

    $('.choose-stages').on("click", function() {
        let stagesJSON = $('input[name=stage]').val();
        let stages = JSON.parse( (stagesJSON.length==0) ? "[]" : stagesJSON);
        console.log(stages);
        $('#stages-list li').each(function(i,j){
            let stage = $(this).data("stage");
            if(stages.indexOf(stage) >= 0){
                $(this).addClass("selected")
            }
        })
        $('#modalChooseStages').modal("show");
    })

    $('.choose-stage').on("click", function(){
        if($(this).hasClass("selected")){
            $(this).removeClass("selected");
        }else{
            $(this).addClass("selected")
        }
    })

    $('.applyChosenStages').on("click", function() {
        let selectedStages = [];
        $('#stages-list li.selected').each(function(i,j){
            let stage = $(this).data("stage");
            selectedStages.push(stage)
        })
        console.log(selectedStages)
        $('input[name=stage]').val(JSON.stringify(selectedStages));
        $('#modalChooseStages').modal("hide");
        setTimeout(function(){
            $('.monitor').trigger("change")
        },500)
    })

    $('#modalChooseStages').on("hidden.bs.modal",function(){
        $('#stages-list li.selected').removeClass("selected");
    })

})

function assignUsers(taskIds, userIds,customerId,projectId,sprintId)
{
    Overlay("on")
    $.ajax({
        url: base_url + "tasks/assignUsers",
        method: "POST",
        dataType: "JSON",
        data: {taskIds:taskIds, userIds:userIds,customerId:customerId,projectId:projectId,sprintId:sprintId},
        success: function(response)
        {
            if(response.result){
                window.location.reload();
            }else{
                Overlay("off")
                alertify.alert('Error',response.reason)
            }
        }
    })
}

function deleteTasks(taskIds)
{
    Overlay("on")
    $.ajax({
        url: base_url + "tasks/deleteMultiple",
        method: "POST",
        dataType: "JSON",
        data: {taskIds:taskIds},
        success: function(response)
        {
            if(response.result){
                window.location.reload();
            }else{
                Overlay("off")
                alertify.alert('Error',response.reason)
            }
        }
    })
}

function changeStage(taskIds, stage)
{
    Overlay("on")
    $.ajax({
        url: base_url + "tasks/bulkChangeStage",
        method: "POST",
        dataType: "JSON",
        data: {taskIds:taskIds, stage:stage},
        success: function(response)
        {
            if(response.result){
                window.location.reload();
            }else{
                Overlay("off")
                alertify.alert('Error',response.reason)
            }
        }
    })
}

function changeSprint(taskIds, sprintId)
{
    Overlay("on")
    $.ajax({
        url: base_url + "tasks/bulkChangeSprint",
        method: "POST",
        dataType: "JSON",
        data: {taskIds:taskIds, sprintId:sprintId},
        success: function(response)
        {
            if(response.result){
                window.location.reload();
            }else{
                Overlay("off")
                alertify.alert('Error',response.reason)
            }
        }
    })
}

function setDueDate(taskIds, dueDate)
{
    Overlay("on")
    $.ajax({
        url: base_url + "tasks/bulkSetDueDate",
        method: "POST",
        dataType: "JSON",
        data: {taskIds:taskIds, dueDate:dueDate},
        success: function(response)
        {
            if(response.result){
                window.location.reload();
            }else{
                Overlay("off")
                alertify.alert('Error',response.reason)
            }
        }
    })
}