<?php
$email_mode = isset($email_mode) ? $email_mode : 'create';
$changes = isset($changes) && is_array($changes) ? $changes : [];
$files_added = isset($files_added) && is_array($files_added) ? $files_added : [];
$files_removed = isset($files_removed) && is_array($files_removed) ? $files_removed : [];
$actor_name = isset($actor_name) ? $actor_name : '';
?>
<div style='width:100%; text-align: center;'>
    <h3><?php echo strtoupper($title);?></h3>
</div>

<?php if ($email_mode === 'update'): ?>
<div style="margin:0px auto;max-width:800px;">
    <p style="margin:0 0 16px 0;">
        <?php if ($actor_name !== ''): ?>
        <strong><?php echo htmlspecialchars($actor_name, ENT_QUOTES, 'UTF-8'); ?></strong> updated task
        <?php else: ?>
        Task updated
        <?php endif; ?>
        <strong><?php echo htmlspecialchars($data['task_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong>
        — <?php echo htmlspecialchars($data['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
        (<?php echo htmlspecialchars($projectInfo->customerName ?? '', ENT_QUOTES, 'UTF-8'); ?> /
        <?php echo htmlspecialchars($projectInfo->projectName ?? '', ENT_QUOTES, 'UTF-8'); ?> /
        <?php echo htmlspecialchars($projectInfo->sprintName ?? '', ENT_QUOTES, 'UTF-8'); ?>)
    </p>

    <?php
    $this->load->view('_email/partials/taskChangeSummary', [
        'changes' => $changes,
        'files_added' => $files_added,
        'files_removed' => $files_removed,
        'change_heading' => 'What changed',
    ]);
    ?>

    <?php if (empty($changes) && empty($files_added) && empty($files_removed)): ?>
    <div style="margin-bottom:20px;padding:12px;border:1px solid #dee2e6;background:#f8f9fa;color:#6c757d;">
        No field or attachment changes were detected. The task was saved with the same values as before.
    </div>
    <?php endif; ?>

    <table align="center" border="1" cellpadding="8" cellspacing="0" role="presentation" style="width:100%;margin-top:10px;">
        <tbody>
            <tr>
                <th class='text-left' style="width:35%;">TASK NUMBER</th>
                <td><?php echo htmlspecialchars($data['task_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <th class='text-left'>TASK NAME</th>
                <td><?php echo htmlspecialchars($data['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <th class='text-left'>SOURCE</th>
                <td><?php echo task_source_email_display_html($data['source'] ?? 'admin'); ?></td>
            </tr>
            <tr>
                <th class='text-left'>STAGE</th>
                <td>
                    <?php $stage = $data['stage'] ?? ''; ?>
                    <span class="stage-button stage-button-<?php echo htmlspecialchars($stage, ENT_QUOTES, 'UTF-8'); ?>"><?php echo $stage !== '' ? strtoupper(str_replace('_', ' ', $stage)) : '—'; ?></span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?php else: ?>
<div style="margin:0px auto;max-width:800px;">
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
                <th class='text-left'>CUSTOMER</th>
                <td><?php echo $projectInfo->customerName;?></td>
            </tr>
            <tr>
                <th class='text-left'>PROJECT</th>
                <td><?php echo $projectInfo->projectName;?></td>
            </tr>
            <tr>
                <th class='text-left'>SPRINT</th>
                <td><?php echo $projectInfo->sprintName;?></td>
            </tr>
            <tr>
                <th class='text-left'>TASK NUMBER</th>
                <td><?php echo $data['task_number'];?></td>
            </tr>
            <tr>
                <th class='text-left'>SECTION</th>
                <td><?php echo $data['section'];?></td>
            </tr>
            <tr>
                <th class='text-left'>TASK NAME</th>
                <td><?php echo $data['name'];?></td>
            </tr>
            <tr>
                <th class='text-left'>SOURCE</th>
                <td><?php echo task_source_email_display_html($data['source'] ?? 'admin');?></td>
            </tr>
            <tr>
                <th class='text-left'>TASK DESCRIPTION</th>
                <td><?php echo nl2br($data['description']);?></td>
            </tr>
            <tr>
                <th colspan='2'>SCOPE</th>
            </tr>
            <tr>
                <th class="text-left">EXPECTED</th>
                <td><?php echo nl2br($data['scope_client_expectation']);?></td>
            </tr>
            <tr>
                <th class="text-left">NOT IN SCOPE</th>
                <td><?php echo nl2br($data['scope_not_included']);?></td>
            </tr>
            <tr>
                <th class="text-left">WHEN IS DONE</th>
                <td><?php echo nl2br($data['scope_when_done']);?></td>
            </tr>
            <tr>
                <th class='text-left'>STAGE</th>
                <td>
                    <?php $stage = $data['stage'] ?? ''; ?>
                    <span class="stage-button stage-button-<?php echo htmlspecialchars($stage, ENT_QUOTES, 'UTF-8');?>"><?php echo $stage !== '' ? strtoupper(str_replace("_"," ",$stage)) : '—'; ?></span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php endif; ?>

<div style='margin:30px auto; max-width:800px;'>
    <a class='btn' href="<?php echo $link;?>">
        <div class="label"><?php echo $link_label;?></div>
    </a>
</div>
