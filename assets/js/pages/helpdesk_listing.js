jQuery(function () {

    function applyFilters() {
        var params = new URLSearchParams();
        $('#hd-filters [data-key]').each(function () {
            var key = $(this).data('key');
            var val = $(this).is(':checkbox') ? ($(this).is(':checked') ? '1' : '') : $.trim($(this).val() || '');
            if (val !== '') params.set(key, val);
        });
        var display = new URLSearchParams(window.location.search).get('display');
        if (display) params.set('display', display);
        window.location.href = base_url + 'helpdesk/listing?' + params.toString();
    }

    $('.hd-filter').on('change', applyFilters);
    $('#f_search_btn').on('click', applyFilters);
    $('#f_search').on('keydown', function (e) {
        if (e.key === 'Enter') applyFilters();
    });

    // Relative SLA countdown ("in 3h", "2h overdue").
    function rel(ms) {
        var abs = Math.abs(ms), m = Math.round(abs / 60000);
        if (m < 60) return m + 'm';
        var h = Math.floor(m / 60);
        if (h < 48) return h + 'h' + (m % 60 ? ' ' + (m % 60) + 'm' : '');
        return Math.floor(h / 24) + 'd';
    }
    function tick() {
        $('.hd-due[data-due]').each(function () {
            var diff = new Date($(this).data('due')).getTime() - Date.now();
            $(this).text(diff < 0 ? rel(diff) + ' overdue' : 'in ' + rel(diff)).toggleClass('hd-due-over', diff < 0);
        });
    }
    tick();
    setInterval(tick, 60000);

    // Delete in place: remove the row only once the server confirms, then refresh the counters.
    $('body').on('click', '.hd-delete', function (e) {
        e.preventDefault();
        var $btn = $(this);
        bootbox.confirm({
            message: $btn.data('message') || 'Delete this ticket?',
            buttons: {
                confirm: { label: 'Yes', className: 'btn-danger' },
                cancel: { label: 'No', className: 'btn-primary' }
            },
            callback: function (ok) {
                if (!ok) return;
                $btn.prop('disabled', true);
                $.ajax({
                    url: $btn.data('url'),
                    method: 'POST',
                    dataType: 'JSON',
                    data: { uuid: $btn.data('uuid') }
                }).done(function (r) {
                    if (!r || !r.result) {
                        toastr.error((r && r.reason) || 'Unable to delete the ticket.');
                        $btn.prop('disabled', false);
                        return;
                    }
                    var $tbody = $btn.closest('tbody');
                    $btn.closest('tr').fadeOut(250, function () {
                        $(this).remove();
                        if (!$tbody.children('tr').length) {
                            $tbody.closest('table').hide();
                            $('#hd-empty-after-delete').show();
                        }
                    });
                    if (r.counts) {
                        $.each(r.counts, function (key, val) {
                            $('[data-hd-count="' + key + '"]').text(val);
                        });
                    }
                    var $total = $('#hd-total');
                    var total = Math.max(0, (parseInt($total.data('total'), 10) || 1) - 1);
                    $total.data('total', total).text(total + ' ticket' + (total === 1 ? '' : 's'));
                    toastr.warning('Ticket deleted.');
                }).fail(function () {
                    toastr.error('Unable to delete the ticket.');
                    $btn.prop('disabled', false);
                });
            }
        });
        return false;
    });
});
