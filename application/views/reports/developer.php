<?php
$from = $filters['from'];
$to = $filters['to'];
$developer_id = !empty($filters['developer_id']) ? $filters['developer_id'] : '';
$customer_id = !empty($filters['customer_id']) ? $filters['customer_id'] : '';
$project_id = !empty($filters['project_id']) ? $filters['project_id'] : '';
$sprint_id = !empty($filters['sprint_id']) ? $filters['sprint_id'] : '';
$rateVal = ($rate !== null) ? $rate : '';
?>
<form action="<?php echo base_url('reports/developer'); ?>" method="get" class="no-print mb-3">
    <div class="row">
        <div class="col-md-2">
            <label for="from">From</label>
            <input class="form-control" type="date" name="from" id="from" value="<?php echo htmlspecialchars($from); ?>" required>
        </div>
        <div class="col-md-2">
            <label for="to">To</label>
            <input class="form-control" type="date" name="to" id="to" value="<?php echo htmlspecialchars($to); ?>" required>
        </div>
        <div class="col-md-3">
            <label for="developer_id">Developer <span class="text-danger">*</span></label>
            <select name="developer_id" id="developer_id" class="form-control" required>
                <option value="">Select developer</option>
                <?php foreach ($developers as $d): ?>
                <option value="<?php echo (int) $d->id; ?>" <?php echo ((string) $developer_id === (string) $d->id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($d->name . ' (' . $d->email . ')'); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="rate">Hourly rate <span class="text-danger">*</span></label>
            <input class="form-control" type="number" step="0.01" min="0" name="rate" id="rate" value="<?php echo htmlspecialchars((string) $rateVal); ?>" required placeholder="e.g. 500">
        </div>
        <div class="col-md-1">
            <label for="currency">Currency</label>
            <input class="form-control" type="text" name="currency" id="currency" maxlength="8" value="<?php echo htmlspecialchars($currency); ?>">
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-md-2">
            <label for="customer_id">Customer</label>
            <select name="customer_id" id="customer_id" class="form-control">
                <option value="">All</option>
                <?php foreach ($customers as $c): ?>
                <option value="<?php echo (int) $c->customer_id; ?>" <?php echo ((string) $customer_id === (string) $c->customer_id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($c->company_name); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="project_id">Project</label>
            <select name="project_id" id="project_id" class="form-control">
                <option value="">All</option>
                <?php foreach ($projects as $project): ?>
                <option value="<?php echo (int) $project->id; ?>" <?php echo ((string) $project_id === (string) $project->id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($project->name); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 <?php echo empty($project_id) ? 'd-none' : ''; ?>" id="sprint_filter_wrap">
            <label for="sprint_id">Sprint</label>
            <select name="sprint_id" id="sprint_id" class="form-control">
                <option value="">All</option>
                <?php foreach ($sprints as $sprint): ?>
                <option value="<?php echo (int) $sprint->id; ?>" <?php echo ((string) $sprint_id === (string) $sprint->id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($sprint->name); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 mt-4">
            <button type="submit" class="btn btn-info"><i class="fa fa-check"></i> Generate</button>
            <a href="<?php echo base_url('reports/developer'); ?>" class="btn btn-warning"><i class="fa fa-undo"></i></a>
            <?php if (!empty($generated)): ?>
            <a class="btn btn-danger" href="<?php echo base_url('reports/developer?' . http_build_query(array_merge($filters, ['rate' => $rate, 'currency' => $currency, 'output' => 'pdf']))); ?>">
                <i class="fa fa-file-pdf"></i> Download PDF
            </a>
            <?php endif; ?>
        </div>
    </div>
</form>

<?php if (!empty($generated)): ?>
<form action="<?php echo base_url('reports/save'); ?>" method="post" class="no-print mb-3">
    <input type="hidden" name="report_type" value="developer">
    <input type="hidden" name="from" value="<?php echo htmlspecialchars($from); ?>">
    <input type="hidden" name="to" value="<?php echo htmlspecialchars($to); ?>">
    <input type="hidden" name="developer_id" value="<?php echo htmlspecialchars((string) $developer_id); ?>">
    <input type="hidden" name="customer_id" value="<?php echo htmlspecialchars((string) $customer_id); ?>">
    <input type="hidden" name="project_id" value="<?php echo htmlspecialchars((string) $project_id); ?>">
    <input type="hidden" name="sprint_id" value="<?php echo htmlspecialchars((string) $sprint_id); ?>">
    <input type="hidden" name="rate" value="<?php echo htmlspecialchars((string) $rate); ?>">
    <input type="hidden" name="currency" value="<?php echo htmlspecialchars($currency); ?>">
    <div class="row">
        <div class="col-md-6">
            <label for="title">Save as (optional title)</label>
            <input type="text" class="form-control" name="title" id="title" placeholder="e.g. June 2026 — John">
        </div>
        <div class="col-md-3 mt-4">
            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save report</button>
        </div>
    </div>
</form>
<?php endif; ?>

<?php if (empty($generated)): ?>
<div class="alert alert-secondary">Select a developer, date range, and hourly rate, then click Generate.</div>
<?php else: ?>
<div class="mb-3">
    <strong>Developer:</strong> <?php echo htmlspecialchars($subject ? $subject->name . ' (' . $subject->email . ')' : ''); ?><br>
    <strong>Period:</strong> <?php echo htmlspecialchars($from); ?> → <?php echo htmlspecialchars($to); ?><br>
    <strong>Rate:</strong> <?php echo htmlspecialchars($currency . ' ' . number_format((float) $rate, 2)); ?> / hour
</div>

<div class="box">
    <div class="box-body table-responsive no-padding">
        <table class="table table-bordered table-hover">
            <thead>
                <tr class="text-center text-uppercase">
                    <th>Date</th>
                    <th>Code</th>
                    <th>Task</th>
                    <th>Customer</th>
                    <th>Project</th>
                    <th>Notes</th>
                    <th>Start</th>
                    <th>Finish</th>
                    <th>Duration</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                <tr><td colspan="10" class="text-center text-muted">No timesheet entries for this period.</td></tr>
                <?php else: ?>
                <?php foreach ($rows as $row):
                    $mins = $this->Reports_model->rowMinutes($row);
                    $hrs = round($mins / 60, 2);
                    $amt = $this->Reports_model->amount($hrs, $rate);
                ?>
                <tr>
                    <td class="text-center"><?php echo htmlspecialchars(substr($row->start_time, 0, 10)); ?></td>
                    <td><?php echo htmlspecialchars(!empty($row->taskRef) ? $row->taskRef : $row->taskNumber); ?></td>
                    <td><?php echo htmlspecialchars($row->taskName); ?></td>
                    <td><?php echo htmlspecialchars($row->customerName); ?></td>
                    <td><?php echo htmlspecialchars($row->projectName); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($row->notes)); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row->start_time); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row->finish_time); ?></td>
                    <td class="text-center"><?php echo $this->Reports_model->formatDuration($mins); ?></td>
                    <td class="text-right"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($amt, 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <?php if (!empty($rows)): ?>
            <tfoot>
                <tr>
                    <th colspan="8" class="text-right">TOTAL (<?php echo (int) $totals['entries']; ?> entries)</th>
                    <th class="text-center"><?php echo $this->Reports_model->formatDuration($totals['minutes']); ?> (<?php echo number_format($totals['hours'], 2); ?> h)</th>
                    <th class="text-right"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($totals['amount'], 2); ?></th>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>
<?php endif; ?>

<?php $this->load->view('reports/partials/filter_cascade_js'); ?>
