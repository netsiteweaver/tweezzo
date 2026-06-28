<div style='width:100%; text-align: center;'>
    <h3>TASK LIST & PROGRESS VIEW</h3>
    <!-- <h4><?php //echo "Project: {$tasks[0]->project_name} - Sprint: {$tasks[0]->sprint_name}";?></h4> -->
    <!-- <h4><?php //echo "Project: {$tasks[0]->company_name} | {$tasks[0]->project_name}";?></h4> -->
</div>
<?php $sprintLabel = (isset($tasks[0]) && !empty($tasks[0]->sprint_name)) ? (' "' . $tasks[0]->sprint_name . '"') : ''; ?>
<?php if (!empty($notify_mode) && $notify_mode === 'staging_validation'): ?>
<div style="margin:10px auto;max-width:800px;padding:12px;border:1px solid #f4d03f;background:#fff8db;color:#7d6608;">
    <span style="display:inline-block;font-size:18px;line-height:1;margin-right:8px;vertical-align:middle;">&#9203;</span>
    <span style="vertical-align:middle;">
        All tasks for sprint<?php echo $sprintLabel; ?> are now in <b>STAGING</b> and waiting for validation.
    </span>
</div>
<?php elseif (!empty($notify_mode) && $notify_mode === 'completed_update'): ?>
<div style="margin:10px auto;max-width:800px;padding:12px;border:1px solid #b5dfb8;background:#eaf8ec;color:#1e6b2e;">
    <span style="display:inline-block;font-size:18px;line-height:1;margin-right:8px;vertical-align:middle;">&#10004;</span>
    <span style="vertical-align:middle;">
        All tasks for sprint<?php echo $sprintLabel; ?> are now <b>COMPLETED</b>.
    </span>
</div>
<?php endif; ?>
<div style="margin:0px auto;max-width:800px;">
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
                <th>#</th>
                <th>SECTION</th>
                <th>TASK NAME</th>
                <th>SOURCE</th>
                <th>SPRINT</th>
                <th>PROJECT</th>
                <th>CUSTOMER</th>
                <th>STAGE</th>
                <!-- <th>ASSIGNED TO</th> -->
            </tr>
            <tr>
                
            </tr>
            <?php foreach($tasks as $task):?>
            <tr>
                <td><?php echo $task->task_number;?></td>
                <td><?php echo $task->section;?></td>
                <td><?php echo $task->name;?></td>
                <td><?php echo task_source_email_display_html(isset($task->source) ? $task->source : ''); ?></td>
                <td><?php echo $task->sprint_name;?></td>
                <td><?php echo $task->project_name;?></td>
                <td><?php echo $task->company_name;?></td>
                <td>
                    <div class="stage-button stage-button-<?php echo $task->stage;?>" >
                        <?php echo strtoupper(str_replace('_',' ',$task->stage));?>
                    </div>
                </td>
                <!-- <td>
                    <?php //foreach($task->users as $i => $u):?>
                    <img style='width:30px;height:30px;padding:3px;border:1px solid #CCCCCC;' src="<?php //echo base_url("uploads/users/".$u->photo);?>" alt="<?php //echo $u->display_name;?>">
                    <?php //echo $u->display_name. ( ( (count($task->users)-1) == $i) ? '' : ',') ;?>
                    <?php //endforeach;?>
                </td> -->
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</div>

<div style='margin-bottom:50px;margin:30px auto; max-width:800px;'>
    <a class='btn' href="<?php echo $link;?>">
        <div class="label"><?php echo $link_label;?></div>
    </a>
</div>

