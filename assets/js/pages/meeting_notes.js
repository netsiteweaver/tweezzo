document.querySelectorAll('.delete-note').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const noteId = this.dataset.id;
        if (confirm('Are you sure you want to delete this note?')) {
            fetch(`meeting_notes/delete/${noteId}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('note-' + noteId).remove();
                } else {
                    alert('Failed to delete the note.');
                }
            });
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.preview-image').forEach(function (img) {
    img.addEventListener('contextmenu', function (e) {
      e.preventDefault(); // prevent the default right-click menu

      if (confirm('Delete this image?')) {
        const imageId = img.getAttribute('data-id');

        fetch('/meeting_notes/delete_image/' + imageId, {
          method: 'DELETE',
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.result) {
                const row = img.closest('div');
                if (row) {
                    console.log('Deleting row:', row); // 👈 DEBUG
                    row.remove();
                }
            } else {
                alert('Failed to delete image.');
            }
        });
      }
    });
  });
});

function downloadPDF() {
    const element = document.getElementById('meeting-notes');
    const opt = {
        margin:       [10, 15, 10, 15], // top, left, bottom, right (in mm)
        // filename:     'Meeting-Minutes-2025-08-04.pdf',
        // image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 1, useCORS: true },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    html2pdf()
        .set(opt)
        .from(element)
        .toPdf()
        .get('pdf')
        .then(function(pdf){
            const totalPages = pdf.internal.getNumberOfPages();
            for (let i = 1; i <= totalPages; i++) {
                pdf.setPage(i);
                pdf.setFontSize(10);
                pdf.text(`Page ${i} of ${totalPages}`, 105, 290, { align: 'center' }); // footer center
            }
        })
        .save('Meeting-Minutes.pdf');
}

jQuery(function(){
    if ($('#meeting_notes_start_date').length) {
        $('.monitor-index').on('change', function () {
            const params = new URLSearchParams();
            const customerId = $('#customer_id_filter').val();
            const startDate = $('#meeting_notes_start_date').val();
            const endDate = $('#meeting_notes_end_date').val();
            if (customerId) {
                params.set('customer_id', customerId);
            }
            if (startDate) {
                params.set('start_date', startDate);
            }
            if (endDate) {
                params.set('end_date', endDate);
            }
            window.location.href = base_url + 'meeting_notes/index?' + params.toString();
        });
    }

    // Live sessions and planned meetings start before any notes are written, so notes stop being required.
    function submitWithoutNotes(button, flag) {
        var $form = $(button).closest('form');
        $('#notes').removeClass('required').prop('required', false);
        $form.find('input[name=start_live], input[name=plan_meeting]').remove();
        $form.append('<input type="hidden" name="' + flag + '" value="1">');
        $form.trigger('submit');
    }
    $('.start-live-session').on('click', function(){ submitWithoutNotes(this, 'start_live'); });
    $('.plan-meeting').on('click', function(){ submitWithoutNotes(this, 'plan_meeting'); });

    // Update on a shared note: ask whether to email the shared people about it.
    var $notifyModal = $('#modalNotifyUpdate');
    if ($notifyModal.length) {
        var $updateForm = $('.btn-update').closest('form');
        var notifyConfirmed = false;

        $('.btn-update').on('click', function(e){
            if (notifyConfirmed) return;
            e.preventDefault();
            $updateForm.find('input[name="notify_share_ids[]"]').remove();
            $notifyModal.modal('show');
        });

        function submitUpdate(ids) {
            ids.forEach(function(id){
                $updateForm.append('<input type="hidden" name="notify_share_ids[]" value="' + parseInt(id, 10) + '">');
            });
            notifyConfirmed = true;
            $notifyModal.modal('hide');
            $updateForm.trigger('submit');
            // If client-side validation stops the submit, ask again next time.
            setTimeout(function(){ notifyConfirmed = false; }, 0);
        }

        function updateNotifyButton() {
            var n = $notifyModal.find('.notify-share:checked').length;
            $notifyModal.find('.notify-send').prop('disabled', n === 0)
                .html('<i class="fa fa-paper-plane"></i> Update &amp; notify' + (n ? ' ' + n : ''));
            $notifyModal.find('.notify-toggle-all').text(n ? 'Untick all' : 'Tick all');
        }

        $notifyModal.on('change', '.notify-share', updateNotifyButton);
        $notifyModal.on('click', '.notify-toggle-all', function(e){
            e.preventDefault();
            var tickAll = $notifyModal.find('.notify-share:checked').length === 0;
            $notifyModal.find('.notify-share').prop('checked', tickAll);
            updateNotifyButton();
        });
        $notifyModal.on('click', '.notify-skip', function(){ submitUpdate([]); });
        $notifyModal.on('click', '.notify-send', function(){
            submitUpdate($notifyModal.find('.notify-share:checked').map(function(){ return this.value; }).get());
        });
        updateNotifyButton();
    }

    $('select[name=customer_id]').on("change",function(){
        let elemName = $('select[name=customer_id] option:selected').text();
        let customerId = $(this).val();
        console.log("Name:",elemName)
        if(customerId=='other'){
            $('input[name=customer_name]').closest(".form-group").removeClass("d-none");
            $('input[name=customer_name]').trigger('select');
        }else{
            $('input[name=customer_name]').closest(".form-group").addClass("d-none");
            $('input[name=customer_name]').val(elemName);
            
            // Auto-populate attendees from customer_access
            if(customerId && customerId !== '' && customerId !== 'other'){
                $.ajax({
                    url: base_url + "ajax/meeting_notes/getAttendeesByCustomerId",
                    data: {customer_id: customerId},
                    method: "POST",
                    dataType: "JSON",
                    success: function(response){
                        if(response.result && response.emails && response.emails.length > 0){
                            let attendeesTextarea = $('#attendees');
                            let currentValue = attendeesTextarea.val().trim();
                            let emailsToAdd = response.emails.join('\n');
                            
                            // Append emails, ensuring proper line breaks
                            if(currentValue){
                                // If there's existing content, add a newline before appending
                                attendeesTextarea.val(currentValue + '\n' + emailsToAdd);
                            }else{
                                // If empty, just set the emails
                                attendeesTextarea.val(emailsToAdd);
                            }
                        }
                    },
                    error: function(){
                        console.log("Error fetching attendees for customer");
                    }
                });
            }
        }
    })
})
// ---------- Live session (edit page of a live note) ----------
jQuery(function(){
    var $live = $('#live-session');
    if (!$live.length || typeof MeetingLive === 'undefined') return;

    var state = JSON.parse($('#live-meeting-data').text());
    var status = state.status;
    var noteId = state.note_id;
    var notesVersion = state.notes_version;
    var lastEntryId = 0;
    var lastSharesJson = '';
    var pollTimer = null;
    var saveTimer = null;
    var saving = false;
    var pendingSave = false;
    var ended = false;
    var esc = MeetingLive.esc;
    var $entries = $('.live-entries');

    function url(action) {
        return base_url + 'meeting_notes/' + action + '/' + noteId;
    }

    // --- Share panel ---
    function renderShares(shares) {
        var json = JSON.stringify(shares || []);
        if (json === lastSharesJson) return;
        lastSharesJson = json;
        if (!shares || !shares.length) {
            $('.live-shares').html('<p class="text-muted small mb-0">Not shared with anyone yet.</p>');
            return;
        }
        var html = '<ul class="list-unstyled mb-0">';
        shares.forEach(function(s){
            var presence = MeetingLive.presenceState(s);
            html += '<li class="d-flex align-items-center justify-content-between border-bottom py-1" data-share-id="' + esc(s.id) + '">' +
                '<span>' +
                    '<i class="fa fa-circle mr-1" title="' + presence.title + '" style="font-size:9px;color:' + presence.color + ';"></i>' +
                    esc(s.name || '(removed user)') +
                    ' <span class="badge badge-' + (s.participant_type === 'customer' ? 'success' : 'secondary') + '">' + (s.participant_type === 'customer' ? 'Client' : 'Developer') + '</span>' +
                '</span>' +
                '<span class="text-nowrap">' +
                    '<label class="small mb-0 mr-2" title="Can add entries"><input type="checkbox" class="share-toggle-contribute" ' + (s.can_contribute == 1 ? 'checked' : '') + '> <i class="fa fa-pen"></i></label>' +
                    '<button type="button" class="btn btn-xs btn-link text-danger p-0 share-remove" title="Stop sharing"><i class="fa fa-times"></i></button>' +
                '</span>' +
            '</li>';
        });
        $('.live-shares').html(html + '</ul>');
    }

    // --- Share modal: tick several client users / developers at once ---
    var shareable = state.shareable || { customers: [], developers: [] };
    var $shareModal = $('#modalShareMeeting');

    function renderShareable() {
        var groups = { customer: shareable.customers || [], developer: shareable.developers || [] };
        $shareModal.find('.share-people').each(function(){
            var type = $(this).data('type');
            var people = groups[type];
            if (!people.length) {
                $(this).html('<p class="text-muted small">' + (type === 'customer' ? 'No other client users for this customer.' : 'No other active developers.') + '</p>');
                return;
            }
            var html = '<ul class="list-group list-group-flush">';
            people.forEach(function(p){
                var key = type + '-' + p.id;
                html += '<li class="list-group-item px-1 py-1 d-flex align-items-center justify-content-between share-person" data-type="' + type + '" data-id="' + esc(p.id) + '" data-search="' + esc((p.name + ' ' + p.email).toLowerCase()) + '">' +
                    '<div class="form-check mb-0">' +
                        '<input type="checkbox" class="form-check-input share-pick" id="sp-' + key + '">' +
                        '<label class="form-check-label" for="sp-' + key + '">' + esc(p.name) + '<br><small class="text-muted">' + esc(p.email) + '</small></label>' +
                    '</div>' +
                    '<label class="small mb-0 text-nowrap" title="Can add entries"><input type="checkbox" class="share-pick-contribute"> <i class="fa fa-pen"></i></label>' +
                '</li>';
            });
            $(this).html(html + '</ul>');
        });
        updateShareCount();
    }

    function updateShareCount() {
        var n = $shareModal.find('.share-pick:checked').length;
        $shareModal.find('.share-save').prop('disabled', n === 0)
            .html('<i class="fa fa-user-plus"></i> ' + (n ? 'Share with ' + n + ' ' + (n === 1 ? 'person' : 'people') : 'Share'));
    }

    function applyShareState(response) {
        if (response.shares) { lastSharesJson = ''; renderShares(response.shares); }
        if (response.shareable) { shareable = response.shareable; renderShareable(); }
    }

    $('.share-open').on('click', function(){
        $shareModal.find('.share-search').val('');
        renderShareable();
        $shareModal.modal('show');
    });
    $shareModal.on('shown.bs.modal', function(){ $shareModal.find('.share-search').trigger('focus'); });

    $shareModal.on('input', '.share-search', function(){
        var term = $(this).val().toLowerCase().trim();
        $shareModal.find('.share-person').each(function(){
            $(this).toggleClass('d-none', term !== '' && $(this).data('search').indexOf(term) === -1);
        });
    });

    $shareModal.on('change', '.share-pick', updateShareCount);
    // Allowing entries implies sharing.
    $shareModal.on('change', '.share-pick-contribute', function(){
        if ($(this).is(':checked')) $(this).closest('.share-person').find('.share-pick').prop('checked', true);
        updateShareCount();
    });

    $shareModal.on('click', '.share-select-all', function(e){
        e.preventDefault();
        var $visible = $shareModal.find('.share-people[data-type="' + $(this).data('type') + '"] .share-person:not(.d-none) .share-pick');
        var allChecked = $visible.length && $visible.filter(':checked').length === $visible.length;
        $visible.prop('checked', !allChecked);
        updateShareCount();
    });

    $shareModal.on('click', '.share-save', function(){
        var people = [];
        $shareModal.find('.share-pick:checked').each(function(){
            var $li = $(this).closest('.share-person');
            people.push({ type: $li.data('type'), id: $li.data('id'), can_contribute: $li.find('.share-pick-contribute').is(':checked') ? 1 : 0 });
        });
        if (!people.length) return;
        $(this).prop('disabled', true);
        $.post(url('share_add'), { people: people }, function(response){
            applyShareState(response);
            if (response.result) {
                $shareModal.modal('hide');
                alertify.success('Shared with ' + response.added + ' ' + (response.added === 1 ? 'person' : 'people'));
            }
            if (response.reason) alertify.alert(response.reason);
        }, 'json').always(function(){ updateShareCount(); });
    });

    $('.live-shares').on('change', '.share-toggle-contribute', function(){
        var shareId = $(this).closest('li').data('share-id');
        $.post(url('share_update'), { share_id: shareId, can_contribute: $(this).is(':checked') ? 1 : 0 }, function(){
            alertify.success('Permission updated');
        }, 'json');
    });

    $('.live-shares').on('click', '.share-remove', function(){
        var shareId = $(this).closest('li').data('share-id');
        $.post(url('share_remove'), { share_id: shareId }, applyShareState, 'json');
    });

    // --- Autosave of the main notes ---
    function setSaveStatus(text) {
        $('.live-save-status').text(text);
    }

    function saveNotes() {
        if (ended) return;
        if (saving) { pendingSave = true; return; }
        saving = true;
        setSaveStatus('Saving...');
        $.ajax({
            url: url('live_save'),
            method: 'POST',
            dataType: 'json',
            showLoader: false,
            data: { notes: $('#notes').summernote('code') },
            success: function(response){
                if (response.result) {
                    notesVersion = response.notes_version;
                    setSaveStatus('Saved ' + response.saved_at);
                } else {
                    setSaveStatus('Not saved');
                }
            },
            error: function(){ setSaveStatus('Not saved (connection issue)'); },
            complete: function(){
                saving = false;
                if (pendingSave) { pendingSave = false; saveNotes(); }
            }
        });
    }

    // Planned meetings are edited with the Update button; autosave only runs once live.
    if (status === 'live') {
        $('#notes').on('summernote.change', function(){
            clearTimeout(saveTimer);
            saveTimer = setTimeout(saveNotes, 800);
        });
    }

    // --- Contributions (live only) ---
    if ($('.live-entry-input').length) {
        $('.live-entry-input').summernote(MeetingLive.editorOptions('Add an entry to the shared thread...'));
    }

    $('.live-entry-add').on('click', function(){
        var $btn = $(this).prop('disabled', true);
        $.post(url('entry_add'), { body: $('.live-entry-input').summernote('code') }, function(response){
            if (response.result) {
                $('.live-entry-input').summernote('reset');
                poll();
            } else {
                alertify.alert(response.reason || 'Could not add entry.');
            }
        }, 'json').always(function(){ $btn.prop('disabled', false); });
    });

    // --- Polling: new entries + who is watching ---
    function poll() {
        $.ajax({
            url: url('poll'),
            method: 'GET',
            dataType: 'json',
            showLoader: false,
            data: { since_version: notesVersion, since_entry_id: lastEntryId },
            success: function(response){
                if (!response.result) return;
                MeetingLive.renderParticipants($('.meeting-participants'), response.participants);
                if (status === 'planned') {
                    renderShares(response.shares);
                    // Went live (scheduled time reached): reload into live mode.
                    if (response.status !== 'planned') { clearInterval(pollTimer); location.reload(); }
                    return;
                }
                lastEntryId = MeetingLive.appendEntries($entries, response.entries, lastEntryId, state.viewer);
                renderShares(response.shares);
                if (response.status !== 'live') endedUi();
            }
        });
    }

    function endedUi() {
        if (ended) return;
        ended = true;
        clearInterval(pollTimer);
        $live.removeClass('alert-light').addClass('alert-secondary')
            .html('<i class="fa fa-flag-checkered"></i> Meeting ended. Shared users keep read-only access.');
        $('.share-open, .live-entry-add').prop('disabled', true);
        $shareModal.modal('hide');
    }

    $('.start-live-now').on('click', function(){
        bootbox.confirm({
            message: 'Start the meeting now? Invitees get a "meeting started" email and can follow your notes live.',
            buttons: {
                confirm: { label: 'Start Now', className: 'btn-danger' },
                cancel: { label: 'Cancel', className: 'btn-default' }
            },
            callback: function(result){
                if (!result) return;
                $.post(url('start_session'), {}, function(response){
                    if (response.result) location.reload();
                    else alertify.alert(response.reason || 'Could not start the meeting.');
                }, 'json');
            }
        });
    });

    $('.end-live-session').on('click', function(){
        bootbox.confirm({
            message: 'End the meeting? Shared users will no longer see live updates or be able to add entries, but they keep read-only access.',
            buttons: {
                confirm: { label: 'End Meeting', className: 'btn-danger' },
                cancel: { label: 'Cancel', className: 'btn-default' }
            },
            callback: function(result){
                if (!result) return;
                clearTimeout(saveTimer);
                // Save the latest text first, then close.
                $.post(url('live_save'), { notes: $('#notes').summernote('code') }).always(function(){
                    $.post(url('end_session'), {}, function(response){
                        if (response.result) endedUi();
                        else alertify.alert(response.reason || 'Could not end the meeting.');
                    }, 'json');
                });
            }
        });
    });

    // Initial render, then poll.
    renderShares(state.shares);
    if (status === 'live') lastEntryId = MeetingLive.appendEntries($entries, state.entries, 0);
    poll();
    pollTimer = setInterval(poll, status === 'live' ? MeetingLive.POLL_MS : 5000);
    MeetingLive.onVisible(function(){ if (!ended) poll(); });
});
