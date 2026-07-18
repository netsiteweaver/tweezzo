<div style='width:100%; text-align: center; background-color: #3c8dbc; padding: 20px; margin-bottom: 20px;'>
    <h2 style='color: #ffffff; margin: 0; font-size: 26px; font-weight: bold;'>Overdue Task Reminder</h2>
    <h3 style='color: #ffffff; margin: 10px 0 0 0; font-size: 18px; font-weight: normal;'>
        A few of your tasks are past their due date
    </h3>
</div>

<div style="margin:0px auto;max-width:800px; padding: 20px;">
    <div style="background-color: #fcf8e3; border-left: 4px solid #f0ad4e; padding: 15px; margin-bottom: 20px;">
        <p style="margin: 0; font-size: 15px; color: #8a6d3b;">
            Hi there — this is a friendly reminder that the tasks below have passed their due date.
            Whenever you get a chance, please review them and update their progress. If anything is
            blocking you, just let your project manager know.
        </p>
    </div>

    <?php foreach($customerProjects as $customerProject): ?>
    <div style="margin-bottom: 30px; border: 1px solid #d2d6de; border-radius: 5px; overflow: hidden;">
        <!-- Customer-Project Header -->
        <div style="background-color: #3c8dbc; padding: 15px; color: #ffffff;">
            <h3 style="margin: 0; font-size: 19px; font-weight: bold;">
<?php echo htmlspecialchars($customerProject['customer_name']); ?> - <?php echo htmlspecialchars($customerProject['project_name']); ?>
            </h3>
            <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">
                <?php echo count($customerProject['tasks']); ?> overdue task<?php echo count($customerProject['tasks']) != 1 ? 's' : ''; ?>
            </p>
        </div>

        <!-- Tasks Table for this Customer-Project -->
        <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f4f4f4; color: #444444;">
                    <th style="padding: 12px; text-align: left; font-weight: bold;">TASK #</th>
                    <th style="padding: 12px; text-align: left; font-weight: bold;">SPRINT</th>
                    <th style="padding: 12px; text-align: left; font-weight: bold;">TASK NAME</th>
                    <th style="padding: 12px; text-align: left; font-weight: bold;">SOURCE</th>
                    <th style="padding: 12px; text-align: left; font-weight: bold;">STAGE</th>
                    <th style="padding: 12px; text-align: left; font-weight: bold;">DUE DATE</th>
                    <th style="padding: 12px; text-align: center; font-weight: bold;">DAYS OVERDUE</th>
                    <th style="padding: 12px; text-align: center; font-weight: bold;">ACTION</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($customerProject['tasks'] as $task):
                $daysOverdue = isset($task->days_overdue) ? $task->days_overdue : 0;
            ?>
                <tr style="background-color: #ffffff;">
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <strong><?php echo htmlspecialchars($task->task_number);?></strong>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($task->sprint_name);?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <strong><?php echo htmlspecialchars($task->name);?></strong>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?php echo task_source_email_display_html(isset($task->source) ? $task->source : ''); ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <?php echo strtoupper(str_replace("_"," ",$task->stage));?>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd; color: #c9302c; font-weight: bold;">
                        <?php echo htmlspecialchars($task->due_date);?>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd; text-align: center; color: #d9822b; font-weight: bold; font-size: 16px;">
                        <?php echo $daysOverdue; ?> <?php echo $daysOverdue == 1 ? 'day' : 'days'; ?>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                        <a style='text-decoration:none; display: inline-block;' href="<?php echo base_url('portal/developers/view?task_uuid=' . $task->uuid);?>">
                            <div style="text-decoration:none; padding:8px 15px; background-color:#3c8dbc; color:#fff;text-align:center; border-radius: 4px; font-weight: bold;">
                                View Task
                            </div>
                        </a>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <?php endforeach;?>
</div>

<div style="margin:30px auto; max-width:800px; padding: 20px; background-color: #f8f9fa; border-radius: 5px;">
    <h3 style="color: #3c8dbc; margin-top: 0; text-align: center;">A few helpful next steps</h3>
    <ul style="font-size: 15px; line-height: 1.8; color: #333;">
        <li><strong>Review each task</strong> and check its current status</li>
        <li><strong>Update task progress</strong> or move it to the appropriate stage</li>
        <li><strong>Complete tasks</strong> that are ready for finalization</li>
        <li><strong>Reach out</strong> about any blockers or delays with your project manager</li>
        <li><strong>Prioritize</strong> whatever is most time-sensitive for the project</li>
    </ul>
    <p style="text-align: center; margin-top: 20px; font-size: 15px; color: #555;">
        Thanks for keeping your tasks up to date — it's much appreciated.
    </p>
</div>

