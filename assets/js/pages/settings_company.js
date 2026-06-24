$(function () {
    var $input = $('#company-logo-input');
    var $previewWrap = $('#company-logo-preview-wrap');
    var $preview = $('#company-logo-preview');
    var $emptyHint = $('#company-logo-empty-hint');

    if (!$input.length) {
        return;
    }

    var initialSrc = $preview.attr('src') || '';

    $input.on('change', function () {
        var file = this.files && this.files[0];

        if (!file) {
            if (initialSrc) {
                $preview.attr('src', initialSrc);
                $previewWrap.removeClass('d-none');
                $emptyHint.addClass('d-none');
            } else {
                $preview.attr('src', '');
                $previewWrap.addClass('d-none');
                $emptyHint.removeClass('d-none');
            }
            return;
        }

        if (!file.type.match(/^image\//)) {
            alert('Please select an image file (PNG, JPG, GIF, or WebP).');
            $input.val('');
            return;
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            $preview.attr('src', e.target.result);
            $previewWrap.removeClass('d-none');
            $emptyHint.addClass('d-none');
        };
        reader.readAsDataURL(file);
    });
});
