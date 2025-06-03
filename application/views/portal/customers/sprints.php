<div class="row table-responsive">
    <div class="col-md-6">
        <table class="table table-bordered">
            <thead>
                <tr class='text-center'>
                    <th>SPRINT NAME</th>
                    <th>PROJECT NAME</th>
                    <th># of TASKS</th>
                    <th># COMPLETED</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($sprints as $sprint):?>
                <tr class='alert <?php echo ((($sprint->completed_tasks / $sprint->tasks_count) * 100) == 100) ? 'alert-success' : ( ((($sprint->completed_tasks / $sprint->tasks_count) * 100) >= 50) ? 'alert-warning' : 'alert-danger' );?>'>
                    <td><?php echo $sprint->name;?></td>
                    <td><?php echo $sprint->project_name;?></td>
                    <td class='text-center'><?php echo $sprint->tasks_count;?></td>
                    <td class='text-center'><?php echo ($sprint->completed_tasks / $sprint->tasks_count) * 100 ;?> %</td>
                    <td>
                        <a href="portal/customers/tasks?sprint_id=<?php echo $sprint->id;?>"><div class="btn btn-outline-secondary"style="color:#fff; background-color: var(--customersPortalBackground)"><i class="bi bi-eye"></i> View Tasks</div></a>
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>