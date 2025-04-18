jQuery(function(){

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