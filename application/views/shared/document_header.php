<?php
$company = isset($company) ? $company : null;
$document_title = isset($document_title) ? $document_title : '';
$document_subtitle = isset($document_subtitle) ? $document_subtitle : '';
$logoUrl = company_logo_url($company);
$addressLine = company_address_line($company);
?>
<div class="row print-only document-header-row">
    <div class="col-md-12 document-header">
        <div class="document-header-top">
            <div class="document-header-brand">
                <?php if (!empty($logoUrl)): ?>
                <img src="<?php echo $logoUrl; ?>" alt="<?php echo htmlspecialchars(isset($company->name) ? $company->name : 'Logo'); ?>" class="document-header-logo">
                <?php endif; ?>
                <div class="document-header-company">
                    <?php
                        $legalName = '';
                        if (!empty($company->legal_name)) {
                            $legalName = $company->legal_name;
                        } elseif (!empty($company->name)) {
                            $legalName = $company->name;
                        }
                    ?>
                    <?php if (!empty($legalName)): ?>
                    <div class="document-header-legal-name"><?php echo htmlspecialchars($legalName); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($addressLine)): ?>
                    <div class="document-header-address"><?php echo htmlspecialchars($addressLine); ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (!empty($document_title)): ?>
            <h1 class="document-header-title"><?php echo htmlspecialchars($document_title); ?></h1>
            <?php endif; ?>
        </div>
        <?php if (!empty($document_subtitle)): ?>
        <div class="document-header-subtitle page-title"><?php echo $document_subtitle; ?></div>
        <?php endif; ?>
    </div>
</div>
