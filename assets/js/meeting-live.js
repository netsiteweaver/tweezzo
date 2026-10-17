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

    var showSeconds = false;

    /** Message times as HH:MM, or HH:MM:SS after showTimeSeconds(true) (back office). */
    function formatTime(datetime) {
        if (!datetime) return '';
        var d = new Date(String(datetime).replace(' ', 'T'));
        if (isNaN(d.getTime())) return esc(datetime);
        var pad = function (n) { return ('0' + n).slice(-2); };
        return pad(d.getHours()) + ':' + pad(d.getMinutes()) + (showSeconds ? ':' + pad(d.getSeconds()) : '');
    }

    function showTimeSeconds(on) { showSeconds = !!on; }

    function renderEntry(entry) {
        var color = AUTHOR_COLORS[entry.author_type] || '#6c757d';
        return '<div class="meeting-entry mb-2 p-2 rounded" data-id="' + esc(entry.id) + '" data-author="' + esc(entry.author_name || 'Unknown') + '" data-role="' + esc(AUTHOR_LABELS[entry.author_type] || entry.author_type) + '" style="border-left:4px solid ' + color + ';background:rgba(127,127,127,.06);">' +
            '<div class="small text-muted mb-1 meeting-entry-meta">' +
                '<strong style="color:' + color + ';">' + esc(entry.author_name || 'Unknown') + '</strong> ' +
                '<span>(' + esc(AUTHOR_LABELS[entry.author_type] || entry.author_type) + ')</span> &middot; ' +
                '<span title="' + esc(entry.created_on) + '">' + formatTime(entry.created_on) + '</span>' +
            '</div>' +
            '<div class="meeting-entry-body">' + entry.body + '</div>' +
        '</div>';
    }

    /**
     * Adds entries not already shown, newest first; returns the highest entry id seen.
     * When viewer ({type, id}) is given, each new entry written by someone else is toasted
     * and counted as unread by a minimised floating panel.
     */
    function appendEntries($container, entries, lastId, viewer) {
        var maxId = lastId || 0;
        var fromOthers = 0;
        $container.find('.meeting-entries-empty').remove();
        (entries || []).forEach(function (entry) {
            var id = parseInt(entry.id, 10);
            if (id <= maxId || $container.find('.meeting-entry[data-id="' + id + '"]').length) return;
            $container.prepend(renderEntry(entry));
            maxId = Math.max(maxId, id);
            if (viewer && !(entry.author_type === viewer.type && String(entry.author_id) === String(viewer.id))) {
                fromOthers++;
                toastEntry(entry, $container.find('.meeting-entry[data-id="' + id + '"]'));
            }
        });
        if (!$container.find('.meeting-entry').length) {
            $container.html('<p class="text-muted meeting-entries-empty mb-0">No messages yet.</p>');
        }
        $container.trigger('meeting:entries', [fromOthers]);
        return maxId;
    }

    /** "New message" toast; clicking it scrolls to the message. */
    function toastEntry(entry, $el) {
        if (typeof alertify === 'undefined') return;
        var text = $('<div>').html(entry.body).text().trim();
        if (text.length > 120) text = text.slice(0, 117) + '...';
        var color = AUTHOR_COLORS[entry.author_type] || '#6c757d';
        var toast = alertify.notify(
            '<div style="text-align:left;"><strong>New message</strong> from <span style="color:' + color + ';font-weight:600;">' + esc(entry.author_name || 'Unknown') + '</span>' +
            ' <small>(' + esc(AUTHOR_LABELS[entry.author_type] || entry.author_type) + ')</small><br><small>' + esc(text) + '</small></div>',
            'message', 6
        );
        toast.callback = function (isClicked) {
            if (isClicked && $el && $el.length) {
                $el.closest('.meeting-float').trigger('meeting:expand');
                $el[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                $el.css('box-shadow', '0 0 0 3px rgba(255,193,7,.6)');
                setTimeout(function () { $el.css('box-shadow', ''); }, 2000);
            }
        };
    }

    // ---- Floating contributions panel ----

    var FLOAT_CSS =
        '.meeting-float{position:fixed;right:16px;bottom:16px;width:380px;max-width:calc(100vw - 32px);z-index:1035;margin:0;' +
            'display:flex;flex-direction:column;max-height:65vh;box-shadow:0 .5rem 1.5rem rgba(0,0,0,.25);}' +
        '.meeting-float .meeting-float-header{display:flex;align-items:center;justify-content:space-between;cursor:pointer;user-select:none;padding:.5rem .75rem;}' +
        '.meeting-float .meeting-float-header::after{display:none;}' +
        '.meeting-float .meeting-float-body{overflow-y:auto;padding:.75rem;flex:1 1 auto;}' +
        '.meeting-float .meeting-float-form{margin-bottom:.75rem;}' +
        '.meeting-float .meeting-float-form .note-editor{margin-bottom:0;}' +
        '.meeting-float.is-collapsed{max-height:none;}' +
        '.meeting-float.is-collapsed .meeting-float-body{display:none;}' +
        '.meeting-float .meeting-float-toggle{font-size:1rem;line-height:1;text-decoration:none;}' +
        '@media (max-width:575.98px){.meeting-float{right:8px;left:8px;bottom:8px;width:auto;max-width:none;}}';

    /** Space between the page content and the open messages panel (portals). */
    var RESERVE_GUTTER = 32;

    function storageGet(key) { try { return window.localStorage.getItem(key); } catch (e) { return null; } }
    function storageSet(key, value) { try { window.localStorage.setItem(key, value); } catch (e) { /* private mode */ } }

    /**
     * Wires a .meeting-float panel: minimise/expand (remembered per meeting; phones start minimised),
     * total entry count, and an unread badge for entries from others that arrive while minimised.
     * options.reserve: element whose right margin makes room for the open panel on wide screens,
     * so the page content stops before it instead of running underneath.
     */
    function floatPanel($panel, options) {
        if (!$panel.length) return;
        options = options || {};
        var $reserve = options.reserve && options.reserve.length ? options.reserve : null;
        var wideScreen = window.matchMedia('(min-width: 992px)');
        function updateReserve() {
            if (!$reserve) return;
            var open = !$panel.hasClass('is-collapsed') && wideScreen.matches;
            // Room = panel width + its 16px gap from the edge + the gutter, minus the space already right of the content.
            var room = 0;
            if (open) {
                var contentRight = $reserve[0].getBoundingClientRect().right + (parseFloat($reserve.css('margin-right')) || 0);
                room = Math.max(0, $panel.outerWidth() + 16 + RESERVE_GUTTER - (window.innerWidth - contentRight));
            }
            $reserve.css('margin-right', room ? room + 'px' : '');
        }
        $(window).on('resize', updateReserve);
        if (!document.getElementById('meeting-float-css')) {
            $('<style id="meeting-float-css">').text(FLOAT_CSS).appendTo('head');
        }
        // Toasts go top-right so they don't sit on the panel.
        if (typeof alertify !== 'undefined' && alertify.set) alertify.set('notifier', 'position', 'top-right');

        var storeKey = 'meetingFloatCollapsed:' + ($panel.data('float-key') || 'default');
        var saved = storageGet(storeKey);
        var unread = 0;

        function setCollapsed(collapsed, remember) {
            $panel.toggleClass('is-collapsed', collapsed);
            $panel.find('.meeting-float-toggle').html(collapsed ? '&#9650;' : '&#9660;')
                .attr('aria-label', collapsed ? 'Expand messages' : 'Minimise messages');
            if (!collapsed) { unread = 0; renderUnread(); }
            if (remember) storageSet(storeKey, collapsed ? '1' : '0');
            updateReserve();
        }
        function renderUnread() {
            $panel.find('.meeting-float-unread').text(unread ? unread + ' new' : '').toggleClass('d-none', !unread);
        }

        $panel.on('click', '.meeting-float-header', function () {
            setCollapsed(!$panel.hasClass('is-collapsed'), true);
        });
        $panel.on('meeting:expand', function () { setCollapsed(false, true); });
        $panel.on('meeting:entries', function (e, fromOthers) {
            var total = $panel.find('.meeting-entry').length;
            $panel.find('.meeting-float-total').text(total ? '(' + total + ')' : '');
            if (fromOthers && $panel.hasClass('is-collapsed')) { unread += fromOthers; renderUnread(); }
        });

        setCollapsed(saved === null ? window.matchMedia('(max-width: 575.98px)').matches : saved === '1', false);
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

    /**
     * Small Summernote config for message boxes. With onSend, Enter sends and Shift+Enter adds a new line
     * (ignored while an IME is composing, e.g. typing with an accent picker).
     */
    function editorOptions(placeholder, onSend) {
        var options = {
            placeholder: placeholder || 'Write a message...',
            minHeight: 60,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol']],
                ['insert', ['link']]
            ]
        };
        if (onSend) {
            options.callbacks = {
                onKeydown: function (e) {
                    var ev = e.originalEvent || e;
                    if ((ev.key === 'Enter' || ev.keyCode === 13) && !ev.shiftKey && !ev.ctrlKey && !ev.altKey && !ev.metaKey && !ev.isComposing) {
                        e.preventDefault();
                        onSend();
                    }
                }
            };
        }
        return options;
    }

    /**
     * Sends the message in a Summernote box through $btn (which carries the busy state),
     * keeping the cursor in the box. Empty messages are ignored.
     */
    function sendMessage($input, $btn, url, field, onDone) {
        if ($btn.prop('disabled')) return;
        if ($input.summernote('isEmpty')) { $input.summernote('focus'); return; }
        $btn.prop('disabled', true);
        var data = {};
        data[field] = $input.summernote('code');
        $.post(url, data, function (response) {
            if (response.result) {
                $input.summernote('reset');
                $input.summernote('focus');
                if (onDone) onDone();
            } else if (typeof alertify !== 'undefined') {
                alertify.alert(response.reason || 'Could not send your message.');
            }
        }, 'json').always(function () { $btn.prop('disabled', false); });
    }

    return { esc: esc, renderEntry: renderEntry, appendEntries: appendEntries, renderParticipants: renderParticipants, floatPanel: floatPanel, showTimeSeconds: showTimeSeconds, presenceState: presenceState, onVisible: onVisible, editorOptions: editorOptions, sendMessage: sendMessage, POLL_MS: 2000 };
})();
