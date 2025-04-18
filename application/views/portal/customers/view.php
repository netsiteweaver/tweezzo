<style>
	p.notes{
		font-style: italic;
		color:#4c4c4c;
		font-size:0.8em;
	}
</style>
<div class="row mb-5">
    <div class="col-md-6">
        <div class="card card-secondary">
            <!-- <div class="card-header">
				<h3 class="card-title">Task</h3>
			</div> -->
            <!-- /.card-header -->
            <!-- form start -->
			<input type="hidden" name="uuid" value="<?php echo $task->uuid;?>">
			<input type="hidden" name="id" value="<?php echo $task->id;?>">
			<input type="hidden" name="sprint_id" value="<?php echo $task->sprint_id;?>">
			<input type="hidden" name="stage" value="<?php echo $this->input->get("stage");?>">
			<div class="card-body">
				<div class="form-group col-md-3">
					<label for="">Task Number</label>
					<input type="text" class="form-control" name="task_number" placeholder="Enter Task Number, e.g. #01.14" value="<?php echo $task->task_number;?>" required disabled>
				</div>
				<div class="form-group">
					<label for="">Task Name</label>
					<input type="text" class="form-control required" name="name" placeholder="Enter Task Name" value="<?php echo $task->name;?>" required autofocus disabled>
				</div>
				<div class="form-group">
					<label for="">Description</label>
					<textarea name="description" id="" rows="5" class="form-control" disabled><?php echo $task->description;?></textarea>
				</div>
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label for="">Stage</label>
							<select class="form-control required" name="stage" required disabled>
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
					</div>
				</div>
				<?php if($task->stage == 'staging'):?>
				<div class="row">
					<div class="col-md-12 mt-4">
						<p class='notes'>This task has successfully passed our internal testing and is now available on the <b>Staging Server</b> for your review.</p>
						<p class='notes'>Please verify it against the requirements and click the <b>Validate</b> button below if everything meets your expectations.</p>
						<p class='notes'>If you find any discrepancies, kindly send us a note <i>(in the space provided below or beside depending whether you are on mobile or desktop respectively)</i> detailing what does not match your expectations (as per the task description).</p>
						<div class="btn btn-info mt-4 validate"><img class='ionicon' src="assets/ionicons/checkmark-done-outline.svg" alt="">Validate</div>
					</div>
				</div>
				<?php elseif($task->stage != 'validated'):?>
				<div class="row mt-4">
					<div class="col-md-12">
						<p class=''>Once this task is pushed to the Staging Server, you will be kindly required to verify it. 
						If it meets the requirements, please click the "Validate" button (currently inactive).
						Once all tasks in the current sprint have been validated, they will be deployed to the Production Server.</p>
						<!-- <div class="btn btn-outline-info mt-4"><img class='ionicon' src="assets/ionicons/checkmark-done-outline.svg" alt="">Validate</div> -->
					</div>
				</div>
				<?php endif;?>
				<div class="row mt-2">
					<div class="col-md-4">
						<a data-lightbox='lifecycle' href="assets/images/task-lifecycle-2.png">
							<img class='img-thumbnail' src="assets/images/task-lifecycle-2.png" alt="">
						</a>
						
					</div>
				</div>
				
			</div>
			<!-- /.card-body -->

			<div class="card-footer">
				<a href="<?php echo "portal/customers/tasks?sprint_id=".$task->sprint_id;?>"><div class="btn btn-warning btn-flat"><i class="bi bi-chevron-left"></i>Back</div></a>
			</div>

        </div>
    </div>
	<div class="col-md-6">
		<div class="card card-secondary">
			<div class="card-body">
				<form id="task_notes" action="tasks/notes" method="POST">
					<input type="hidden" name="task_id" value="<?php echo $task->id;?>">
					<div class="form-group">
						<label for="">Notes</label>
						<textarea name="notes" id="" rows="5" class="form-control" placeholder="Enter your notes. Other users will be able to view your notes." required></textarea>
					</div>
					<div class="form-group">
						<div class="btn btn-flat btn-info float-end" id="saveNote"><img class='ionicon' src='assets/ionicons/save-outline.svg'></i>Save Note</div>
					</div>
				</form>

				<hr class='mt-5'>

				<!-- Display Previous Notes Here -->
				<?php if(!empty($task->notes)):?>
					<table id='previous_notes' class="table table-bordered">
						<tbody>
						<?php foreach($task->notes as $i => $notes):?>
							<tr>
								<td><?php echo $i+1;?></td>
								<td>
								<?php echo nl2br($notes->notes);?>
								<div class="float-end developer" style='padding-top: 5px; padding-bottom:5px;color:#4c4c4c;font-size:0.8em;'>
								<?php echo "by {$notes->developer}{$notes->customer} on " . date_format(date_create($notes->created_on),'Y m d @ H:i');?>
								</div>
								</td>
								<td>
									<?php if($notes->created_by_customer == $_SESSION['customer_id']):?>
										<div class="btn btn-sm btn-danger deleteNote" data-note-id='<?php echo $notes->id;?>'><i class="bi bi-trash"></i></div>
									<?php endif;?>
								</td>
							</tr>
							<?php endforeach;?>
						</tbody>
					</table>
				<?php endif;?>
			</div>
		</div>

		<?php if(count($task->files) > 0):?>
		<div class="card card-secondary mt-5">
			<div class="card-header">ATTACHMENTS</div>
			<div class="card-body">
				<div id="attachments">
					<div class="row">
						<?php foreach($task->files as $file):?>	
							<div class="col-md-2">
								<a href="<?php echo base_url("uploads/tasks/{$file->file_name}");?>" data-lightbox="test">
									<img class='img-thumbnail img-responsize' src="<?php echo base_url("uploads/tasks/{$file->thumb_name}");?>" alt="image missing">
								</a>
							</div>
						<?php endforeach;?>
					</div>
				</div>
			</div>
		</div>
		<?php endif;?>

		<div class="card card-secondary mt-5">
			<div class="card-header">STAGE HISTORY</div>
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
							<td><?php echo "{$history->created_by_email}<span class='text-muted'> [{$history->user_type}]</span>";?></td>
							<td><?php echo "From <b>" . strtoupper(str_replace("_"," ",$history->old_stage)) . "</b> to <b>" . strtoupper(str_replace("_"," ",$history->new_stage))."</b>";?></td>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>