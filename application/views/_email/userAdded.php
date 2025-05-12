<div style='width:100%; text-align: center;'>
    <h3>USER HAS BEEN ADDED</h3>
</div>
<div style="margin:0px auto;max-width:800px;">
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
                <th class='text-left'>NAME</th>
                <td><?php echo $task->section;?></td>
            </tr>
            <tr>
                <th class='text-left'>EMAIL</th>
                <td><?php echo $task->name;?></td>
            </tr>
            <tr>
                <th class='text-left'>CUSTOMER</th>
                <td><?php echo nl2br($task->description);?></td>
            </tr>
            <tr>
                <th class="text-left">SUBMITTED BY</th>
                <td><?php echo "{$task->customerName} &lt;{$task->customerEmail}&gt;";?></td>
            </tr>
        </tbody>
    </table>
</div>
