<div style='width:100%; text-align: center;'>
    <h3>TASKS ASSIGNED</h3>
    <?php
        $headerParts = [];
        if (!empty($sprint)) {
            $headerParts[] = "<b>Sprint:</b> $sprint";
        }
        if (!empty($project)) {
            $headerParts[] = "<b>Project:</b> $project";
        }
        if (!empty($customer)) {
            $headerParts[] = "<b>Customer:</b> $customer";
        }
        echo (!empty($headerParts)) ? "<p>" . implode(" | ", $headerParts) . "</p>" : "";
    ?>
</div>
<div style="margin:0px auto;max-width:800px;">
    <p>Dear <?php echo $user->name;?></p>
    <p>You have been assigned the following Tasks:</p>
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <thead>
            <tr>
                <th>#</th>
                <th>TASK NAME</th>
                <th>SPRINT</th>
                <th>PROJECT</th>
                <th>STAGE</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($tasks as $task):?>
            <tr>
                <td><?php echo $task->task_number;?></td>
                <td><?php echo $task->name;?></td>
                <td><?php echo $task->sprint_name;?></td>
                <td><?php echo $task->project_name;?></td>
                <td>
                    <div class="stage-button stage-button-<?php echo $task->stage;?>">
                    <?php echo strtoupper(str_replace("_"," ",$task->stage));?>
                    </div>
                </td>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</div>
<?php if( (!empty($link)) && (!empty($link_label)) ):?>
<div style='margin:30px auto; max-width:800px;'>
    <a class='btn' href="<?php echo $link;?>">
        <div class="label"><?php echo $link_label;?></div>
    </a>
</div>
<?php endif;?>