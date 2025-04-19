<div style='width:100%; text-align: center;'>
    <h3>Note Deleted</h3>
</div>
<div style="margin:0px auto;max-width:800px;">
    <p>The following note has been deleted:</p>
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
                <th class='text-left'>Note Deleted</th>
                <td><?php echo $note->notes;?></td>
            </tr>
            <tr>
                <th class='text-left'>Task Number</th>
                <td><?php echo $note->task_number;?></td>
            </tr>
            <tr>
                <th class='text-left'>Task Name</th>
                <td><?php echo $note->task_name;?></td>
            </tr>
            <tr>
                <th class='text-left'>Sprint</th>
                <td><?php echo $note->sprint_name;?></td>
            </tr>
            <tr>
                <th class='text-left'>Project</th>
                <td><?php echo $note->project_name;?></td>
            </tr>
            <tr>
                <th class='text-left'>Customer</th>
                <td><?php echo $note->company_name;?></td>
            </tr>
            <tr>
                <th class='text-left'>Deleted By <?php echo ucfirst($type);?></th>
                <td><?php echo "{$note->deleted_by->name} / {$note->deleted_by->email}";?></td>
            </tr>
        </tbody>
    </table>
</div>