<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Developer Timesheet Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        h1 { font-size: 18px; margin: 0 0 8px 0; }
        .meta { margin-bottom: 16px; line-height: 1.5; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 5px 6px; vertical-align: top; }
        th { background: #eee; text-transform: uppercase; font-size: 10px; }
        .right { text-align: right; }
        .center { text-align: center; }
        tfoot th { background: #f5f5f5; }
    </style>
</head>
<body>
    <h1><?php echo htmlspecialchars(!empty($report->title) ? $report->title : 'Developer Timesheet Report'); ?></h1>
    <div class="meta">
        <?php if (!empty($report->report_code)): ?>
        <div><strong>Report #:</strong> <?php echo htmlspecialchars($report->report_code); ?></div>
        <?php endif; ?>
        <div><strong>Developer:</strong> <?php echo htmlspecialchars($report->subject_name); ?><?php if (!empty($report->subject_email)): ?> (<?php echo htmlspecialchars($report->subject_email); ?>)<?php endif; ?></div>
        <div><strong>Period:</strong> <?php echo htmlspecialchars($report->date_from); ?> to <?php echo htmlspecialchars($report->date_to); ?></div>
        <div><strong>Rate:</strong> <?php echo htmlspecialchars($report->currency); ?> <?php echo number_format((float) $report->rate, 2); ?> / hour</div>
        <div><strong>Saved:</strong> <?php echo htmlspecialchars($report->created_on); ?></div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Code</th>
                <th>Task</th>
                <th>Customer</th>
                <th>Project</th>
                <th>Notes</th>
                <th>Duration</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($report->lines)): ?>
            <tr><td colspan="8" class="center">No lines.</td></tr>
            <?php else: ?>
            <?php foreach ($report->lines as $line): ?>
            <tr>
                <td class="center"><?php echo htmlspecialchars($line->line_date); ?></td>
                <td><?php echo htmlspecialchars($line->task_ref); ?></td>
                <td><?php echo htmlspecialchars($line->task_name); ?></td>
                <td><?php echo htmlspecialchars($line->customer_name); ?></td>
                <td><?php echo htmlspecialchars($line->project_name); ?></td>
                <td><?php echo nl2br(htmlspecialchars((string) $line->notes)); ?></td>
                <td class="center"><?php echo $this->Reports_model->formatDuration($line->duration_minutes); ?></td>
                <td class="right"><?php echo htmlspecialchars($report->currency); ?> <?php echo number_format((float) $line->amount, 2); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="right">TOTAL (<?php echo (int) $report->entry_count; ?> entries)</th>
                <th class="center"><?php echo $this->Reports_model->formatDuration($report->total_minutes); ?> (<?php echo number_format((float) $report->total_hours, 2); ?> h)</th>
                <th class="right"><?php echo htmlspecialchars($report->currency); ?> <?php echo number_format((float) $report->total_amount, 2); ?></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
