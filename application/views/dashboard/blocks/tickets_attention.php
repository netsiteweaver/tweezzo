<?php
/**
 * Helpdesk tickets needing attention (overdue first, then unassigned, priority, nearest deadline).
 * Expects: $tickets (Tickets_model::attentionList), $counts (Tickets_model::summaryCounts)
 */
$e = function ($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); };
?>
<style>
    .hd-dash { margin-bottom: 1.5rem; }
    /* Overdue: red edge instead of a row background, so text stays readable in light and dark mode. */
    .hd-dash tr.hd-dash-over > td:first-child { box-shadow: inset 4px 0 0 #dc3545; }
    .hd-dash .hd-dash-subject { color: inherit; }
</style>
<div class="hd-dash">
<div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
    <h4 class="mb-0"><i class="fa fa-life-ring"></i> Tickets needing attention</h4>
    <div class="small">
        <a href="<?php echo base_url('helpdesk/listing?status=new'); ?>" class="mr-3">New: <b><?php echo (int) $counts['new']; ?></b></a>
        <a href="<?php echo base_url('helpdesk/listing?status=active&assigned_to=me'); ?>" class="mr-3">Mine: <b><?php echo (int) $counts['mine']; ?></b></a>
        <a href="<?php echo base_url('helpdesk/listing'); ?>">All tickets <i class="fa fa-arrow-circle-right"></i></a>
    </div>
</div>
<?php if (empty($tickets)): ?>
    <p class="text-muted mb-0 py-3 text-center"><i class="fa fa-check-circle text-success"></i> Nothing waiting on the team right now.</p>
<?php else: ?>
<div class="table-responsive">
    <table class="table table-bordered table-hover table-sm mb-0">
        <thead>
            <tr class="bg-info">
                <th>TICKET</th>
                <th>SUBJECT</th>
                <th>FROM</th>
                <th>PRIORITY</th>
                <th>STATUS</th>
                <th>ASSIGNED</th>
                <th>DUE</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tickets as $t): ?>
            <?php
                $overdue = Tickets_model::isOverdue($t);
                $responded = !empty($t->first_responded_at);
                $due = $responded ? $t->resolution_due : $t->first_response_due;
                $url = base_url('helpdesk/view?uuid=' . rawurlencode($t->uuid));
            ?>
            <tr class="<?php echo $overdue ? 'hd-dash-over' : ''; ?>">
                <td class="text-nowrap"><a href="<?php echo $url; ?>"><b><?php echo $e($t->public_ref); ?></b></a></td>
                <td><a class="hd-dash-subject" href="<?php echo $url; ?>"><?php echo $e($t->subject); ?></a></td>
                <td>
                    <?php echo $e(Tickets_model::requesterName($t) ?: ($t->contact_email ?: '—')); ?>
                    <div class="small text-muted"><?php echo $e($t->company_name ?: ($t->requester_company ?: 'Guest')); ?></div>
                </td>
                <td><?php echo Tickets_model::priorityBadge($t->priority); ?></td>
                <td><?php echo Tickets_model::statusBadge($t->status); ?></td>
                <td><?php echo $t->assignee_name ? $e($t->assignee_name) : '<span class="text-danger">Unassigned</span>'; ?></td>
                <td class="text-nowrap">
                    <?php if ($due): ?>
                        <span class="<?php echo $overdue ? 'text-danger font-weight-bold' : ''; ?>" title="<?php echo $e(date('d M Y H:i', strtotime($due))); ?>">
                            <?php echo $overdue ? '<i class="fa fa-exclamation-triangle"></i> ' : ''; ?><?php echo $e(date('d M H:i', strtotime($due))); ?>
                        </span>
                        <div class="small text-muted"><?php echo $responded ? 'Resolve' : 'Response'; ?> due</div>
                    <?php else: ?>—<?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
</div>
