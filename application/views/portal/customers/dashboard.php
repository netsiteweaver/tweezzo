<style>
    .dash-stat-card {
        border: 0;
        border-radius: 10px;
        color: #fff;
        box-shadow: 0 6px 18px rgba(0,0,0,.12);
    }
    .dash-stat-icon {
        font-size: 1.25rem;
        opacity: .9;
    }
    .dash-bg-blue { background: linear-gradient(135deg, #1c8be6, #0d6efd); }
    .dash-bg-green { background: linear-gradient(135deg, #44ab8e, #2f9d7d); }
    .dash-bg-orange { background: linear-gradient(135deg, #f36930, #e5571c); }
    .dash-bg-amber { background: linear-gradient(135deg, #ff9f1a, #f77f00); }
    .dash-bg-purple { background: linear-gradient(135deg, #6f42c1, #5a32a3); }
    .dash-bg-indigo { background: linear-gradient(135deg, #4e67c7, #3e56b2); }
    .dash-bg-red { background: linear-gradient(135deg, #ff4d4f, #dc3545); }
    .dash-bg-teal { background: linear-gradient(135deg, #20c997, #17a589); }
    .dash-stage-box {
        color: #fff;
        border-radius: 8px;
        padding: 6px 8px;
        min-height: 56px;
        box-shadow: 0 3px 8px rgba(0,0,0,.10);
    }
    .dash-stage-box .label {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        line-height: 1.1;
        opacity: .95;
    }
    .dash-stage-box .count {
        font-size: 1.15rem;
        line-height: 1;
        font-weight: 700;
        margin-top: 2px;
    }
    .stage-new { background-color: #1c8be6; }
    .stage-in-progress { background-color: #44ab8e; }
    .stage-testing { background-color: #98c363; }
    .stage-staging { background-color: #f36930; }
    .stage-validated { background-color: #c44866; }
    .stage-completed { background-color: #4e67c7; }
    .stage-on-hold { background-color: #ff0000; }
    .stage-chip {
        color: #fff;
        display: inline-block;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        min-width: 92px;
        text-align: center;
    }
</style>

<div class="row justify-content-center mb-4 mt-2">
    <div class="col-lg-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-speedometer2 me-2"></i>Customer Portal Overview
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4 justify-content-md-between">
                    <div class="col-12 col-md-2">
                        <div class="dash-stat-card dash-bg-blue p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="small text-uppercase">Total Tasks</div>
                                <i class="bi bi-list-task dash-stat-icon"></i>
                            </div>
                            <div class="h3 mb-0"><?php echo (int) ($dashboard_stats->total_tasks ?? 0); ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-md-2">
                        <div class="dash-stat-card dash-bg-green p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="small text-uppercase">Completed Tasks</div>
                                <i class="bi bi-check2-circle dash-stat-icon"></i>
                            </div>
                            <div class="h3 mb-0"><?php echo (int) ($dashboard_stats->completed_tasks ?? 0); ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-md-2">
                        <div class="dash-stat-card dash-bg-amber p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="small text-uppercase">In Staging</div>
                                <i class="bi bi-hourglass-top dash-stat-icon"></i>
                            </div>
                            <div class="h3 mb-0"><?php echo (int) ($dashboard_stats->staging_tasks ?? 0); ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-md-2">
                        <div class="dash-stat-card dash-bg-orange p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="small text-uppercase">Open Tasks</div>
                                <i class="bi bi-hourglass-split dash-stat-icon"></i>
                            </div>
                            <div class="h3 mb-0"><?php echo (int) ($dashboard_stats->open_tasks ?? 0); ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-md-2">
                        <div class="dash-stat-card dash-bg-purple p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="small text-uppercase">Task Progress</div>
                                <i class="bi bi-graph-up-arrow dash-stat-icon"></i>
                            </div>
                            <div class="h3 mb-0"><?php echo (int) ($dashboard_stats->overall_progress_pct ?? 0); ?>%</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="mb-0">Tasks by Stage</h6>
                </div>
                <div class="row row-cols-2 row-cols-md-4 row-cols-lg-7 g-1 mb-3">
                    <?php if ((int) ($dashboard_stats->new_tasks ?? 0) > 0): ?>
                    <div class="col">
                        <div class="dash-stage-box stage-new">
                            <div class="label"><i class="bi bi-circle-fill me-1"></i>New</div>
                            <div class="count"><?php echo (int) ($dashboard_stats->new_tasks ?? 0); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ((int) ($dashboard_stats->in_progress_tasks ?? 0) > 0): ?>
                    <div class="col">
                        <div class="dash-stage-box stage-in-progress">
                            <div class="label"><i class="bi bi-play-circle me-1"></i>In Progress</div>
                            <div class="count"><?php echo (int) ($dashboard_stats->in_progress_tasks ?? 0); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ((int) ($dashboard_stats->testing_tasks ?? 0) > 0): ?>
                    <div class="col">
                        <div class="dash-stage-box stage-testing">
                            <div class="label"><i class="bi bi-beaker me-1"></i>Testing</div>
                            <div class="count"><?php echo (int) ($dashboard_stats->testing_tasks ?? 0); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ((int) ($dashboard_stats->staging_tasks ?? 0) > 0): ?>
                    <div class="col">
                        <div class="dash-stage-box stage-staging">
                            <div class="label"><i class="bi bi-hourglass-top me-1"></i>Staging</div>
                            <div class="count"><?php echo (int) ($dashboard_stats->staging_tasks ?? 0); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ((int) ($dashboard_stats->validated_tasks ?? 0) > 0): ?>
                    <div class="col">
                        <div class="dash-stage-box stage-validated">
                            <div class="label"><i class="bi bi-patch-check me-1"></i>Validated</div>
                            <div class="count"><?php echo (int) ($dashboard_stats->validated_tasks ?? 0); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ((int) ($dashboard_stats->completed_tasks ?? 0) > 0): ?>
                    <div class="col">
                        <div class="dash-stage-box stage-completed">
                            <div class="label"><i class="bi bi-check2-circle me-1"></i>Completed</div>
                            <div class="count"><?php echo (int) ($dashboard_stats->completed_tasks ?? 0); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ((int) ($dashboard_stats->on_hold_tasks ?? 0) > 0): ?>
                    <div class="col">
                        <div class="dash-stage-box stage-on-hold">
                            <div class="label"><i class="bi bi-pause-circle me-1"></i>On Hold</div>
                            <div class="count"><?php echo (int) ($dashboard_stats->on_hold_tasks ?? 0); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="dash-stat-card dash-bg-indigo p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="small text-uppercase">Sprints</div>
                                <i class="bi bi-flag dash-stat-icon"></i>
                            </div>
                            <div class="h4 mb-0"><?php echo (int) ($dashboard_stats->total_sprints ?? 0); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dash-stat-card dash-bg-teal p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="small text-uppercase">Completed Sprints</div>
                                <i class="bi bi-trophy dash-stat-icon"></i>
                            </div>
                            <div class="h4 mb-0"><?php echo (int) ($dashboard_stats->completed_sprints ?? 0); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dash-stat-card dash-bg-red p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="small text-uppercase">Sprint Progress (Avg)</div>
                                <i class="bi bi-bar-chart-line dash-stat-icon"></i>
                            </div>
                            <div class="h4 mb-0"><?php echo (int) ($dashboard_stats->sprint_progress_pct ?? 0); ?>%</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="mb-0">Sprint Progress</h6>
                    <a href="<?php echo base_url('portal/customers/sprints'); ?>" class="btn btn-sm btn-outline-primary">View all sprints</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Sprint</th>
                                <th>Project</th>
                                <th class="text-center">Tasks</th>
                                <th class="text-center">Completed</th>
                                <th style="width: 220px;">Progress</th>
                                <th class="text-center" style="width: 130px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($dashboard_sprints)): ?>
                                <?php foreach ($dashboard_sprints as $sprint): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($sprint->name); ?></td>
                                        <td><?php echo htmlspecialchars($sprint->project_name); ?></td>
                                        <td class="text-center"><?php echo (int) $sprint->tasks_count; ?></td>
                                        <td class="text-center"><?php echo (int) $sprint->completed_tasks; ?></td>
                                        <td>
                                            <?php $barColor = ((int) $sprint->progress_pct >= 100) ? '#44ab8e' : (((int) $sprint->progress_pct >= 60) ? '#1c8be6' : '#f36930'); ?>
                                            <div class="progress" style="height:18px;">
                                                <div class="progress-bar" role="progressbar" style="width: <?php echo (int) $sprint->progress_pct; ?>%; background-color: <?php echo $barColor; ?>;" aria-valuenow="<?php echo (int) $sprint->progress_pct; ?>" aria-valuemin="0" aria-valuemax="100">
                                                    <?php echo (int) $sprint->progress_pct; ?>%
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?php echo base_url('portal/customers/tasks?sprint_id=' . (int) $sprint->id); ?>" class="btn btn-sm btn-outline-primary">
                                                View Tasks
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-muted">No sprint data available yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <h6 class="mb-2">How Task Progress Is Calculated</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 220px;">Stage</th>
                                    <th style="width: 140px;" class="text-center">Weight</th>
                                    <th>Meaning</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="stage-chip stage-new">New</span></td>
                                    <td class="text-center">0%</td>
                                    <td>Task is created and not started.</td>
                                </tr>
                                <tr>
                                    <td><span class="stage-chip stage-in-progress">In Progress</span></td>
                                    <td class="text-center">20%</td>
                                    <td>Work has started.</td>
                                </tr>
                                <tr>
                                    <td><span class="stage-chip stage-testing">Testing</span></td>
                                    <td class="text-center">40%</td>
                                    <td>Implementation is under testing.</td>
                                </tr>
                                <tr>
                                    <td><span class="stage-chip stage-staging">Staging</span></td>
                                    <td class="text-center">60%</td>
                                    <td>Ready on staging environment for review.</td>
                                </tr>
                                <tr>
                                    <td><span class="stage-chip stage-validated">Validated</span></td>
                                    <td class="text-center">80%</td>
                                    <td>Accepted by customer but not fully closed.</td>
                                </tr>
                                <tr>
                                    <td><span class="stage-chip stage-completed">Completed</span></td>
                                    <td class="text-center">100%</td>
                                    <td>Finished and completed.</td>
                                </tr>
                                <tr>
                                    <td><span class="stage-chip stage-on-hold">On Hold</span></td>
                                    <td class="text-center">20%</td>
                                    <td>Paused temporarily.</td>
                                </tr>
                                <tr>
                                    <td><span class="stage-chip" style="background-color:#6c757d;">Stopped</span></td>
                                    <td class="text-center">0%</td>
                                    <td>Stopped/cancelled and not progressing.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted d-block mt-2">
                        Task Progress is calculated as the average of these stage weights across open tasks.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
