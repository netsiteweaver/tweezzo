<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered">
            <tbody>
                <tr class='text-center'>
                    <th class='table-primary' rowspan='2' style='width:16.667%;'>COMPANY</th>
                    <th class='table-primary' rowspan='2'># of TASKS</th>
                    <th class='table-primary' colspan='6'>TASK STAGE PROGRESS</th>
                </tr>
                <tr class='text-center table-info'>
                    <th class=''>NEW</th>
                    <th class=''>IN PROGRESS</th>
                    <th class=''>TESTING</th>
                    <th class=''>STAGING</th>
                    <th class=''>VALIDATED</th>
                    <th class=''>COMPLETED</th>
                </tr>
            </tbody>
            <tbody>
                <?php foreach($task_progress as $c => $item):?>
                    <?php if($item->tasksAll == 0) continue;?>
                <tr class='text-center'>
                    <td class='text-left'><?php echo $c;?></td>
                    <td><?php echo $item->tasksAll;?></td>
                    <td><?php echo (!empty($item->pctNew)) ? $item->pctNew . '%' : '0%';?></td>
                    <td><?php echo (!empty($item->pctInProgress)) ? $item->pctInProgress . '%' : '0%';?></td>
                    <td><?php echo (!empty($item->pctTesting)) ? $item->pctTesting . '%' : '0%';?></td>
                    <td><?php echo (!empty($item->pctStaging)) ? $item->pctStaging . '%' : '0%';?></td>
                    <td><?php echo (!empty($item->pctValidated)) ? $item->pctValidated . '%' : '0%';?></td>
                    <td><?php echo (!empty($item->pctCompleted)) ? $item->pctCompleted . '%' : '0%';?></td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>