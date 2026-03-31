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
    .dash-bg-purple { background: linear-gradient(135deg, #6f42c1, #5a32a3); }
    .dash-bg-indigo { background: linear-gradient(135deg, #4e67c7, #3e56b2); }
    .dash-bg-red { background: linear-gradient(135deg, #ff4d4f, #dc3545); }
    .dash-bg-teal { background: linear-gradient(135deg, #20c997, #17a589); }
</style>

<div class="row justify-content-center mb-4 mt-2">
    <div class="col-lg-10">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-speedometer2 me-2"></i>Customer Portal Overview
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="dash-stat-card dash-bg-blue p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="small text-uppercase">Total Tasks</div>
                                <i class="bi bi-list-task dash-stat-icon"></i>
                            </div>
                            <div class="h3 mb-0"><?php echo (int) ($dashboard_stats->total_tasks ?? 0); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dash-stat-card dash-bg-green p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="small text-uppercase">Completed Tasks</div>
                                <i class="bi bi-check2-circle dash-stat-icon"></i>
                            </div>
                            <div class="h3 mb-0"><?php echo (int) ($dashboard_stats->completed_tasks ?? 0); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dash-stat-card dash-bg-orange p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="small text-uppercase">Open Tasks</div>
                                <i class="bi bi-hourglass-split dash-stat-icon"></i>
                            </div>
                            <div class="h3 mb-0"><?php echo (int) ($dashboard_stats->open_tasks ?? 0); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dash-stat-card dash-bg-purple p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="small text-uppercase">Task Progress</div>
                                <i class="bi bi-graph-up-arrow dash-stat-icon"></i>
                            </div>
                            <div class="h3 mb-0"><?php echo (int) ($dashboard_stats->overall_progress_pct ?? 0); ?>%</div>
                        </div>
                    </div>
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
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-muted">No sprint data available yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
