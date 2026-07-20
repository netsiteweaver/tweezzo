<?php include APPPATH . 'views/reports/partials/doc_branding_vars.php'; ?>
<div class="report-doc-header<?php echo !empty($doc_is_pdf) ? ' report-doc-header-fixed' : ''; ?>">
    <img src="<?php echo htmlspecialchars($doc_logo_src); ?>" alt="Tweezzo">
    <div class="brand-right">
        <strong>Tweezzo</strong><br>
        Timesheet Report
    </div>
</div>
