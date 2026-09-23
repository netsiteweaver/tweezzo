<h4 class='text-center'>Overall Progress per Client</h4>
<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead>
            <tr class='text-center'>
                <th class='table-primary' style='width:16.667%;'>COMPANY</th>
                <th class='table-primary'>TOTAL TASKS</th>
                <th class='table-primary'>COMPLETED</th>
                <th class='table-primary'>%</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($task_progress as $c => $item):?>
            <tr class='text-center'>
                <td><?php echo $item->company_name;?></td>
                <td><?php echo $item->total_tasks;?></td>
                <td><?php echo $item->completed_tasks;?></td>
                <td style='width:100px'>
                    <div class="progress position-relative" style="height: 20px;">
                        <div
                        class="progress-bar
                                <?php
                                if ($item->overall_progress_pct >= 80) echo 'bg-success';
                                elseif ($item->overall_progress_pct >= 50) echo 'bg-info';
                                elseif ($item->overall_progress_pct >= 20) echo 'bg-warning';
                                else echo 'bg-danger';
                                ?>"
                        role="progressbar"
                        style="width: <?= $item->overall_progress_pct ?>%;"
                        aria-valuenow="<?= $item->overall_progress_pct ?>"
                        aria-valuemin="0"
                        aria-valuemax="100">
                        </div>
                        <span class="w-100 text-center" style='position:absolute; bottom:10px;'><?= $item->overall_progress_pct ?>%</span>
                    </div>
                </td>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</div>
