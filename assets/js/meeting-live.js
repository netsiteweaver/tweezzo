/**
 * Shared helpers for live meeting notes (back office + customer/developer portals).
 * Entry bodies are sanitised server-side (Meeting_note_model::sanitize_entry_html).
 */
var MeetingLive = (function () {
    var AUTHOR_LABELS = { admin: 'Team', customer: 'Client', developer: 'Developer' };
    var AUTHOR_COLORS = { admin: '#17a2b8', customer: '#28a745', developer: '#6f42c1' };

    function esc(value) {
        return $('<div>').text(value == null ? '' : String(value)).html();
    }

    function formatTime(datetime) {
        if (!datetime) return '';
        var d = new Date(String(datetime).replace(' ', 'T'));
        if (isNaN(d.getTime())) return esc(datetime);
        return ('0' + d.getHours()).slice(-2) + ':' + ('0' + d.getMinutes()).slice(-2);
    }

    function renderEntry(entry) {
        var color = AUTHOR_COLORS[entry.author_type] || '#6c757d';
        return '<div class="meeting-entry mb-2 p-2 rounded" data-id="' + esc(entry.id) + '" style="border-left:4px solid ' + color + ';background:rgba(127,127,127,.06);">' +
            '<div class="small text-muted mb-1">' +
                '<strong style="color:' + color + ';">' + esc(entry.author_name || 'Unknown') + '</strong> ' +
                '<span>(' + esc(AUTHOR_LABELS[entry.author_type] || entry.author_type) + ')</span> &middot; ' +
                '<span title="' + esc(entry.created_on) + '">' + formatTime(entry.created_on) + '</span>' +
            '</div>' +
            '<div class="meeting-entry-body">' + entry.body + '</div>' +
        '</div>';
    }

    /**
     * Appends entries not already shown; returns the highest entry id seen.
     * When viewer ({type, id}) is given, each new entry written by someone else is toasted.
     */
    function appendEntries($container, entries, lastId, viewer) {
        var maxId = lastId || 0;
        $container.find('.meeting-entries-empty').remove();
        (entries || []).forEach(function (entry) {
            var id = parseInt(entry.id, 10);
            if (id <= maxId || $container.find('.meeting-entry[data-id="' + id + '"]').length) return;
            $container.append(renderEntry(entry));
            maxId = Math.max(maxId, id);
            if (viewer && !(entry.author_type === viewer.type && String(entry.author_id) === String(viewer.id))) {
                toastEntry(entry, $container.find('.meeting-entry[data-id="' + id + '"]'));
            }
        });
        if (!$container.find('.meeting-entry').length) {
            $container.html('<p class="text-muted meeting-entries-empty mb-0">No contributions yet.</p>');
        }
        return maxId;
    }

    /** "New contribution" toast; clicking it scrolls to the entry. */
    function toastEntry(entry, $el) {
        if (typeof alertify === 'undefined') return;
        var text = $('<div>').html(entry.body).text().trim();
        if (text.length > 120) text = text.slice(0, 117) + '...';
        var color = AUTHOR_COLORS[entry.author_type] || '#6c757d';
        var toast = alertify.notify(
            '<div style="text-align:left;"><strong>New contribution</strong> from <span style="color:' + color + ';font-weight:600;">' + esc(entry.author_name || 'Unknown') + '</span>' +
            ' <small>(' + esc(AUTHOR_LABELS[entry.author_type] || entry.author_type) + ')</small><br><small>' + esc(text) + '</small></div>',
            'message', 6
        );
        toast.callback = function (isClicked) {
            if (isClicked && $el && $el.length) {
                $el[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                $el.css('box-shadow', '0 0 0 3px rgba(255,193,7,.6)');
                setTimeout(function () { $el.css('box-shadow', ''); }, 2000);
            }
        };
    }

    var ROLE_LABELS = { team: 'Team', customer: 'Client', developer: 'Developer' };
    var ROLE_COLORS = { team: '#17a2b8', customer: '#28a745', developer: '#6f42c1' };

    /** online = viewing now, away = page open in a background tab (browsers throttle its polling), else not on the page. */
    function presenceState(p) {
        if (p.online == 1 || p.online === true) return { key: 'online', color: '#28a745', title: 'Viewing now' };
        if (p.away == 1 || p.away === true) return { key: 'away', color: '#ffc107', title: 'Page open in the background' };
        return { key: 'offline', color: '#adb5bd', title: 'Not on the page' };
    }

    /** Re-poll as soon as a background tab is brought back, instead of waiting for the throttled timer. */
    function onVisible(callback) {
        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'visible') callback();
        });
    }

    /** Participant chips with a presence dot, e.g. "● Jane (Client)". */
    function renderParticipants($container, participants) {
        if (!participants || !participants.length) {
            $container.html('<span class="text-muted small">Nobody yet.</span>');
            return;
        }
        var here = participants.filter(function (p) { return p.online || p.away; }).length;
        var html = '<span class="small text-muted me-2 mr-2">' + here + ' of ' + participants.length + ' online</span>';
        participants.forEach(function (p) {
            var color = ROLE_COLORS[p.role] || '#6c757d';
            var state = presenceState(p);
            html += '<span class="d-inline-block border rounded-pill px-2 py-0 me-1 mr-1 mb-1 small" style="' + (state.key === 'offline' ? 'opacity:.55;' : '') + '" title="' + state.title + '">' +
                '<span style="display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:5px;background:' + state.color + ';"></span>' +
                esc(p.name) + (p.me ? ' (you)' : '') +
                ' <span style="color:' + color + ';">' + esc(ROLE_LABELS[p.role] || p.role) + '</span>' +
            '</span>';
        });
        $container.html(html);
    }

    /** Small Summernote config for entry boxes. */
    function editorOptions(placeholder) {
        return {
            placeholder: placeholder || 'Write your entry...',
            minHeight: 80,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol']],
                ['insert', ['link']]
            ]
        };
    }

    return { esc: esc, renderEntry: renderEntry, appendEntries: appendEntries, renderParticipants: renderParticipants, presenceState: presenceState, onVisible: onVisible, editorOptions: editorOptions, POLL_MS: 2000 };
})();
