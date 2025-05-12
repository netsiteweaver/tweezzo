<div style='width:100%; text-align: center;'>
    <h3>TASKS DUE REMINDER</h3>   
    <h4>The following tasks are due in the next <?php echo $days;?> days:</h4>
</div>
<div style="margin:0px auto;max-width:800px;">
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
                <th>TASK #</th>
                <th>CUSTOMER</th>
                <th>PROJECT</th>
                <th>SPRINT</th>
                <th>TASK NAME</th>
                <th>STAGE</th>
                <th>DUE DATE</th>
                <th></th>
            </tr>
            <tr>
                
            </tr>
<?php foreach($tasks as $task):?>
            <tr>
                <td><?php echo $task['tasks']->task_number;?></td>
                <td><?php echo $task['tasks']->company_name;?></td>
                <td><?php echo $task['tasks']->project_name;?></td>
                <td><?php echo $task['tasks']->sprint_name;?></td>
                <td><?php echo $task['tasks']->name;?></td>
                <td><?php echo strtoupper(str_replace("_"," ",$task['tasks']->stage));?></td>
                <td><?php echo $task['tasks']->due_date;?></td>
                <td>
                    <a style='text-decoration:none;' href="<?php echo base_url('portal/developers/view?task_uuid'.$task['tasks']->uuid);?>">
                        <div style="text-decoration:none; padding:5px 10px; background-color:#4c4c4c; color:#fff;text-align:center;"><i class="bi bi-eye"></i> View Task</div>
                    </a>
                </td>
            </tr>
<?php endforeach;?>
        </tbody>
    </table>
</div>

<!-- <div style='margin:30px auto; max-width:800px;'>
    <a class='btn' href="<?php //echo $link;?>">
        <div class="label"><?php //echo $link_label;?></div>
    </a>
</div> -->

