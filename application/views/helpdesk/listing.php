<?php
$e = function ($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); };
$get = function ($k) { return (string) $this->input->get($k); };
$statusFilter = $filters['status'];
$cards = [
    ['label' => 'New',        'count' => $counts['new'],        'icon' => 'fa-inbox',               'color' => 'primary', 'qs' => 'status=new'],
    ['label' => 'Active',     'count' => $counts['active'],     'icon' => 'fa-life-ring',           'color' => 'info',    'qs' => 'status=active'],
    ['label' => 'Unassigned', 'count' => $counts['unassigned'], 'icon' => 'fa-user-slash',          'color' => 'secondary', 'qs' => 'status=active&assigned_to=unassigned'],
    ['label' => 'Overdue',    'count' => $counts['overdue'],    'icon' => 'fa-exclamation-triangle','color' => 'danger',  'qs' => 'status=active&overdue=1'],
    ['label' => 'Mine',       'count' => $counts['mine'],       'icon' => 'fa-user-check',          'color' => 'success', 'qs' => 'status=active&assigned_to=me'],
];
?>
<style>
    .hd-card { display:block; color:inherit; text-decoration:none; }
    .hd-card:hover { text-decoration:none; color:inherit; opacity:.9; }
    .hd-row-overdue { background: rgba(220,53,69,.06); }
    .hd-subject { font-weight:600; }
    .hd-meta { font-size:12px; color:#888; }
    .hd-due-over { color:#dc3545; font-weight:600; }
</style>

<div class="row mt-3">
    <?php foreach ($cards as $c): ?>
    <div class="col-6 col-md">
        <a class="hd-card" href="<?php echo base_url('helpdesk/listing?' . $c['qs']); ?>">
            <div class="info-box">
                <span class="info-box-icon bg-<?php echo $c['color']; ?>"><i class="fa <?php echo $c['icon']; ?>"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo $c['label']; ?></span>
                    <span class="info-box-number"><?php echo (int) $c['count']; ?></span>
                </div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<div class="row no-print align-items-end" id="hd-filters">
    <div class="col-md-1 mb-2">
        <?php if ($perms['add']): ?>
        <a href="<?php echo base_url('helpdesk/add'); ?>" class="btn btn-flat btn-success btn-block"><i class="fa fa-plus"></i> New</a>
        <?php endif; ?>
    </div>
    <div class="col-md-2 mb-2">
        <label for="f_status">Status</label>
        <select class="form-control hd-filter" id="f_status" data-key="status">
            <option value="active" <?php echo $statusFilter === 'active' ? 'selected' : ''; ?>>All active</option>
            <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All tickets</option>
            <?php foreach (Tickets_model::statuses() as $k => $s): ?>
            <option value="<?php echo $k; ?>" <?php echo $statusFilter === $k ? 'selected' : ''; ?>><?php echo $s['label']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-1 mb-2">
        <label for="f_priority">Priority</label>
        <select class="form-control hd-filter" id="f_priority" data-key="priority">
            <option value="">All</option>
            <?php foreach (Tickets_model::priorities() as $k => $p): ?>
            <option value="<?php echo $k; ?>" <?php echo $get('priority') === $k ? 'selected' : ''; ?>><?php echo $p['label']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2 mb-2">
        <label for="f_assigned">Assignee</label>
        <select class="form-control hd-filter" id="f_assigned" data-key="assigned_to">
            <option value="">Anyone</option>
            <option value="me" <?php echo $get('assigned_to') === 'me' ? 'selected' : ''; ?>>Me</option>
            <option value="unassigned" <?php echo $get('assigned_to') === 'unassigned' ? 'selected' : ''; ?>>Unassigned</option>
            <?php foreach ($assignees as $u): ?>
            <option value="<?php echo $u->id; ?>" <?php echo $get('assigned_to') === (string) $u->id ? 'selected' : ''; ?>><?php echo $e($u->name) . ($u->user_type === 'developer' ? ' (dev)' : ''); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2 mb-2">
        <label for="f_customer">Customer</label>
        <select class="form-control hd-filter" id="f_customer" data-key="customer_id">
            <option value="">All customers</option>
            <?php foreach ($customers as $c): ?>
            <option value="<?php echo $c->customer_id; ?>" <?php echo $get('customer_id') === (string) $c->customer_id ? 'selected' : ''; ?>><?php echo $e($c->company_name) . " ({$c->ticket_count})"; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-1 mb-2">
        <label for="f_category">Category</label>
        <select class="form-control hd-filter" id="f_category" data-key="category_id">
            <option value="">All</option>
            <?php foreach ($categories as $c): ?>
            <option value="<?php echo $c->id; ?>" <?php echo $get('category_id') === (string) $c->id ? 'selected' : ''; ?>><?php echo $e($c->name); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-1 mb-2">
        <div class="custom-control custom-checkbox mb-2">
            <input type="checkbox" class="custom-control-input hd-filter" id="f_overdue" data-key="overdue" value="1" <?php echo $get('overdue') ? 'checked' : ''; ?>>
            <label class="custom-control-label" for="f_overdue">Overdue</label>
        </div>
    </div>
    <div class="col-md-2 mb-2">
        <label for="f_search">Search</label>
        <div class="input-group">
            <input type="text" class="form-control" id="f_search" data-key="search_text" placeholder="Number, ref, subject, email…" value="<?php echo $e($get('search_text')); ?>">
            <div class="input-group-append"><button class="btn btn-default" id="f_search_btn" type="button"><i class="fa fa-search"></i></button></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body table-responsive p-0">
                <?php if (!empty($tickets)): ?>
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:110px;">Ticket</th>
                            <th>Subject</th>
                            <th style="width:180px;">Requester</th>
                            <th style="width:90px;">Priority</th>
                            <th style="width:130px;">Status</th>
                            <th style="width:150px;">Assignee</th>
                            <th style="width:150px;">SLA</th>
                            <th style="width:140px;">Last activity</th>
                            <th class="no-print" style="width:100px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($tickets as $t): ?>
                        <?php $overdue = Tickets_model::isOverdue($t); ?>
                        <tr class="<?php echo $overdue ? 'hd-row-overdue' : ''; ?>">
                            <td>
                                <a href="<?php echo base_url('helpdesk/view?uuid=' . $t->uuid); ?>"><b><?php echo $e($t->ticket_number); ?></b></a>
                                <div class="hd-meta"><?php echo $e($t->public_ref); ?> · <?php echo $e(ucfirst($t->source)); ?></div>
                            </td>
                            <td>
                                <a class="hd-subject text-dark" href="<?php echo base_url('helpdesk/view?uuid=' . $t->uuid); ?>"><?php echo $e($t->subject); ?></a>
                                <div class="hd-meta">
                                    <?php echo $t->category_name ? $e($t->category_name) . ' · ' : ''; ?>
                                    <?php echo (int) $t->reply_count; ?> repl<?php echo $t->reply_count == 1 ? 'y' : 'ies'; ?>
                                    <?php if (!empty($t->task_number)): ?> · <i class="fa fa-tasks"></i> <?php echo $e($t->task_number); ?><?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php echo $e(Tickets_model::requesterName($t) ?: $t->contact_email ?: '—'); ?>
                                <div class="hd-meta"><?php echo $e($t->company_name ?: ($t->requester_company ?: ($t->customer_id ? '' : 'Guest'))); ?></div>
                            </td>
                            <td><?php echo Tickets_model::priorityBadge($t->priority); ?></td>
                            <td><?php echo Tickets_model::statusBadge($t->status); ?></td>
                            <td><?php echo $t->assignee_name ? $e($t->assignee_name) : '<span class="text-muted">Unassigned</span>'; ?></td>
                            <td>
                                <?php if (in_array($t->status, Tickets_model::CLOSED_STATUSES, true)): ?>
                                    <span class="text-muted">—</span>
                                <?php elseif (in_array($t->status, Tickets_model::PAUSED_STATUSES, true)): ?>
                                    <span class="text-muted"><i class="fa fa-pause"></i> Paused</span>
                                <?php else: ?>
                                    <?php $dueLabel = empty($t->first_responded_at) ? 'Response' : 'Resolve'; $due = empty($t->first_responded_at) ? $t->first_response_due : $t->resolution_due; ?>
                                    <span class="hd-due <?php echo $overdue ? 'hd-due-over' : ''; ?>" data-due="<?php echo $e(date('c', strtotime($due))); ?>" title="<?php echo $e(date('d M Y H:i', strtotime($due))); ?>"><?php echo $e(date('d M H:i', strtotime($due))); ?></span>
                                    <div class="hd-meta"><?php echo $dueLabel; ?> due</div>
                                <?php endif; ?>
                            </td>
                            <td><span class="hd-meta"><?php echo $e(date('d M Y H:i', strtotime($t->last_activity_at ?: $t->created_on))); ?></span></td>
                            <td class="no-print text-right">
                                <a href="<?php echo base_url('helpdesk/view?uuid=' . $t->uuid); ?>" class="btn btn-flat btn-default btn-sm"><i class="fas fa-eye"></i></a>
                                <?php if ($perms['delete']): ?>
                                <button data-url="<?php echo base_url('helpdesk/delete'); ?>" data-uuid="<?php echo $t->uuid; ?>" data-message="Delete ticket <?php echo $e($t->ticket_number); ?>?" class="deleteAjax btn btn-flat btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p class="p-3 mb-0 text-muted">No tickets match these filters.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="hd-meta mb-2"><?php echo (int) $total_rows; ?> ticket<?php echo $total_rows == 1 ? '' : 's'; ?></div>
    </div>
</div>

<?php if (!empty($pagination)) echo $pagination; ?>
