<div style='width:100%; text-align: center;'>
    <h3><img style='width:36px; height:36px;' src="<?php echo base_url('assets/images/OUT-OF-SCOPE-36PX.png');?>" alt=""> Note Out Of Scope</h3>
</div>
<div style="margin:0px auto;max-width:800px;">
    <p>Based on this task's requirement (as in task name and description), this note is out of scope and will be discarded.</p>
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
                <th class='text-left'>Task Number</th>
                <td><?php echo $note->task_number;?></td>
            </tr>
            <tr>
                <th class='text-left'>Task Name</th>
                <td><?php echo $note->task_name;?></td>
            </tr>
            <tr>
                <th class='text-left'>Task Description</th>
                <td><?php echo nl2br($note->task_description);?></td>
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
                <th class='text-left'>Note</th>
                <td><?php echo $note->notes;?></td>
            </tr>
            <!-- <tr>
                <th class='text-left'>Deleted By <?php //echo ucfirst($type);?></th>
                <td><?php //echo "{$note->deleted_by->name} / {$note->deleted_by->email}";?></td>
            </tr> -->
        </tbody>
        <p>Just a quick reminder that to keep our sprint on track and ensure timely delivery, it's important that we respect the defined scope of each task.<br>At the moment, our portal doesn't support creating tasks, so any new requests or scope changes during a sprint can disrupt the flow and delay progress.<br>If you have ideas or changes in mind, feel free to note them down separately or send them our way — we'll make sure to revisit them when planning the next sprint.<br>Thanks a lot for your understanding and collaboration!</p>
    </table>
</div>