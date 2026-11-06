<?php
$e = function ($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); };
$val = function ($k, $default = '') use ($old) { return isset($old[$k]) ? $old[$k] : $default; };
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h3 class="mb-1"><i class="bi bi-life-preserver me-2"></i>Contact support</h3>
        <p class="text-muted">Tell us what's going on and we'll get back to you by email. Existing customers can also <a href="portal/customers/signin">sign in to the portal</a> to follow all their tickets.</p>

        <form method="post" action="support/submit" enctype="multipart/form-data" class="card" id="support-form">
            <input type="hidden" name="form_token" value="<?php echo $e($token); ?>">
            <div style="position:absolute;left:-10000px;" aria-hidden="true">
                <label for="website">Leave this field empty</label>
                <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Your name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" maxlength="100" required value="<?php echo $e($val('requester_name')); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" maxlength="150" required value="<?php echo $e($val('requester_email')); ?>">
                        <div class="form-text">We'll send updates and a private link to your ticket here.</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="company" class="form-label">Company</label>
                        <input type="text" class="form-control" id="company" name="company" maxlength="150" value="<?php echo $e($val('requester_company')); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <?php foreach ($categories as $c): ?>
                            <option value="<?php echo $c->id; ?>" <?php echo (string) $val('category_id') === (string) $c->id ? 'selected' : ''; ?>><?php echo $e($c->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="priority" class="form-label">Urgency</label>
                        <select class="form-select" id="priority" name="priority">
                            <?php foreach (['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent — business blocked'] as $k => $label): ?>
                            <option value="<?php echo $k; ?>" <?php echo $val('priority', 'normal') === $k ? 'selected' : ''; ?>><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="subject" name="subject" maxlength="255" required value="<?php echo $e($val('subject')); ?>">
                </div>
                <div class="mb-3">
                    <label for="body" class="form-label">How can we help? <span class="text-danger">*</span></label>
                    <textarea class="form-control hd-editor" id="body" name="body" rows="8" data-height="220" required><?php echo $e($val('body')); ?></textarea>
                </div>
                <div class="mb-1">
                    <label for="attachments" class="form-label">Attachments</label>
                    <input class="form-control hd-attachments" type="file" id="attachments" name="attachments[]" multiple data-max-files="<?php echo Tickets_model::MAX_FILES; ?>" data-max-kb="<?php echo Tickets_model::MAX_FILE_KB; ?>" data-types="<?php echo Tickets_model::ALLOWED_TYPES; ?>" accept="<?php echo '.' . str_replace('|', ',.', Tickets_model::ALLOWED_TYPES); ?>">
                    <div class="form-text">Up to <?php echo Tickets_model::MAX_FILES; ?> files, <?php echo Tickets_model::MAX_FILE_KB / 1024; ?> MB each.</div>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary" id="support-submit"><i class="bi bi-send me-1"></i>Send request</button>
            </div>
        </form>
    </div>
</div>
<script src="assets/js/helpdesk-editor.js?v=3" defer></script>
<script>
    document.getElementById('support-form').addEventListener('submit', function (ev) {
        var bodyEl = document.getElementById('body');
        if (window.hdEditor ? hdEditor.isEmpty(bodyEl) : !bodyEl.value.trim()) {
            ev.preventDefault();
            alert('Please tell us how we can help.');
            if (window.hdEditor) hdEditor.focus(bodyEl);
            return;
        }
        document.getElementById('support-submit').disabled = true;
    });
</script>
