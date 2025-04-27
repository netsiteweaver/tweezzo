jQuery(function(){

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
        reset();
        let customer_id = $(this).data("customer-id");
        $('#addTaskModal .select-customer').removeClass("selected");
        $(this).addClass("selected")
        $('#addTaskModal input[name=customer_id]').val(customer_id);
        $('.list-group.projects').closest('.form-group').removeClass('d-none');
        $('.list-group.projects .list-group-item').each(function(){
            if($(this).data('customer-id') == customer_id){
                $(this).removeClass('d-none')
            }else{
                $(this).addClass('d-none');
            }
        })
    })

    $('.select-project').on('click', function() {
        let project_id = $(this).data("project-id");console.log(project_id)
        $('#addTaskModal .select-project').removeClass("selected");
        $(this).addClass("selected")
        $('#addTaskModal input[name=project_id]').val(project_id);
        $('.list-group.sprints').closest('.form-group').removeClass('d-none');
        $('.list-group.sprints .list-group-item').each(function(){
            if($(this).data('project-id') == project_id){
                $(this).removeClass('d-none')
            }else{
                $(this).addClass('d-none');
            }
        })
    })

    $('.select-sprint').on('click', function() {
        let sprint_id = $(this).data("sprint-id");console.log(sprint_id)
        $('#addTaskModal .select-sprint').removeClass("selected");
        $(this).addClass("selected")
        $('#addTaskModal input[name=sprint_id]').val(sprint_id);

        $('.data-input').removeClass('d-none');

    })

    function reset()
    {
        $('.list-group.projects .list-group-item').removeClass('d-none');
        $('.list-group.projects').closest('.form-group').addClass('d-none');
        $('.list-group.sprints .list-group-item').removeClass('d-none');
        $('.list-group.sprints').closest('.form-group').addClass('d-none');
        $('#addTaskModal input[name=customer_id]').val('');
        $('#addTaskModal input[name=project_id]').val('');
        $('#addTaskModal input[name=sprint_id]').val('');
    }

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

        alertify.confirm('Stage Change', `Are you sure you want to move this task's stage ?`
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