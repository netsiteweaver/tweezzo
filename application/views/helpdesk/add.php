<?php $e = function ($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }; ?>
<div class="row mt-3">
    <div class="col-lg-9">
        <form method="post" action="<?php echo base_url('helpdesk/save'); ?>" enctype="multipart/form-data" class="card">
            <div class="card-header"><h3 class="card-title"><i class="fa fa-life-ring"></i> New ticket on behalf of a customer</h3></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="customer_id">Customer</label>
                        <select class="form-control" name="customer_id" id="customer_id">
                            <option value="">— No customer / guest —</option>
                            <?php foreach ($customers as $c): ?>
                            <option value="<?php echo $c->customer_id; ?>"><?php echo $e($c->company_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 form-group" id="contact-wrap">
                        <label for="customer_access_id">Contact <small class="text-muted">(receives the email updates)</small></label>
                        <select class="form-control" name="customer_access_id" id="customer_access_id">
                            <option value="">— None —</option>
                            <?php foreach ($contacts as $c): ?>
                            <option value="<?php echo $c->id; ?>" data-customer="<?php echo $c->customer_id; ?>"><?php echo $e("{$c->name} <{$c->email}>"); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row" id="guest-wrap">
                    <div class="col-md-6 form-group">
                        <label for="requester_name">Requester name</label>
                        <input type="text" class="form-control" name="requester_name" id="requester_name" maxlength="100">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="requester_email">Requester email</label>
                        <input type="email" class="form-control" name="requester_email" id="requester_email" maxlength="150">
                    </div>
                </div>

                <div class="form-group">
                    <label for="subject">Subject <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="subject" id="subject" maxlength="255" required>
                </div>
                <div class="form-group">
                    <label for="body">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="body" id="body" rows="8" required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-3 form-group">
                        <label for="priority">Priority</label>
                        <select class="form-control" name="priority" id="priority">
                            <?php foreach (Tickets_model::priorities() as $k => $p): ?>
                            <option value="<?php echo $k; ?>" <?php echo $k === 'normal' ? 'selected' : ''; ?>><?php echo $p['label']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="category_id">Category</label>
                        <select class="form-control" name="category_id" id="category_id">
                            <option value="">—</option>
                            <?php foreach ($categories as $c): ?>
                            <option value="<?php echo $c->id; ?>"><?php echo $e($c->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="project_id">Project</label>
                        <select class="form-control" name="project_id" id="project_id">
                            <option value="">—</option>
                            <?php foreach ($projects as $p): ?>
                            <option value="<?php echo $p->id; ?>" data-customer="<?php echo $p->customer_id; ?>"><?php echo $e($p->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="assigned_to">Assign to</label>
                        <select class="form-control" name="assigned_to" id="assigned_to">
                            <option value="">Unassigned</option>
                            <?php foreach ($assignees as $u): ?>
                            <option value="<?php echo $u->id; ?>"><?php echo $e($u->name) . ($u->user_type === 'developer' ? ' (dev)' : ''); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="attachments">Attachments</label>
                    <input type="file" class="form-control-file" name="attachments[]" id="attachments" multiple>
                    <small class="text-muted">Up to <?php echo Tickets_model::MAX_FILES; ?> files, <?php echo Tickets_model::MAX_FILE_KB / 1024; ?> MB each (<?php echo str_replace('|', ', ', Tickets_model::ALLOWED_TYPES); ?>).</small>
                </div>
            </div>
            <div class="card-footer text-right">
                <a href="<?php echo base_url('helpdesk/listing'); ?>" class="btn btn-default">Cancel</a>
                <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Create ticket</button>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var $ = window.jQuery;
    function filterByCustomer() {
        var cid = $('#customer_id').val();
        ['#customer_access_id', '#project_id'].forEach(function (sel) {
            $(sel + ' option[data-customer]').each(function () {
                var show = cid !== '' && String($(this).data('customer')) === cid;
                $(this).prop('hidden', !show).prop('disabled', !show);
            });
            if ($(sel + ' option:selected').prop('disabled')) $(sel).val('');
        });
        $('#contact-wrap').toggle(cid !== '');
        $('#guest-wrap').toggle($('#customer_access_id').val() === '');
    }
    $('#customer_id').on('change', filterByCustomer);
    $('#customer_access_id').on('change', function () { $('#guest-wrap').toggle($(this).val() === ''); });
    filterByCustomer();
});
</script>
