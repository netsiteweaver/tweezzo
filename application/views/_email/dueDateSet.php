<div style='width:100%; text-align: center;'>
    <h3>DUE DATE UPDATED</h3>   
    <h4>The following tasks have its due date set to <?php echo $dueDate;?></h4>
</div>
<div style="margin:0px auto;max-width:800px;">
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
                <th>#</th>
                <th>TASK NAME</th>
                <th>SPRINT</th>
                <th>PROJECT</th>
                <th>CUSTOMER</th>
            </tr>
            <tr>
                
            </tr>
<?php foreach($tasks as $task):?>
            <tr>
                <td><?php echo $task->task_number;?></td>
                <td><?php echo $task->name;?></td>
                <td><?php echo $task->sprint_name;?></td>
                <td><?php echo $task->project_name;?></td>
                <td><?php echo $task->company_name;?></td>
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

