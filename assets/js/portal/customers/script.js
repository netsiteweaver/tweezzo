jQuery(function(){

    $('.autosubmit').on("change", function(){
        $('.apply').trigger("click");
    })

    $('.view-notes').on("click", function() {
        let taskId = $(this).closest("tr").data("id");
        let taskNumber = $(this).closest("tr").find("td.task-number").html();
        let taskSection = $(this).closest("tr").find("td.task-section").html();
        let taskName = $(this).closest("tr").find("td.task-name").text();

        Overlay("on");
        $.ajax({
            url: base_url + "portal/customers/loadNotes",
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

    $("#saveNote").on("click", function() {
        let taskUuid = $('input[name=uuid]').val();
        let taskId = $('input[name=id]').val();
        let sprintId = $('input[name=sprint_id]').val();
        let notes = $('textarea[name=notes]').val();
        // alert(taskId);
        Overlay("on");
        $.ajax({
            url: "portal/customers/saveNote",
            data: {task_id:taskId, note:notes},
            method: "POST",
            dataType: "JSON",
            success: function(response) {
                if(response.result) {
                    // window.location.href = "portal/customers/tasks?sprint_id="+sprintId;
                    window.location.href = "portal/customers/view?uuid="+taskUuid;
                }else{
                    alert(response.reason)
                }
                Overlay("off");
            }
        })
    })

    $('.apply').on("click", function(){
        let sortBy = $('select[name=sort_by]').val();
        let sortDir = $('select[name=sort_dir]').val();
        let sprintId = $('input[name=sprint_id]').val();
        let stages = $('select[name=stages]').val();
        let notes_only = $('select[name=notes_only]').val();
        Overlay("on");
        window.location.href = `portal/customers/tasks?sprint_id=${sprintId}&sort_by=${sortBy}&sort_dir=${sortDir}&stages=${stages}&notes_only=${notes_only}`;
    })

    $('.toggle-filter').on('click', function(){
        if($('select[name=stages]').hasClass("d-none")){
            $('select[name=stages]').removeClass("d-none")
        }else{
            $('select[name=stages]').addClass("d-none")
        }
    })

    $(".validate").on("click", function() {
        let taskId = $('input[name=id]').val();
        Overlay("on");
        $.ajax({
            url: "portal/customers/validateTask",
            data: {task_id:taskId},
            method: "POST",
            dataType: "JSON",
            success: function(response) {
                if(response.result) {
                    $('select[name=stage]').val("validated");
                    $(".validate").parent().remove();
                }else{
                    alert(response.reason)
                }
                Overlay("off");
            }
        })
    })

    $('.deleteNote').on("click", function(){
        let note_id = $(this).data("note-id");
        let row = $(this).closest("tr");
        alertify.confirm('Delete Confirmation','Are you sure you want to delete your note?'
            , function(){
                Overlay("on");
                $.ajax({
                    url: "portal/customers/deleteNote",
                    data:{note_id:note_id},
                    method:"POST",
                    dataType:"JSON",
                    success: function(response) {
                        if(response.affected_rows==1){
                            $(row).remove();
                            alertify.success("Note deleted");
                            $('#previous_notes tbody tr').each(function(i,j){
                                $(this).find("td").eq(0).text(i+1)
                            })
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