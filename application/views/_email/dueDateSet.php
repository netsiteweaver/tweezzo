<div style='width:100%; text-align: center;'>
    <h3>DUE DATE UPDATED</h3>
</div>
<div style="margin:0px auto;max-width:800px;">
    <?php
    $this->load->view('_email/partials/taskChangeSummary', [
        'changes' => isset($changes) && is_array($changes) ? $changes : task_email_build_due_date_change($dueDate ?? ''),
        'files_added' => [],
        'files_removed' => [],
        'change_heading' => 'What changed',
    ]);
    ?>
    <h4 style="margin:0 0 16px 0;">The following tasks are affected:</h4>
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
                <th>#</th>
                <th>TASK NAME</th>
                <th>SOURCE</th>
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
                <td><?php echo task_source_email_display_html(isset($task->source) ? $task->source : ''); ?></td>
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

