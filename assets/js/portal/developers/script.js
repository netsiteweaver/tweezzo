// Show loader on page load
$(window).on('load', function() {
	Overlay('off');
});

// Track active AJAX requests that should show loader
var activeLoaderRequests = 0;

// Session ping functionality for portal users
var pingActive;

function isLoggedIn()
{
	$.ajax({
		url: base_url + "ajax/ping",
		method: "get",
        data:{type:"developer"},
		dataType:"json",
		success: function(response)
		{
			console.log(response)
			if( (response.result==false) && (response.reason == 'login') )
			{
				clearInterval(pingActive);
				// Redirect to portal login
				window.location.href = base_url + "portal/developers/signin";
			}
		}
	})
}

function portalDeveloperAjaxShowsLoader(settings) {
	if (!settings || settings.showLoader === false) {
		return false;
	}
	var u = String(settings.url || '');
	return u.indexOf('ajax/ping') === -1
		&& u.indexOf('isLoggedIn') === -1
		&& u.indexOf('developers/taskPoll') === -1
		&& u.indexOf('taskPoll') === -1;
}

// Show loader during AJAX requests (except background / silent polls)
$(document).ajaxSend(function(event, jqxhr, settings) {
	if (portalDeveloperAjaxShowsLoader(settings)) {
		activeLoaderRequests++;
		if (activeLoaderRequests > 0) {
			Overlay('on');
		}
	}
});

$(document).ajaxComplete(function(event, jqxhr, settings) {
	if (portalDeveloperAjaxShowsLoader(settings)) {
		activeLoaderRequests--;
		if (activeLoaderRequests <= 0) {
			activeLoaderRequests = 0;
			Overlay('off');
		}
	}
});

var developerTaskPollSuppressUntil = 0;
window.developerTaskPollSuppress = function(ms) {
	var d = typeof ms === 'number' ? ms : 50000;
	developerTaskPollSuppressUntil = Date.now() + d;
};

