<style>
	#users-list li {
		margin-bottom: 5px;
		border:	1px solid #ccc;
	}
	#users-list li.assigned{
		background-color:rgb(183, 221, 210);
		/* border:1px solid #ccc !important; */
	}
	#users-list li.assigned img {
		border:4px solid #20c997 !important;
	}
	.myCursor {
		cursor: url('../../assets/images/delete-16px.png'), auto;
	}
</style>
<div class="row">
    <div class="col-md-6">
        <div class="card card-secondary">
			<input type="hidden" name="uuid" value="<?php echo $task->uuid;?>">
			<input type="hidden" name="task_id" value="<?php echo $task->id;?>">
			<input type="hidden" name="customer_id" value="<?php echo $this->input->get("customer_id");?>">
			<input type="hidden" name="stage" value="<?php echo $this->input->get("stage");?>">
			<input type="hidden" name="qs" value="<?php echo $qs;?>">
			<div class="card-header bg-purple">
				<h3 class='card-title'>Task Information</h3>
			</div>
			<div class="card-body">
				<div class="form-group">
					<label for="">Customer</label>
					<select class="form-control " name="customer_id"  disabled>
						<option value="">Select</option>
						<?php foreach($customers as $c):?>
						<option value="<?php echo $c->customer_id;?>" <?php echo ($task->customer_id == $c->customer_id) ? 'selected' : '';?>><?php echo "{$c->company_name}";?></option>
						<?php endforeach;?>
					</select>
				</div>

				<div class="form-group">
					<label for="project_id">Projects</label>
					<select name="project_id" id="project_id" class="form-control" disabled>
						<?php foreach($projects as $project):?>
						<option value="<?php echo $project->id;?>" <?php echo ($task->project_id == $project->id) ? 'selected' : '';?>><?php echo $project->name;?></option>
						<?php endforeach;?>
					</select>
				</div>

				<div class="form-group">
					<label for="">Sprint</label>
					<input type="text" class="form-control" name="sprint" placeholder="[OPTIONAL] Enter Sprint, e.g. 2503" value="<?php echo $task->sprint_name;?>" disabled>
				</div>


				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Section</label>
							<input type="text" class="form-control" name="section" placeholder="Enter Task Number, e.g. #01.14" value="<?php echo $task->section;?>"  disabled>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Task Number</label>
							<input type="text" class="form-control" name="task_number" placeholder="Enter Task Number, e.g. #01.14" value="<?php echo $task->task_number;?>"  disabled>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="">Task Name</label>
					<input type="text" class="form-control " name="name" placeholder="Enter Task Name" value="<?php echo $task->name;?>"   disabled>
				</div>
				<div class="form-group">
					<label for="">Description</label>
					<div class='form-control' style='height:150px; overflow-y:scroll;background-color: #e9ecef;'><?php echo $task->description;?></div>
					<!-- <textarea name="description" id="" rows="5" class="form-control" disabled><?php echo strip_tags($task->description);?></textarea> -->
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Due Date</label>
							<input type="date" class="form-control" name="task_number" placeholder="" value="<?php echo $task->due_date;?>"  disabled>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Estimated Hours</label>
							<input type="number" class="form-control" name="" placeholder="" value="<?php echo $task->estimated_hours;?>"  disabled>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="">Stage</label>
					<select class="form-control " name="stage"  disabled>
						<option value="" disabled>Select</option>
						<option value="new" <?php echo ($task->stage == 'new')?'selected':'';?>>New</option>
						<option value="in_progress" <?php echo ($task->stage == 'in_progress')?'selected':'';?>>In Progress</option>
						<option value="completed" <?php echo ($task->stage == 'completed')?'selected':'';?>>Completed</option>
						<option value="on_hold" <?php echo ($task->stage == 'on_hold')?'selected':'';?>>On Hold</option>
						<option value="stopped" <?php echo ($task->stage == 'stopped')?'selected':'';?>>Stopped</option>

					</select>
				</div>

				<?php if(count($task->files) > 0):?>
				<div id="attachments">
					<div class="row"><div class="col-md-12 text-center">ATTACHMENTS</div></div>
					<div class="row">
						<?php foreach($task->files as $file):?>	
							<div class="col-md-2">
								<a href="<?php echo base_url("uploads/tasks/{$file->file_name}");?>" data-lightbox="test">
									<img class='img-thumbnail' src="<?php echo base_url("uploads/tasks/{$file->thumb_name}");?>" alt="image missing">
								</a>
							</div>
						<?php endforeach;?>
					</div>
				</div>
				<?php endif;?>

				<form id="task_notes" action="tasks/notes" method="POST">
					<input type="hidden" name="task_id" value="<?php echo $task->id;?>">
					<div class="form-group">
						<label for="">Notes</label>
						<textarea name="notes" id="" rows="5" class="summernote form-control" placeholder="Enter your notes. Other users will be able to view your notes." ></textarea>
					</div>
					<div class="form-group">
						<button type="submit" class="btn btn-flat btn-info" id="saveNote"><i class='fa fa-edit'></i> Save Note</button>
					</div>
				</form>

				<!-- Display Previous Notes Here -->
				<?php if(!empty($task->notes)):?>
					<table id='previousNotes' class="table table-bordered table-hover">
						<tbody>
						<?php foreach($task->notes as $i => $note):?>
							<tr class='<?php echo ($note->out_of_scope == '1') ? 'out-of-scope' : '';?>'>
								<td><?php echo $i+1;?></td>
								<td><?php echo nl2br($note->notes);?><br>
									<span class="float-right" style='color:#4c4c4c; padding:3px 8px; font-size:0.8em; font-style:italic;'>
										<?php echo $note->name.$note->customer;?> - <?php echo date('d-M-Y h:i A',strtotime($note->created_on));?>
									</span>
								</td>
								<td>
									<?php if($note->created_by == $_SESSION['user_id']):?>
										<div class="btn btn-xs btn-danger deleteNote" data-note-id='<?php echo $note->id;?>'><i class="fa fa-trash"></i></div>
									<?php endif;?>	
								</td>
							</tr>
						<?php endforeach;?>
						</tbody>
					</table>
				<?php endif;?>
			</div>
			<!-- /.card-body -->

			<div class="card-footer">
				<a href="<?php echo "tasks/listing?customer_id=".$this->input->get('customer_id')."&stage=".$this->input->get("stage");?>"><div class="btn btn-warning btn-flat"><i class="fa fa-chevron-left"></i> Back</div></a>
				<!-- <div class="btn btn-danger btn-flat float-right"><i class="fa fa-trash"></i> Delete</div> -->
			</div>
        </div>
    </div>

	<div class="col-md-6">
		<div class="card">
			<div class="card-header bg-danger">
				<h3 class='card-title'>Scope</h3>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<p class="title">What's expected from this task</p>
						<textarea class="form-control textarea textarea-200" name="" id=""><?php echo nl2br($task->scope_client_expectation);?></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<p class="title">What's not included</p>
						<textarea class="form-control textarea textarea-200" name="" id=""><?php echo nl2br($task->scope_not_included);?></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<p class="title">When it's considered done</p>
						<textarea class="form-control textarea textarea-200" name="" id=""><?php echo nl2br($task->scope_when_done);?></textarea>
					</div>
				</div>

			</div>
			<div class="card-footer">

			</div>
		</div>
		<div class="card card-secondary">
			<div class="card-header bg-purple">
				<h3 class='card-title'>Stage History</h3>
			</div>
			<div class="card-body">
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>DATE</th>
							<th>USER</th>
							<th>STAGE CHANGE</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($task->stage_history as $history):?>
						<tr>
							<td><?php echo date('d-M-Y h:i A',strtotime($history->created_on));?></td>
							<td><?php echo "{$history->created_by_email}<span class='pull-right float-right'>[{$history->user_type}]</span>";?></td>
							<td><?php echo "From <b>" . strtoupper(str_replace("_"," ",$history->old_stage)) . "</b> to <b>" . strtoupper(str_replace("_"," ",$history->new_stage))."</b>";?></td>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="card card-secondary">
			<div class="card-header bg-purple">
				<h3 class='card-title'>Assigned Users</h3>
			</div>
			<div class="card-body">
				<ul id="users-list" class="list-group">
					<?php foreach($users as $user):?>
					<?php if($user->user_type != 'developer') continue;?>
					<li data-id="<?php echo $user->id;?>" class="list-group-item <?php echo in_array($user->id, $task->assigned_users) ? 'assigned':'';?>">
						<img style='width:50px;padding:2px;background-color:#eee;border:1px solid #ccc;border-radius: 50%;' src="uploads/users/<?php echo $user->photo;?>" alt="">
						<?php echo "{$user->email} ({$user->name})";?>
					</li>
					<?php endforeach;?>
				</ul>
			</div>
		</div>
	</div>
</div>