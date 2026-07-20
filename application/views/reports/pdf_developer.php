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
    <h1>Developer Timesheet Report</h1>
    <div class="meta">
        <div><strong>Developer:</strong> <?php echo htmlspecialchars($subject ? $subject->name . ' (' . $subject->email . ')' : ''); ?></div>
        <div><strong>Period:</strong> <?php echo htmlspecialchars($filters['from']); ?> to <?php echo htmlspecialchars($filters['to']); ?></div>
        <div><strong>Rate:</strong> <?php echo htmlspecialchars($currency); ?> <?php echo number_format((float) $rate, 2); ?> / hour</div>
        <div><strong>Generated:</strong> <?php echo date('Y-m-d H:i'); ?></div>
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
            <?php if (empty($rows)): ?>
            <tr><td colspan="8" class="center">No timesheet entries for this period.</td></tr>
            <?php else: ?>
            <?php foreach ($rows as $row):
                $mins = $this->Reports_model->rowMinutes($row);
                $hrs = round($mins / 60, 2);
                $amt = $this->Reports_model->amount($hrs, $rate);
            ?>
            <tr>
                <td class="center"><?php echo htmlspecialchars(substr($row->start_time, 0, 10)); ?></td>
                <td><?php echo htmlspecialchars(!empty($row->taskRef) ? $row->taskRef : $row->taskNumber); ?></td>
                <td><?php echo htmlspecialchars($row->taskName); ?></td>
                <td><?php echo htmlspecialchars($row->customerName); ?></td>
                <td><?php echo htmlspecialchars($row->projectName); ?></td>
                <td><?php echo nl2br(htmlspecialchars($row->notes)); ?></td>
                <td class="center"><?php echo $this->Reports_model->formatDuration($mins); ?></td>
                <td class="right"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($amt, 2); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <?php if (!empty($rows)): ?>
        <tfoot>
            <tr>
                <th colspan="6" class="right">TOTAL (<?php echo (int) $totals['entries']; ?> entries)</th>
                <th class="center"><?php echo $this->Reports_model->formatDuration($totals['minutes']); ?> (<?php echo number_format($totals['hours'], 2); ?> h)</th>
                <th class="right"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($totals['amount'], 2); ?></th>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>
</body>
</html>