jQuery(function(){

    // Show loader initially
	Overlay('on');

	// Start session ping for developers
	pingActive = setInterval(function(){
		isLoggedIn();
	},1000)

    // init('developers');

    $('.resetFilter').on('click', function(){
        window.location.href = base_url + "portal/developers/notes";
    })

    $('.download').on('click', function(){
        downloadTableAsCSV('notes','notes',{ includeColumns: [1,2,3,4] });
    })

    $('.summernote').summernote({
		callbacks: {
			// callback for pasting text only (no formatting)
			onPaste: function (e) {
			  var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
			  console.log(bufferText)
			  e.preventDefault();
			  bufferText = bufferText.replace(/\r?\n/g, '<br>');
			  document.execCommand('insertHtml', false, bufferText);
			}
		},
		height: 150,
		tabsize: 4,
		placeholder: 'Enter text here ...',
		toolbar: [
		  // [groupName, [list of button]]
		  ['style', ['bold', 'italic', 'underline', 'clear']],
		  ['font', ['strikethrough']],
		//   ['fontsize', ['fontsize']],
		  ['color', ['color']],
		  ['para', ['ul', 'ol', 'paragraph']],
		//   ['height', ['height']],
		  ['view', ['fullscreen', 'codeview']],
		],
        onInit: function() {
            // Remove any bold styling from the default paragraph
            $('#editor').summernote('formatBlock', 'p');
        }
	});

    $('.select-customer').on('click', function() {
        // reset();
        let customer_id = $(this).data("customer-id");
        let customer_name = $(this).data("customer-name");
        $('#addTaskModal .select-customer').removeClass("selected");
        $('#addTaskModal input[name=customer_id]').val(customer_id);
        $('.list-group.projects').closest('.form-group').removeClass('d-none');
        $('.list-group.projects .list-group-item').each(function(){
            if($(this).data('customer-id') == customer_id){
                $(this).removeClass('d-none');
                $('#selection').closest('.row').removeClass('d-none');
                $('.list-group.customers').closest('.form-group').addClass('d-none');
                $('#selection #selected-customer').text(customer_name)
            }else{
                $(this).addClass('d-none');
            }
        })
    })

    $('.select-project').on('click', function() {
        let project_id = $(this).data("project-id");
        let project_name = $(this).data("project-name");
        $('#addTaskModal .select-project').removeClass("selected");
        $('#addTaskModal input[name=project_id]').val(project_id);
        $('.list-group.sprints').closest('.form-group').removeClass('d-none');
        $('.list-group.sprints .list-group-item').each(function(){
            if($(this).data('project-id') == project_id){
                $(this).removeClass('d-none')
                $('#selection #selected-project').text(project_name)
                $('.list-group.projects').closest('.form-group').addClass('d-none');
            }else{
                $(this).addClass('d-none');
            }
        })
    })

    $('.select-sprint').on('click', function() {
        let sprint_id = $(this).data("sprint-id");
        let sprint_name = $(this).data("sprint-name");
        $('#addTaskModal .select-sprint').removeClass("selected");
        $('#addTaskModal input[name=sprint_id]').val(sprint_id);
        $('.data-input').removeClass('d-none');
        // $('#addTaskModal .modal-dialog').removeClass('modal-lg').addClass('modal-xl');
        // $('.data-input-right').removeClass('d-none');
        $('#selection #selected-sprint').text(sprint_name)
        $('.list-group.sprints').closest('.form-group').addClass('d-none');

        $('#addTaskModal input[name=task_number]').trigger("focus");
    })

    $('#resetModalAddTask').on('click', function() {
        resetModalAddTask();
    })

    function resetModalAddTask()
    {
        $('#addTaskModal input[name=customer_id]').val('');
        $('#addTaskModal input[name=project_id]').val('');
        $('#addTaskModal input[name=sprint_id]').val('');

        $('.list-group.customers').closest('.form-group').removeClass('d-none');
        $('.list-group.projects').closest('.form-group').addClass('d-none');
        $('.list-group.sprints').closest('.form-group').addClass('d-none');

        $('.list-group.projects .list-group-item').addClass('d-none');
        $('.list-group.sprints .list-group-item').addClass('d-none');

        $('.data-input').addClass('d-none');
        // $('.data-input-left').addClass('d-none');
        // selection bar with reset
        $('#selection').closest('.row').addClass('d-none');

        $('#selection #selected-customer').text('')
        $('#selection #selected-project').text('')
        $('#selection #selected-sprint').text('')

        $("#addTaskModal .data-input input, #addTaskModal .data-input textarea").val("")

        // $('#addTaskModal .modal-dialog').removeClass('modal-xl').addClass('modal-lg');
    }

    $('#addTaskModal .monitorx').on("keyup",function(){
        let valid = true;
        
        $('#addTaskModal .monitorx').each(function(){
            let elem = $(this).val();
            if(elem == "") valid = false;
        })
        if(valid) {
            $('#addTaskModal .submit-task').removeClass("d-none");
        }else{
            $('#addTaskModal .submit-task').addClass("d-none");
        }
    })

    $('.submit-task').on('click',function(){
        let btn = $(this);
        if($(this).hasClass("running")) return;

        // $(this).addClass("running");
        let sprint_id = $('#addTaskModal input[name=sprint_id]').val();
        let task_number = $('input[name=task_number]').val();
        let name = $('input[name=name]').val();
        let section = $('input[name=section]').val();
        let description = $('textarea[name=description]').val();
        let scope_when_done = $('textarea[name=scope_when_done]').val();   
        let scope_not_included = $('textarea[name=scope_not_included]').val();
        let scope_client_expectation = $('textarea[name=scope_client_expectation]').val();

        Overlay("on");
        $.ajax({
            url: base_url + "portal/developers/submitTask",
            method: "POST",
            dataType: "JSON",
            data: {sprint_id:sprint_id,task_number:task_number,name:name,section:section,description:description,scope_when_done:scope_when_done,scope_not_included:scope_not_included,scope_client_expectation:scope_client_expectation},
            success: function(response)
            {
                if(response.result){
                    alertify.success("Task submitted");
                    $('#addTaskModal .submission').addClass("d-none");
                    $('#addTaskModal .thankyou').removeClass("d-none");
                    $('button.submit-task').addClass("d-none").removeClass("running");
                    $('button.submit-another-task').removeClass("d-none");
                    resetModalAddTask();
                }else{
                    alertify.error(response.reason);
                    $('.submit-task').removeClass("d-none");   
                }
                $(btn).removeClass("running")
            },
            complete: function(response) {
                Overlay("off");
                $(btn).removeClass("running")
            }
        })
    })

    $('.submit-another-task').on('click',function(){
        $(this).addClass("d-none");
        $('button.submit-task').removeClass("d-none");
        $('#addTaskModal .thankyou').addClass("d-none");
        $('#addTaskModal .submission.selection').removeClass("d-none");
    })

    $("#addTaskModal").on('hidden.bs.modal',function(){
        resetModalAddTask();
    })

    $('.add-task').on('click', function(){
        $('#addTaskModal').modal("show")
    })

    $('.create-task').on('click', function(){
        let customer_id = $('input[name=customer_id]').val();
        let project_id = $('input[name=project_id]').val();
        let sprint_id = $('input[name=sprint_id]').val();
        
        let section = $('input[name=section]').val();
        let task_number = $('input[name=task_number]').val();
        let name = $('input[name=name]').val();
        let description = $('textarea[name=description]').val();
        let due_date = $('input[name=due_date]').val();
        console.log(customer_id, project_id, sprint_id,section,task_number,name,description,due_date)

        alertify.alert("Not yet imeplemented")
    })

    $('.monitor').on('change',function(){
        let startDate = $('input[name=start_date]').val();
        let endDate = $('input[name=end_date]').val();
        let projectId = $('input[name=project_id]').val();
        let sprintId = $('input[name=sprint_id]').val();
        let customerId = $('input[name=customer_id]').val();
        let qs = "?start_date=" + startDate + "&end_date=" + endDate + "&project_id=" + projectId + "&sprint_id=" + sprintId + "&customer_id=" + customerId;
        window.location.href = base_url + "portal/developers/notes" + qs;
    })

    $('#notes .filter-project').on('click', function() {
        let projectId = $(this).data('project-id');
        let sprintId = $('input[name=sprint_id]').val();
        let customerId = $('input[name=customer_id]').val();
        let startDate = $('input[name=start_date]').val();
        let endDate = $('input[name=end_date]').val();
        let qs = "?start_date=" + startDate + "&end_date=" + endDate + "&project_id=" + projectId + "&sprint_id=" + sprintId + "&customer_id=" + customerId;
        window.location.href = base_url + "portal/developers/notes" + qs;
    })

    $('#notes .filter-sprint').on('click', function() {
        let sprintId = $(this).data('sprint-id');
        let projectId = $('input[name=project_id]').val();
        let customerId = $('input[name=customer_id]').val();
        let startDate = $('input[name=start_date]').val();
        let endDate = $('input[name=end_date]').val();
        let qs = "?start_date=" + startDate + "&end_date=" + endDate + "&project_id=" + projectId + "&sprint_id=" + sprintId + "&customer_id=" + customerId;
        window.location.href = base_url + "portal/developers/notes" + qs;
    })

    $('#notes .filter-customer').on('click', function() {
        let customerId = $(this).data('customer-id');
        let projectId = $('input[name=project_id]').val();
        let sprintId = $('input[name=sprint_id]').val();
        let startDate = $('input[name=start_date]').val();
        let endDate = $('input[name=end_date]').val();
        let qs = "?start_date=" + startDate + "&end_date=" + endDate + "&project_id=" + projectId + "&sprint_id=" + sprintId + "&customer_id=" + customerId;
        window.location.href = base_url + "portal/developers/notes" + qs;
    })

    $('#mySprints td.select-sprint').on("click", function() {
        let sprintId = $(this).closest("tr").data("sprint-id");
        let projectId = $(this).closest("tr").data("project-id");
        let customerId = $(this).closest("tr").data("customer-id");
        console.log(sprintId,projectId,customerId);
        window.location.href = base_url + "portal/developers/tasks?sprint_id="+sprintId+"&project_id="+projectId+"&customer_id="+customerId;
    });

    $('.view-notes').on("click", function() {
        let taskId = $(this).closest("tr").data("id");
        let taskNumber = $(this).closest("tr").find("td.task-number").html();
        let taskSection = $(this).closest("tr").find("td.task-section").html();
        let taskName = $(this).closest("tr").find("td.task-name").text();

        Overlay("on");
        $.ajax({
            url: base_url + "portal/developers/loadNotes",
            method: "POST",
            dataType: "JSON",
            data: {task_id:taskId},
            success: function(response)
            {
                if(response.result){
                    if(response.notes.length == 0){
                        alertify.set('notifier','position', 'top-right');
                        alertify.error("No notes found for this task.")
                    }else{
                        $('#modalNotes .modal-body tbody').empty();
                        $('#modalNotes .modal-title').html(`<b>Notes for</b>: ${taskNumber} / ${taskSection} / ${taskName}`);
                        $(response.notes).each(function(i,j){
                            let html = `<tr><td style='font-size:10px; color:#ccc; '>${i+1}</td><td>${nl2br(j.notes)}<br><div style='text-align:right; font-size:12px; color:#999;'>by `
                            if(j.customer !== null){
                                html += j.customer;
                            }else if(j.name !== null){
                                html += j.name;
                            }
                            html += ` on ${j.created_on.substring(0,16)}</div></td></tr>`;
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

    $('.changeStage').on('click', function() {
        let stage = $(this).data("stage");
        let taskId = $('input[name=task_id]').val();
        let stageLabel = $(this).text();

        alertify.confirm('Stage Change', `Are you sure you want to move this task to ${stageLabel}?`
            , function(){ 
                Overlay("on");
                $.ajax({
                    url: 'portal/developers/moveStage',
                    data: {task_id:taskId, stage:stage},
                    method:"POST",
                    dataType:"JSON",
                    complete: function(response) {
                        Overlay("off");
                        window.location.reload(true);
                    }
                })
            }
            , function(){ 
                alertify.error('Cancel')
            }
        );

    })

    $('.autosubmit').on("change", function(){
        let customer_id = $('#customer_id').val();
        let project_id = $('#project_id').val();
        let sprint_id = $('#sprint_id').val();

        console.log(customer_id, localStorage.getItem('DevelopersPortalLastSelectedCustomer'));
        if(customer_id!==localStorage.getItem('DevelopersPortalLastSelectedCustomer')){
            project_id = "";
            $('#project_id').val('');

            sprint_id = "";
            $('#sprint_id').val('')
        }
        
        if(customer_id=="") {
            project_id = "";
            $('#project_id').val('');

            sprint_id = "";
            $('#sprint_id').val('')
        }

        if(project_id=="") {
            sprint_id = "";
            $('#sprint_id').val('')
        }

        if(customer_id!=='') {
            localStorage.setItem('DevelopersPortalLastSelectedCustomer',customer_id);
        }else{ 
            localStorage.removeItem('DevelopersPortalLastSelectedCustomer')
            localStorage.removeItem('DevelopersPortalLastSelectedProject')
            localStorage.removeItem('DevelopersPortalLastSelectedSprint')
        }

        if(project_id!=='') {
            localStorage.setItem('DevelopersPortalLastSelectedProject',project_id);
        }else{ 
            localStorage.removeItem('DevelopersPortalLastSelectedProject')
            localStorage.removeItem('DevelopersPortalLastSelectedSprint')
        }

        if(sprint_id!=='') {
            localStorage.setItem('DevelopersPortalLastSelectedSprint',sprint_id);
        }else{ 
            localStorage.removeItem('DevelopersPortalLastSelectedSprint')
        }

        // console.log(customer_id,project_id,sprint_id)
        $('form#tasks').trigger("submit");
    })

    $('#reset').on("click", function(){
        resetForm();
    })

    $('.deleteNote').on("click", function(){
        let note_id = $(this).data("note-id");
        let row = $(this).closest("tr");
        alertify.confirm('Delete Confirmation','Are you sure you want to delete your note?'
            , function(){
                Overlay("on");
                $.ajax({
                    url: "portal/developers/deleteNote",
                    data:{note_id:note_id},
                    method:"POST",
                    dataType:"JSON",
                    success: function(response) {
                        if(response.affected_rows==1){
                            $(row).remove();
                            alertify.success("Note deleted")
                        }
                    },
                    complete: function(ev) {
                        Overlay("off");
                    }
                })
            }
            ,function() {
                alertify.error('Cancelled')
            }
        )
       
    })

    $('.timer_stop').on("click",function(){
        if($(this).hasClass("running")) return false;
        let item = $(this);
        let actionType = $(this).hasClass("task-view") ? 'view' : 'listing';
        let task_id = null;
        // $(this).addClass("running");
        //remove previously selected row
        if(actionType == 'listing'){
            $('#task_list tr.selected').removeClass("selected");
            //mark current as selected
            $(this).closest("tr").addClass("selected");

            task_id = $(this).closest("tr").data("id");
        }else{
            task_id = $(this).data("task-id");
        }

        $.ajax({
            url: base_url + "portal/developers/timer_stop",
            data: {task_id:task_id},
            method:"POST",
            dataType:"JSON",
            success:function(response){
                if(!response.result){
                    alertify.alert("Error",response.reason);
                    $('#running-task').addClass("d-none");
                    $('#running-task a').prop("href",`portal/developers/view?task_uuid=`)
                }else{
                    // $("#task_list tr.selected").find('.bi-stop-circle-fill').addClass("d-none");
                    // $("#task_list tr.selected").find('.bi-play-circle-fill').removeClass("d-none");
                    $('#running-task').addClass("d-none");
                    $('#running-task a').prop("href",`portal/developers/view?task_uuid=`)
                }
                if(actionType == 'listing'){
                    $("#task_list tr.selected").find('.bi-stop-circle-fill').addClass("d-none");
                    $("#task_list tr.selected").find('.bi-play-circle-fill').removeClass("d-none");
                    $('#task_list tr.selected').removeClass("selected");
                }else{
                    $(item).closest(".card-footer").remove();
                }


                
            }
        })
        
    })

    $('.timer_start').on("click",function(){
        if($(this).hasClass("running")) return false;

        // $(this).addClass("running");
        //remove previously selected row
        $('#task_list tr.selected').removeClass("selected");
        //mark current as selected
        $(this).closest("tr").addClass("selected");

        let task_id = $(this).closest("tr").data("id");
        let task_name = $(this).closest("tr").find(".task-name").text();

        $.ajax({
            url: base_url + "portal/developers/timer_start",
            data: {task_id:task_id},
            method:"POST",
            dataType:"JSON",
            success:function(response){
                if(!response.result){
                    alertify.alert("Error",response.reason);
                }else{
                    $('#modalTaskTimer textarea[name=notes]').val('');
                    $('#modalTaskTimer .modal-body .task-name').text(task_name)
                    $('#modalTaskTimer').modal("show")
                }
            }
        })
    })

    $('.insert-timer').on("click", function(){
        let task_id = $('#task_list tr.selected').closest("tr").data("id");
        let notes = $('#modalTaskTimer textarea[name=notes]').val();
        console.log(task_id,notes)

        $.ajax({
            url: base_url + "portal/developers/timer_insert",
            data: {task_id:task_id, notes:notes},
            method:"POST",
            dataType:"JSON",
            success:function(response){
                console.log(response)
                if(!response.result){
                    alertify.alert("Error",response.reason);
                }else{
                    $('#modalTaskTimer').modal("hide");
                    $("#task_list tr.selected").find('.bi-stop-circle-fill').removeClass("d-none");
                    $("#task_list tr.selected").find('.bi-play-circle-fill').addClass("d-none");

                    $('#running-task').removeClass("d-none");
                    $('#running-task a').prop("href",`portal/developers/view?task_uuid=${response.task_uuid}`)
                }
            }
        })
    })

    $('#modalTaskTimer').on("hidden.bs.modal", function(){
        $('#task_list tr.selected').removeClass("selected");
        $('#modalTaskTimer .modal-body .task-name').text('');
        $('#modalTaskTimer textarea[name=notes]').val('');
    })

    $('.choose-stages').on("click", function() {
        let stagesJSON = $('input[name=stage]').val();
        let stages = JSON.parse( (stagesJSON.length==0) ? "[]" : stagesJSON);
        console.log(stagesJSON, stages);
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

        let selectedStages = [];
        $('#stages-list li.selected').each(function(i,j){
            let stage = $(this).data("stage");
            selectedStages.push(stage)
        })
        // console.log(selectedStages)
        // $('input[name=stage]').val(JSON.stringify(selectedStages));
        $('#modalChooseStages').modal("hide");
        $('input[name=stage]').val(JSON.stringify(selectedStages))
        Overlay("on");
        setTimeout(function(){
            // let url = '/portal/developers/tasks/listing?customer_id='+customer_id+"&project_id="+project_id+"&sprint_id="+sprint_id+"&stage="+JSON.stringify(selectedStages)+"&order_by="+order_by+"&order_dir="+order_dir+"&display="+display+"&assigned_to="+assigned_to+"&notes_only="+notes_only+"&search_text="+search_text;
            $('.autosubmit').trigger("change")
        },100)
    })

    $('#modalChooseStages').on("hidden.bs.modal",function(){
        $('#stages-list li.selected').removeClass("selected");
    })

    // Inline stage change from the tasks listing: click the stage badge, pick a stage.
    var stageMoveTaskId = null;
    var stageMoveBusy = false;
    var stageNotePending = null;

    function stageMoveLabel(stage) {
        return String(stage || '').replace(/_/g, ' ').toUpperCase();
    }

    function closeStageMoveMenu() {
        $('#stageMoveMenu').addClass('d-none');
        stageMoveTaskId = null;
    }

    function refreshStageTotals() {
        let $totals = $('#task_list_totals');
        if (!$totals.length) return;

        let order = ['new','in_progress','testing','staging','validated','completed','on_hold'];
        let counts = {};
        order.forEach(function(stage){ counts[stage] = 0; });

        let total = 0;
        $('#task_list tbody .stage-move-trigger').each(function(){
            let stage = String($(this).data('stage'));
            total++;
            if (counts.hasOwnProperty(stage)) counts[stage]++;
        });

        let parts = order.map(function(stage){ return stageMoveLabel(stage) + ": " + counts[stage]; });
        $totals.text("TOTAL: " + total + " | " + parts.join(" | "));
    }

    function notifyStageMoved(taskId, stage, previousStage) {
        let $message = $('<span>').text("Moved to " + stageMoveLabel(stage) + ".");

        // Developers cannot set "validated", so a task moved out of it cannot be put back.
        if (previousStage !== 'validated') {
            let $undo = $('<button type="button" class="stage-move-undo">Undo</button>');
            $undo.on('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                moveTaskStage(taskId, previousStage, stage, false);
            });
            $message.append($undo);
        }

        alertify.success($message.get(0), 6);
    }

    function moveTaskStage(taskId, stage, previousStage, allowUndo, note, notePublic) {
        if (!taskId || !stage || stageMoveBusy) return;
        stageMoveBusy = true;

        let payload = {task_id: taskId, stage: stage};
        if (note) {
            payload.note = note;
            if (notePublic) payload.display_type = 'public';
        }

        $.ajax({
            url: 'portal/developers/moveStage',
            data: payload,
            method: "POST",
            dataType: "JSON",
            showLoader: false,
            success: function(response){
                if (response && response.result === false) {
                    alertify.error(response.reason || "Could not change the stage.");
                    return;
                }

                $('#task_list .stage-move-trigger[data-task-id="' + taskId + '"]')
                    .removeClass('stage-button-' + previousStage)
                    .addClass('stage-button-' + stage)
                    .attr('data-stage', stage)
                    .data('stage', stage)
                    .find('.stage-move-label').text(stageMoveLabel(stage));

                refreshStageTotals();

                if (response && response.note_saved) {
                    let $count = $('#task_list tr[data-id="' + taskId + '"] .notes-count');
                    $count.text((parseInt($count.text(), 10) || 0) + 1);
                }

                if (allowUndo) {
                    notifyStageMoved(taskId, stage, previousStage);
                } else {
                    alertify.success("Stage restored to " + stageMoveLabel(stage));
                }
            },
            error: function(){
                alertify.error("Could not change the stage. Please try again.");
            },
            complete: function(){
                stageMoveBusy = false;
            }
        })
    }

    $(document).on('click', '.stage-move-trigger', function(e){
        e.stopPropagation();

        let $menu = $('#stageMoveMenu');
        if (!$menu.length) return;

        let taskId = $(this).data('task-id');
        if ((stageMoveTaskId === taskId) && !$menu.hasClass('d-none')) {
            closeStageMoveMenu();
            return;
        }
        stageMoveTaskId = taskId;

        let current = String($(this).data('stage'));
        $menu.find('.stage-move-option').each(function(){
            $(this).toggleClass('current', String($(this).data('stage')) === current);
        });

        // Fixed positioning so the menu is not clipped by the scrolling table container.
        let rect = this.getBoundingClientRect();
        $menu.removeClass('d-none');
        let height = $menu.outerHeight();
        let width = $menu.outerWidth();
        let top = ((rect.bottom + height + 8) > window.innerHeight) ? (rect.top - height - 4) : (rect.bottom + 4);
        let left = Math.min(rect.left, window.innerWidth - width - 8);
        $menu.css({top: Math.max(8, top) + "px", left: Math.max(8, left) + "px"});
    })

    $(document).on('click', '#stageMoveMenu .stage-move-option', function(e){
        e.stopPropagation();
        if ($(this).hasClass('current') || $(this).is('[disabled]')) {
            closeStageMoveMenu();
            return;
        }

        let taskId = stageMoveTaskId;
        let stage = String($(this).data('stage'));
        let previousStage = String($('#task_list .stage-move-trigger[data-task-id="' + taskId + '"]').data('stage'));
        let taskName = $('#task_list tr[data-id="' + taskId + '"] .task-name').text().trim();
        closeStageMoveMenu();

        stageNotePending = {task_id: taskId, stage: stage, previous_stage: previousStage};
        $('#modalStageNoteTitle').text("Move to " + stageMoveLabel(stage));
        $('#modalStageNote .stage-note-summary').text(
            taskName + " — " + stageMoveLabel(previousStage) + " → " + stageMoveLabel(stage)
        );
        $('#modalStageNote .confirmStageMove').text("Move to " + stageMoveLabel(stage));
        $('#modalStageNote').modal("show");
    })

    $('#modalStageNote').on("shown.bs.modal", function(){
        $('#stage_note').trigger("focus");
    })

    $('#modalStageNote').on("hidden.bs.modal", function(){
        stageNotePending = null;
        $('#stage_note').val('');
        $('#stage_note_display_type').prop("checked", true);
    })

    $(document).on('click', '#modalStageNote .confirmStageMove', function(){
        if (!stageNotePending) return;

        let pending = stageNotePending;
        let note = $('#stage_note').val().trim();
        let notePublic = $('#stage_note_display_type').is(":checked");

        $('#modalStageNote').modal("hide");
        moveTaskStage(pending.task_id, pending.stage, pending.previous_stage, true, note, notePublic);
    })

    $(document).on('click', closeStageMoveMenu);
    $(document).on('keydown', function(e){
        if (e.key === 'Escape') closeStageMoveMenu();
    })
    $(window).on('resize scroll', closeStageMoveMenu);
    $('.table-responsive').on('scroll', closeStageMoveMenu);

    $(document).ready(function () {
        const $modal = $('#myModal');
    
        $modal.on('hide.bs.modal', function (e) {
          // Prevent Bootstrap from immediately hiding the modal
          e.preventDefault();
    
          const $dialog = $modal.find('.modal-dialog');
          $dialog.css({
            transform: 'scale(1.7)',
            opacity: '0'
          });
    
          // Wait for the animation to finish before closing
          setTimeout(function () {
            // Actually hide the modal
            $modal.modal('hide');
            // Reset dialog transform for next open
            $dialog.css({
              transform: '',
              opacity: ''
            });
          }, 300); // Match CSS transition duration
        });
    
        $modal.on('show.bs.modal', function () {
          const $dialog = $modal.find('.modal-dialog');
          $dialog.css({
            transform: 'scale(1)',
            opacity: '1'
          });
        });
      });

    $(document).on('click', '.delete-task-attachment', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var btn = $(this);
        var id = btn.data('task-image-id');
        var col = btn.closest('div.col-md-4');
        alertify.confirm('Delete attachment', 'Remove this file from the task?',
            function() {
                Overlay('on');
                $.ajax({
                    url: base_url + 'portal/developers/deleteTaskImage',
                    method: 'POST',
                    data: { task_image_id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.result) {
                            col.remove();
                            var $att = $('#attachments');
                            if ($att.length && $att.find('.col-md-4').length === 0) {
                                $att.closest('.card').remove();
                            }
                            alertify.success('Attachment removed');
                            if (typeof window.developerTaskPollSuppress === 'function') {
                                window.developerTaskPollSuppress(50000);
                            }
                            var $pr = $('#developer-task-view-root');
                            if ($pr.length) {
                                $.ajax({
                                    url: base_url + 'portal/developers/taskPoll',
                                    type: 'GET',
                                    data: { task_uuid: $pr.data('taskUuid') },
                                    dataType: 'json',
                                    showLoader: false,
                                    success: function(pr) {
                                        if (pr && pr.result && pr.snapshot) {
                                            $pr.attr('data-task-poll-snapshot', JSON.stringify(pr.snapshot));
                                        }
                                    }
                                });
                            }
                        } else {
                            alertify.error(response.reason || 'Could not delete attachment');
                        }
                    },
                    complete: function() {
                        Overlay('off');
                    }
                });
            },
            function() {}
        );
    });

    ;(function initDeveloperTaskViewPoll() {
        var $root = $('#developer-task-view-root');
        if (!$root.length) {
            return;
        }
        var uuid = $root.data('taskUuid');
        if (!uuid) {
            return;
        }
        function canonicalSnapshot(s) {
            try {
                if (s === null || s === undefined || s === '') {
                    return '';
                }
                var o = typeof s === 'string' ? JSON.parse(s) : s;
                return JSON.stringify(o);
            } catch (e) {
                return String(s);
            }
        }
        var snapAttr = $root.attr('data-task-poll-snapshot') || '';
        var lastCanon = canonicalSnapshot(snapAttr);
        var needsBaseline = !String(snapAttr).trim();
        var pollMs = 5000;
        setInterval(function() {
            if (Date.now() < developerTaskPollSuppressUntil) {
                return;
            }
            $.ajax({
                url: base_url + 'portal/developers/taskPoll',
                type: 'GET',
                data: { task_uuid: uuid },
                dataType: 'json',
                showLoader: false,
                success: function(res) {
                    if (!res || !res.result || !res.snapshot) {
                        return;
                    }
                    var nextCanon = canonicalSnapshot(JSON.stringify(res.snapshot));
                    if (needsBaseline) {
                        needsBaseline = false;
                        lastCanon = nextCanon;
                        $root.attr('data-task-poll-snapshot', JSON.stringify(res.snapshot));
                        return;
                    }
                    if (nextCanon === lastCanon) {
                        return;
                    }
                    if (window.__developerTaskPollReloading) {
                        return;
                    }
                    window.__developerTaskPollReloading = true;
                    var nextSnap = JSON.stringify(res.snapshot);
                    var onDecline = function() {
                        lastCanon = nextCanon;
                        $root.attr('data-task-poll-snapshot', nextSnap);
                        window.__developerTaskPollReloading = false;
                    };
                    var showPrompt = function() {
                        try {
                            if (typeof alertify !== 'undefined') {
                                alertify.confirm(
                                    'Task updated',
                                    'This task has been changed (for example stage, notes, or attachments). Reload the page to see the latest? If you are writing a note or have other unsaved work on this page, it will be lost if you reload.',
                                    function() {
                                        window.location.reload();
                                    },
                                    onDecline
                                );
                            } else if (window.confirm('This task has been updated. Reload to see the latest? Unsaved changes will be lost.')) {
                                window.location.reload();
                            } else {
                                onDecline();
                            }
                        } catch (e) {
                            window.__developerTaskPollReloading = false;
                        }
                    };
                    setTimeout(showPrompt, 0);
                }
            });
        }, pollMs);
    })();

})

function resetForm()
{
    $('#customer_id').val("");
    $('#project_id').val("");
    $('#sprint_id').val("");
    $('#stage').val("");
    $('#order_by').val("");
    $('#order_dir').val("");
    $('#display').val("");

    $('form#tasks').trigger("submit");
}

function nl2br (str, is_xhtml) {
    if (typeof str === 'undefined' || str === null) {
        return '';
    }
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

function Overlay(option)
{
	if(typeof option == 'undefined') option = 'on';
	option = option.toLowerCase();
	let options = ['on','off'];
	if(!options.includes(option)) option = 'on';

	if(option == 'on') {
		$('#overlay').removeClass('d-none');
	}else{
		$('#overlay').addClass('d-none');
	}
}
