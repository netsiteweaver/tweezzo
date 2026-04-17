<div style='width:100%; text-align: center;'>
    <h3><?php echo strtoupper($title);?></h3>
</div>
<div style="margin:0px auto;max-width:800px;">
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
                <td>
                    <?php if($type == 'validated'):?>
                        This is to inform you that <strong><?php echo $task->validatedBy;?></strong> validated task <strong><?php echo "{$task->taskName} / {$task->taskNumber}.";?></strong>
                        <br><br>
                        Please proceed with the production push request for this validated task.
                    <?php else:?>
                        This is to inform you that <strong><?php echo $task->rejectedBy;?></strong> rejected task <strong><?php echo "{$task->taskName} / {$task->taskNumber} because {$task->rejectedReason}.";?></strong>
                        <br><br>
                        Please review the rejected task and proceed with necessary amendments if needed.
                    <?php endif;?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
