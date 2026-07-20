<?php include APPPATH . 'views/reports/partials/doc_branding_vars.php'; ?>
<div class="report-doc-footer<?php echo !empty($doc_is_pdf) ? ' report-doc-footer-fixed' : ''; ?>">
    Tweezzo<?php echo $doc_version !== '' ? ' v' . htmlspecialchars($doc_version) : ''; ?>
    &nbsp;|&nbsp;
    Generated <?php echo date('Y-m-d H:i'); ?>
</div>
