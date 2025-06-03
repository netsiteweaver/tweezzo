<style>
.alert-success {
	color: unset;
	background-color: unset;
    border-color: #ccc;
	border-left:5px solid #badbcc !important;
	border-right:5px solid #badbcc !important;
}
.alert-warning {
	color: unset;
	background-color: unset;
    border-color: #ccc;
	border-left:5px solid #ffecb5 !important;
	border-right:5px solid #ffecb5 !important;
}
.alert-danger {
	color: unset;
	background-color: unset;
    border-color: #ccc;
	border-left:5px solid #f5c2c7 !important;
	border-right:5px solid #f5c2c7 !important;
}
</style>
<div class="row table-responsive">
    <div class="col-md-6">
        <table class="table table-bordered">
            <thead>
                <tr class='text-center'>
                    <th>SPRINT NAME</th>
                    <th>PROJECT NAME</th>
                    <th># of TASKS</th>
                    <th># COMPLETED</th>
                    <th style='width:150px'></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($sprints as $sprint):?>
                <tr class='alert <?php echo ((($sprint->completed_tasks / $sprint->tasks_count) * 100) == 100) ? 'alert-success' : ( ((($sprint->completed_tasks / $sprint->tasks_count) * 100) >= 50) ? 'alert-warning' : 'alert-danger' );?>'>
                    <td><?php echo $sprint->name;?></td>
                    <td><?php echo $sprint->project_name;?></td>
                    <td class='text-center'><?php echo $sprint->tasks_count;?></td>
                    <td class='text-center'><?php echo intval (($sprint->completed_tasks / $sprint->tasks_count) * 100) ;?> %</td>
                    <td>
                        <a href="portal/customers/tasks?sprint_id=<?php echo $sprint->id;?>"><div class="btn btn-outline-secondary"style="color:#fff; background-color: var(--customersPortalBackground)"><i class="bi bi-eye"></i> View Tasks</div></a>
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>