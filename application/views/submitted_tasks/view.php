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
			<?php $request_stage = isset($task->request_stage) ? $task->request_stage : (isset($task->stage) ? $task->stage : 'new'); ?>
			<div class="card-header bg-purple d-flex justify-content-between align-items-center">
				<h3 class='card-title mb-0'>Request Information</h3>
				<span class="badge badge-<?php echo $request_stage === 'rejected' ? 'danger' : ($request_stage === 'validated' ? 'success' : 'warning'); ?>"><?php echo strtoupper(str_replace('_', ' ', $request_stage)); ?></span>
			</div>
			<div class="card-body">
				<?php if ($request_stage === 'rejected' && !empty($task->rejection_reason)): ?>
				<div class="alert alert-danger">
					<strong>Rejected</strong><?php echo !empty($task->rejected_on) ? ' on ' . date('d M Y H:i', strtotime($task->rejected_on)) : ''; ?><?php echo !empty($task->rejected_by_name) ? ' by ' . htmlspecialchars($task->rejected_by_name) : ''; ?>
					<p class="mb-0 mt-2"><?php echo nl2br(htmlspecialchars($task->rejection_reason)); ?></p>
				</div>
				<?php endif; ?>
				<?php if ($request_stage === 'validated' && !empty($task->converted_task_uuid)): ?>
				<div class="alert alert-success">
					<strong>Approved</strong><?php echo !empty($task->validated_on) ? ' on ' . date('d M Y H:i', strtotime($task->validated_on)) : ''; ?><?php echo !empty($task->validated_by_name) ? ' by ' . htmlspecialchars($task->validated_by_name) : ''; ?>
					<p class="mb-0 mt-2"><a href="<?php echo base_url('tasks/view?task_uuid=' . $task->converted_task_uuid); ?>" class="alert-link">View converted task &rarr;</a></p>
				</div>
				<?php endif; ?>
				<div class="form-group">
					<label for="">Customer</label>
					<select class="form-control " name="customer_id"  disabled>
						<option value="">Select</option>
						<?php foreach($customers as $c):?>
						<option value="<?php echo $c->customer_id;?>" <?php echo ($task->customer_id == $c->customer_id) ? 'selected' : '';?>><?php echo "{$c->company_name}";?></option>
						<?php endforeach;?>
					</select>
				</div>

				<?php if(!empty($task->submitted_by)): ?>
				<div class="form-group">
					<label for="">Submitted by</label>
					<input type="text" class="form-control" value="<?php echo htmlspecialchars($task->submitted_by); ?><?php echo !empty($task->submitted_by_email) ? ' (' . htmlspecialchars($task->submitted_by_email) . ')' : ''; ?>" disabled readonly>
				</div>
				<?php endif; ?>

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
							<input type="text" class="form-control" name="task_number" placeholder="Enter Task Number, e.g. #01.14" value="<?php echo isset($task->task_number) ? htmlspecialchars($task->task_number) : '';?>"  disabled>
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
							<input type="date" class="form-control" name="task_number" placeholder="" value="<?php echo isset($task->due_date) ? htmlspecialchars($task->due_date) : '';?>"  disabled>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Estimated Hours</label>
							<input type="number" class="form-control" name="" placeholder="" value="<?php echo isset($task->estimated_hours) ? htmlspecialchars($task->estimated_hours) : '';?>"  disabled>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="">Stage</label>
					<select class="form-control " name="stage"  disabled>
						<option value="" disabled>Select</option>
						<option value="new" <?php echo ($request_stage == 'new')?'selected':'';?>>New</option>
						<option value="validated" <?php echo ($request_stage == 'validated')?'selected':'';?>>Validated</option>
						<option value="rejected" <?php echo ($request_stage == 'rejected')?'selected':'';?>>Rejected</option>
					</select>
				</div>

				<?php if(!empty($task->files) && count($task->files) > 0):?>
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
				<a href="<?php echo base_url('submitted_tasks/listing?' . $qs); ?>" class="btn btn-warning btn-flat"><i class="fa fa-chevron-left"></i> Back</a>
				<?php if ($request_stage === 'new' && !empty($perms['edit'])): ?>
				<button type="button" class="btn btn-danger btn-flat" data-toggle="modal" data-target="#modalReject"><i class="fa fa-times"></i> Reject</button>
				<?php endif; ?>
				<?php if ($request_stage === 'new' && !empty($perms['add'])): ?>
				<button type="button" class="btn btn-success btn-flat" data-toggle="modal" data-target="#modalApproveConvert"><i class="fa fa-check"></i> Approve &amp; Convert to Task</button>
				<?php endif; ?>
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
						<?php if (!empty($task->stage_history)): foreach($task->stage_history as $history):?>
						<tr>
							<td><?php echo date('d-M-Y h:i A',strtotime($history->created_on));?></td>
							<td><?php echo "{$history->created_by_email}<span class='pull-right float-right'>[{$history->user_type}]</span>";?></td>
							<td><?php echo "From <b>" . strtoupper(str_replace("_"," ",$history->old_stage)) . "</b> to <b>" . strtoupper(str_replace("_"," ",$history->new_stage))."</b>";?></td>
						</tr>
						<?php endforeach; else: ?>
						<tr><td colspan="3" class="text-muted">No stage history</td></tr>
						<?php endif; ?>
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

