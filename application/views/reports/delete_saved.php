<div class="card">
    <div class="card-body">
        <h5>Delete this saved report?</h5>
        <p>
            <strong><?php echo htmlspecialchars(!empty($report->title) ? $report->title : $report->subject_name); ?></strong><br>
            <?php if (!empty($report->report_code)): ?>Code: <code><?php echo htmlspecialchars($report->report_code); ?></code><br><?php endif; ?>
            Type: <?php echo htmlspecialchars(ucfirst($report->report_type)); ?><br>
            Period: <?php echo htmlspecialchars($report->date_from . ' → ' . $report->date_to); ?><br>
            Amount: <?php echo htmlspecialchars($report->currency); ?> <?php echo number_format((float) $report->total_amount, 2); ?>
        </p>
        <a href="<?php echo base_url('reports/saved'); ?>" class="btn btn-warning">Cancel</a>
        <a href="<?php echo base_url('reports/delete_saved/' . $report->uuid . '/confirm'); ?>" class="btn btn-danger">Confirm delete</a>
    </div>
</div>
