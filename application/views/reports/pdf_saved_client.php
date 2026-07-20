<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Client Timesheet Report</title>
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
    <h1><?php echo htmlspecialchars(!empty($report->title) ? $report->title : 'Client Timesheet Report'); ?></h1>
    <div class="meta">
        <?php if (!empty($report->report_code)): ?>
        <div><strong>Report #:</strong> <?php echo htmlspecialchars($report->report_code); ?></div>
        <?php endif; ?>
        <div><strong>Customer:</strong> <?php echo htmlspecialchars($report->subject_name); ?></div>
        <div><strong>Period:</strong> <?php echo htmlspecialchars($report->date_from); ?> to <?php echo htmlspecialchars($report->date_to); ?></div>
        <div><strong>Rate:</strong> <?php echo htmlspecialchars($report->currency); ?> <?php echo number_format((float) $report->rate, 2); ?> / hour</div>
        <?php if (!empty($report->billable_only)): ?>
        <div><strong>Filter:</strong> Billable tasks only</div>
        <?php endif; ?>
        <div><strong>Saved:</strong> <?php echo htmlspecialchars($report->created_on); ?></div>
    </div>
    <table>
        <thead>
            <tr>
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
            <tr><td colspan="8" class="center">No lines.</td></tr>
            <?php else: ?>
            <?php foreach ($report->lines as $line): ?>
            <tr>
                <td><?php echo htmlspecialchars($line->task_ref); ?></td>
                <td><?php echo htmlspecialchars($line->task_name); ?></td>
                <td><?php echo htmlspecialchars($line->project_name); ?></td>
                <td><?php echo htmlspecialchars($line->sprint_name); ?></td>
                <td class="center"><?php echo htmlspecialchars(!empty($line->work_type) ? $line->work_type : '—'); ?></td>
                <td class="center"><?php echo (int) $line->entry_count; ?></td>
                <td class="center"><?php echo number_format((float) $line->hours, 2); ?></td>
                <td class="right"><?php echo htmlspecialchars($report->currency); ?> <?php echo number_format((float) $line->amount, 2); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="right">TOTAL (<?php echo (int) $report->entry_count; ?> tasks)</th>
                <th class="center"><?php echo number_format((float) $report->total_hours, 2); ?> h</th>
                <th class="right"><?php echo htmlspecialchars($report->currency); ?> <?php echo number_format((float) $report->total_amount, 2); ?></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
