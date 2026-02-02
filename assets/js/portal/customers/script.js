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
		dataType:"json",
        data:{type:"customer"},
		success: function(response)
		{
			console.log(response)
			if( (response.result==false) && (response.reason == 'login') )
			{
				clearInterval(pingActive);
				// Redirect to portal login
				window.location.href = base_url + "portal/customers/signin";
			}
		}
	})
}

$('#task_notes').off('submit').on('submit', function(e) {
    e.preventDefault();
    var form = this;
    var notesField = $(form).find('[name="notes"]');
    var notesValue = notesField.val() || '';
    if (notesValue.trim() === '' || notesValue.replace(/<[^>]*>/g, '').trim() === '') {
        alert('Please enter a note before submitting.');
        notesField.focus();
        return false;
    }
    var formData = new FormData(form);
    var $btn = $('#saveNote');
    $btn.prop('disabled', true);
    $.ajax({
        url: '/portal/customers/saveNote',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if(response.result && response.note) {
                var note = response.note;
                var fileLink = note.file ? '<br><a href="/uploads/notes/' + note.file + '" target="_blank" class="badge bg-secondary"><i class="bi bi-paperclip"></i> Download Attachment</a>' : '';
                var outOfScope = note.out_of_scope == '1' ? 'out-of-scope' : '';
                var deleteBtn = "<div class='btn btn-sm btn-danger deleteNote' data-note-id='" + note.id + "'><i class='bi bi-trash'></i></div>";
                var outOfScopeImg = note.out_of_scope == '1' ? "<img style='width:24px; height:24px;' src='/assets/images/OUT-OF-SCOPE-36PX.png' alt=''>" : '';
                var devInfo = "<div class='float-end developer' title='" + (note.country_code || '') + "'>by " + (note.developer || '') + (note.customer || '') + " <i class='flag flag-" + (note.country_code || '') + "'></i> on " + (note.created_on_fmt || note.created_on) + "</div>";
                var row = "<tr class='" + outOfScope + "'>" +
                    // "<td>NEW</td>" +
                    "<td>" + (note.notes ? note.notes.replace(/\n/g, '<br>') : '') + fileLink + devInfo + "</td>" +
                    "<td>" + deleteBtn + outOfScopeImg + "</td>" +
                    "</tr>";
                $('#previous_notes tbody').prepend(row);
                // Clear the form
                form.reset();
                if(window.jQuery && $('.summernote').length) {
                  $('.summernote').summernote('reset');
                }
            } else {
                alert(response.reason || 'Failed to save note.');
            }
        },
        error: function() {
            alert('An error occurred while saving the note.');
        },
        complete: function() {
            $btn.prop('disabled', false);
        }
    });
});

// Show loader during AJAX requests (except background requests)
$(document).ajaxSend(function(event, jqxhr, settings) {
	// Exclude background requests from showing loader
	if (settings.showLoader !== false && 
		!settings.url.includes('ajax/ping') && 
		!settings.url.includes('isLoggedIn')) {
		activeLoaderRequests++;
		if (activeLoaderRequests > 0) {
			Overlay('on');
		}
	}
});

$(document).ajaxComplete(function(event, jqxhr, settings) {
	// Only hide loader if this was a request that showed it
	if (settings.showLoader !== false && 
		!settings.url.includes('ajax/ping') && 
		!settings.url.includes('isLoggedIn')) {
		activeLoaderRequests--;
		if (activeLoaderRequests <= 0) {
			activeLoaderRequests = 0;
			Overlay('off');
		}
	}
});

