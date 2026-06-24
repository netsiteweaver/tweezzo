<?php
$footerLogo = !empty($logoDark) ? $logoDark : (!empty($logoLight) ? $logoLight : 'LOGO-TWEEZZO-HORIZONTAL-TRANSPARENT-50PX-TextDark.png');
$footerVersion = !empty($version) ? $version : '1.0';
?>
<div class='tweezzo-footer-container'>
  <div class="tweezzo-footer">
    <img src="<?php echo base_url('assets/images/' . $footerLogo); ?>" alt="Tweezzo" class="tweezzo-footer-logo">
    <span>Powered by <b>Tweezzo v<?php echo htmlspecialchars($footerVersion); ?></b> - Open Source - GPLv3</span>
  </div>
</div>