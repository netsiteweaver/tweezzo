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

    // Rows removed by the shared deleteAjax handler: reload so counters stay right.
    $(document).ajaxSuccess(function (e, xhr, settings) {
        if (settings.url && settings.url.indexOf('helpdesk/delete') !== -1) {
            setTimeout(function () { window.location.reload(); }, 800);
        }
    });
});
