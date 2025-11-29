<div style='width:100%; text-align: center; background-color: #ff4444; padding: 20px; margin-bottom: 20px;'>
    <h2 style='color: #ffffff; margin: 0; font-size: 28px; font-weight: bold;'>⚠️ URGENT: OVERDUE TASKS</h2>   
    <h3 style='color: #ffffff; margin: 10px 0 0 0; font-size: 20px; font-weight: normal;'>
        The following tasks are past their due date and require immediate attention
    </h3>
</div>

<div style="margin:0px auto;max-width:800px; padding: 20px;">
    <div style="background-color: #fff3cd; border-left: 4px solid #ff4444; padding: 15px; margin-bottom: 20px;">
        <p style="margin: 0; font-size: 16px; font-weight: bold; color: #856404;">
            ⚠️ <strong>Action Required:</strong> Please complete these overdue tasks as soon as possible. 
            Delayed tasks may impact project timelines and customer satisfaction.
        </p>
    </div>
    
    <?php foreach($customerProjects as $customerProject): ?>
    <div style="margin-bottom: 30px; border: 2px solid #ff4444; border-radius: 5px; overflow: hidden;">
        <!-- Customer-Project Header -->
        <div style="background-color: #ff4444; padding: 15px; color: #ffffff;">
            <h3 style="margin: 0; font-size: 20px; font-weight: bold;">
                📁 <?php echo htmlspecialchars($customerProject['customer_name']); ?> - <?php echo htmlspecialchars($customerProject['project_name']); ?>
            </h3>
            <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">
                <?php echo count($customerProject['tasks']); ?> overdue task<?php echo count($customerProject['tasks']) != 1 ? 's' : ''; ?>
            </p>
        </div>
        
        <!-- Tasks Table for this Customer-Project -->
        <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #ff6666; color: #ffffff;">
                    <th style="padding: 12px; text-align: left; font-weight: bold;">TASK #</th>
                    <th style="padding: 12px; text-align: left; font-weight: bold;">SPRINT</th>
                    <th style="padding: 12px; text-align: left; font-weight: bold;">TASK NAME</th>
                    <th style="padding: 12px; text-align: left; font-weight: bold;">STAGE</th>
                    <th style="padding: 12px; text-align: left; font-weight: bold;">DUE DATE</th>
                    <th style="padding: 12px; text-align: center; font-weight: bold;">DAYS OVERDUE</th>
                    <th style="padding: 12px; text-align: center; font-weight: bold;">ACTION</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($customerProject['tasks'] as $task): 
                $daysOverdue = isset($task->days_overdue) ? $task->days_overdue : 0;
                $rowColor = $daysOverdue > 7 ? '#ffcccc' : '#ffe6e6';
            ?>
                <tr style="background-color: <?php echo $rowColor; ?>;">
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <strong><?php echo htmlspecialchars($task->task_number);?></strong>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($task->sprint_name);?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <strong><?php echo htmlspecialchars($task->name);?></strong>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <?php echo strtoupper(str_replace("_"," ",$task->stage));?>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd; color: #ff0000; font-weight: bold;">
                        <?php echo htmlspecialchars($task->due_date);?>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd; text-align: center; color: #ff0000; font-weight: bold; font-size: 16px;">
                        <?php echo $daysOverdue; ?> <?php echo $daysOverdue == 1 ? 'day' : 'days'; ?>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                        <a style='text-decoration:none; display: inline-block;' href="<?php echo base_url('portal/developers/view?task_uuid=' . $task->uuid);?>">
                            <div style="text-decoration:none; padding:8px 15px; background-color:#ff4444; color:#fff;text-align:center; border-radius: 4px; font-weight: bold;">
                                ✓ Complete Task
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
    <h3 style="color: #ff4444; margin-top: 0; text-align: center;">📋 Next Steps</h3>
    <ul style="font-size: 16px; line-height: 1.8; color: #333;">
        <li><strong>Review each task</strong> and assess the current status</li>
        <li><strong>Update task progress</strong> or move to the appropriate stage</li>
        <li><strong>Complete tasks</strong> that are ready for finalization</li>
        <li><strong>Communicate</strong> any blockers or delays with your project manager</li>
        <li><strong>Prioritize</strong> tasks that are most overdue or critical to the project</li>
    </ul>
    <p style="text-align: center; margin-top: 20px; font-size: 16px; font-weight: bold; color: #ff4444;">
        Please take immediate action on these overdue tasks. Thank you for your attention to this matter.
    </p>
</div>

