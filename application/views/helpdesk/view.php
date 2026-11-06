<?php
$e = function ($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); };
$canEdit = !empty($perms['edit']);
$overdue = Tickets_model::isOverdue($ticket);
$fmt = function ($d) { return $d ? date('d M Y H:i', strtotime($d)) : '—'; };
$slaState = function ($due, $doneAt) use ($ticket, $fmt) {
    if (!empty($doneAt)) {
        return '<span class="' . ($doneAt <= $due ? 'text-success' : 'text-danger') . '">' . ($doneAt <= $due ? 'Met' : 'Missed') . '</span> <small class="text-muted">(' . $fmt($doneAt) . ')</small>';
    }
    if (in_array($ticket->status, Tickets_model::PAUSED_STATUSES, true)) {
        return '<span class="text-muted">Paused</span> <small class="text-muted">(due ' . $fmt($due) . ')</small>';
    }
    $over = $due < date('Y-m-d H:i:s');
    return '<span class="' . ($over ? 'text-danger font-weight-bold' : '') . '">' . ($over ? 'Overdue' : 'Due') . '</span> <small class="text-muted">' . $fmt($due) . '</small>';
};
?>
<div class="row mt-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap">
                    <div>
                        <h4 class="mb-1"><?php echo $e($ticket->subject); ?></h4>
                        <div class="text-muted">
                            <?php echo $e($ticket->ticket_number); ?> · <span title="Reference shown to the customer"><i class="fa fa-user-tag"></i> <?php echo $e($ticket->public_ref); ?></span> ·
                            <?php echo Tickets_model::statusBadge($ticket->status); ?>
                            <?php echo Tickets_model::priorityBadge($ticket->priority); ?>
                            <?php if ($overdue): ?><span class="badge badge-danger"><i class="fa fa-exclamation-triangle"></i> SLA overdue</span><?php endif; ?>
                        </div>
                    </div>
                    <a href="<?php echo base_url('helpdesk/listing'); ?>" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
                </div>
            </div>
        </div>

        <?php $this->load->view('helpdesk/_thread', ['ticket' => $ticket, 'thread' => $thread, 'fileUrl' => base_url('helpdesk/download/{id}')]); ?>

        <?php if ($canEdit): ?>
        <form method="post" action="<?php echo base_url('helpdesk/reply'); ?>" enctype="multipart/form-data" class="card mt-3" id="reply-form">
            <input type="hidden" name="uuid" value="<?php echo $e($ticket->uuid); ?>">
            <div class="card-header p-2">
                <ul class="nav nav-pills" id="reply-mode">
                    <li class="nav-item"><a class="nav-link active" href="#" data-internal="0"><i class="fa fa-reply"></i> Reply to customer</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" data-internal="1"><i class="fa fa-sticky-note"></i> Internal note</a></li>
                </ul>
                <input type="hidden" name="is_internal" id="is_internal" value="0">
            </div>
            <div class="card-body">
                <?php if (empty($ticket->contact_email)): ?>
                <div class="alert alert-warning py-2 reply-only">This ticket has no contact email, so the customer won't be notified of replies.</div>
                <?php endif; ?>
                <textarea class="form-control" name="body" id="reply-body" rows="6" placeholder="Write your reply…"></textarea>
                <div class="row mt-2 align-items-end">
                    <div class="col-md-6">
                        <input type="file" class="form-control-file" name="attachments[]" multiple>
                        <small class="text-muted">Up to <?php echo Tickets_model::MAX_FILES; ?> files, <?php echo Tickets_model::MAX_FILE_KB / 1024; ?> MB each.</small>
                    </div>
                    <div class="col-md-3">
                        <label class="mb-0 small" for="reply-status">Then set status</label>
                        <select class="form-control form-control-sm" name="status" id="reply-status">
                            <option value="">Keep (<?php echo $e(Tickets_model::statusLabel($ticket->status)); ?>)</option>
                            <?php foreach (Tickets_model::statuses() as $k => $s): ?>
                            <?php if ($k === $ticket->status) continue; ?>
                            <option value="<?php echo $k; ?>" <?php echo ($k === 'pending_customer' && in_array($ticket->status, ['new', 'open'], true)) ? 'selected' : ''; ?>><?php echo $s['label']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 text-right">
                        <button type="submit" class="btn btn-primary btn-block" id="reply-submit"><i class="fa fa-paper-plane"></i> Send reply</button>
                    </div>
                </div>
            </div>
        </form>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Ticket</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label for="sel-status">Status</label>
                    <select class="form-control hd-field" id="sel-status" data-field="status" <?php echo $canEdit ? '' : 'disabled'; ?>>
                        <?php foreach (Tickets_model::statuses() as $k => $s): ?>
                        <option value="<?php echo $k; ?>" <?php echo $ticket->status === $k ? 'selected' : ''; ?>><?php echo $s['label']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="sel-priority">Priority</label>
                    <select class="form-control hd-field" id="sel-priority" data-field="priority" <?php echo $canEdit ? '' : 'disabled'; ?>>
                        <?php foreach (Tickets_model::priorities() as $k => $p): ?>
                        <option value="<?php echo $k; ?>" <?php echo $ticket->priority === $k ? 'selected' : ''; ?>><?php echo $p['label']; ?> (<?php echo $sla[$k]['first_response'] + 0; ?>h / <?php echo $sla[$k]['resolution'] + 0; ?>h)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="sel-assignee">Assignee</label>
                    <div class="input-group">
                        <select class="form-control hd-field" id="sel-assignee" data-field="assigned_to" <?php echo $canEdit ? '' : 'disabled'; ?>>
                            <option value="">Unassigned</option>
                            <?php foreach ($assignees as $u): ?>
                            <option value="<?php echo $u->id; ?>" <?php echo (int) $ticket->assigned_to === (int) $u->id ? 'selected' : ''; ?>><?php echo $e($u->name) . ($u->user_type === 'developer' ? ' (dev)' : ''); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($canEdit && (int) $ticket->assigned_to !== (int) $_SESSION['user_id']): ?>
                        <div class="input-group-append"><button class="btn btn-default" type="button" id="assign-me" title="Assign to me">Me</button></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="sel-category">Category</label>
                    <select class="form-control hd-field" id="sel-category" data-field="category_id" <?php echo $canEdit ? '' : 'disabled'; ?>>
                        <option value="">—</option>
                        <?php foreach ($categories as $c): ?>
                        <option value="<?php echo $c->id; ?>" <?php echo (int) $ticket->category_id === (int) $c->id ? 'selected' : ''; ?>><?php echo $e($c->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <hr>
                <dl class="mb-0">
                    <dt>Requester</dt>
                    <dd>
                        <?php echo $e($ticket->contact_name ?: '—'); ?>
                        <?php if ($ticket->contact_email): ?><br><a href="mailto:<?php echo $e($ticket->contact_email); ?>"><?php echo $e($ticket->contact_email); ?></a><?php endif; ?>
                        <br><small class="text-muted">
                            <?php if ($ticket->company_name): ?><?php echo $e($ticket->company_name); ?>
                            <?php elseif ($ticket->requester_company): ?><?php echo $e($ticket->requester_company); ?> (not a customer)
                            <?php else: ?>Guest<?php endif; ?>
                        </small>
                    </dd>
                    <?php if ($ticket->project_name): ?>
                    <dt>Project</dt><dd><?php echo $e($ticket->project_name); ?></dd>
                    <?php endif; ?>
                    <dt>Source</dt><dd><?php echo $e(ucfirst($ticket->source)); ?> · created <?php echo $fmt($ticket->created_on); ?></dd>
                    <dt>First response</dt><dd><?php echo $slaState($ticket->first_response_due, $ticket->first_responded_at); ?></dd>
                    <dt>Resolution</dt><dd><?php echo $slaState($ticket->resolution_due, $ticket->resolved_at); ?></dd>
                    <dt>Linked task</dt>
                    <dd>
                        <?php if ($ticket->task_id): ?>
                            <a href="<?php echo base_url('tasks/view?task_uuid=' . $ticket->task_uuid); ?>"><i class="fa fa-tasks"></i> <?php echo $e($ticket->task_number); ?></a>
                        <?php elseif ($canEdit): ?>
                            <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#modalConvert"><i class="fa fa-exchange-alt"></i> Convert to task</button>
                        <?php else: ?>—<?php endif; ?>
                    </dd>
                </dl>
            </div>
        </div>

        <div class="card collapsed-card">
            <div class="card-header">
                <h3 class="card-title">History</h3>
                <div class="card-tools"><button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button></div>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush small">
                    <?php foreach ($history as $h): ?>
                    <li class="list-group-item">
                        <b><?php echo $e($h->changed_by_name ?: 'System'); ?></b>
                        <?php if ($h->field === 'created'): ?>
                            created the ticket (<?php echo $e($h->new_value); ?>)
                        <?php elseif ($h->field === 'task'): ?>
                            converted it to task <?php echo $e($h->new_value); ?>
                        <?php elseif ($h->field === 'deleted'): ?>
                            deleted the ticket
                        <?php else: ?>
                            changed <?php echo $e(str_replace(['_id', '_'], ['', ' '], $h->field)); ?>
                            <?php if ($h->old_value !== null): ?>from <i><?php echo $e($h->old_value); ?></i><?php endif; ?>
                            to <i><?php echo $e($h->new_value ?? 'none'); ?></i>
                        <?php endif; ?>
                        <div class="text-muted"><?php echo $fmt($h->created_on); ?></div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php if ($canEdit && empty($ticket->task_id)): ?>
<div class="modal fade" id="modalConvert" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?php echo base_url('helpdesk/convertToTask'); ?>">
                <input type="hidden" name="uuid" value="<?php echo $e($ticket->uuid); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Convert ticket to task</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="convert_sprint_id">Sprint <span class="text-danger">*</span></label>
                        <select class="form-control" id="convert_sprint_id" name="sprint_id" required>
                            <option value="">Select sprint</option>
                            <?php foreach ($sprints as $s): ?>
                            <option value="<?php echo $s->id; ?>" <?php echo count($sprints) === 1 ? 'selected' : ''; ?>><?php echo $e(($ticket->customer_id ? '' : $s->company_name . ' / ') . $s->project_name . ' / ' . $s->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (empty($sprints)): ?><small class="text-danger">No active sprint for this customer.</small><?php endif; ?>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="convert_estimated_hours">Estimated hours</label>
                            <input type="number" step="0.25" min="0" class="form-control" id="convert_estimated_hours" name="estimated_hours" value="1">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="convert_work_type">Work type</label>
                            <select class="form-control" id="convert_work_type" name="work_type">
                                <option value="support" selected>Support</option>
                                <option value="bugfix">Bugfix</option>
                                <option value="development">Development</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="convert_ref">Ref</label>
                            <input type="text" class="form-control" id="convert_ref" name="ref" placeholder="e.g. INV001234">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="convert_billable">Billable</label>
                            <select class="form-control" id="convert_billable" name="billable">
                                <option value="1">Yes</option>
                                <option value="0" selected>No</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Assign developers (optional)</label>
                        <ul id="convert-users-list" class="list-group" style="max-height:200px;overflow-y:auto;">
                            <?php foreach ($developers as $d): ?>
                            <li data-id="<?php echo $d->id; ?>" class="list-group-item list-group-item-action convert-user-item" style="cursor:pointer;"><?php echo $e("{$d->name} ({$d->email})"); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <input type="hidden" name="userIds" id="convert_user_ids" value="[]">
                    </div>
                    <small class="text-muted">Image attachments are copied to the task. The ticket stays open and is linked to the new task.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Create task</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
    var HD_TICKET_UUID = <?php echo json_encode($ticket->uuid); ?>;
</script>
