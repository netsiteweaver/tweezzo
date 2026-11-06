/**
 * Rich-text editor for helpdesk messages (admin, customer/developer portals and the public form).
 * Turns every <textarea class="hd-editor"> into a Summernote editor with a small toolbar.
 * The server sanitizes the HTML; images go through the attachments field, not the editor.
 * Without Summernote (script blocked), the plain textarea still works: the server keeps line breaks.
 * Forms validate with hdEditor.isEmpty(el) in their own submit handler.
 * Load with `defer` so it runs after the layout's jQuery/Summernote scripts.
 */
(function () {
    var $ = window.jQuery;
    var hasEditor = !!($ && $.fn && $.fn.summernote);

    function isEmpty(el) {
        var $el = $(el);
        if (hasEditor && $el.next('.note-editor').length) {
            return $el.summernote('isEmpty') || $.trim($('<div>').html($el.summernote('code')).text()) === '';
        }
        return $.trim($el.val()) === '';
    }

    function focus(el) {
        var $el = $(el);
        if (hasEditor && $el.next('.note-editor').length) $el.summernote('focus');
        else $el.trigger('focus');
    }

    function setPlaceholder(el, text) {
        var $el = $(el);
        $el.attr('placeholder', text);
        $el.next('.note-editor').find('.note-placeholder').text(text);
    }

    function reset(el) {
        var $el = $(el);
        if (hasEditor && $el.next('.note-editor').length) $el.summernote('reset');
        else $el.val('');
    }

    window.hdEditor = { isEmpty: isEmpty, focus: focus, setPlaceholder: setPlaceholder, reset: reset };

    if (!$) return;

    // Bootstrap 5 attaches its jQuery plugins ($.fn.tooltip, used by summernote-bs5) on
    // DOMContentLoaded, so initialise on jQuery ready, which runs after it.
    $(function () {
        if (hasEditor && !$.fn.tooltip) {
            $.fn.tooltip = function () { return this; }; // toolbar tooltips are cosmetic
        }
        $('textarea.hd-editor').each(function () {
            var $t = $(this);
            // A hidden required textarea blocks submission silently; forms check hdEditor.isEmpty() instead.
            $t.prop('required', false);
            if (!hasEditor) return;
            $t.summernote({
                height: parseInt($t.data('height'), 10) || 180,
                placeholder: $t.attr('placeholder') || '',
                disableDragAndDrop: true,
                dialogsInBody: true,
                styleTags: ['p', 'blockquote', 'pre', 'h4'],
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                    ['para', ['ul', 'ol']],
                    ['insert', ['link']]
                ],
                callbacks: {
                    // Pasted/dropped images would be inlined as base64; ask for an attachment instead.
                    onImageUpload: function () {
                        alert('Please add images using the Attachments field below the message.');
                    }
                }
            });
        });
    });
})();
