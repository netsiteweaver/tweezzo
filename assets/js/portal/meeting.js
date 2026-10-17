/**
 * Portal (customer + developer) view of a shared meeting note.
 * Follows the note live by polling and lets permitted users add entries.
 */
jQuery(function(){
    var $root = $('#portal-meeting');
    if (!$root.length || typeof MeetingLive === 'undefined') return;

    var noteRef = $root.data('note-ref');
    var portal = $root.data('portal');
    var notesVersion = parseInt($root.data('notes-version'), 10) || 0;
    var status = $root.data('status');
    var canContribute = $root.data('can-contribute') == 1;
    var viewer = { type: $root.data('viewer-type'), id: $root.data('viewer-id') };
    var $entries = $root.find('.meeting-entries');
    var $form = $root.find('.meeting-entry-form');
    var $input = $root.find('.meeting-entry-input');
    var editorReady = false;
    var pollTimer = null;
    var lastEntryId = MeetingLive.appendEntries($entries, JSON.parse($('#portal-meeting-entries').text() || '[]'), 0);

    function url(action) {
        return base_url + 'portal/' + portal + '/' + action + '/' + noteRef;
    }

    function setCanContribute(allowed) {
        canContribute = allowed;
        $form.toggleClass('d-none', !allowed);
        if (allowed && !editorReady) {
            $input.summernote(MeetingLive.editorOptions());
            editorReady = true;
        }
    }

    function stopPolling() {
        clearInterval(pollTimer);
        pollTimer = null;
    }

    function showEnded() {
        status = 'closed';
        stopPolling();
        $root.find('.meeting-participants-row').remove();
        setCanContribute(false);
        $root.find('.meeting-status-badge').empty();
        $root.find('.meeting-ended-banner').removeClass('d-none');
    }

    function poll() {
        $.ajax({
            url: url('meetingPoll'),
            method: 'GET',
            dataType: 'json',
            showLoader: false,
            data: { since_version: notesVersion, since_entry_id: lastEntryId },
            success: function(response){
                if (!response.result) {
                    if (response.removed) {
                        stopPolling();
                        setCanContribute(false);
                        $root.find('.meeting-status-badge').empty();
                        $root.find('.meeting-removed-banner').removeClass('d-none');
                        $root.find('.meeting-participants-row').remove();
                    }
                    return;
                }
                if (response.participants) MeetingLive.renderParticipants($root.find('.meeting-participants'), response.participants);
                if (typeof response.host_badge !== 'undefined') $root.find('.meeting-host-badge').html(response.host_badge);
                if (status === 'planned') {
                    // Scheduled time reached (or started early): reload into live mode.
                    if (response.status !== 'planned') { stopPolling(); location.reload(); }
                    return;
                }
                if (typeof response.notes !== 'undefined') {
                    // Keep the reader's scroll position while the notes grow.
                    var scrollY = window.scrollY;
                    $root.find('.meeting-notes-body').html(response.notes);
                    window.scrollTo(0, scrollY);
                    notesVersion = response.notes_version;
                    var now = new Date();
                    $root.find('.meeting-updated').text('Updated ' + ('0' + now.getHours()).slice(-2) + ':' + ('0' + now.getMinutes()).slice(-2) + ':' + ('0' + now.getSeconds()).slice(-2));
                }
                lastEntryId = MeetingLive.appendEntries($entries, response.entries, lastEntryId, viewer);
                if (response.can_contribute !== canContribute) setCanContribute(response.can_contribute);
                if (response.status !== 'live') showEnded();
            }
        });
    }

    $root.on('click', '.meeting-entry-add', function(){
        var $btn = $(this).prop('disabled', true);
        $.post(url('meetingAddEntry'), { body: $input.summernote('code') }, function(response){
            if (response.result) {
                $input.summernote('reset');
                poll();
            } else {
                alertify.alert(response.reason || 'Could not add your entry.');
            }
        }, 'json').always(function(){ $btn.prop('disabled', false); });
    });

    setCanContribute(canContribute);
    if (status === 'live') {
        poll();
        pollTimer = setInterval(poll, MeetingLive.POLL_MS);
    } else if (status === 'planned') {
        poll();
        pollTimer = setInterval(poll, 10000);
    }
    MeetingLive.onVisible(function(){ if (pollTimer) poll(); });
});
