// Show loader on page load
$(window).on('load', function() {
	Overlay('off');
});

// Track active AJAX requests that should show loader
var activeLoaderRequests = 0;

// Skip task-view poll toasts briefly after the customer’s own actions (note save, etc.)
var customerTaskPollSuppressUntil = 0;
window.customerTaskPollSuppress = function(ms) {
	customerTaskPollSuppressUntil = Date.now() + (ms || 45000);
};

function portalEscapeHtmlAttr(s) {
	return String(s == null ? '' : s)
		.replace(/&/g, '&amp;')
		.replace(/"/g, '&quot;')
		.replace(/</g, '&lt;')
		.replace(/>/g, '&gt;');
}

// Session ping functionality for portal users
var pingActive;

function isLoggedIn()
{
	// $.ajax({
	// 	url: base_url + "ajax/ping",
	// 	method: "get",
	// 	dataType:"json",
    //     data:{type:"customer"},
	// 	success: function(response)
	// 	{
	// 		console.log(response)
	// 		if( (response.result==false) && (response.reason == 'login') )
	// 		{
	// 			clearInterval(pingActive);
	// 			// Redirect to portal login
	// 			window.location.href = base_url + "portal/customers/signin";
	// 		}
	// 	}
	// })
}

$('#task_notes').off('submit').on('submit', function(e) {
    e.preventDefault();
    var form = this;
    var notesField = $(form).find('[name="notes"]');
    var $summer = $(form).find('.summernote');
    if ($summer.length && typeof $summer.summernote === 'function') {
        notesField.val($summer.summernote('code'));
    }
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
                var bu = (typeof base_url !== 'undefined' ? base_url : '/');
                if (bu && bu.slice(-1) !== '/') {
                    bu += '/';
                }
                var outOfScope = note.out_of_scope == '1' ? 'out-of-scope' : '';
                var deleteBtn = "<div class='btn btn-sm btn-danger deleteNote' data-note-id='" + note.id + "'><i class='bi bi-trash'></i></div>";
                var outOfScopeImg = note.out_of_scope == '1' ? "<img style='width:24px; height:24px;' src='" + bu + "assets/images/OUT-OF-SCOPE-36PX.png' alt=''>" : '';
                var createdStr = note.created_on_fmt || note.created_on || '';
                var devInfo = "<div class='float-end developer' title='" + (note.country_code || '') + "'>by " + (note.developer || '') + (note.customer || '') + " <i class='flag flag-" + (note.country_code || '') + "'></i> on " + createdStr + "</div>";
                var noteHtml = note.notes ? note.notes.replace(/\n/g, '<br>') : '';
                var row = "<tr class='" + outOfScope + "'>" +
                    "<td>" + noteHtml + devInfo + "</td>" +
                    "<td>" + deleteBtn + outOfScopeImg + "</td>" +
                    "</tr>";
                $('#previous_notes tbody').prepend(row);
                if (note.attachment_file) {
                    var $badge = $('#attachments-tab .badge');
                    if ($badge.length) {
                        var n = parseInt($badge.text(), 10);
                        $badge.text((isNaN(n) ? 0 : n) + 1);
                    }
                    var $pane = $('div.tab-pane#attachments');
                    var thumbUrl = bu + 'uploads/tasks/' + encodeURIComponent(note.attachment_thumb || note.attachment_file);
                    var fullUrl = bu + 'uploads/tasks/' + encodeURIComponent(note.attachment_file);
                    var delBtn = '';
                    if (note.attachment_task_image_id) {
                        delBtn = '<button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 py-0 px-1 delete-task-attachment" data-task-image-id="' + parseInt(note.attachment_task_image_id, 10) + '" title="Delete attachment"><i class="bi bi-trash"></i></button>';
                    }
                    var lbTitle = note.attachment_lightbox_caption ? ' data-title="' + portalEscapeHtmlAttr(note.attachment_lightbox_caption) + '"' : '';
                    var col = '<div class="col-md-2 position-relative pb-4"><a href="' + fullUrl + '" data-lightbox="task-attachments"' + lbTitle + '><img class="img-thumbnail img-responsize" src="' + thumbUrl + '" alt=""></a>' + delBtn + '</div>';
                    var $gridRow = $pane.find('.card .card-body .row').first();
                    if ($gridRow.length) {
                        $gridRow.prepend(col);
                    } else {
                        $pane.find('> .text-muted').first().replaceWith(
                            '<div class="card card-secondary">' +
                            '<div class="card-header">ATTACHMENTS</div>' +
                            '<div class="card-body"><div class="row">' + col + '</div></div></div>'
                        );
                    }
                }
                // Clear the form
                form.reset();
                $('#note_file_preview').hide();
                if(window.jQuery && $('.summernote').length) {
                  $('.summernote').summernote('reset');
                }
                if (typeof window.customerTaskPollSuppress === 'function') {
                    window.customerTaskPollSuppress(50000);
                }
                var $pollRoot = $('#customer-task-view-root');
                if ($pollRoot.length) {
                    $.ajax({
                        url: base_url + 'portal/customers/taskPoll',
                        type: 'GET',
                        data: { task_uuid: $pollRoot.data('taskUuid') },
                        dataType: 'json',
                        showLoader: false,
                        success: function(pr) {
                            if (pr && pr.result && pr.snapshot) {
                                $pollRoot.attr('data-task-poll-snapshot', JSON.stringify(pr.snapshot));
                            }
                        }
                    });
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

function portalAjaxShowsLoader(settings) {
	if (!settings || settings.showLoader === false) {
		return false;
	}
	var u = String(settings.url || '');
	return u.indexOf('ajax/ping') === -1
		&& u.indexOf('isLoggedIn') === -1
		&& u.indexOf('customers/taskPoll') === -1
		&& u.indexOf('taskPoll') === -1;
}

// Show loader during AJAX requests (except background / silent polls)
$(document).ajaxSend(function(event, jqxhr, settings) {
	if (portalAjaxShowsLoader(settings)) {
		activeLoaderRequests++;
		if (activeLoaderRequests > 0) {
			Overlay('on');
		}
	}
});

$(document).ajaxComplete(function(event, jqxhr, settings) {
	if (portalAjaxShowsLoader(settings)) {
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

    // --- Submitted task image upload (client-side compress + preview) ---
    var taskImagesQueue = [];
    var taskImagesPreviewUrls = [];
    var taskImagesCompressing = false;

    function revokeTaskImagePreviews()
    {
        taskImagesPreviewUrls.forEach(function(u){
            try { URL.revokeObjectURL(u); } catch(e) {}
        });
        taskImagesPreviewUrls = [];
        taskImagesQueue = [];
    }

    function compressImageFile(file, maxDim, quality)
    {
        return new Promise(function(resolve, reject){
            var reader = new FileReader();
            reader.onerror = function(){ reject(new Error('Failed to read image')); };
            reader.onload = function(e){
                var img = new Image();
                img.onload = function(){
                    var w = img.width;
                    var h = img.height;
                    var scale = 1;
                    if (w > maxDim || h > maxDim) {
                        scale = Math.min(maxDim / w, maxDim / h);
                    }
                    var canvas = document.createElement('canvas');
                    canvas.width = Math.max(1, Math.round(w * scale));
                    canvas.height = Math.max(1, Math.round(h * scale));
                    var ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                    canvas.toBlob(function(blob){
                        if (!blob) {
                            reject(new Error('Failed to compress image'));
                            return;
                        }
                        resolve(blob);
                    }, 'image/jpeg', quality);
                };
                img.onerror = function(){ reject(new Error('Failed to load image')); };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    $('#task_images_input').on('change', function(){
        var input = this;
        var files = input.files;

        revokeTaskImagePreviews();
        $('#task_images_preview').empty();

        if (!files || files.length === 0) {
            return;
        }

        taskImagesCompressing = true;
        $('.submit-task').prop('disabled', true).addClass('disabled');
        $('#task_images_preview').html("<div class='text-muted' id='task_images_status'>Compressing images...</div>");

        // These totals are captured in the handler scope so we can use them after compression finishes.
        var originalTotalBytes = 0;
        var compressedTotalBytes = 0;

        (async function(){
            var maxDim = 1200;
            var quality = 0.8;

            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                if (!file || !file.type || !file.type.match('image.*')) {
                    continue;
                }

                try {
                    var compressedBlob = await compressImageFile(file, maxDim, quality);
                    originalTotalBytes += (typeof file.size === 'number' ? file.size : 0);
                    compressedTotalBytes += (compressedBlob && typeof compressedBlob.size === 'number') ? compressedBlob.size : 0;
                    // normalize name to .jpg since we always encode as JPEG
                    var baseName = file.name.replace(/\.[^/.]+$/, '');
                    var compressedName = baseName + '.jpg';
                    var previewUrl = URL.createObjectURL(compressedBlob);
                    taskImagesQueue.push({ blob: compressedBlob, name: compressedName });
                    taskImagesPreviewUrls.push(previewUrl);

                    $('#task_images_preview').append(
                        `<div class='col-4'>
                            <img class='img-thumbnail' style='width:100%;height:auto;' src='${previewUrl}' alt='preview'>
                        </div>`
                    );
                } catch (err) {
                    console.error(err);
                }
            }
        })().then(function(){
            taskImagesCompressing = false;
            $('.submit-task').prop('disabled', false).removeClass('disabled');
            if (taskImagesQueue.length === 0) {
                $('#task_images_preview').html("<div class='text-muted'>No valid images selected.</div>");
                return;
            }

            // Show compression stats (remove the 'Compressing...' message)
            var statusEl = document.getElementById('task_images_status');
            if (statusEl) statusEl.remove();

            // Compute totals from the queue blobs
            var compressedBytes = compressedTotalBytes;

            // We can safely estimate savings only when at least one blob exists and original bytes were tracked.
            // If originalTotalBytes wasn't set (shouldn't happen), we just show compressed size.
            if (typeof originalTotalBytes === 'number' && originalTotalBytes > 0 && compressedBytes > 0) {
                var saved = originalTotalBytes - compressedBytes;
                var pct = (saved > 0) ? (saved / originalTotalBytes) * 100 : 0;
                var fmt = function(b){
                    if (!b || b <= 0) return '0 B';
                    var kb = b / 1024;
                    var mb = kb / 1024;
                    if (mb >= 1) return mb.toFixed(1) + ' MB';
                    return kb.toFixed(0) + ' KB';
                };
                $('#task_images_preview').prepend(
                    `<div class="text-muted mb-2">
                        Compressed ${taskImagesQueue.length} image(s). Saved ${fmt(saved)} (${pct.toFixed(0)}%).
                    </div>`
                );
            } else {
                $('#task_images_preview').prepend(
                    `<div class="text-muted mb-2">
                        Compressed ${taskImagesQueue.length} image(s).
                    </div>`
                );
            }
        }).catch(function(){
            taskImagesCompressing = false;
            $('.submit-task').prop('disabled', false).removeClass('disabled');
            $('#task_images_preview').html("<div class='text-muted'>Failed to process images.</div>");
        });
    });

    $('.resetFilter').on('click', function(){
        window.location.href = base_url + "portal/customers/notes";
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
        let jobDescription = $("#addUserAccessModal input[name=job_description]").val();
        let pswd = $("#addUserAccessModal input[name=password]").val();
        let pswd2 = $("#addUserAccessModal input[name=confirm_password]").val();
        let valid = true;
        let errorMessage = "";
        console.log(name,email,pswd,pswd2)

        if(name.length < 4){
            valid = false;
            errorMessage += "Please enter a name (4 chars min)<br>";
        }

        if(!jobDescription || jobDescription.trim().length < 5){
            valid = false;
            errorMessage += "Please enter a job description (5 chars min)<br>";
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
            data:{name:name,email:email,job_description:jobDescription,password:pswd},
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
                        let safeJob = (j.job_description || '').replace(/'/g, '&#39;');
                        let row = "<tr data-id='"+j.id+"' data-job-description='"+safeJob+"'>";
                        row += `<td>${j.name}</td>`;
                        row += `<td>${safeJob}</td>`;
                        row += `<td>${j.email}</td>`;
                        // country flag (if any)
                        if (j.country_code) {
                            row += `<td><i class="flag flag-${j.country_code}"></i></td>`;
                        } else {
                            row += "<td></td>";
                        }
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
                    $("#addUserAccessModal input[name=job_description]").val("");
                    $("#addUserAccessModal input[name=password]").val("");
                    $("#addUserAccessModal input[name=confirm_password]").val("");
                }
            }
        })
    })

    // When clicking on name or job/email cell, load user into the form for editing
    $('#addUserAccessModal #existing_users').on('click', 'td:nth-child(1), td:nth-child(2), td:nth-child(3)', function(){
        let row = $(this).closest('tr');
        let id = row.data('id');
        let name = row.find('td').eq(0).text();
        let jobDescription = row.find('td').eq(1).text();
        let email = row.find('td').eq(2).text();
        $("#addUserAccessModal input[name=access_id]").val(id);
        $("#addUserAccessModal input[name=name]").val(name);
        $("#addUserAccessModal input[name=email]").val(email);
        $("#addUserAccessModal input[name=job_description]").val(jobDescription);
        // Do not pre-fill password fields for security; admin can enter a new one if needed.
        $("#addUserAccessModal input[name=password]").val("");
        $("#addUserAccessModal input[name=confirm_password]").val("");
        $("#addUserAccessModal .update-user-access").removeClass("d-none");
    });


    // Update existing user access (including optional password change)
    $('.update-user-access').on('click', function(){
        let accessId = $("#addUserAccessModal input[name=access_id]").val();
        let name = $("#addUserAccessModal input[name=name]").val();
        let email = $("#addUserAccessModal input[name=email]").val();
        let jobDescription = $("#addUserAccessModal input[name=job_description]").val();
        let pswd = $("#addUserAccessModal input[name=password]").val();
        let pswd2 = $("#addUserAccessModal input[name=confirm_password]").val();

        if(!accessId){
            alertify.error("Please select a user to update from the list on the right.");
            return;
        }

        let valid = true;
        let errorMessage = "";

        if(name.length < 4){
            valid = false;
            errorMessage += "Please enter a name (4 chars min)<br>";
        }

        if(!jobDescription || jobDescription.trim().length < 5){
            valid = false;
            errorMessage += "Please enter a job description (5 chars min)<br>";
        }

        if(!validEmail(email)){
            valid = false;
            errorMessage += "Please enter a valid email<br>";
        }

        // Password is optional; if provided, validate it and confirmation
        if(pswd.length > 0 && pswd.length < 4){
            valid = false;
            errorMessage += "Please enter a password (4 chars min) or leave it blank to keep the current one<br>";
        }
        if(pswd !== pswd2){
            valid = false;
            errorMessage += "Confirmation password does not match<br>";
        }

        if(!valid){
            alertify.error(errorMessage);
            return false;
        }

        $.ajax({
            url: base_url + "portal/customers/updateUserAccess",
            method: "POST",
            dataType: "json",
            data:{
                access_id: accessId,
                name: name,
                email: email,
                job_description: jobDescription,
                // Only send password when admin entered one; backend will keep other fields unchanged
                password: pswd
            },
            success:function(response)
            {
                if(!response.result){
                    alertify.alert(response.reason || "Failed to update user.");
                }else{
                    // Reflect changes in the existing users table
                    $("#addUserAccessModal #existing_users tbody tr").each(function(){
                        if($(this).data('id') == accessId){
                            $(this).find('td').eq(0).text(name);
                            $(this).find('td').eq(1).text(jobDescription);
                            $(this).find('td').eq(2).text(email);
                            $(this).attr('data-job-description', jobDescription);
                        }
                    });
                    alertify.success("User updated successfully.");
                    // Clear the form so it's ready for a fresh entry
                    $("#addUserAccessModal input[name=access_id]").val("");
                    $("#addUserAccessModal input[name=name]").val("");
                    $("#addUserAccessModal input[name=email]").val("");
                    $("#addUserAccessModal input[name=job_description]").val("");
                    $("#addUserAccessModal input[name=password]").val("");
                    $("#addUserAccessModal input[name=confirm_password]").val("");
                    $("#addUserAccessModal .update-user-access").addClass("d-none");
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
        let taskRef = $(this).closest("tr").find("td.task-ref-cell .task-ref-text").first().text().trim();
        let taskSection = $(this).closest("tr").find("td.task-section").text();
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
                        $('#modalNotes .modal-title').html(`<b>Notes for</b>: ${taskRef} / ${taskSection} / ${taskName}`);
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
        // Clear any active/selected state in the user list
        $('#existing_users tbody tr').removeClass("active selected");
        // Reset the form fields
        $("#addUserAccessModal input[name=access_id]").val("");
        $("#addUserAccessModal input[name=name]").val("");
        $("#addUserAccessModal input[name=email]").val("");
        $("#addUserAccessModal input[name=job_description]").val("");
        $("#addUserAccessModal input[name=password]").val("");
        $("#addUserAccessModal input[name=confirm_password]").val("");
        // Hide the update button until a user is selected again
        $("#addUserAccessModal .update-user-access").addClass("d-none");
    })

    // Legacy handler for pages that use #saveNote without #task_notes (AJAX + redirect).
    // Task view uses #task_notes submit (FormData, correct "notes" field); this click runs first
    // and wrongly posted "note" vs "notes", causing a spurious "2 Notes cannot be empty" alert.
    $("#saveNote").on("click", function() {
        if ($(this).closest("#task_notes").length) {
            return;
        }
        let taskUuid = $('input[name=uuid]').val();
        let taskId = $('input[name=id]').val();
        let sprintId = $('input[name=sprint_id]').val();
        let notes = $('.summernote').summernote('code').replace(/^\s*<p>(.*?)<\/p>/i, '$1');
        Overlay("on");
        $.ajax({
            url: "portal/customers/saveNote",
            data: {task_id: taskId, notes: notes},
            method: "POST",
            dataType: "JSON",
            success: function(response) {
                if(response.result) {
                    window.location.href = "portal/customers/view?task_uuid="+taskUuid;
                } else {
                    alert(response.reason);
                }
                Overlay("off");
            }
        });
    });

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
                            // Update the stage badge in the top right with proper styling
                            $('#task-stage-badge').removeClass().addClass('stage-button stage-button-validated').text('VALIDATED');
                            // Remove validation section
                            $(".validate").closest('.card.card-info').remove();
                            // Show success message and reload after a short delay
                            alertify.success("Task validated successfully!");
                            setTimeout(function(){
                                window.location.reload();
                            }, 1000);
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
                            // Update the stage badge in the top right with proper styling
                            $('#task-stage-badge').removeClass().addClass('stage-button stage-button-on_hold').text('ON HOLD');
                            // Remove validation section
                            $(".validate").closest('.card.card-info').remove();
                            // Show success message and reload after a short delay
                            alertify.success("Task rejected. Changes will be made.");
                            setTimeout(function(){
                                window.location.reload();
                            }, 1000);
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
                            if (typeof window.customerTaskPollSuppress === 'function') {
                                window.customerTaskPollSuppress(50000);
                            }
                            var $pr = $('#customer-task-view-root');
                            if ($pr.length) {
                                $.ajax({
                                    url: base_url + 'portal/customers/taskPoll',
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

    $(document).on('click', '.delete-task-attachment', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var btn = $(this);
        var id = btn.data('task-image-id');
        var col = btn.closest('.col-md-2');
        alertify.confirm('Delete attachment', 'Remove this file from the task?',
            function() {
                Overlay('on');
                $.ajax({
                    url: 'portal/customers/deleteTaskImage',
                    method: 'POST',
                    data: { task_image_id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.result) {
                            col.remove();
                            var $badge = $('#attachments-tab .badge');
                            if ($badge.length) {
                                var n = parseInt($badge.text(), 10);
                                n = (isNaN(n) ? 0 : n) - 1;
                                if (n < 0) { n = 0; }
                                $badge.text(n);
                            }
                            var $pane = $('div.tab-pane#attachments');
                            if ($pane.find('.col-md-2').length === 0) {
                                $pane.find('.card').remove();
                                if (!$pane.children('.text-muted').length) {
                                    $pane.append('<div class="text-muted">No attachments for this task.</div>');
                                }
                            }
                            alertify.success('Attachment removed');
                            if (typeof window.customerTaskPollSuppress === 'function') {
                                window.customerTaskPollSuppress(50000);
                            }
                            var $pr = $('#customer-task-view-root');
                            if ($pr.length) {
                                $.ajax({
                                    url: base_url + 'portal/customers/taskPoll',
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

    $('.submit-task').on('click',function(){
        $(this).addClass("d-none");
        let name = $('input[name=name]').val();
        let section = $('input[name=section]').val();
        let description = $('textarea[name=description]').val();
        let scope_when_done = $('textarea[name=scope_when_done]').val();   
        let scope_not_included = $('textarea[name=scope_not_included]').val();
        let scope_client_expectation = $('textarea[name=scope_client_expectation]').val();

        if (taskImagesCompressing) {
            alertify.error("Please wait while images are being compressed.");
            $(this).removeClass("d-none");
            return false;
        }

        var formData = new FormData();
        formData.append('name', name);
        formData.append('section', section);
        formData.append('description', description);
        formData.append('scope_when_done', scope_when_done);
        formData.append('scope_not_included', scope_not_included);
        formData.append('scope_client_expectation', scope_client_expectation);

        // Append compressed images (if any)
        if (taskImagesQueue && taskImagesQueue.length) {
            for (var i = 0; i < taskImagesQueue.length; i++) {
                var item = taskImagesQueue[i];
                if (item && item.blob) {
                    formData.append('task_images[]', item.blob, item.name);
                }
            }
        }

        Overlay("on");
        $.ajax({
            url: base_url + "portal/customers/submitTask",
            method: "POST",
            dataType: "JSON",
            data: formData,
            processData: false,
            contentType: false,
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
        $('#task_images_input').val('');
        $('#task_images_preview').empty();
        revokeTaskImagePreviews();
    })

    ;(function initCustomerTaskViewPoll() {
        var $root = $('#customer-task-view-root');
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
        // Task live-update poll interval (ms). Use ~20000 in production; shorter while testing.
        var pollMs = 5000;
        setInterval(function() {
            if (Date.now() < customerTaskPollSuppressUntil) {
                return;
            }
            $.ajax({
                url: base_url + 'portal/customers/taskPoll',
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
                    if (window.__customerTaskPollReloading) {
                        return;
                    }
                    window.__customerTaskPollReloading = true;
                    var nextSnap = JSON.stringify(res.snapshot);
                    var onDecline = function() {
                        lastCanon = nextCanon;
                        $root.attr('data-task-poll-snapshot', nextSnap);
                        window.__customerTaskPollReloading = false;
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
                            window.__customerTaskPollReloading = false;
                        }
                    };
                    setTimeout(showPrompt, 0);
                }
            });
        }, pollMs);
    })();

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
