<?php $e = function ($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }; ?>
<div class="row mt-4 justify-content-center">
    <div class="col-lg-8">
        <h4 class="mb-3"><i class="bi bi-life-preserver me-2"></i>New support ticket</h4>
        <form method="post" action="portal/customers/saveTicket" enctype="multipart/form-data" class="card" id="ticket-form">
            <div class="card-body">
                <div class="mb-3">
                    <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="subject" name="subject" maxlength="255" required placeholder="A short summary of the problem">
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <?php foreach ($categories as $c): ?>
                            <option value="<?php echo $c->id; ?>"><?php echo $e($c->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="priority" class="form-label">Priority</label>
                        <select class="form-select" id="priority" name="priority">
                            <option value="low">Low — question or minor issue</option>
                            <option value="normal" selected>Normal</option>
                            <option value="high">High — important feature not working</option>
                            <option value="urgent">Urgent — site down / business blocked</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="project_id" class="form-label">Project</label>
                        <select class="form-select" id="project_id" name="project_id">
                            <option value="">Not sure / general</option>
                            <?php if (!empty($projects)) foreach ($projects as $p): ?>
                            <option value="<?php echo $p->id; ?>" <?php echo count($projects) === 1 ? 'selected' : ''; ?>><?php echo $e($p->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="body" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="body" name="body" rows="8" required placeholder="What happened? What did you expect? Steps to reproduce, page URL, error messages…"></textarea>
                </div>
                <div class="mb-1">
                    <label for="attachments" class="form-label">Attachments <small class="text-muted">(screenshots, documents)</small></label>
                    <input class="form-control" type="file" id="attachments" name="attachments[]" multiple>
                    <div class="form-text">Up to <?php echo Tickets_model::MAX_FILES; ?> files, <?php echo Tickets_model::MAX_FILE_KB / 1024; ?> MB each.</div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2">
                <a href="portal/customers/tickets" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary" id="ticket-submit"><i class="bi bi-send me-1"></i>Submit ticket</button>
            </div>
        </form>
    </div>
</div>
<script>
    document.getElementById('ticket-form').addEventListener('submit', function () {
        document.getElementById('ticket-submit').disabled = true;
    });
</script>