jQuery(function(){

    // Show loader initially
	Overlay('on');

	// Start session ping for customers
	pingActive = setInterval(function(){
		isLoggedIn();
	},1000)

    // init('customers');

    // Image preview for note_file
    $('#note_file').on('change', function(){
        var input = this;
        var previewBox = $('#note_file_preview');
        var previewImg = $('#note_file_img');
        if (input.files && input.files[0]) {
            var file = input.files[0];
            if (file.type.match('image.*')) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.attr('src', e.target.result);
                    previewBox.show();
                }
                reader.readAsDataURL(file);
            } else {
                previewBox.hide();
                previewImg.attr('src', '');
            }
        } else {
            previewBox.hide();
            previewImg.attr('src', '');
        }
    });

    $('.resetFilter').on('click', function(){
        window.location.href = base_url + "portal/customers/notes";
    })

    $('.download').on('click', function(){
        downloadTableAsCSV('notes','notes',{ includeColumns: [1,2,3,4] });
    })
    
    zoomIconForSeconds('submitTask',6);
    setTimeout(function(){
        zoomIconForSeconds('addUser',9);
    },1000)
    

    // $('#submitTask').hover(function(){
    //     zoomIconForSeconds('submitTask',3)
    // })


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
		height: 100,
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

    $('#notes .filter-project').on('click', function() {
        let projectId = $(this).data('project-id');
        let sprintId = $('input[name=sprint_id]').val();
        let startDate = $('input[name=start_date]').val();
        let endDate = $('input[name=end_date]').val();
        let qs = "?start_date=" + startDate + "&end_date=" + endDate + "&project_id=" + projectId + "&sprint_id=" + sprintId;
        window.location.href = base_url + "portal/customers/notes" + qs;
    })

    $('#notes .filter-sprint').on('click', function() {
        let sprintId = $(this).data('sprint-id');
        let projectId = $('input[name=project_id]').val();
        let startDate = $('input[name=start_date]').val();
        let endDate = $('input[name=end_date]').val();
        let qs = "?start_date=" + startDate + "&end_date=" + endDate + "&project_id=" + projectId + "&sprint_id=" + sprintId;
        window.location.href = base_url + "portal/customers/notes" + qs;
    })

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

    $('.create-task').on('click', function(){
        let project_id = $('input[name=project_id]').val();
        let sprint_id = $('input[name=sprint_id]').val();
        let section = $('input[name=section]').val();
        let task_number = $('input[name=task_number]').val();
        let name = $('input[name=name]').val();
        let description = $('textarea[name=description]').val();
        let due_date = $('input[name=due_date]').val();
        console.log(project_id, sprint_id,section,task_number,name,description,due_date)

        alertify.alert("Not yet imeplemented")
    })

    $('.create-user-access').on('click', function(){
        let name = $("#addUserAccessModal input[name=name]").val();
        let email = $("#addUserAccessModal input[name=email]").val();
        let pswd = $("#addUserAccessModal input[name=password]").val();
        let pswd2 = $("#addUserAccessModal input[name=confirm_password]").val();
        let valid = true;
        let errorMessage = "";
        console.log(name,email,pswd,pswd2)

        if(name.length < 4){
            valid = false;
            errorMessage += "Please enter a name (4 chars min)<br>";
        }

        if(!validEmail(email)){
            valid = false;
            errorMessage += "Please enter a valid email<br>";
        }

        if(pswd.length < 4){
            valid = false;
            errorMessage += "Please enter a password (4 chars min)<br>";
        }

        if(pswd != pswd2){
            valid = false;
            errorMessage += "Confirmation password does not match<br>";
        }
        
        if(!valid){
            alertify.error(errorMessage);
            return false;
        }

        $.ajax({
            url: base_url + "portal/customers/createUserAccess",
            method: "POST",
            dataType: "json",
            data:{name:name,email:email,password:pswd},
            success:function(response)
            {
                if(!response.result){
                    alertify.alert(response.reason)
                }else{
                    $("#addUserAccessModal #existing_users tbody").empty();
                    $("#addUserAccessModal .label-existing-users").html(response.users.length + " Existing User" + (( response.users.count >1 ) ? 's' : '') + " <i>(Max 5 Users)</i>")
                    if(response.users.length >= 5){
                        $("#addUserAccessModal .create-user-access").remove();
                    }
                    $(response.users).each(function(i,j){
                        let row = "<tr data-id='"+j.id+"'>";
                        row += `<td>${j.name}</td>`;
                        row += `<td>${j.email}</td>`;
                        if(j.admin==0){
                            row += "<td class='remove-user'><i class='bi bi-trash'></i></td>";
                        }else{
                            row += '<td><img src="assets/images/crown.png" style="width:16px;" alt=""></td>';
                        }
                        
                        $("#addUserAccessModal #existing_users tbody").append(row);
                    })
                    alertify.alert("User has been added");
                    $("#addUserAccessModal input[name=name]").val("");
                    $("#addUserAccessModal input[name=email]").val("");
                    $("#addUserAccessModal input[name=password]").val("");
                    $("#addUserAccessModal input[name=confirm_password]").val("");
                }
            }
        })
    })

    $('.autosubmit').on("change", function(){
        $('.apply').trigger("click");
    })

    $('.add-task').on('click', function(){
        $('#addTaskModal').modal("show")
    })

    $('.add-user-access').on('click', function(){
        $('#addUserAccessModal').modal("show")
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

    $('#addUserAccessModal').on('click','.remove-user', function(){
        let userId = $(this).closest("tr").data("id");
        $(this).closest("tr").addClass("selected active");
        alertify.confirm(
            "Are you sure you want remove this user?",
            function(){
                Overlay("on");
                $.ajax({
                    url: "portal/customers/removeUser",
                    data: {userId:userId},
                    method: "POST",
                    dataType: "JSON",
                    success: function(response) {
                        if(response.result) {
                            $('#existing_users tbody tr.active').remove();
                        }else{
                            alertify.alert(response.reason);
                        }
                        Overlay("off");
                        $('#existing_users tbody tr.active').removeClass("active")
                    }
                });
            },
            function(){
                $('#existing_users tbody tr.active').removeClass("active")
            })
    })

    $('#addUserAccessModal').on('hidden.bs.modal', function (e) {
        $('#existing_users tbody tr.active').removeClass("active")
    })

    $("#saveNote").on("click", function() {
        let taskUuid = $('input[name=uuid]').val();
        let taskId = $('input[name=id]').val();
        let sprintId = $('input[name=sprint_id]').val();
        // let notes = $('textarea[name=notes]').val();
        let notes = $('.summernote').summernote('code').replace(/^\s*<p>(.*?)<\/p>/i, '$1');
        Overlay("on");
        $.ajax({
            url: "portal/customers/saveNote",
            data: {task_id:taskId, note:notes},
            method: "POST",
            dataType: "JSON",
            success: function(response) {
                if(response.result) {
                    // window.location.href = "portal/customers/tasks?sprint_id="+sprintId;
                    window.location.href = "portal/customers/view?task_uuid="+taskUuid;
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
        alertify.confirm(
            "Are you sure you want to validate this task?",
            function(){
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
                            $(".reject").closest('.row').remove();
                        }else{
                            alert(response.reason)
                        }
                        Overlay("off");
                    }
                })
            },
            function(){
                
            })

        
    })

    $(".reject").on("click", function() {
        let taskId = $('input[name=id]').val();
        let rejectReason = $('textarea[name=reject_reason]').val();

        if(rejectReason.length<10){
            alertify.alert("Error","Please explain why this task is being rejected to help us make necessary corrections");
            return false;
        }
        alertify.confirm(
            "Are you sure you want to reject this task?",
            function(){
                Overlay("on");
                $.ajax({
                    url: "portal/customers/rejectTask",
                    data: {task_id:taskId, reject_reason:rejectReason},
                    method: "POST",
                    dataType: "JSON",
                    success: function(response) {
                        if(response.result) {
                            $('select[name=stage]').val("on_hold");
                            $(".validate").parent().remove();
                            $(".reject").closest('.row').remove();
                        }else{
                            alert(response.reason)
                        }
                        Overlay("off");
                    }
                })
            },
            function(){
                
            })

        
    })

    $('#previous_notes').on("click",".deleteNote",function(){


        let note_id = $(this).data("note-id");
        let row = $(this).closest("tr");
        $(this).closest("tr").addClass("alert alert-danger");
        alertify.confirm('Delete Confirmation','Are you sure you want to delete your note?'
            , function(){
                Overlay("on");
                $.ajax({
                    url: "portal/customers/deleteNote",
                    data:{note_id:note_id},
                    method:"POST",
                    dataType:"JSON",
                    success: function(response) {
                        // if(response.affected_rows==1){
                            $(row).remove();
                            alertify.success("Note deleted");
                            // $('#previous_notes tbody tr').each(function(i,j){
                            //     $(this).find("td").eq(0).text(i+1)
                            // })
                        // }
                    },
                    complete: function(ev) {
                        Overlay("off");
                        $('#previous_notes tr').removeClass("alert alert-danger");
                    }
                })
            }
            ,function() {
                alertify.error('Cancelled');
                console.log('cleaning up')
                $('#previous_notes tr').removeClass("alert alert-danger")
            }
        )
       
    })

    $('.submit-task').on('click',function(){
        $(this).addClass("d-none");
        let name = $('input[name=name]').val();
        let section = $('input[name=section]').val();
        let description = $('textarea[name=description]').val();
        let scope_when_done = $('textarea[name=scope_when_done]').val();   
        let scope_not_included = $('textarea[name=scope_not_included]').val();
        let scope_client_expectation = $('textarea[name=scope_client_expectation]').val();

        Overlay("on");
        $.ajax({
            url: base_url + "portal/customers/submitTask",
            method: "POST",
            dataType: "JSON",
            data: {name:name,section:section,description:description,scope_when_done:scope_when_done,scope_not_included:scope_not_included,scope_client_expectation:scope_client_expectation},
            success: function(response)
            {
                if(response.result){
                    alertify.success("Task submitted");
                    $('#addTaskModal .submission').addClass("d-none");
                    $('#addTaskModal .thankyou').removeClass("d-none");
                }else{
                    alertify.error(response.reason);
                    $('.submit-task').removeClass("d-none");   
                }
            },
            complete: function(response) {
                Overlay("off");
            }
        })
    })

    $('#addTaskModal').on('hidden.bs.modal', function (e) {
        $('#addTaskModal .submission').removeClass("d-none");
        $('#addTaskModal .thankyou').addClass("d-none");
        $('#addTaskModal input[name=name]').val('');
        $('#addTaskModal input[name=section]').val('');
        $('#addTaskModal textarea[name=description]').val('');        
        $('#addTaskModal textarea[name=scope_when_done]').val('');        
        $('#addTaskModal textarea[name=scope_not_included]').val('');        
        $('#addTaskModal textarea[name=scope_client_expectation]').val('');     
        $('.submit-task').removeClass("d-none");   
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

function zoomIconForSeconds(elementID, seconds) {
    const icon = document.getElementById(elementID);
    
    icon.classList.add('zoom-animation'); // Start animation
    
    setTimeout(() => {
      icon.classList.remove('zoom-animation'); // Stop animation after X seconds
    }, seconds * 1000); // seconds → milliseconds
  }

function validEmail(email) {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return pattern.test(email);
}  


const progressiveImages = document.querySelectorAll(".progressive-image");

progressiveImages.forEach(container => {
  const placeholder = container.querySelector(".placeholder");
  const fullRes = container.querySelector(".full-res");

  fullRes.addEventListener("load", function() {
    // Hide the placeholder
    placeholder.classList.add("d-none");

    // Show the full-res image
    fullRes.classList.remove("d-none");
  });

  // Start loading the full-res image
    fullRes.src = fullRes.src; // re-trigger loading in some browsers
});

// --- Stage Filter Modal Logic ---
jQuery(function(){
    // Pre-select checkboxes in modal based on hidden input
    $('#stageFilterModal').on('show.bs.modal', function() {
        var selected = ($('#selectedStages').val() || '').split(',').filter(Boolean);
        $('.stage-checkbox').each(function(){
            $(this).prop('checked', selected.includes($(this).val()));
        });
        // Select All if all are checked
        $('#selectAllStages').prop('checked', $('.stage-checkbox:checked').length === $('.stage-checkbox').length);
    });

    // Select All logic
    $('#selectAllStages').on('change', function(){
        $('.stage-checkbox').prop('checked', this.checked);
    });
    $('.stage-checkbox').on('change', function(){
        $('#selectAllStages').prop('checked', $('.stage-checkbox:checked').length === $('.stage-checkbox').length);
    });

    // Apply button: update hidden input and submit
    function applyStageFilter() {
        var selected = $('.stage-checkbox:checked').map(function(){ return $(this).val(); }).get();
        if (selected.length === 0) {
            $('#selectedStages').val('');
        } else {
            $('#selectedStages').val(selected.join(','));
        }
        // Find the closest form and submit
        $('#selectedStages').closest('form').submit();
    }
    $('#applyStageFilter').on('click', function(){
        $('#stageFilterModal').modal('hide');
    });
    // Auto-apply on modal close
    $('#stageFilterModal').on('hidden.bs.modal', function(){
        applyStageFilter();
    });
});
