/**
 * Per-user dashboard layout: drag blocks to reorder, click the eye to hide one.
 * Both are saved against the logged-in user as soon as they change.
 */
$(function () {

    var $grid = $('#dashboard-blocks');
    if (!$grid.length) return;

    var $toggle = $('#dashboard-customise-toggle');
    var $reset  = $('#dashboard-customise-reset');
    var $status = $('#dashboard-customise-status');
    var statusTimer = null;

    function say(message, isError) {
        $status.text(message).css('color', isError ? '#dd4b39' : '');
        clearTimeout(statusTimer);
        statusTimer = setTimeout(function () { $status.text(''); }, 3000);
    }

    function currentLayout() {
        var order = [], hidden = [];
        $grid.children('.dashboard-block').each(function () {
            var key = $(this).data('block-key');
            order.push(key);
            if ($(this).hasClass('is-hidden')) hidden.push(key);
        });
        return { order: order, hidden: hidden };
    }

    function save() {
        var layout = currentLayout();
        $.post($grid.data('save-url'), {
            order:  JSON.stringify(layout.order),
            hidden: JSON.stringify(layout.hidden)
        }, null, 'json')
        .done(function (response) {
            say(response && response.result ? 'Layout saved.' : 'Could not save layout.',
                !(response && response.result));
        })
        .fail(function () { say('Could not save layout.', true); });
    }

    $grid.sortable({
        items: '> .dashboard-block',
        cancel: '.dashboard-block-toggle',
        placeholder: 'dashboard-block-placeholder',
        forcePlaceholderSize: true,
        tolerance: 'pointer',
        opacity: 0.8,
        disabled: true,
        start: function (event, ui) {
            // Match the placeholder to the dragged block's bootstrap width.
            ui.placeholder.attr('class',
                ui.item.attr('class')
                    .replace('dashboard-block', 'dashboard-block-placeholder')
                    .replace('is-hidden', ''));
            ui.placeholder.height(ui.item.outerHeight());
        },
        update: save
    });

    $toggle.on('click', function () {
        var editing = $grid.toggleClass('edit-mode').hasClass('edit-mode');
        $grid.sortable(editing ? 'enable' : 'disable');
        $reset.toggle(editing);
        $toggle.html(editing
            ? '<i class="fas fa-check"></i> Done'
            : '<i class="fas fa-th-large"></i> Customise');
        if (editing) say('Drag blocks to reorder, or use the eye to hide one.');
    });

    $grid.on('click', '.dashboard-block-toggle', function (e) {
        e.preventDefault();
        var $block = $(this).closest('.dashboard-block');
        var nowHidden = $block.toggleClass('is-hidden').hasClass('is-hidden');
        $(this).attr('title', nowHidden ? 'Show this block' : 'Hide this block')
               .find('i').attr('class', nowHidden ? 'fas fa-eye' : 'fas fa-eye-slash');
        save();
    });

    $reset.on('click', function () {
        if (!confirm('Reset your dashboard back to the default layout?')) return;
        $.post($grid.data('reset-url'), {}, null, 'json')
        .done(function () { window.location.reload(); })
        .fail(function () { say('Could not reset layout.', true); });
    });

});
