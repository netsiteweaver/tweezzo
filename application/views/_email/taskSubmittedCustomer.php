<div style='width:100%; text-align: center;'>
    <h3><?php echo strtoupper($title);?></h3>
</div>
<div style="margin:0px auto;max-width:800px;">
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
                <th class='text-left'>SECTION</th>
                <td><?php echo $task->section;?></td>
            </tr>
            <tr>
                <th class='text-left'>TASK NAME</th>
                <td><?php echo $task->name;?></td>
            </tr>
            <tr>
                <th class='text-left'>TASK DESCRIPTION</th>
                <td><?php echo nl2br($task->description);?></td>
            </tr>
            <tr>
                <th colspan='2'>SCOPE</th>
            </tr>
            <tr>
                <th class="text-left">WHAT IS EXPECTED</th>
                <td><?php echo nl2br($task->scope_client_expectation);?></td>
            </tr>
            <tr>
                <th class="text-left">WHAT IS NOT INCLUDED</th>
                <td><?php echo nl2br($task->scope_not_included);?></td>
            </tr>
            <tr>
                <th class="text-left">WHEN IS DONE</th>
                <td><?php echo nl2br($task->scope_when_done);?></td>
            </tr>
            <tr>
                <th class="text-left">SUBMITTED BY</th>
                <td><?php echo "{$task->customerName} &lt;{$task->customerEmail}&gt;";?></td>
            </tr>
        </tbody>
    </table>
</div>
