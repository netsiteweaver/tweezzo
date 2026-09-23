/**
 * Preview + square crop for the user photo field.
 *
 * Binds to any <input type="file" data-photo-crop>. The cropped result is put
 * back into that same input via DataTransfer, so the form keeps posting a plain
 * multipart file and no server-side change is needed.
 *
 * The output keeps the source MIME type and filename, because CodeIgniter's
 * Upload library validates the extension against the file's real MIME type.
 */
(function () {
    'use strict';

    var OUTPUT_SIZE = 512;                              // square avatar
    var SAFE_TYPES  = ['image/jpeg', 'image/png'];

    $(function () {

        var $inputs = $('input[type=file][data-photo-crop]');
        var $modal  = $('#photoCropModal');
        if (!$inputs.length || !$modal.length) return;

        if (typeof Cropper === 'undefined') {
            // Without the library the field still works as a plain file input.
            return;
        }

        var $image   = $('#photoCropImage');
        var cropper  = null;
        var current  = null;   // the input being edited
        var original = null;   // the file as the user picked it
        var objectUrls = [];

        function trackUrl(url) {
            objectUrls.push(url);
            return url;
        }

        function releaseUrls() {
            objectUrls.forEach(function (url) { URL.revokeObjectURL(url); });
            objectUrls = [];
        }

        function partsFor($input) {
            // The preview block is the one following this input's row.
            var $scope = $input.closest('form');
            return {
                preview: $scope.find('.photo-crop-preview'),
                img:     $scope.find('.photo-crop-preview-img'),
                meta:    $scope.find('.photo-crop-meta')
            };
        }

        function humanSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return Math.round(bytes / 1024) + ' KB';
            return (bytes / 1024 / 1024).toFixed(1) + ' MB';
        }

        function showPreview($input, file) {
            var p = partsFor($input);
            p.img.attr('src', trackUrl(URL.createObjectURL(file)));
            p.meta.text(file.name + ' · ' + humanSize(file.size));
            p.preview.removeClass('d-none');
        }

        function hidePreview($input) {
            var p = partsFor($input);
            p.preview.addClass('d-none');
            p.img.attr('src', '');
            p.meta.text('');
        }

        $inputs.on('change', function () {
            var $input = $(this);
            var file = this.files && this.files[0];

            if (!file) {
                hidePreview($input);
                original = null;
                return;
            }

            if (!/^image\//.test(file.type)) {
                hidePreview($input);
                return;
            }

            current  = $input;
            original = file;
            showPreview($input, file);
        });

        $(document).on('click', '.photo-crop-clear', function () {
            var $input = $(this).closest('form').find('input[type=file][data-photo-crop]');
            $input.val('');
            hidePreview($input);
            original = null;
            releaseUrls();
        });

        $(document).on('click', '.photo-crop-open', function () {
            var $input = $(this).closest('form').find('input[type=file][data-photo-crop]');
            var file = original || ($input[0].files && $input[0].files[0]);
            if (!file) return;

            current  = $input;
            // Always crop from the untouched original so repeated crops don't
            // compound quality loss.
            original = original || file;

            $image.attr('src', trackUrl(URL.createObjectURL(original)));
            $modal.modal('show');
        });

        $modal.on('shown.bs.modal', function () {
            if (cropper) cropper.destroy();
            cropper = new Cropper($image[0], {
                aspectRatio: 1,
                viewMode: 1,
                autoCropArea: 0.9,
                responsive: true,
                background: false
            });
        });

        $modal.on('hidden.bs.modal', function () {
            if (cropper) { cropper.destroy(); cropper = null; }
        });

        $modal.on('click', '[data-crop-action]', function () {
            if (!cropper) return;
            switch ($(this).data('crop-action')) {
                case 'zoom-in':      cropper.zoom(0.1); break;
                case 'zoom-out':     cropper.zoom(-0.1); break;
                case 'rotate-left':  cropper.rotate(-90); break;
                case 'rotate-right': cropper.rotate(90); break;
                case 'reset':        cropper.reset(); break;
            }
        });

        $('#photoCropApply').on('click', function () {
            if (!cropper || !current || !original) return;

            var canvas = cropper.getCroppedCanvas({
                width: OUTPUT_SIZE,
                height: OUTPUT_SIZE,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high'
            });
            if (!canvas) return;

            // Keep the original type so the extension still matches the MIME
            // type; anything unexpected falls back to JPEG with a .jpg name.
            var type = SAFE_TYPES.indexOf(original.type) !== -1 ? original.type : 'image/jpeg';
            var name = type === original.type
                            ? original.name
                            : original.name.replace(/\.[^.]+$/, '') + '.jpg';

            canvas.toBlob(function (blob) {
                if (!blob) return;

                var cropped = new File([blob], name, {
                    type: type,
                    lastModified: Date.now()
                });

                try {
                    var dt = new DataTransfer();
                    dt.items.add(cropped);
                    current[0].files = dt.files;
                } catch (e) {
                    // No DataTransfer support: keep the uncropped original
                    // rather than silently dropping the user's file.
                    $modal.modal('hide');
                    return;
                }

                showPreview(current, cropped);
                $modal.modal('hide');
            }, type, 0.92);
        });

        $(window).on('unload', releaseUrls);
    });

})();
