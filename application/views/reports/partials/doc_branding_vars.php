<?php
/**
 * Shared branding for report PDFs and printouts.
 * Include this file (do not load as a CI view) so variables stay in the caller scope.
 * Set $doc_is_pdf = true for Dompdf; false/omit for browser print.
 */
$doc_is_pdf = !empty($doc_is_pdf);
$logoName = !empty($logoDark) ? $logoDark : (!empty($logo) ? $logo : 'LOGO-TWEEZZO-HORIZONTAL.png');
$logoFs = FCPATH . 'assets/images/' . $logoName;
if (!is_file($logoFs)) {
    $logoFs = FCPATH . 'assets/images/LOGO-TWEEZZO-HORIZONTAL.png';
}
if ($doc_is_pdf) {
    // Dompdf chroot is FCPATH — use a path relative to the app root.
    $doc_logo_src = 'assets/images/' . basename($logoFs);
} else {
    $doc_logo_src = base_url('assets/images/' . basename($logoFs));
}
$doc_version = !empty($version) ? $version : '';
?>
