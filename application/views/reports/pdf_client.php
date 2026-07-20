<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Client Timesheet Report</title>
    <?php $this->load->view('reports/partials/doc_branding_styles', ['doc_is_pdf' => true]); ?>
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
    <?php $this->load->view('reports/partials/doc_header', ['doc_is_pdf' => true]); ?>

    <h1>Client Timesheet Report</h1>
    <div class="meta">
        <div><strong>Customer:</strong> <?php echo htmlspecialchars($subject ? $subject->company_name : ''); ?></div>
        <div><strong>Period:</strong> <?php echo htmlspecialchars($filters['from']); ?> to <?php echo htmlspecialchars($filters['to']); ?></div>
        <div><strong>Rate:</strong> <?php echo htmlspecialchars($currency); ?> <?php echo number_format((float) $rate, 2); ?> / hour</div>
        <?php if (!empty($filters['billable_only'])): ?>
        <div><strong>Filter:</strong> Billable tasks only</div>
        <?php endif; ?>
        <div><strong>Generated:</strong> <?php echo date('Y-m-d H:i'); ?></div>
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
            <?php if (empty($rows)): ?>
            <tr><td colspan="8" class="center">No timesheet hours for this period.</td></tr>
            <?php else: ?>
            <?php foreach ($rows as $row):
                $amt = $this->Reports_model->amount($row->totalHours, $rate);
            ?>
            <tr>
                <td><?php echo htmlspecialchars(!empty($row->taskRef) ? $row->taskRef : $row->taskNumber); ?></td>
                <td><?php echo htmlspecialchars($row->taskName); ?></td>
                <td><?php echo htmlspecialchars($row->projectName); ?></td>
                <td><?php echo htmlspecialchars($row->sprintName); ?></td>
                <td class="center"><?php echo htmlspecialchars(!empty($row->workType) ? $row->workType : '—'); ?></td>
                <td class="center"><?php echo (int) $row->entryCount; ?></td>
                <td class="center"><?php echo number_format($row->totalHours, 2); ?></td>
                <td class="right"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($amt, 2); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <?php if (!empty($rows)): ?>
        <tfoot>
            <tr>
                <th colspan="6" class="right">TOTAL (<?php echo (int) $totals['entries']; ?> tasks)</th>
                <th class="center"><?php echo number_format($totals['hours'], 2); ?> h</th>
                <th class="right"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($totals['amount'], 2); ?></th>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>

    <?php $this->load->view('reports/partials/doc_footer', ['doc_is_pdf' => true]); ?>
</body>
</html>
