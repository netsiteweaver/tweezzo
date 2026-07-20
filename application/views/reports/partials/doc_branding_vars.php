<?php
/**
 * Shared branding for report PDFs and printouts.
 * Include this file (do not load as a CI view) so variables stay in the caller scope.
 * Set $doc_is_pdf = true for Dompdf; false/omit for browser print.
 */

if (!function_exists('report_pdf_logo_path')) {
    /**
     * Return a Dompdf-safe relative logo path (chroot = FCPATH).
     * Transparent PNGs are flattened onto white — Dompdf paints alpha as black.
     *
     * @param string $logoFs Absolute filesystem path to the source logo
     * @return string
     */
    function report_pdf_logo_path($logoFs)
    {
        $fallback = 'assets/images/LOGO-TWEEZZO-HORIZONTAL.png';
        if (!is_file($logoFs)) {
            return $fallback;
        }

        $ext = strtolower(pathinfo($logoFs, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'gif'], true)) {
            return 'assets/images/' . basename($logoFs);
        }

        if ($ext !== 'png' || !function_exists('imagecreatefrompng')) {
            if (is_file(FCPATH . $fallback)) {
                return $fallback;
            }
            return 'assets/images/' . basename($logoFs);
        }

        $src = @imagecreatefrompng($logoFs);
        if ($src === false) {
            return is_file(FCPATH . $fallback) ? $fallback : ('assets/images/' . basename($logoFs));
        }

        $width = imagesx($src);
        $height = imagesy($src);
        $hasAlpha = false;
        $stepX = max(1, (int) floor($width / 40));
        $stepY = max(1, (int) floor($height / 20));
        for ($y = 0; $y < $height && !$hasAlpha; $y += $stepY) {
            for ($x = 0; $x < $width; $x += $stepX) {
                $rgba = imagecolorat($src, $x, $y);
                $alpha = ($rgba & 0x7F000000) >> 24;
                if ($alpha > 0) {
                    $hasAlpha = true;
                    break;
                }
            }
        }

        if (!$hasAlpha) {
            imagedestroy($src);
            return 'assets/images/' . basename($logoFs);
        }

        $cacheDir = FCPATH . 'application/cache/pdf_logos/';
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }
        $cacheName = md5($logoFs . '|' . (string) @filemtime($logoFs) . '|' . (string) @filesize($logoFs)) . '.png';
        $cacheFs = $cacheDir . $cacheName;
        $cacheRel = 'application/cache/pdf_logos/' . $cacheName;

        if (!is_file($cacheFs)) {
            $dst = imagecreatetruecolor($width, $height);
            $white = imagecolorallocate($dst, 255, 255, 255);
            imagefilledrectangle($dst, 0, 0, $width, $height, $white);
            imagealphablending($dst, true);
            imagecopy($dst, $src, 0, 0, 0, 0, $width, $height);
            imagesavealpha($dst, false);
            if (@imagepng($dst, $cacheFs, 6) === false) {
                imagedestroy($src);
                imagedestroy($dst);
                return is_file(FCPATH . $fallback) ? $fallback : ('assets/images/' . basename($logoFs));
            }
            imagedestroy($dst);
        }

        imagedestroy($src);
        return is_file($cacheFs) ? $cacheRel : (is_file(FCPATH . $fallback) ? $fallback : ('assets/images/' . basename($logoFs)));
    }
}

$doc_is_pdf = !empty($doc_is_pdf);
$logoName = !empty($logoDark) ? $logoDark : (!empty($logo) ? $logo : 'LOGO-TWEEZZO-HORIZONTAL.png');
$logoFs = FCPATH . 'assets/images/' . $logoName;
if (!is_file($logoFs)) {
    $logoFs = FCPATH . 'assets/images/LOGO-TWEEZZO-HORIZONTAL.png';
}

if ($doc_is_pdf) {
    $doc_logo_src = report_pdf_logo_path($logoFs);
} else {
    $doc_logo_src = base_url('assets/images/' . basename($logoFs));
}
$doc_version = !empty($version) ? $version : '';
?>
