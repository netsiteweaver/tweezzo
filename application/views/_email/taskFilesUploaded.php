<?php
$changes = isset($changes) && is_array($changes) ? $changes : [];
$files_added = isset($files_added) && is_array($files_added) ? $files_added : [];
$actor_name = isset($actor_name) ? $actor_name : '';
?>
<div style='width:100%; text-align: center;'>
    <h3>TASK ATTACHMENTS ADDED</h3>
</div>
<div style="margin:0px auto;max-width:800px;">
    <?php if ($actor_name !== ''): ?>
    <p style="margin:0 0 16px 0;">
        <strong><?php echo htmlspecialchars($actor_name, ENT_QUOTES, 'UTF-8'); ?></strong> uploaded
        <?php echo count($files_added); ?> file<?php echo count($files_added) === 1 ? '' : 's'; ?> to task
        <strong><?php echo htmlspecialchars($taskDetails->task_number ?? '', ENT_QUOTES, 'UTF-8'); ?></strong>
        — <?php echo htmlspecialchars($taskDetails->name ?? '', ENT_QUOTES, 'UTF-8'); ?>
    </p>
    <?php endif; ?>

    <?php $this->load->view('_email/partials/taskChangeSummary', [
        'changes' => $changes,
        'files_added' => $files_added,
        'files_removed' => [],
        'change_heading' => isset($change_heading) ? $change_heading : 'What changed',
    ]); ?>

    <table style='width:100%;margin-bottom:20px;'>
        <tbody>
            <tr>
                <th style='width: 150px; text-align: left; border:1px solid #CCC; padding: 5px 10px;'>Customer</th>
                <td style='border:1px solid #CCC; padding: 5px 10px;'><?php echo htmlspecialchars($taskDetails->company_name ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <th style='width: 150px; text-align: left; border:1px solid #CCC; padding: 5px 10px;'>Project</th>
                <td style='border:1px solid #CCC; padding: 5px 10px;'><?php echo htmlspecialchars($taskDetails->project_name ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <th style='width: 150px; text-align: left; border:1px solid #CCC; padding: 5px 10px;'>Sprint</th>
                <td style='border:1px solid #CCC; padding: 5px 10px;'><?php echo htmlspecialchars($taskDetails->sprint_name ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <th style='width: 150px; text-align: left; border:1px solid #CCC; padding: 5px 10px;'>Source</th>
                <td style='border:1px solid #CCC; padding: 5px 10px;'><?php echo task_source_email_display_html(isset($taskDetails->source) ? $taskDetails->source : ''); ?></td>
            </tr>
        </tbody>
    </table>
</div>

<?php if (!empty($link)): ?>
<div style='margin:30px auto; max-width:800px;'>
    <a class='btn' href="<?php echo $link; ?>">
        <div class="label"><?php echo htmlspecialchars($link_label ?? 'View Task', ENT_QUOTES, 'UTF-8'); ?></div>
    </a>
</div>
<?php endif; ?>
