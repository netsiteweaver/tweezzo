<div style='width:100%; text-align: center;'>
    <h3><?php echo strtoupper($title);?></h3>
</div>
<div style="margin:0px auto;max-width:800px;">
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
                <td>
                    <?php if($type == 'validated'):?>
                        This is to inform you that <?php echo $task->validatedBy;?> validated task <?php echo "{$task->taskName} / {$task->taskNumber}.";?>
                    <?php else:?>
                        This is to inform you that <?php echo $task->rejectedBy;?> rejected task <?php echo "{$task->taskName} / {$task->taskNumber} because {$task->rejectedReason}.";?>
                    <?php endif;?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
