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
                <th style='width: 150px; text-align: left; border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'>Project</th>
                <td style='border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'><?php echo "{$task->project_name}";?></td>
            </tr>
            <tr>
                <th style='width: 150px; text-align: left; border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'>Stage</th>
                <td style='border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'><?php echo strtoupper(str_replace("_"," ",$task->task_stage));?></td>
            </tr>
        </tbody>
    </table>
</div>

<div style="margin:0px auto;max-width:800px;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
                <td align="left" vertical-align="middle" style="word-break:break-word;">
                    <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                        style="border-collapse:separate;line-height:100%;">
                        <tbody>
                            <tr>
                                <td align="center" bgcolor="#2e58ff" role="presentation"
                                    style="border:none;cursor:auto;background-color: #2e58ff;"
                                    valign="middle">
                                    <a href="<?php echo base_url($url);?>"
                                        style="display: inline-block; background:rgb(27, 86, 153); color: #ffffff; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: bold; line-height: 30px; margin: 0; text-decoration: none; text-transform: uppercase; padding: 10px 25px; mso-padding-alt: 0px;;"
                                        target="_blank"> <?php echo $label;?> </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>

    </table>
</div>