<h4 class='text-center'>Overall Progress per Client</h4>
<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead>
            <tr class='text-center bg-primary'>
                <th style='width:40%;'>COMPANY</th>
                <th style='width:20%;'>TOTAL TASKS</th>
                <th style='width:20%;'>COMPLETED</th>
                <th style='width:20%;'>%</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($task_progress as $c => $item):?>
            <tr class='text-center'>
                <td style='width:40%;'><?php echo $item->company_name;?></td>
                <td style='width:20%;' class='align-middle'><?php echo $item->total_tasks;?></td>
                <td style='width:20%;' class='align-middle'><?php echo $item->completed_tasks;?></td>
                <td style='width:20%;' class='align-middle'>
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
                        <span class="w-100 text-center small" style='position:absolute; top:0; left:0; line-height:20px;'><?= $item->overall_progress_pct ?>%</span>
                    </div>
                </td>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</div>
