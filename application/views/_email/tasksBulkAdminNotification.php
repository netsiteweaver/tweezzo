<div style='width:100%; text-align: center;'>
    <h4><?php echo isset($action_heading) ? htmlspecialchars($action_heading) : 'Bulk task change'; ?></h4>
</div>
<?php if (!empty($action_detail)) : ?>
<div style="margin:10px auto;max-width:800px;">
    <?php echo $action_detail; ?>
</div>
<?php endif; ?>
<?php if (!empty($changes) && is_array($changes)) : ?>
<div style="margin:10px auto;max-width:800px;">
    <?php $this->load->view('_email/partials/taskChangeSummary', [
        'changes' => $changes,
        'files_added' => isset($files_added) ? $files_added : [],
        'files_removed' => isset($files_removed) ? $files_removed : [],
        'change_heading' => 'What changed',
    ]); ?>
</div>
<?php endif; ?>
<?php if (!empty($performed_by)) : ?>
<p style="margin:10px auto;max-width:800px;color:#555;font-size:14px;">
    Performed by: <?php echo htmlspecialchars($performed_by); ?>
</p>
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
            </tr>
<?php foreach ($tasks as $task) : ?>
            <tr>
                <td><?php echo htmlspecialchars(isset($task->task_number) ? $task->task_number : ''); ?></td>
                <td><?php echo htmlspecialchars(isset($task->section) ? $task->section : ''); ?></td>
                <td><?php echo htmlspecialchars(isset($task->name) ? $task->name : ''); ?></td>
                <td><?php echo task_source_email_display_html(isset($task->source) ? $task->source : ''); ?></td>
                <td><?php echo htmlspecialchars(isset($task->sprint_name) ? $task->sprint_name : ''); ?></td>
                <td><?php echo htmlspecialchars(isset($task->project_name) ? $task->project_name : ''); ?></td>
                <td><?php echo htmlspecialchars(isset($task->company_name) ? $task->company_name : ''); ?></td>
            </tr>
<?php endforeach; ?>
        </tbody>
    </table>
</div>
