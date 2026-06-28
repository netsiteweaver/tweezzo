<?php
$changes = isset($changes) && is_array($changes) ? $changes : [];
$files_added = isset($files_added) && is_array($files_added) ? $files_added : [];
$files_removed = isset($files_removed) && is_array($files_removed) ? $files_removed : [];
$change_heading = isset($change_heading) && $change_heading !== '' ? $change_heading : 'What changed';
?>
<?php if (!empty($changes)): ?>
<div style="margin-bottom:20px;padding:14px;border:1px solid #f4d03f;background:#fff8db;">
    <h4 style="margin:0 0 12px 0;font-size:16px;"><?php echo htmlspecialchars($change_heading, ENT_QUOTES, 'UTF-8'); ?></h4>
    <table align="center" border="1" cellpadding="8" cellspacing="0" role="presentation" style="width:100%;border-collapse:collapse;background:#fff;">
        <tbody>
            <tr style="background:#f8f9fa;">
                <th style="text-align:left;width:28%;">Field</th>
                <th style="text-align:left;width:36%;">Before</th>
                <th style="text-align:left;width:36%;">After</th>
            </tr>
            <?php foreach ($changes as $change): ?>
            <tr>
                <td style="vertical-align:top;"><strong><?php echo htmlspecialchars($change['label'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                <td style="vertical-align:top;color:#842029;background:#f8d7da;"><?php echo $change['old']; ?></td>
                <td style="vertical-align:top;color:#0f5132;background:#d1e7dd;"><?php echo $change['new']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php if (!empty($files_added)): ?>
<div style="margin-bottom:20px;padding:14px;border:1px solid #b5dfb8;background:#eaf8ec;">
    <h4 style="margin:0 0 12px 0;font-size:16px;">Files added (<?php echo count($files_added); ?>)</h4>
    <table role="presentation" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <?php foreach ($files_added as $file): ?>
            <td style="padding:0 12px 8px 0;vertical-align:top;text-align:center;">
                <a href="<?php echo htmlspecialchars($file['url'], ENT_QUOTES, 'UTF-8'); ?>" style="text-decoration:none;color:#333;">
                    <img src="<?php echo htmlspecialchars($file['thumb_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="" width="72" height="72" style="display:block;border:1px solid #ccc;border-radius:4px;object-fit:cover;">
                    <span style="display:block;font-size:11px;margin-top:4px;max-width:90px;word-break:break-all;"><?php echo htmlspecialchars($file['file_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                </a>
            </td>
            <?php endforeach; ?>
        </tr>
    </table>
</div>
<?php endif; ?>

<?php if (!empty($files_removed)): ?>
<div style="margin-bottom:20px;padding:14px;border:1px solid #f5c2c7;background:#f8d7da;">
    <h4 style="margin:0 0 12px 0;font-size:16px;">Files removed (<?php echo count($files_removed); ?>)</h4>
    <ul style="margin:0;padding-left:20px;">
        <?php foreach ($files_removed as $file): ?>
        <li style="margin-bottom:4px;"><?php echo htmlspecialchars($file['file_name'], ENT_QUOTES, 'UTF-8'); ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>
