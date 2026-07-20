jQuery(function ($) {
    var format = 'YYYY-MM-DD HH:mm';
    var $start = $('#start_time');
    var $finish = $('#finish_time');
    var $duration = $('#timesheet-duration-preview');

    function parseVal($el) {
        var raw = $.trim($el.val() || '');
        if (!raw) {
            return null;
        }
        var m = moment(raw, format, true);
        return m.isValid() ? m : null;
    }

    function updateDuration() {
        var start = parseVal($start);
        var finish = parseVal($finish);
        if (!start || !finish) {
            $duration.text('—');
            return;
        }
        var mins = finish.diff(start, 'minutes');
        if (mins < 0) {
            $duration.html('<span class="text-danger">Finish must be after start</span>');
            return;
        }
        var hours = Math.floor(mins / 60);
        var rem = mins % 60;
        $duration.text(
            (hours < 10 ? '0' : '') + hours + ':' +
            (rem < 10 ? '0' : '') + rem +
            ' (' + mins + ' min)'
        );
    }

    function initPicker($el) {
        var initial = parseVal($el) || moment();
        $el.daterangepicker({
            singleDatePicker: true,
            timePicker: true,
            timePicker24Hour: true,
            timePickerIncrement: 1,
            showDropdowns: true,
            autoUpdateInput: true,
            locale: {
                format: format,
                applyLabel: 'Apply',
                cancelLabel: 'Clear'
            },
            startDate: initial,
            drops: 'auto'
        });

        $el.on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format(format));
            updateDuration();
        });

        $el.on('cancel.daterangepicker', function () {
            $(this).val('');
            updateDuration();
        });

        $el.on('change blur', updateDuration);
    }

    if ($start.length && $finish.length && typeof $.fn.daterangepicker === 'function' && typeof moment === 'function') {
        initPicker($start);
        initPicker($finish);
        updateDuration();
    }

    $('form').on('submit', function () {
        var start = parseVal($start);
        var finish = parseVal($finish);
        if (!start || !finish) {
            alertify.error('Please select both start and finish times.');
            return false;
        }
        if (!finish.isAfter(start)) {
            alertify.error('Finish time must be after start time.');
            return false;
        }
        // Normalize values before post
        $start.val(start.format(format));
        $finish.val(finish.format(format));
        return true;
    });
});
