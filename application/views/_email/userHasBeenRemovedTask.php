<div style='width:100%; text-align: center;'>
    <h3>TASK REMOVED</h3>
    <!-- <h4><?php //echo "Project: {$tasks[0]->project_name} - Sprint: {$tasks[0]->sprint_name}";?></h4> -->
    <!-- <h4><?php //echo "Project: {$tasks[0]->company_name} | {$tasks[0]->project_name}";?></h4> -->
</div>
<div style="margin:0px auto;max-width:800px;">
    <p>Dear <?php echo $user->name;?></p>
    <p>You have been removed from the following Task:</p>
    <p><b>Task</b>: <?php echo $task->name;?></p>
    <p><b>Section</b>: <?php echo $task->section;?></p>
    <p><b>Task #</b>: <?php echo $task->task_number;?></p>
    <p><b>Task Description</b>: <?php echo $task->description;?></p>
    <p><b>Project</b>: <?php echo $task->project_name;?></p>
    <p><b>Sprint</b>: <?php echo $task->sprint_name;?></p>
    <p><b>Client</b>: <?php echo $task->company_name;?></p>
</div>
<?php if( (!empty($link)) && (!empty($link_label)) ):?>
<div style='margin:30px auto; max-width:800px;'>
    <a class='btn' href="<?php echo $link;?>">
        <div class="label"><?php echo $link_label;?></div>
    </a>
</div>
<?php endif;?>

