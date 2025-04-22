<style>

</style>

<div class="row">
    <div class="col-md-6">
        <!-- <div class="card card-secondary"> -->
			<!-- <div class="card-header bg-info">
				<h3>Customer</h3>
			</div> -->
            <!-- <div class="card-body"> -->
                <table class="table table">
                    <tbody>
                        <tr>
                            <th>Project Name</th>
                            <td><?php echo $project->name;?></td>
                        </tr>
                        <tr>
                            <th>Start Date</th>
                            <td><?php echo $project->start_date;?></td>
                        </tr>
                        <tr>
                            <th>End Date</th>
                            <td><?php echo $project->end_date;?></td>
                        </tr>
                    </tbody>
                </table>
            <!-- </div> -->
        <!-- </div> -->
        <!-- <div class="card card-secondary"> -->
			<!-- <div class="card-header bg-info"> -->
				<h3>Assigned Developers</h3>
			<!-- </div> -->
			<!-- <div class="card-body"> -->
				<ul id="users-list" class="list-group">
					<?php foreach($developers as $user):?>
					<?php if($user->user_type != 'developer') continue;?>
					<li data-id="<?php echo $user->id;?>" class="list-group-item select-user <?php //echo in_array($user->id, $task->assigned_users) ? 'assigned':'';?>">
						<img style='width:50px;padding:2px;background-color:#eee;border:1px solid #ccc;border-radius: 50%;' src="uploads/users/<?php echo $user->photo;?>" alt="">
						<?php echo "{$user->email} ({$user->name})";?>
					</li>
					<?php endforeach;?>
				</ul>
			<!-- </div> -->
		<!-- </div> -->
    </div>
</div>