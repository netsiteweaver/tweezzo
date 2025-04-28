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
	tr.private td{
		background-color:#eee;
	}
</style>

<div class="row">
    <div class="col-md-6">
        <div class="card ">
			<div class="card-header bg-teal">
				<h3>Task Information</h3>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<div class="message" style='padding-top:20px; text-align:center; font-weight:bold; color:#ff0000;'></div>
					</div>
				</div>
				<form id="add_user" action="tasks/save" method="post" enctype="multipart/form-data">
					<input type="hidden" name="uuid" value="<?php echo $task->uuid;?>">
					<input type="hidden" name="id" value="<?php echo $task->id;?>">
					<input type="hidden" name="qs" value="<?php echo $qs;?>">
					<!-- <input type="hidden" name="stage" value="<?php echo $this->input->get("stage");?>"> -->
					<input type="hidden" name="_customer_id" value="<?php echo $this->input->get("customer_id");?>">
					
						<div class="form-group">
							<label for="customer_id">Customer</label>
							<select class="form-control required" name="customer_id" id="customer_id" required>
								<option value="">Select</option>
								<?php foreach($customers as $c):?>
								<option value="<?php echo $c->customer_id;?>" <?php echo ($task->customer_id == $c->customer_id) ? 'selected' : '';?>><?php echo "{$c->company_name}";?></option>
								<?php endforeach;?>
							</select>
						</div>
						<div class="form-group">
							<label for="project_id">Projects</label>
							<select name="project_id" id="project_id" class="form-control" required>
								<?php foreach($projects as $project):?>
								<option value="<?php echo $project->id;?>" <?php echo ($task->project_id == $project->id) ? 'selected' : '';?>><?php echo $project->name;?></option>
								<?php endforeach;?>
							</select>
						</div>
						<div class="form-group">
							<label for="sprint_id">Sprint</label>
							<select name="sprint_id" id="sprint_id" class="form-control" required>
								<?php foreach($sprints as $sprint):?>
								<option value="<?php echo $sprint->id;?>" <?php echo ($task->sprint_id == $sprint->id) ? 'selected' : '';?>><?php echo $sprint->name;?></option>
								<?php endforeach;?>
							</select>
						</div>
						<div class="ready">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>Section</label>
										<input type="text" class="form-control" name="section" placeholder="e.g. #01.14" value="<?php echo $task->section;?>" required>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Task Number</label>
										<input type="text" class="form-control" name="task_number" placeholder="e.g. #01.14" value="<?php echo $task->task_number;?>" required>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label>Task Name</label>
								<input type="text" class="form-control required" name="name" placeholder="Enter Task Name" value="<?php echo $task->name;?>" required autofocus>
							</div>
							<div class="form-group">
								<label>Description</label>
								<textarea name="description" id="" rows="5" class="summernote form-control"><?php echo $task->description;?></textarea>
							</div>
							<div class="row">
								
								<div class="col-md-6">
									<div class="form-group">
										<label>Due Date</label>
										<input type="date" class="form-control" name="due_date" placeholder="" value="<?php echo $task->due_date;?>">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Estimated Hours</label>
										<input type="number" step='0.25' min='0' class="form-control" name="estimated_hours"  value="<?php echo $task->estimated_hours;?>">
									</div>
								</div>
								
							</div>
							<div class="form-group">
								<label>Stage</label>
								<select class="form-control required" name="stage" required>
									<option value="" disabled>Select</option>
									<option value="new" <?php echo ($task->stage == 'new') ? 'selected' : '';?>>New</option>
									<option value="in_progress" <?php echo ($task->stage == 'in_progress') ? 'selected' : '';?>>In Progress</option>
									<option value="testing" <?php echo ($task->stage == 'testing') ? 'selected' : '';?>>Testing</option>
									<option value="staging" <?php echo ($task->stage == 'staging') ? 'selected' : '';?>>Staging</option>
									<option value="validated" <?php echo ($task->stage == 'validated') ? 'selected' : '';?>>Validated</option>
									<option value="completed" <?php echo ($task->stage == 'completed') ? 'selected' : '';?>>Completed</option>
									<option value="on_hold" <?php echo ($task->stage == 'on_hold') ? 'selected' : '';?>>On Hold</option>
									<option value="stopped" <?php echo ($task->stage == 'stopped') ? 'selected' : '';?>>Stopped</option>

								</select>
							</div>
							<div class="form-group col-md-2">
								<label>Progress</label>
								<input type="text" class="form-control text-right" name="progress" value="<?php echo $task->progress;?> %" readonly>
							</div>
							<input type="hidden" name="deleted_images" value="[]">
							<div class="row">
								<?php foreach($task->files as $file):?>	
									<div class="col-md-2">
										<a href="<?php echo base_url("uploads/tasks/{$file->file_name}");?>" data-lightbox="test">
											<img class='img-thumbnail' src="<?php echo base_url("uploads/tasks/{$file->thumb_name}");?>" alt="image missing">
										</a>
										<div class='delete_file myCursor' data-file-id='<?php echo $file->id;?>' style="position:absolute;top:5px;left:15px;color:red"><i class="fa fa-times"></i></div>
									</div>
								<?php endforeach;?>
							</div>
							<div class="form-group">
								<label for="">Upload files</label>
								<input type="file" class="form-control" name="file1" id="file" accept=".jpg, .png, .jpeg" placeholder="Upload File">
								<input type="file" class="form-control" name="file2" id="file" accept=".jpg, .png, .jpeg" placeholder="Upload File">
								<input type="file" class="form-control" name="file3" id="file" accept=".jpg, .png, .jpeg" placeholder="Upload File">
								<input type="file" class="form-control" name="file4" id="file" accept=".jpg, .png, .jpeg" placeholder="Upload File">
								<input type="file" class="form-control" name="file5" id="file" accept=".jpg, .png, .jpeg" placeholder="Upload File">
								<small class="form-text text-muted">Upload a file related to the task. (.jpg, .png, .jpeg)</small>
							</div>
						</div>
					
					<!-- /.card-body -->

					<div class="card-footer ready">
						<button type="submit" class="btn btn-success" id="save"><i class='fa fa-save'></i> Save Task</button>
					</div>
				</form>

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
				
					<table id='previousNotes' class="table table-bordered">
						<tbody>
						<?php if(!empty($task->notes)):?>
						<?php foreach($task->notes as $i => $note):?>
							<tr class='<?php echo ($note->display_type == 'private') ? 'private' : '';?> <?php echo ($note->out_of_scope == '1') ? 'alert alert-danger' : '';?>'>
								<td><?php echo $i+1;?></td>
								<td><?php echo nl2br($note->notes);?>
									<span class="float-right" style='color:#4c4c4c; padding:3px 8px; font-size:0.8em; font-style:italic;'>
										<?php echo $note->name.$note->customer;?> - <?php echo date('d-M-Y h:i A',strtotime($note->created_on));?>
									</span>
								</td>
								<td>
									<?php if($note->created_by == $_SESSION['user_id']):?>
										<div class="btn btn-xs btn-danger deleteNote" data-note-id='<?php echo $note->id;?>'><i class="fa fa-trash"></i></div>
									<?php endif;?>	
								</td>
								<td>
									<?php if($note->out_of_scope == '0'):?>
									<div class="cursor-pointer outOfScope" data-note-id='<?php echo $note->id;?>'>
										<img style='width:24px; height:24px;' src="<?php echo base_url('assets/images/OUT-OF-SCOPE-36PX.png');?>" alt="">
									</div>
									<?php endif;?>
								</td>
							</tr>
						<?php endforeach;?>
						<?php endif;?>
						</tbody>
					</table>
				
			</div>
        </div>
    </div>
	<div class="col-md-6">
		<div class="card card-secondary">
			<div class="card-header bg-teal">
				<h3>Stage History</h3>
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
			<div class="card-header bg-teal">
				<h3>Assigned Users</h3>
			</div>
			<div class="card-body">
				<ul id="users-list" class="list-group">
					<?php foreach($developers as $user):?>
					<?php if($user->user_type != 'developer') continue;?>
					<li data-id="<?php echo $user->id;?>" class="list-group-item select-user <?php echo in_array($user->id, $task->assigned_users) ? 'assigned':'';?>">
						<img style='width:50px;padding:2px;background-color:#eee;border:1px solid #ccc;border-radius: 50%;' src="uploads/users/<?php echo $user->photo;?>" alt="">
						<?php echo "{$user->email} ({$user->name})";?>
					</li>
					<?php endforeach;?>
				</ul>
			</div>
		</div>

	</div>
</div>
<div class="row">
	<div class="col-md-2">
		<a href="tasks/listing?customer_id=<?php echo $this->input->get("customer_id");?>"><div class="btn btn-warning"><i class="fa fa-chevron-left"></i> Back</div></a>
	</div>
</div>