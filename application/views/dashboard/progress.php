<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered">
            <tbody>
                <tr class='text-center'>
                    <th class='table-primary' rowspan='2' style='width:16.667%;'>COMPANY</th>
                    <th class='table-primary' rowspan='2'># of TASKS</th>
                    <th class='table-primary' colspan='9'>TASK STAGE PROGRESS</th>
                </tr>
                <tr class='text-center table-info'>
                    <th class=''>1-NEW</th>
                    <th class=''>2-IN PROGRESS</th>
                    <th class=''>3-TESTING</th>
                    <th>(1+2+3)</th>
                    <th class=''>4-STAGING</th>
                    <th class=''>5-VALIDATED</th>
                    <th class=''>6-COMPLETED</th>
                    <th>(4+5+6)</th>
                    <th class=''>7-ON HOLD</th>
                </tr>
            </tbody>
            <tbody>
                <?php foreach($task_progress as $c => $item):?>
                    <?php if($item->tasksAll == 0) continue;?>
                <tr class='text-center'>
                    <td class='text-left'><?php echo $c;?></td>
                    <td class='table-primary'><?php echo $item->tasksAll;?></td>
                    <td><?php echo (!empty($item->pctNew)) ? $item->pctNew . '%' : '0%';?></td>
                    <td><?php echo (!empty($item->pctInProgress)) ? $item->pctInProgress . '%' : '0%';?></td>
                    <td><?php echo (!empty($item->pctTesting)) ? $item->pctTesting . '%' : '0%';?></td>
                    <td class='table-info'><?php echo intval($item->pctNew) + intval($item->pctInProgress) + intval($item->pctTesting);?>%</td>
                    <td><?php echo (!empty($item->pctStaging)) ? $item->pctStaging . '%' : '0%';?></td>
                    <td><?php echo (!empty($item->pctValidated)) ? $item->pctValidated . '%' : '0%';?></td>
                    <td><?php echo (!empty($item->pctCompleted)) ? $item->pctCompleted . '%' : '0%';?></td>
                    <td class='table-info'><?php echo intval($item->pctStaging) + intval($item->pctValidated) + intval($item->pctCompleted);?>%</td>
                    <td class='table-danger'><?php echo (!empty($item->pctOnHold)) ? $item->pctOnHold . '%' : '0%';?></td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>