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
.progressive-image {
  /* position: relative; */
  /* overflow: hidden; */
  
}

.progressive-image img {
  height:200px;; width:100%; padding:5px; border:1px solid #ccc; border-radius:5px;
  /* display: block; */
  /* width: 100%; */
  /* height: auto; */
  /* transition: opacity 0.5s; */
}

.progressive-image .full-res {
  /* height:200px;; width:100%; padding:5px; border:1px solid #ccc; border-radius:5px; */
}
</style>
<?php if($sprints[0]->progress_pct == 100):?>
<div class="row mb-3">
    <div class="col-md-6 progressive-image" style='position:relative;'>
        <img style='' src="./assets/images/tasks complete - 600x188px.jpg" class="placeholder" alt="loading image">
        <img style='' src="./assets/images/tasks complete - 600x188px.png" class="full-res d-none" alt="image loaded">
        <span style='-webkit-text-stroke-width: 1px;-webkit-text-stroke-color: #ccc; position:absolute; top:80px; left:28%; font-weight:bold; font-size:36px; text-shadow: 1px 1px 2px black;color:#fff;z-index:999;'>Wow! All Tasks Completed! </span>
    </div>
</div>
    
<?php endif;?>
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