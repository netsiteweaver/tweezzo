<?php
$e = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };

// Customer-friendly stage names (display only)
$stageInfo = [
    'new'         => ['label' => 'Not started',           'class' => 'stage-new'],
    'in_progress' => ['label' => 'In progress',           'class' => 'stage-in-progress'],
    'testing'     => ['label' => 'Being tested',          'class' => 'stage-testing'],
    'staging'     => ['label' => 'Ready for your review', 'class' => 'stage-staging'],
    'validated'   => ['label' => 'Approved',              'class' => 'stage-validated'],
    'completed'   => ['label' => 'Done',                  'class' => 'stage-completed'],
    'on_hold'     => ['label' => 'On hold',               'class' => 'stage-on-hold'],
    'stopped'     => ['label' => 'Stopped',               'class' => 'stage-stopped'],
];

$timeAgo = function ($datetime) {
    if (empty($datetime)) return '';
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    $days = floor($diff / 86400);
    return $days == 1 ? 'yesterday' : $days . ' days ago';
};

$hour = (int) date('G');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');

$reviewTotal = (int) ($awaiting_review['total'] ?? 0);
$reviewTasks = $awaiting_review['tasks'] ?? [];
$hasAttention = $reviewTotal > 0 || !empty($pending_tickets) || !empty($upcoming_meetings);
?>
<style>
    .stage-new { background-color: #1c8be6; }
    .stage-in-progress { background-color: #44ab8e; }
    .stage-testing { background-color: #98c363; }
    .stage-staging { background-color: #f36930; }
    .stage-validated { background-color: #c44866; }
    .stage-completed { background-color: #4e67c7; }
    .stage-on-hold { background-color: #ff0000; }
    .stage-stopped { background-color: #6c757d; }

    .dash-greeting h4 { font-weight: 600; }
    .dash-card { border: 0; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,.08); }
    .dash-card .card-header { background: transparent; border-bottom: 1px solid rgba(0,0,0,.06); font-weight: 600; }

    .attention-group + .attention-group { border-top: 1px dashed rgba(0,0,0,.1); margin-top: .75rem; padding-top: .75rem; }
    .attention-title { font-weight: 600; display: flex; align-items: center; gap: .5rem; margin-bottom: .5rem; }
    .attention-title .badge { font-size: .8rem; }
    .attention-item { display: flex; align-items: center; gap: .75rem; padding: .45rem 0; }
    .attention-item + .attention-item { border-top: 1px solid rgba(0,0,0,.04); }
    .attention-item .meta { font-size: .8rem; color: #6c757d; }
    .attention-item .grow { flex: 1 1 auto; min-width: 0; }
    .attention-item .name { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .all-caught-up { color: #2f9d7d; font-size: 1.05rem; }

    .sprint-card + .sprint-card { border-top: 1px solid rgba(0,0,0,.06); margin-top: 1rem; padding-top: 1rem; }
    .sprint-pct { font-size: 1.8rem; font-weight: 700; line-height: 1; }
    .stacked-bar { display: flex; height: 12px; border-radius: 6px; overflow: hidden; background: #e9ecef; }
    .stacked-bar > div { height: 100%; }
    .stage-links { font-size: .9rem; }
    .stage-links a { text-decoration: none; color: inherit; white-space: nowrap; }
    .stage-links a:hover { text-decoration: underline; }
    .stage-dot { display: inline-block; width: 9px; height: 9px; border-radius: 50%; margin-right: 4px; }

    .update-item + .update-item { border-top: 1px solid rgba(0,0,0,.05); }
    .update-item { padding: .6rem 0; }
    .update-item .note { color: #444; font-size: .92rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    .stage-chip { color: #fff; display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 12px; font-weight: 600; min-width: 92px; text-align: center; }
</style>

<!-- 1. Greeting + quick actions -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3 mb-3 dash-greeting">
    <div>
        <h4 class="mb-0"><?php echo $e($greeting . (!empty($customer_first_name) ? ', ' . $customer_first_name : '')); ?></h4>
        <div class="text-muted small">Here is what's happening with your projects.</div>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-primary btn-sm add-task"><i class="bi bi-plus-circle me-1"></i>Submit a request</button>
        <a href="portal/customers/newTicket" class="btn btn-outline-primary btn-sm"><i class="bi bi-life-preserver me-1"></i>Get support</a>
        <a href="portal/customers/tasks" class="btn btn-outline-secondary btn-sm"><i class="bi bi-list-check me-1"></i>All tasks</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-7">

        <!-- 2. Needs your attention -->
        <div class="card dash-card mb-3">
            <div class="card-header"><i class="bi bi-bell me-2"></i>Needs your attention</div>
            <div class="card-body">
                <?php if (!$hasAttention): ?>
                    <div class="all-caught-up"><i class="bi bi-check-circle-fill me-2"></i>You're all caught up. Nothing needs your action right now.</div>
                <?php endif; ?>

                <?php if ($reviewTotal > 0): ?>
                <div class="attention-group">
                    <div class="attention-title">
                        <span class="stage-dot stage-staging"></span>
                        <?php echo $can_validate ? 'Please test and validate' : 'Ready for review'; ?>
                        <span class="badge rounded-pill stage-staging"><?php echo $reviewTotal; ?></span>
                    </div>
                    <?php foreach ($reviewTasks as $t): ?>
                    <div class="attention-item">
                        <div class="grow">
                            <div class="name" title="<?php echo $e($t->name); ?>"><span class="text-muted me-1"><?php echo $e($t->task_ref); ?></span><?php echo $e($t->name); ?></div>
                            <div class="meta">
                                <?php echo $e($t->project_name . ' · ' . $t->sprint_name); ?>
                                <?php if (!empty($t->staging_since)): ?> · ready <?php echo $e($timeAgo($t->staging_since)); ?><?php endif; ?>
                            </div>
                        </div>
                        <a href="portal/customers/view?task_uuid=<?php echo $e($t->uuid); ?>" class="btn btn-sm btn-outline-primary">Review</a>
                    </div>
                    <?php endforeach; ?>
                    <?php if ($reviewTotal > count($reviewTasks)): ?>
                    <a href="portal/customers/tasks?stages=staging" class="small">See all <?php echo $reviewTotal; ?> tasks to review <i class="bi bi-arrow-right"></i></a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($pending_tickets)): ?>
                <div class="attention-group">
                    <div class="attention-title">
                        <i class="bi bi-life-preserver text-primary"></i> Support tickets waiting for your reply
                        <span class="badge rounded-pill bg-primary"><?php echo count($pending_tickets); ?></span>
                    </div>
                    <?php foreach ($pending_tickets as $t): ?>
                    <div class="attention-item">
                        <div class="grow">
                            <div class="name"><span class="text-muted me-1"><?php echo $e($t->public_ref); ?></span><?php echo $e($t->subject); ?></div>
                            <div class="meta">Last activity <?php echo $e($timeAgo($t->last_activity_at)); ?></div>
                        </div>
                        <a href="portal/customers/ticket/<?php echo $e($t->uuid); ?>" class="btn btn-sm btn-outline-primary">Reply</a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($upcoming_meetings)): ?>
                <div class="attention-group">
                    <div class="attention-title mb-0"><i class="bi bi-people text-success"></i> Meeting notes</div>
                    <div class="small text-muted mb-2">Notes shared live with others in the meeting.</div>
                    <?php foreach ($upcoming_meetings as $m): ?>
                    <div class="attention-item">
                        <div class="grow">
                            <div class="name">
                                <?php if ($m->status === 'live'): ?><span class="badge bg-danger me-1">Live now</span><?php endif; ?>
                                <?php echo $e(date('D j M, H:i', strtotime($m->meeting_datetime))); ?>
                            </div>
                            <?php if (!empty($m->customer_name)): ?><div class="meta"><?php echo $e($m->customer_name); ?></div><?php endif; ?>
                        </div>
                        <a href="portal/customers/meeting/<?php echo $e($m->uuid); ?>" class="btn btn-sm <?php echo $m->status === 'live' ? 'btn-warning' : 'btn-outline-secondary'; ?>"><?php echo $m->status === 'live' ? 'Join' : 'Open'; ?></a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 3. Project progress -->
        <div class="card dash-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bar-chart-line me-2"></i>Project progress</span>
                <a href="portal/customers/sprints" class="small fw-normal">All sprints</a>
            </div>
            <div class="card-body">
                <?php if (empty($dashboard_sprints)): ?>
                    <div class="text-muted">No active work at the moment.</div>
                <?php endif; ?>
                <?php foreach ($dashboard_sprints as $sp): ?>
                    <?php
                        $total = (int) $sp->tasks_count;
                        $pct = (int) $sp->progress_pct;
                    ?>
                    <div class="sprint-card">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <div class="fw-semibold"><?php echo $e($sp->project_name); ?></div>
                                <div class="text-muted small"><?php echo $e($sp->name); ?> · <?php echo $total; ?> task<?php echo $total === 1 ? '' : 's'; ?></div>
                            </div>
                            <div class="text-end">
                                <div class="sprint-pct"><?php echo $pct; ?>%</div>
                                <div class="text-muted small">complete</div>
                            </div>
                        </div>
                        <?php if ($total > 0): ?>
                        <div class="stacked-bar mb-2" role="img" aria-label="Tasks by stage">
                            <?php foreach (['completed','validated','staging','testing','in_progress','on_hold','new','stopped'] as $st):
                                $n = (int) ($sp->{$st . '_tasks'} ?? 0);
                                if ($n === 0) continue; ?>
                                <div class="<?php echo $stageInfo[$st]['class']; ?>" style="width: <?php echo round($n / $total * 100, 2); ?>%" title="<?php echo $e($stageInfo[$st]['label'] . ': ' . $n); ?>"></div>
                            <?php endforeach; ?>
                        </div>
                        <div class="stage-links d-flex flex-wrap gap-3 mb-2">
                            <?php foreach (['staging','new','in_progress','testing','validated','completed','on_hold','stopped'] as $st):
                                $n = (int) ($sp->{$st . '_tasks'} ?? 0);
                                if ($n === 0) continue; ?>
                                <a href="portal/customers/tasks?sprint_id=<?php echo (int) $sp->id; ?>&stages=<?php echo $st; ?>">
                                    <span class="stage-dot <?php echo $stageInfo[$st]['class']; ?>"></span><?php echo $n; ?> <?php echo $e(strtolower($stageInfo[$st]['label'])); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        <a href="portal/customers/tasks?sprint_id=<?php echo (int) $sp->id; ?>" class="btn btn-sm btn-outline-primary">See tasks</a>
                    </div>
                <?php endforeach; ?>

                <div class="mt-3">
                    <a class="small text-muted" data-bs-toggle="collapse" href="#progressHowTo" role="button" aria-expanded="false" aria-controls="progressHowTo">
                        <i class="bi bi-info-circle me-1"></i>How is progress calculated?
                    </a>
                    <div class="collapse mt-2" id="progressHowTo">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle mb-0">
                                <thead>
                                    <tr><th style="width: 200px;">Stage</th><th style="width: 90px;" class="text-center">Weight</th><th>Meaning</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td><span class="stage-chip stage-new">Not started</span></td><td class="text-center">0%</td><td>Task is created and not started.</td></tr>
                                    <tr><td><span class="stage-chip stage-in-progress">In progress</span></td><td class="text-center">20%</td><td>Work has started.</td></tr>
                                    <tr><td><span class="stage-chip stage-testing">Being tested</span></td><td class="text-center">40%</td><td>Our team is testing the change.</td></tr>
                                    <tr><td><span class="stage-chip stage-staging">Ready for review</span></td><td class="text-center">60%</td><td>Ready for you to check on the test (staging) site.</td></tr>
                                    <tr><td><span class="stage-chip stage-validated">Approved</span></td><td class="text-center">80%</td><td>You approved it; it is being finalised.</td></tr>
                                    <tr><td><span class="stage-chip stage-completed">Done</span></td><td class="text-center">100%</td><td>Finished and completed.</td></tr>
                                    <tr><td><span class="stage-chip stage-on-hold">On hold</span></td><td class="text-center">20%</td><td>Paused temporarily.</td></tr>
                                    <tr><td><span class="stage-chip stage-stopped">Stopped</span></td><td class="text-center">0%</td><td>Stopped/cancelled and not progressing.</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted d-block mt-2">Progress is the average of these weights across the sprint's open tasks.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Recent updates -->
    <div class="col-lg-5">
        <div class="card dash-card h-100">
            <div class="card-header"><i class="bi bi-chat-dots me-2"></i>Recent updates from the team</div>
            <div class="card-body">
                <?php if (empty($recent_updates)): ?>
                    <div class="text-muted">No updates in the last 14 days.</div>
                <?php endif; ?>
                <?php foreach ($recent_updates as $u): ?>
                    <?php $author = !empty($u->author_display_name) ? $u->author_display_name : ($u->author_name ?: 'Team'); ?>
                    <div class="update-item">
                        <div class="small text-muted mb-1">
                            <strong class="text-body"><?php echo $e($author); ?></strong>
                            on <a href="portal/customers/view?task_uuid=<?php echo $e($u->task_uuid); ?>"><?php echo $e($u->task_ref); ?></a>
                            · <?php echo $e($timeAgo($u->created_on)); ?>
                        </div>
                        <div class="small fw-semibold mb-1"><?php echo $e($u->task_name); ?></div>
                        <div class="note"><?php echo $e(trim(strip_tags(html_entity_decode((string) $u->notes, ENT_QUOTES, 'UTF-8')))); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