<!-- Reject modal -->
<div class="modal fade" id="modalReject" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="post" action="<?php echo base_url('submitted_tasks/rejectRequest'); ?>">
				<input type="hidden" name="uuid" value="<?php echo htmlspecialchars($task->uuid); ?>">
				<input type="hidden" name="qs" value="<?php echo htmlspecialchars($qs); ?>">
				<div class="modal-header">
					<h5 class="modal-title">Reject Request</h5>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="reject_reason">Reason <span class="text-danger">*</span></label>
						<textarea class="form-control" id="reject_reason" name="reason" rows="4" required placeholder="Enter the reason for rejecting this request..."></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-danger"><i class="fa fa-times"></i> Reject</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Approve & Convert modal -->
<div class="modal fade" id="modalApproveConvert" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="post" action="<?php echo base_url('submitted_tasks/approveConvert'); ?>">
				<input type="hidden" name="uuid" value="<?php echo htmlspecialchars($task->uuid); ?>">
				<input type="hidden" name="qs" value="<?php echo htmlspecialchars($qs); ?>">
				<div class="modal-header">
					<h5 class="modal-title">Approve &amp; Convert to Task</h5>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="approve_sprint_id">Sprint <span class="text-danger">*</span></label>
						<select class="form-control" id="approve_sprint_id" name="sprint_id" required>
							<?php if (!empty($sprints) && count($sprints) === 1): ?>
								<?php foreach ($sprints as $s): ?>
									<option value="<?php echo $s->id; ?>" selected><?php echo htmlspecialchars($s->project_name . ' / ' . $s->name); ?></option>
									<?php break; ?>
								<?php endforeach; ?>
							<?php else: ?>
								<option value="">Select sprint</option>
								<?php if (!empty($sprints)): foreach ($sprints as $s): ?>
									<option value="<?php echo $s->id; ?>"><?php echo htmlspecialchars($s->project_name . ' / ' . $s->name); ?></option>
								<?php endforeach; endif; ?>
							<?php endif; ?>
						</select>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="approve_estimated_hours">Estimated Hours</label>
								<input type="number" step="0.25" min="0" class="form-control" id="approve_estimated_hours" name="estimated_hours" value="1">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="approve_work_type">Work type</label>
								<select class="form-control" id="approve_work_type" name="work_type">
									<option value="development" selected>Development</option>
									<option value="maintenance">Maintenance</option>
									<option value="support">Support</option>
									<option value="other">Other</option>
								</select>
							</div>
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-md-6">
							<div class="form-group">
								<label for="approve_ref">Ref</label>
								<input type="text" class="form-control" id="approve_ref" name="ref" placeholder="e.g. INV001234 or QUO001234">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="approve_billable">Billable</label>
								<select class="form-control" id="approve_billable" name="billable">
									<option value="1">Yes</option>
									<option value="0" selected>No</option>
								</select>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label>Assign users (optional)</label>
						<ul id="approve-users-list" class="list-group" style="max-height: 200px; overflow-y: auto;">
							<?php foreach ($users as $user): ?>
							<?php if ($user->user_type != 'developer') continue; ?>
							<li data-id="<?php echo $user->id; ?>" class="list-group-item list-group-item-action approve-user-item" style="cursor: pointer;">
								<?php echo htmlspecialchars($user->email . ' (' . $user->name . ')'); ?>
							</li>
							<?php endforeach; ?>
						</ul>
						<input type="hidden" name="userIds" id="approve_user_ids" value="[]">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Approve &amp; Convert</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
(function(){
	var selected = [];
	var input = document.getElementById('approve_user_ids');
	var items = document.querySelectorAll('#approve-users-list .approve-user-item');
	items.forEach(function(li){
		li.addEventListener('click', function(){
			var id = parseInt(li.getAttribute('data-id'), 10);
			var i = selected.indexOf(id);
			if (i === -1) { selected.push(id); li.classList.add('active'); }
			else { selected.splice(i, 1); li.classList.remove('active'); }
			input.value = JSON.stringify(selected);
		});
	});
})();
</script>