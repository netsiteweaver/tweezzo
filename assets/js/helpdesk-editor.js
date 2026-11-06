/**
 * Helpdesk form helpers (admin, customer/developer portals and the public form):
 *  1. Rich-text editor for messages.
 *  2. Attachment preview (further down).
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

/**
 * Attachment preview for <input type="file" class="hd-attachments"> (data-max-files, data-max-kb, data-types).
 * Shows thumbnails / file badges with size, lets users remove files, adds to the selection when
 * picking again, and blocks submitting files the server would reject.
 */
(function () {
    var inputs = document.querySelectorAll('input[type=file].hd-attachments');
    if (!inputs.length) return;

    // DataTransfer lets us rebuild the input's FileList (remove/accumulate). Older browsers: preview only.
    var canEdit = (function () {
        try { return !!new DataTransfer().items; } catch (e) { return false; }
    })();

    var css = document.createElement('style');
    css.textContent =
        '.hd-att-preview{display:flex;flex-wrap:wrap;gap:8px;margin-top:8px}' +
        '.hd-att-item{position:relative;width:120px;border:1px solid rgba(128,128,128,.35);border-radius:6px;padding:6px;font-size:12px;line-height:1.3;background:rgba(128,128,128,.06)}' +
        '.hd-att-item.hd-att-bad{border-color:#dc3545;background:rgba(220,53,69,.08)}' +
        '.hd-att-thumb{height:70px;display:flex;align-items:center;justify-content:center;border-radius:4px;overflow:hidden;background:rgba(128,128,128,.12);margin-bottom:4px}' +
        '.hd-att-thumb img{max-width:100%;max-height:100%;object-fit:cover}' +
        '.hd-att-ext{font-weight:700;font-size:14px;letter-spacing:.5px;opacity:.7;text-transform:uppercase}' +
        '.hd-att-name{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}' +
        '.hd-att-size{opacity:.7}' +
        '.hd-att-err{color:#dc3545;font-weight:600}' +
        '.hd-att-remove{position:absolute;top:-8px;right:-8px;width:22px;height:22px;border-radius:50%;border:0;background:#6c757d;color:#fff;font-size:14px;line-height:22px;padding:0;cursor:pointer}' +
        '.hd-att-remove:hover{background:#dc3545}';
    document.head.appendChild(css);

    function humanSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(0) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    function extOf(name) {
        var m = /\.([^.]+)$/.exec(name);
        return m ? m[1].toLowerCase() : '';
    }

    function fileKey(f) { return f.name + '|' + f.size + '|' + f.lastModified; }

    function setup(input) {
        var maxFiles = parseInt(input.getAttribute('data-max-files'), 10) || 5;
        var maxBytes = (parseInt(input.getAttribute('data-max-kb'), 10) || 10240) * 1024;
        var types = (input.getAttribute('data-types') || '').toLowerCase().split('|');
        var files = [];   // current selection
        var urls = [];    // object URLs to revoke on re-render

        var box = document.createElement('div');
        box.className = 'hd-att-preview';
        // Put the preview after the input's help text when it directly follows the input.
        var anchor = input.nextElementSibling && /form-text|text-muted/.test(input.nextElementSibling.className) ? input.nextElementSibling : input;
        anchor.parentNode.insertBefore(box, anchor.nextSibling);

        function problem(f, index) {
            if (types.length && types[0] !== '' && types.indexOf(extOf(f.name)) === -1) return 'File type not allowed';
            if (f.size > maxBytes) return 'Larger than ' + humanSize(maxBytes);
            if (index >= maxFiles) return 'Max ' + maxFiles + ' files';
            return '';
        }

        function syncInput() {
            if (!canEdit) return;
            var dt = new DataTransfer();
            files.forEach(function (f) { dt.items.add(f); });
            input.files = dt.files;
        }

        function render() {
            urls.forEach(function (u) { URL.revokeObjectURL(u); });
            urls = [];
            box.innerHTML = '';
            files.forEach(function (f, i) {
                var err = problem(f, i);
                var item = document.createElement('div');
                item.className = 'hd-att-item' + (err ? ' hd-att-bad' : '');
                item.title = f.name;

                var thumb = document.createElement('div');
                thumb.className = 'hd-att-thumb';
                if (/^image\//.test(f.type) && !err) {
                    var img = document.createElement('img');
                    var u = URL.createObjectURL(f);
                    urls.push(u);
                    img.src = u;
                    img.alt = '';
                    thumb.appendChild(img);
                } else {
                    var ext = document.createElement('span');
                    ext.className = 'hd-att-ext';
                    ext.textContent = extOf(f.name) || 'file';
                    thumb.appendChild(ext);
                }
                item.appendChild(thumb);

                var name = document.createElement('div');
                name.className = 'hd-att-name';
                name.textContent = f.name;
                item.appendChild(name);

                var meta = document.createElement('div');
                meta.className = err ? 'hd-att-err' : 'hd-att-size';
                meta.textContent = err || humanSize(f.size);
                item.appendChild(meta);

                if (canEdit) {
                    var rm = document.createElement('button');
                    rm.type = 'button';
                    rm.className = 'hd-att-remove';
                    rm.setAttribute('aria-label', 'Remove ' + f.name);
                    rm.innerHTML = '&times;';
                    rm.addEventListener('click', function () {
                        files.splice(i, 1);
                        syncInput();
                        render();
                    });
                    item.appendChild(rm);
                }
                box.appendChild(item);
            });
            box.style.display = files.length ? '' : 'none';
        }

        input.addEventListener('change', function () {
            var picked = Array.prototype.slice.call(input.files || []);
            if (canEdit) {
                // Picking again adds to the selection instead of replacing it.
                var seen = {};
                files.forEach(function (f) { seen[fileKey(f)] = true; });
                picked.forEach(function (f) { if (!seen[fileKey(f)]) files.push(f); });
                syncInput();
            } else {
                files = picked;
            }
            render();
        });

        input.hdAttachmentErrors = function () {
            var bad = [];
            files.forEach(function (f, i) { var e = problem(f, i); if (e) bad.push(f.name + ': ' + e); });
            return bad;
        };
        render();
    }

    Array.prototype.forEach.call(inputs, setup);

    // Capture phase: runs before the forms' own submit handlers (which disable the button).
    document.addEventListener('submit', function (e) {
        var form = e.target;
        var bad = [];
        Array.prototype.forEach.call(form.querySelectorAll('input[type=file].hd-attachments'), function (inp) {
            if (inp.hdAttachmentErrors) bad = bad.concat(inp.hdAttachmentErrors());
        });
        if (bad.length) {
            e.preventDefault();
            e.stopPropagation();
            alert('Please remove these files before sending:\n\n' + bad.join('\n'));
        }
    }, true);
})();
