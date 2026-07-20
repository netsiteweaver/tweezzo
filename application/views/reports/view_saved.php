<?php
$isDeveloper = ($report->report_type === 'developer');
$currency = $report->currency;
?>
<div class="no-print mb-3">
    <a href="<?php echo base_url('reports/saved'); ?>" class="btn btn-warning"><i class="fa fa-chevron-left"></i> Back</a>
    <a href="<?php echo base_url('reports/view_saved/' . $report->uuid . '?output=pdf'); ?>" class="btn btn-danger"><i class="fa fa-file-pdf"></i> Download PDF</a>
    <a href="<?php echo base_url('reports/delete_saved/' . $report->uuid); ?>" class="btn btn-outline-danger"><i class="fa fa-trash"></i> Delete</a>
</div>

<div class="mb-3">
    <h4>
        <?php if (!empty($report->report_code)): ?>
        <code><?php echo htmlspecialchars($report->report_code); ?></code>
        —
        <?php endif; ?>
        <?php echo htmlspecialchars(!empty($report->title) ? $report->title : 'Saved report'); ?>
    </h4>
    <strong>Type:</strong> <?php echo htmlspecialchars(ucfirst($report->report_type)); ?><br>
    <strong><?php echo $isDeveloper ? 'Developer' : 'Customer'; ?>:</strong>
    <?php echo htmlspecialchars($report->subject_name); ?>
    <?php if (!empty($report->subject_email)): ?>
        (<?php echo htmlspecialchars($report->subject_email); ?>)
    <?php endif; ?><br>
    <strong>Period:</strong> <?php echo htmlspecialchars($report->date_from); ?> → <?php echo htmlspecialchars($report->date_to); ?><br>
    <strong>Rate:</strong> <?php echo htmlspecialchars($currency); ?> <?php echo number_format((float) $report->rate, 2); ?> / hour<br>
    <?php if (!empty($report->billable_only)): ?><strong>Filter:</strong> Billable tasks only<br><?php endif; ?>
    <strong>Saved:</strong> <?php echo htmlspecialchars($report->created_on); ?>
    <?php if (!empty($report->created_by_name)): ?> by <?php echo htmlspecialchars($report->created_by_name); ?><?php endif; ?>
</div>

<div class="box">
    <div class="box-body table-responsive no-padding">
        <?php if ($isDeveloper): ?>
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
                <?php if (empty($report->lines)): ?>
                <tr><td colspan="10" class="text-center text-muted">No lines in this report.</td></tr>
                <?php else: ?>
                <?php foreach ($report->lines as $line): ?>
                <tr>
                    <td class="text-center"><?php echo htmlspecialchars($line->line_date); ?></td>
                    <td><?php echo htmlspecialchars($line->task_ref); ?></td>
                    <td><?php echo htmlspecialchars($line->task_name); ?></td>
                    <td><?php echo htmlspecialchars($line->customer_name); ?></td>
                    <td><?php echo htmlspecialchars($line->project_name); ?></td>
                    <td><?php echo nl2br(htmlspecialchars((string) $line->notes)); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars((string) $line->start_time); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars((string) $line->finish_time); ?></td>
                    <td class="text-center"><?php echo $this->Reports_model->formatDuration($line->duration_minutes); ?></td>
                    <td class="text-right"><?php echo htmlspecialchars($currency); ?> <?php echo number_format((float) $line->amount, 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="8" class="text-right">TOTAL (<?php echo (int) $report->entry_count; ?> entries)</th>
                    <th class="text-center"><?php echo $this->Reports_model->formatDuration($report->total_minutes); ?> (<?php echo number_format((float) $report->total_hours, 2); ?> h)</th>
                    <th class="text-right"><?php echo htmlspecialchars($currency); ?> <?php echo number_format((float) $report->total_amount, 2); ?></th>
                </tr>
            </tfoot>
        </table>
        <?php else: ?>
        <table class="table table-bordered table-hover">
            <thead>
                <tr class="text-center text-uppercase">
                    <th>Code</th>
                    <th>Task</th>
                    <th>Project</th>
                    <th>Sprint</th>
                    <th>Work type</th>
                    <th>Entries</th>
                    <th>Hours</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($report->lines)): ?>
                <tr><td colspan="8" class="text-center text-muted">No lines in this report.</td></tr>
                <?php else: ?>
                <?php foreach ($report->lines as $line): ?>
                <tr>
                    <td><?php echo htmlspecialchars($line->task_ref); ?></td>
                    <td><?php echo htmlspecialchars($line->task_name); ?></td>
                    <td><?php echo htmlspecialchars($line->project_name); ?></td>
                    <td><?php echo htmlspecialchars($line->sprint_name); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars(!empty($line->work_type) ? $line->work_type : '—'); ?></td>
                    <td class="text-center"><?php echo (int) $line->entry_count; ?></td>
                    <td class="text-center"><?php echo number_format((float) $line->hours, 2); ?></td>
                    <td class="text-right"><?php echo htmlspecialchars($currency); ?> <?php echo number_format((float) $line->amount, 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-right">TOTAL (<?php echo (int) $report->entry_count; ?> tasks)</th>
                    <th></th>
                    <th class="text-center"><?php echo number_format((float) $report->total_hours, 2); ?> h</th>
                    <th class="text-right"><?php echo htmlspecialchars($currency); ?> <?php echo number_format((float) $report->total_amount, 2); ?></th>
                </tr>
            </tfoot>
        </table>
        <?php endif; ?>
    </div>
</div>
