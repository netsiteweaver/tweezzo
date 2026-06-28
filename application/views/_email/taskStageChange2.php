<div style='margin:30px auto; max-width:800px;'>
    <table style='width:100%;margin-bottom:20px;'>
        <tbody>
            <tr>
                <th style='padding: 5px 10px; font-size: 18px;'>Task has changed Stage.</th>
            </tr>
        </tbody>
    </table>

    <?php if (!empty($actor_name)): ?>
    <p style="margin:0 0 16px 0;">
        <strong><?php echo htmlspecialchars($actor_name, ENT_QUOTES, 'UTF-8'); ?></strong> changed the stage for this task.
    </p>
    <?php endif; ?>

    <?php
    $stageChanges = isset($changes) && is_array($changes) ? $changes : [];
    if (empty($stageChanges) && !empty($old_stage) && !empty($task->task_stage)) {
        $stageChanges = task_email_build_stage_change($old_stage, $task->task_stage);
    }
    $this->load->view('_email/partials/taskChangeSummary', [
        'changes' => $stageChanges,
        'files_added' => [],
        'files_removed' => [],
        'change_heading' => 'What changed',
    ]);
    ?>

    <table style='width:100%;margin-bottom:20px;'>
        <tbody>
            <tr>
                <th style='width: 150px; text-align: left; border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'>Task</th>
                <td style='border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'><?php echo "{$task->task_number} - {$task->task_name}";?></td>
            </tr>
            <tr>
                <th style='width: 150px; text-align: left; border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'>Task Description</th>
                <td style='border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'><?php echo nl2br($task->task_description);?></td>
            </tr>
            <tr>
                <th style='width: 150px; text-align: left; border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'>Section</th>
                <td style='border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'><?php echo "{$task->task_section}";?></td>
            </tr>
            <tr>
                <th style='width: 150px; text-align: left; border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'>Source</th>
                <td style='border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'><?php echo task_source_email_display_html(isset($task->source) ? $task->source : ''); ?></td>
            </tr>
            <tr>
                <th style='width: 150px; text-align: left; border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'>Sprint</th>
                <td style='border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'><?php echo "{$task->sprint_name}";?></td>
            </tr>
            <tr>
                <th style='width: 150px; text-align: left; border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'>Project</th>
                <td style='border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'><?php echo "{$task->project_name}";?></td>
            </tr>
            <tr>
                <th style='width: 150px; text-align: left; border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'>Customer</th>
                <td style='border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'><?php echo "{$task->customer_name}";?></td>
            </tr>
            <tr>
                <th style='width: 150px; text-align: left; border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'>Stage</th>
                <td style='border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'><?php echo strtoupper(str_replace("_"," ",$task->task_stage));?></td>
            </tr>
        </tbody>
    </table>
</div>