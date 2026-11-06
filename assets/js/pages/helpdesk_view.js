jQuery(function () {

    // Reply vs internal note toggle.
    $('#reply-mode a').on('click', function (e) {
        e.preventDefault();
        var internal = $(this).data('internal') == 1;
        $('#reply-mode a').removeClass('active');
        $(this).addClass('active');
        $('#is_internal').val(internal ? 1 : 0);
        $('#reply-form').toggleClass('border-warning', internal);
        hdEditor.setPlaceholder('#reply-body', internal ? 'Internal note — only visible to staff…' : 'Write your reply…');
        $('#reply-submit').html(internal ? '<i class="fa fa-sticky-note"></i> Add note' : '<i class="fa fa-paper-plane"></i> Send reply');
        $('.reply-only').toggle(!internal);
        if (internal) $('#reply-status').val('');
    });

    $('#reply-form').on('submit', function () {
        var hasFiles = $(this).find('input[type=file]').get(0).files.length > 0;
        if (hdEditor.isEmpty('#reply-body') && !hasFiles) {
            toastr.error('Please write a message.');
            return false;
        }
        $('#reply-submit').prop('disabled', true);
    });

    // Sidebar fields save immediately.
    function saveField($el, value) {
        var prev = $el.data('prev');
        $.ajax({
            url: base_url + 'helpdesk/updateField',
            method: 'POST',
            dataType: 'JSON',
            data: { uuid: HD_TICKET_UUID, field: $el.data('field'), value: value },
            success: function (r) {
                if (r.result) {
                    toastr.success('Saved');
                    // Reload so SLA, badges and history reflect the change.
                    setTimeout(function () { window.location.reload(); }, 500);
                } else {
                    toastr.error(r.reason || 'Unable to save');
                    $el.val(prev);
                }
            },
            error: function () {
                toastr.error('Unable to save');
                $el.val(prev);
            }
        });
    }
    $('.hd-field').each(function () { $(this).data('prev', $(this).val()); });
    $('.hd-field').on('change', function () { saveField($(this), $(this).val()); });
    $('#assign-me').on('click', function () { saveField($('#sel-assignee'), 'me'); });

    // Convert modal: developer multi-select.
    var selected = [];
    $('.convert-user-item').on('click', function () {
        var id = parseInt($(this).data('id'), 10);
        var i = selected.indexOf(id);
        if (i === -1) { selected.push(id); $(this).addClass('active'); }
        else { selected.splice(i, 1); $(this).removeClass('active'); }
        $('#convert_user_ids').val(JSON.stringify(selected));
    });

    // Attach-to-customer modal: only show the chosen customer's contacts.
    function filterLinkContacts() {
        var cid = $('#link_customer_id').val();
        var $sel = $('#link_customer_access_id');
        $sel.find('option[data-customer]').each(function () {
            var show = cid !== '' && String($(this).data('customer')) === cid;
            $(this).prop('hidden', !show).prop('disabled', !show);
        });
        if ($sel.find('option:selected').prop('disabled')) $sel.val('');
        $('#link-contact-wrap').toggle(cid !== '');
        var hasContact = $sel.val() !== '';
        $('#link-help-contact').toggle(hasContact);
        $('#link-help-keep').toggle(!hasContact);
    }
    $('#link_customer_id, #link_customer_access_id').on('change', filterLinkContacts);
    if ($('#link_customer_id').length) filterLinkContacts();

    // Jump to the reply anchor after posting.
    if (window.location.hash && $(window.location.hash).length) {
        $('html, body').animate({ scrollTop: $(window.location.hash).offset().top - 80 }, 300);
    }
});
