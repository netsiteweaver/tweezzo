<?php
$e = function ($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); };
$isCustomer = $portal === 'customers';
?>
<div class="row mt-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h4 class="mb-0"><i class="bi bi-life-preserver me-2"></i><?php echo $isCustomer ? 'Support tickets' : 'My support tickets'; ?></h4>
            <div class="d-flex gap-2 align-items-center">
                <?php if ($isCustomer): ?>
                <div class="btn-group btn-group-sm" role="group">
                    <a href="portal/customers/tickets?show=all" class="btn btn-outline-secondary <?php echo $show === 'all' ? 'active' : ''; ?>">All</a>
                    <a href="portal/customers/tickets?show=active" class="btn btn-outline-secondary <?php echo $show === 'active' ? 'active' : ''; ?>">Open</a>
                    <a href="portal/customers/tickets?show=done" class="btn btn-outline-secondary <?php echo $show === 'done' ? 'active' : ''; ?>">Resolved</a>
                </div>
                <a href="portal/customers/newTicket" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>New ticket</a>
                <?php else: ?>
                <div class="btn-group btn-group-sm" role="group">
                    <a href="portal/developers/tickets" class="btn btn-outline-secondary <?php echo $show === 'active' ? 'active' : ''; ?>">Open</a>
                    <a href="portal/developers/tickets?show=all" class="btn btn-outline-secondary <?php echo $show === 'all' ? 'active' : ''; ?>">All</a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (empty($tickets)): ?>
            <div class="alert alert-info">
                <?php if ($isCustomer): ?>
                    You haven't opened any support tickets yet. Need help? <a href="portal/customers/newTicket">Open a ticket</a>.
                <?php else: ?>
                    No tickets are assigned to you.
                <?php endif; ?>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Subject</th>
                        <?php if (!$isCustomer): ?><th>Customer</th><?php endif; ?>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Last update</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $t): ?>
                    <tr>
                        <td class="text-nowrap"><?php echo $e($isCustomer ? $t->public_ref : Tickets_model::staffRef($t)); ?></td>
                        <td>
                            <?php echo $e($t->subject); ?>
                            <div class="small text-muted">
                                <?php echo $t->category_name ? $e($t->category_name) . ' · ' : ''; ?>
                                <?php echo $isCustomer ? 'by ' . $e($t->contact_name ?: '—') . ' · ' : ''; ?>
                                <?php echo (int) $t->reply_count; ?> repl<?php echo $t->reply_count == 1 ? 'y' : 'ies'; ?>
                            </div>
                        </td>
                        <?php if (!$isCustomer): ?><td><?php echo $e($t->company_name ?: ($t->contact_name ?: 'Guest')); ?></td><?php endif; ?>
                        <td><?php echo Tickets_model::priorityBadge($t->priority, true); ?></td>
                        <td>
                            <?php echo Tickets_model::statusBadge($t->status, true); ?>
                            <?php if ($isCustomer && $t->status === 'pending_customer'): ?><div class="small text-warning">Waiting for your reply</div><?php endif; ?>
                            <?php if (!$isCustomer && Tickets_model::isOverdue($t)): ?><span class="badge bg-danger">Overdue</span><?php endif; ?>
                        </td>
                        <td class="text-nowrap small"><?php echo $e(date('d M Y H:i', strtotime($t->last_activity_at ?: $t->created_on))); ?></td>
                        <td class="text-nowrap">
                            <a href="portal/<?php echo $portal; ?>/ticket/<?php echo $e($t->uuid); ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-chat-left-text me-1"></i>View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
