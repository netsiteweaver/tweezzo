jQuery(function(){

    // tableSort("#task-list","tasks");

    // Copy task reference to clipboard
    $(document).on("click", ".copy-task-ref", function(e) {
        var btn = e.currentTarget;
        var ref = $(btn).data("ref") || ($(btn).closest(".task-ref-cell").find(".task-ref-text").text() || "").trim();
        if (!ref) return;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(ref).then(function() {
                $(btn).addClass("copied").attr("title", "Copied!");
                setTimeout(function() { $(btn).removeClass("copied").attr("title", "Copy reference"); }, 1500);
            });
        } else {
            var ta = document.createElement("textarea");
            ta.value = ref;
            ta.style.position = "fixed";
            ta.style.left = "-9999px";
            document.body.appendChild(ta);
            ta.select();
            try {
                document.execCommand("copy");
                $(btn).addClass("copied").attr("title", "Copied!");
                setTimeout(function() { $(btn).removeClass("copied").attr("title", "Copy reference"); }, 1500);
            } catch (err) {}
            document.body.removeChild(ta);
        }
    });

    $('.view-notes').on("click", function() {
        let taskId = $(this).closest("tr").data("id");
        let taskNumber = $(this).closest("tr").find("td.task-number .task-ref-text").text() || $(this).closest("tr").find("td.task-number").text();
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
        let notifyMode = $(this).data("notify-mode") || '';
        let promptMessage = 'Do you want to email this task list to the customer? <br>You can keep one email or multiple emails separated by comma.';
        if (notifyMode === 'staging_validation') {
            promptMessage = 'All tasks in this sprint are at STAGING. Do you want to inform the client and ask for validation? <br>You can keep one email or multiple emails separated by comma.';
        } else if (notifyMode === 'completed_update') {
            promptMessage = 'All tasks in this sprint are COMPLETED. Do you want to inform the client? <br>You can keep one email or multiple emails separated by comma.';
        }
        let initialEmails = String(customerEmail || '').split(/[\s,;]+/).filter(function(v){ return v && v.trim() !== ''; }).join('\n');

        if($(this).hasClass("disabled")) return false;
        if($(this).hasClass("running")) return false;
        
        $(this).addClass("running");

        let messageHtml = ''
            + '<div>' + promptMessage + '</div>'
            + '<div class="mt-2">'
            + '<textarea id="client-email-list-input" class="form-control" rows="8" '
            + 'placeholder="email1@example.com&#10;email2@example.com">' + $('<div>').text(initialEmails).html() + '</textarea>'
            + '</div>';

        alertify.confirm('Email', messageHtml,
            function(){
                let email = ($('#client-email-list-input').val() || '').trim();
                if(email === ''){
                    toastr.error("Please enter at least one email");
                    $(btn).removeClass('running');
                    return;
                }

                let customer_id = $('#customer_id').val();
                let project_id = $('#project_id').val();
                let sprint_id = $('#sprint_id').val();
                let stage = $('#stage').val();
                let assigned_to = $('#assigned_to').val();
                let order_by = $('#order_by').val();
                let order_dir = $('#order_dir').val();
                let display = $('#display').val();
                let closed_filter = $('#closed_filter').val() || 'open';

                params = '?customer_id='+customer_id+"&project_id="+project_id+"&sprint_id="+sprint_id+"&stage="+stage+"&assigned_to="+assigned_to+"&order_by="+order_by+"&order_dir="+order_dir+"&display="+display+"&customer_email="+encodeURIComponent(email)+"&type=customer&output=email&notify_mode="+encodeURIComponent(notifyMode)+"&closed_filter="+encodeURIComponent(closed_filter);
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
        );
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
                let closed_filter = $('#closed_filter').val() || 'open';

                params = '?customer_id='+customer_id+"&project_id="+project_id+"&sprint_id="+sprint_id+"&stage="+stage+"&assigned_to="+assigned_to+"&order_by="+order_by+"&order_dir="+order_dir+"&display="+display+"&customer_email="+email+"&output=email&type=developer&closed_filter="+encodeURIComponent(closed_filter);
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

    $('.close-task').on("click", function() {
        let row = $(this);
        let stage = $(this).closest("tr").find(".stage").text().trim().toLocaleLowerCase();
        let id = $(this).closest("tr").data("id");

        let taskIds = [];
        taskIds.push(id);

        console.log(stage)
        let check = ['staging','completed'].indexOf(stage);
        if(check >= 0){
            alertify.confirm(
                "Confirmation",
                "Are you sure you want to mark this task as closed?",
                function(){
                    Overlay("on")
                    $.ajax({
                        url: base_url + "tasks/closeMultiple",
                        method: "POST",
                        dataType: "JSON",
                        data: {taskIds:taskIds},
                        success: function(response)
                        {
                            if(response.result){
                                 $(row).closest("tr").remove();
                                  Overlay("off")
                                // window.location.reload();
                            }else{
                                Overlay("off")
                                alertify.alert('Error',response.reason)
                            }
                        }
                    })
                   
                },
                function(){
                    
                }
            )
        }else{
            alertify.alert(
                "Cannot Close",
                `Task is currently at <b>${stage.toUpperCase()}</b> stage and cannot be close yet`
            )
        }
        console.log(stage.indexOf(['staging','completed']))

        // let selected = $('#task-list tbody tr td input.select_task:checked').length
        // if(selected>0){
        //     $('#withSelectedBtn').removeClass("disabled");
        // }else{
        //     $('#withSelectedBtn').addClass("disabled");
        // }
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

    $('.close-multiple').on("click", function(){
        $('#modalCloseConfirmation .modal-body').empty();
        let html = "<ul class='list-group'>"
        $(":checkbox.select_task:checked").each(function(i,j){
            let taskNumber = $(this).closest("tr").find("td.task-number").html();
            let taskSection = $(this).closest("tr").find("td.task-section").html();
            let taskName = $(this).closest("tr").find("td.task-name span").html();
            html += `<li class='list-group-item'>[${taskNumber}] <b>${taskSection}</b>: ${taskName}</li>`
        })
        html += "</ul>"
        console.log(html);
        $('#modalCloseConfirmation .modal-body').html(html);
        $('#modalCloseConfirmation').modal("show")
    })

    $('.proceedWithClosing').on("click", function(){
        let taskIds = [];
        $(":checkbox.select_task:checked").each(function(i,j){
            taskIds.push($(this).closest("tr").data("id"));
        })
        closeTasks(taskIds);
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

    $('.remove-assignees-multiple').on("click", function(){
        $('#remove_all_assignees').prop("checked", false);
        $('#users-list-remove li').removeClass("assigned");
        $('#modalRemoveAssignees').modal("show");
    })

    $(document).on("click", "#users-list-remove li.select-user-remove", function(){
        $(this).toggleClass("assigned");
    })

    $('#remove_all_assignees').on("change", function(){
        if($(this).is(":checked")) {
            $('#users-list-remove li').removeClass("assigned");
        }
    })

    $('#modalRemoveAssignees').on("hidden.bs.modal", function(){
        $('#remove_all_assignees').prop("checked", false);
        $('#users-list-remove li').removeClass("assigned");
    })

    $('.proceedRemoveAssignees').on("click", function(){
        let removeAll = $('#remove_all_assignees').is(":checked");
        let userIds = [];
        if(!removeAll) {
            $('#users-list-remove li.select-user-remove.assigned').each(function(){
                userIds.push($(this).data("id"));
            })
            if(userIds.length === 0) {
                toastr.error("Select at least one user to remove, or check remove all.");
                return;
            }
        }
        let taskIds = [];
        $(":checkbox.select_task:checked").each(function(){
            taskIds.push($(this).closest("tr").data("id"));
        })
        bulkRemoveAssignees(taskIds, removeAll ? "1" : "0", userIds);
    })

    $('.work-type-multiple').on("click", function(){
        $('#bulk_work_type').val("");
        $('#modalBulkWorkType').modal("show");
    })

    $('.applyBulkWorkType').on("click", function(){
        let taskIds = [];
        $(":checkbox.select_task:checked").each(function(){
            taskIds.push($(this).closest("tr").data("id"));
        })
        bulkSetWorkType(taskIds, $('#bulk_work_type').val());
    })

    $('.billable-multiple').on("click", function(){
        $('#bulk_billable_mode').val("1");
        $('#modalBulkBillable').modal("show");
    })

    $('.applyBulkBillable').on("click", function(){
        let taskIds = [];
        $(":checkbox.select_task:checked").each(function(){
            taskIds.push($(this).closest("tr").data("id"));
        })
        bulkSetBillable(taskIds, $('#bulk_billable_mode').val());
    })

    $('.estimated-hours-multiple').on("click", function(){
        $('#bulk_est_mode_set').prop("checked", true);
        $('#bulk_est_hours_value').val("");
        $('#modalBulkEstimatedHours').modal("show");
    })

    $('.applyBulkEstimatedHours').on("click", function(){
        let taskIds = [];
        $(":checkbox.select_task:checked").each(function(){
            taskIds.push($(this).closest("tr").data("id"));
        })
        let mode = $('input[name="bulk_est_hours_mode"]:checked').val();
        let hours = $('#bulk_est_hours_value').val();
        bulkEstimatedHours(taskIds, mode, hours);
    })

    $('.section-multiple').on("click", function(){
        $('#bulk_section_value').val("");
        $('#modalBulkSection').modal("show");
    })

    $('.applyBulkSection').on("click", function(){
        let taskIds = [];
        $(":checkbox.select_task:checked").each(function(){
            taskIds.push($(this).closest("tr").data("id"));
        })
        bulkSetSection(taskIds, $('#bulk_section_value').val());
    })

    $('.clear-due-date-multiple').on("click", function(){
        $('#modalClearDueDateConfirmation .modal-body').empty();
        let html = "<p>The due date will be cleared for:</p><ul class='list-group'>"
        $(":checkbox.select_task:checked").each(function(){
            let taskNumber = $(this).closest("tr").find("td.task-number").html();
            let taskSection = $(this).closest("tr").find("td.task-section").html();
            let taskName = $(this).closest("tr").find("td.task-name span").length
                ? $(this).closest("tr").find("td.task-name span").html()
                : $(this).closest("tr").find("td.task-name").html();
            html += `<li class='list-group-item'>[${taskNumber}] <b>${taskSection}</b>: ${taskName}</li>`
        })
        html += "</ul>"
        $('#modalClearDueDateConfirmation .modal-body').html(html);
        $('#modalClearDueDateConfirmation').modal("show");
    })

    $('.proceedWithClearDueDate').on("click", function(){
        let taskIds = [];
        $(":checkbox.select_task:checked").each(function(){
            taskIds.push($(this).closest("tr").data("id"));
        })
        bulkClearDueDate(taskIds);
    })

    $('.reopen-multiple').on("click", function(){
        let html = "<ul class='list-group'>"
        $(":checkbox.select_task:checked").each(function(){
            let taskNumber = $(this).closest("tr").find("td.task-number").html();
            let taskSection = $(this).closest("tr").find("td.task-section").html();
            let taskName = $(this).closest("tr").find("td.task-name span").length
                ? $(this).closest("tr").find("td.task-name span").html()
                : $(this).closest("tr").find("td.task-name").html();
            html += `<li class='list-group-item'>[${taskNumber}] <b>${taskSection}</b>: ${taskName}</li>`
        })
        html += "</ul>"
        $('#modalReopenConfirmation .bulk-reopen-task-list').html(html);
        $('#modalReopenConfirmation').modal("show");
    })

    $('.proceedWithReopen').on("click", function(){
        let taskIds = [];
        $(":checkbox.select_task:checked").each(function(){
            taskIds.push($(this).closest("tr").data("id"));
        })
        reopenTasks(taskIds);
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
        let work_type = $('#work_type').val();
        let source = $('#source').val();
        let billable = $('#billable').val();
        let closed_filter = $('#closed_filter').val();

        if(customer_id!=='') {
            localStorage.setItem('LastSelectedCustomer',customer_id);
        }else{ 
            localStorage.removeItem('LastSelectedCustomer')
            localStorage.removeItem('LastSelectedProject')
            localStorage.removeItem('LastSelectedSprint')
        }

        if(project_id!=='') {
            localStorage.setItem('LastSelectedProject',project_id);
        }else{ 
            localStorage.removeItem('LastSelectedProject')
            localStorage.removeItem('LastSelectedSprint')
        }

        if(sprint_id!=='') {
            localStorage.setItem('LastSelectedSprint',sprint_id);
        }else{ 
            localStorage.removeItem('LastSelectedSprint')
        }
        
        Overlay("on");
        setTimeout(function(){
            window.location.href = base_url + 'tasks/listing?customer_id='+customer_id+"&project_id="+project_id+"&sprint_id="+sprint_id+"&stage="+stage+"&order_by="+order_by+"&order_dir="+order_dir+"&display="+display+"&assigned_to="+assigned_to+"&notes_only="+notes_only+"&search_text="+encodeURIComponent(search_text)+"&work_type="+work_type+"&source="+encodeURIComponent(source)+"&billable="+billable+"&closed_filter="+encodeURIComponent(closed_filter);
        },100)
    })

    $('.search').on("click", function(){
        Overlay("on");
        $('.monitor').trigger("change");
    })

    $('.choose-stages').on("click", function() {
        let stagesJSON = $('input[name=stage]').val();
        let stages = JSON.parse( (stagesJSON.length==0) ? "[]" : stagesJSON);
        // console.log(stages);
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

    $('.select-all').on("click", function(){
        if($(this).hasClass("all-selected")){
            $(this).removeClass("all-selected")
            $('ul#stages-list li.list-group-item').removeClass("selected")
        }else{
            $(this).addClass("all-selected")
            $('ul#stages-list li.list-group-item').addClass("selected")
        }
        
    })

    $('.applyChosenStages').on("click", function() {
        let customer_id = $('#customer_id').val();
        let project_id = $('#project_id').val();
        let sprint_id = $('#sprint_id').val();
        // let stage = $('#stage').val();
        let order_by = $('#order_by').val();
        let order_dir = $('#order_dir').val();
        let display = $('#display').val();
        let assigned_to = $('#assigned_to').val();
        let notes_only = $('#notes_only').val();
        let search_text = $('#search_text').val();
        let work_type = $('#work_type').val();
        let source = $('#source').val();
        let billable = $('#billable').val();
        let closed_filter = $('#closed_filter').val();

        let selectedStages = [];
        $('#stages-list li.selected').each(function(i,j){
            let stage = $(this).data("stage");
            selectedStages.push(stage)
        })
        // console.log(selectedStages)
        // $('input[name=stage]').val(JSON.stringify(selectedStages));
        $('#modalChooseStages').modal("hide");

        Overlay("on");
        setTimeout(function(){
            window.location.href = '/tasks/listing?customer_id='+customer_id+"&project_id="+project_id+"&sprint_id="+sprint_id+"&stage="+JSON.stringify(selectedStages)+"&order_by="+order_by+"&order_dir="+order_dir+"&display="+display+"&assigned_to="+assigned_to+"&notes_only="+notes_only+"&search_text="+encodeURIComponent(search_text)+"&work_type="+work_type+"&source="+encodeURIComponent(source)+"&billable="+billable+"&closed_filter="+encodeURIComponent(closed_filter);

            // $('.monitor').trigger("change")
        },100)
    })

    $('#modalChooseStages').on("hidden.bs.modal",function(){
        $('#stages-list li.selected').removeClass("selected");
    })

    // Initialize Bootstrap tooltips for completed date info icons
    $('[data-toggle="tooltip"]').tooltip();
    
    // Show alert on click for completed date info
    $('.completed-date-info').on('click', function(e) {
        e.preventDefault();
        var completedDate = $(this).data('completed-date');
        if(typeof alertify !== 'undefined') {
            alertify.alert('Task Completed', 'This task was completed on: ' + completedDate);
        } else {
            alert('This task was completed on: ' + completedDate);
        }
    });

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

function closeTasks(taskIds)
{
    Overlay("on")
    $.ajax({
        url: base_url + "tasks/closeMultiple",
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

function reopenTasks(taskIds)
{
    Overlay("on")
    $.ajax({
        url: base_url + "tasks/reopenMultiple",
        method: "POST",
        dataType: "JSON",
        data: {taskIds: taskIds},
        success: function(response)
        {
            if(response.result){
                window.location.reload();
            }else{
                Overlay("off")
                alertify.alert('Error', response.reason)
            }
        }
    })
}

function bulkClearDueDate(taskIds)
{
    Overlay("on")
    $.ajax({
        url: base_url + "tasks/bulkClearDueDate",
        method: "POST",
        dataType: "JSON",
        data: {taskIds: taskIds},
        success: function(response)
        {
            if(response.result){
                window.location.reload();
            }else{
                Overlay("off")
                alertify.alert('Error', response.reason)
            }
        }
    })
}

function bulkSetWorkType(taskIds, workType)
{
    Overlay("on")
    $.ajax({
        url: base_url + "tasks/bulkSetWorkType",
        method: "POST",
        dataType: "JSON",
        data: {taskIds: taskIds, work_type: workType},
        success: function(response)
        {
            if(response.result){
                window.location.reload();
            }else{
                Overlay("off")
                alertify.alert('Error', response.reason)
            }
        }
    })
}

function bulkSetBillable(taskIds, billableMode)
{
    Overlay("on")
    $.ajax({
        url: base_url + "tasks/bulkSetBillable",
        method: "POST",
        dataType: "JSON",
        data: {taskIds: taskIds, billable_mode: billableMode},
        success: function(response)
        {
            if(response.result){
                window.location.reload();
            }else{
                Overlay("off")
                alertify.alert('Error', response.reason)
            }
        }
    })
}

function bulkEstimatedHours(taskIds, mode, hours)
{
    Overlay("on")
    $.ajax({
        url: base_url + "tasks/bulkEstimatedHours",
        method: "POST",
        dataType: "JSON",
        data: {taskIds: taskIds, mode: mode, hours: hours},
        success: function(response)
        {
            if(response.result){
                window.location.reload();
            }else{
                Overlay("off")
                alertify.alert('Error', response.reason)
            }
        }
    })
}

function bulkRemoveAssignees(taskIds, removeAll, userIds)
{
    Overlay("on")
    $.ajax({
        url: base_url + "tasks/bulkRemoveAssignees",
        method: "POST",
        dataType: "JSON",
        data: {taskIds: taskIds, removeAll: removeAll, userIds: userIds},
        success: function(response)
        {
            if(response.result){
                window.location.reload();
            }else{
                Overlay("off")
                alertify.alert('Error', response.reason)
            }
        }
    })
}

function bulkSetSection(taskIds, section)
{
    Overlay("on")
    $.ajax({
        url: base_url + "tasks/bulkSetSection",
        method: "POST",
        dataType: "JSON",
        data: {taskIds: taskIds, section: section},
        success: function(response)
        {
            if(response.result){
                window.location.reload();
            }else{
                Overlay("off")
                alertify.alert('Error', response.reason)
            }
        }
    })
}