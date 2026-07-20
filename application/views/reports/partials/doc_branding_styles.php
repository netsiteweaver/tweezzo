<?php include APPPATH . 'views/reports/partials/doc_branding_vars.php'; ?>
<style>
    .report-doc-header {
        width: 100%;
        border-bottom: 1px solid #ccc;
        padding-bottom: 10px;
        margin-bottom: 18px;
        overflow: hidden;
    }
    .report-doc-header img {
        height: 40px;
        max-width: 220px;
    }
    .report-doc-header .brand-right {
        float: right;
        text-align: right;
        font-size: 11px;
        color: #666;
        line-height: 1.4;
        padding-top: 6px;
    }
    .report-doc-footer {
        width: 100%;
        border-top: 1px solid #ccc;
        padding-top: 8px;
        margin-top: 24px;
        font-size: 10px;
        color: #666;
        text-align: center;
    }
    <?php if (!empty($doc_is_pdf)): ?>
    .report-doc-header-fixed {
        position: fixed;
        top: -95px;
        left: 0;
        right: 0;
        height: 70px;
        overflow: visible;
        padding-top: 8px;
        box-sizing: border-box;
    }
    .report-doc-footer-fixed {
        position: fixed;
        bottom: -45px;
        left: 0;
        right: 0;
        height: 35px;
        margin-top: 0;
    }
    @page {
        margin: 120px 36px 55px 36px;
    }
    body {
        margin: 0;
        padding: 0;
    }
    <?php endif; ?>
</style>
