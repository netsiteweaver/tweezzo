<div style='width:100%; text-align: center;'>
    <h3>TASK LIST & PROGRESS VIEW</h3>
    <!-- <h4><?php //echo "Project: {$tasks[0]->project_name} - Sprint: {$tasks[0]->sprint_name}";?></h4> -->
    <!-- <h4><?php //echo "Project: {$tasks[0]->company_name} | {$tasks[0]->project_name}";?></h4> -->
</div>
<div style="margin:0px auto;max-width:800px;">
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
                <th>#</th>
                <th>SECTION</th>
                <th>TASK NAME</th>
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

