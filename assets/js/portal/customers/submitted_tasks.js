document.addEventListener('DOMContentLoaded', function () {
    function safeText(value) {
        return value && String(value).trim() !== '' ? value : '-';
    }

    function setText(selector, value) {
        var el = document.querySelector(selector);
        if (el) {
            el.textContent = safeText(value);
        }
    }

    var buttons = document.querySelectorAll('.view-submitted-task');
    buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var stage = (btn.getAttribute('data-stage') || '').toString();
            var stageText = stage ? stage.replace(/_/g, ' ').toUpperCase() : '-';
            var rejectionReason = (btn.getAttribute('data-rejection-reason') || '').trim();

            setText('[data-field="submitted_on"]', btn.getAttribute('data-submitted-on'));
            setText('[data-field="submitted_by"]', btn.getAttribute('data-submitted-by'));
            setText('[data-field="status"]', stageText);
            setText('[data-field="section"]', btn.getAttribute('data-section'));
            setText('[data-field="task_name"]', btn.getAttribute('data-task-name'));
            setText('[data-field="description"]', btn.getAttribute('data-description'));
            setText('[data-field="scope_client_expectation"]', btn.getAttribute('data-scope-client-expectation'));
            setText('[data-field="scope_not_included"]', btn.getAttribute('data-scope-not-included'));
            setText('[data-field="scope_when_done"]', btn.getAttribute('data-scope-when-done'));

            // Render submitted images (if any)
            var imagesContainer = document.querySelector('[data-field="task_images"]');
            if (imagesContainer) {
                var imagesRaw = btn.getAttribute('data-task-images');
                var images = [];
                if (imagesRaw) {
                    try { images = JSON.parse(imagesRaw); } catch (e) { images = []; }
                }

                var root = (typeof base_url !== 'undefined') ? base_url : (window.base_url || '');
                if (!images || !images.length) {
                    imagesContainer.innerHTML = "<span class='text-muted'>No images.</span>";
                } else {
                    var html = "<div class='row g-2'>";
                    images.forEach(function (img) {
                        if (!img || !img.file_name) return;
                        var fileUrl = root + "uploads/tasks/" + img.file_name;
                        var thumbUrl = root + "uploads/tasks/" + (img.thumb_name || img.file_name);
                        html += "<div class='col-4'>" +
                            "<a href='" + fileUrl + "' data-lightbox='submittedTaskImages'>" +
                            "<img class='img-thumbnail' style='width:100%;height:auto;' src='" + thumbUrl + "' alt='attachment'>" +
                            "</a>" +
                            "</div>";
                    });
                    html += "</div>";
                    imagesContainer.innerHTML = html;
                }
            }

            var rejectionEl = document.querySelector('[data-field="rejection_reason"]');
            var rejectionWrap = document.querySelector('[data-field-wrap="rejection_reason"]');
            if (rejectionEl && rejectionWrap) {
                if (stage === 'rejected' && rejectionReason !== '') {
                    rejectionEl.textContent = rejectionReason;
                    rejectionWrap.classList.remove('d-none');
                } else {
                    rejectionEl.textContent = '';
                    rejectionWrap.classList.add('d-none');
                }
            }
        });
    });

    var deleteButtons = document.querySelectorAll('.delete-submitted-task');
    deleteButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var taskUuid = btn.getAttribute('data-task-uuid');
            if (!taskUuid) {
                return;
            }
            if (!window.confirm('Delete this submitted task request?')) {
                return;
            }

            var body = new URLSearchParams();
            body.append('task_uuid', taskUuid);

            fetch((window.base_url || '/') + 'portal/customers/deleteSubmittedTask', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: body.toString()
            })
                .then(function (response) { return response.json(); })
                .then(function (payload) {
                    if (payload && payload.result === true) {
                        var row = btn.closest('tr');
                        if (row) {
                            row.remove();
                        }
                    } else {
                        window.alert((payload && payload.reason) ? payload.reason : 'Unable to delete task request.');
                    }
                })
                .catch(function () {
                    window.alert('Unable to delete task request.');
                });
        });
    });
});
