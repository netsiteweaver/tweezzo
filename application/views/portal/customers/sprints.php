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
<?php
$all_sprints_complete = !empty($sprints);
if ($all_sprints_complete) {
    foreach ($sprints as $_sp) {
        if ((int) $_sp->progress_pct < 100) {
            $all_sprints_complete = false;
            break;
        }
    }
}
?>
<?php $this->load->view("portal/customers/shared/intro_block", ['intro' => [
    'key'    => 'sprint',
    'icon'   => 'bi-trophy',
    'image'  => 'sprints-bw.jpg',
    'title'  => 'What is a sprint?',
    'lead'   => 'A sprint is a fixed block of work inside a project. Rather than delivering everything at once, we group tasks into sprints and deliver them together, so you can see steady progress.',
    'points' => [
        'The <strong>% complete</strong> column shows how far along a sprint is &mdash; green means finished.',
        'Use <strong>View Tasks</strong> to see exactly what is included in a sprint.',
        'A sprint is finished when every task inside it has been completed.',
    ],
]]);?>
<?php if ($all_sprints_complete):?>
<div class="row justify-content-center mb-3">
    <div class="col-lg-6 col-md-8 progressive-image" style='position:relative;'>
        <img style='' src="./assets/images/tasks complete - 600x188px.jpg" class="placeholder" alt="loading image">
        <img style='' src="./assets/images/tasks complete - 600x188px.png" class="full-res d-none" alt="image loaded">
        <span style='-webkit-text-stroke-width: 1px;-webkit-text-stroke-color: #ccc; position:absolute; top:80px; left:28%; font-weight:bold; font-size:36px; text-shadow: 1px 1px 2px black;color:#fff;z-index:999;'>Wow! All Tasks Completed! </span>
    </div>
</div>
    
<?php endif;?>
<div class="row justify-content-center table-responsive">
    <div class="col-lg-10 col-md-11">
        <table class="table table-bordered">
            <thead>
                <tr class='text-center'>
                    <th>SPRINT NAME</th>
                    <th>PROJECT NAME</th>
                    <th># of TASKS</th>
                    <th>DONE (tasks)</th>
                    <th>% COMPLETE</th>
                    <th style='width:150px'></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($sprints as $sprint):?>
                <?php
                    $pct = isset($sprint->progress_pct) ? (int) $sprint->progress_pct : 0;
                    $row_class = $pct >= 100 ? 'alert-success' : ($pct >= 50 ? 'alert-warning' : 'alert-danger');
                ?>
                <tr class='alert <?php echo $row_class;?>'>
                    <td><?php echo $sprint->name;?></td>
                    <td><?php echo $sprint->project_name;?></td>
                    <td class='text-center'><?php echo (int) $sprint->tasks_count;?></td>
                    <td class='text-center'><?php echo (int) $sprint->completed_tasks;?> / <?php echo (int) $sprint->tasks_count;?></td>
                    <td class='text-center'><?php echo $pct;?> %</td>
                    <td>
                        <a href="portal/customers/tasks?sprint_id=<?php echo $sprint->id;?>"><div class="btn btn-outline-secondary"style="color:#fff; background-color: var(--customersPortalBackground)"><i class="bi bi-eye"></i> View Tasks</div></a>
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>