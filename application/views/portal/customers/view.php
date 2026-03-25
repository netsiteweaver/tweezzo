<style>
	p.notes{
		font-style: italic;
		color:#4c4c4c;
		font-size:0.8em;
	}
	#previous_notes .developer {
		padding-top: 5px; padding-bottom:5px;color:#4c4c4c;font-size:0.8em;
	}
	/* tr.out-of-scope, #previous_notes tr.out-of-scope .developer {
		color:#fff;
		background-color:#ff0000;
	} */
</style>
<div class="row justify-content-center mb-5">
	<div class="col-lg-8 col-md-10">
		<div class="card card-secondary shadow-lg animate__animated animate__fadeIn">
			<div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
				<div>
					<i class="bi bi-clipboard-check"></i>
					<span class="fw-bold">Task #<?php echo $task->task_number;?>: <?php echo htmlspecialchars($task->name);?></span>
				</div>
				<div class="stage-button stage-button-<?php echo $task->stage;?>" id="task-stage-badge" style="display:inline-block;"><?php echo strtoupper(str_replace("_"," ",$task->stage));?></div>
			</div>
			<div class="card-body">
				<!-- Progress Bar for Stage -->
				<?php 
				$stages_order = ['new','in_progress','testing','staging','validated','completed','on_hold','stopped'];
				$progress = (array_search($task->stage, $stages_order) !== false) ? round((array_search($task->stage, $stages_order)+1)/count($stages_order)*100) : 0;
				?>
				<div class="progress mb-3" style="height: 18px;">
				  <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress;?>%;" aria-valuenow="<?php echo $progress;?>" aria-valuemin="0" aria-valuemax="100">
					<?php echo $progress;?>%
				  </div>
				</div>
				<!-- Tabs -->
								<ul class="nav nav-tabs mb-3" id="taskTab" role="tablist">
									<li class="nav-item" role="presentation">
										<button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">Details</button>
									</li>
									<li class="nav-item" role="presentation">
										<button class="nav-link" id="scope-tab" data-bs-toggle="tab" data-bs-target="#scope" type="button" role="tab">Scope</button>
									</li>
									<li class="nav-item" role="presentation">
										<button class="nav-link" id="attachments-tab" data-bs-toggle="tab" data-bs-target="#attachments" type="button" role="tab">Attachments</button>
									</li>
									<li class="nav-item" role="presentation">
										<button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">History</button>
									</li>
								</ul>
				<div class="tab-content" id="taskTabContent">
									   <!-- Details Tab -->
									   <div class="tab-pane fade show active" id="details" role="tabpanel">
										   <input type="hidden" name="id" value="<?php echo $task->id;?>">
										   <div class="mb-3">
											   <strong>Work type:</strong> <?php echo !empty($task->work_type) ? ucfirst($task->work_type) : '—';?>
											   &nbsp;|&nbsp; <strong>Billable:</strong> <?php echo isset($task->billable) && $task->billable == 1 ? 'Yes' : (isset($task->billable) && $task->billable == 0 ? 'No' : '—');?>
											   <?php if (isset($task->billable) && $task->billable == 1): ?>
											   &nbsp;|&nbsp; <strong>Settled:</strong> <?php echo isset($task->settled) && $task->settled == 1 ? 'Yes' : (isset($task->settled) && $task->settled == 0 ? 'No' : '—');?>
											   <?php if (isset($task->settled_on) && $task->settled_on): ?>
											   &nbsp;|&nbsp; <strong>Date settled:</strong> <?php echo date('d M Y', strtotime($task->settled_on));?>
											   <?php endif; ?>
											   <?php if (isset($task->ref) && $task->ref !== ''): ?>
											   &nbsp;|&nbsp; <strong>Ref:</strong> <?php echo htmlspecialchars($task->ref);?>
											   <?php endif; ?>
											   <?php endif; ?>
										   </div>
										   <div class="mb-3"><strong>Description:</strong><br><?php echo $task->description;?></div>
										   
										   <!-- Validation Section for Staging Tasks -->
										   <?php if($task->stage == 'staging'):?>
										   <div class="card card-info mb-3">
											   <div class="card-body">
												   <p class='mb-2'><strong>Task Validation</strong></p>
												   <p class='text-muted small mb-3'>This task has successfully passed our internal testing and is now available on the <b>Staging Server</b> for your review.</p>
												   <p class='text-muted small mb-3'>Please verify it against the requirements and click the <b>Validate</b> button below if everything meets your expectations.</p>
												   <p class='mb-3'>
													   <a href="<?php echo base_url('portal/customers/validationGuide');?>" class="btn btn-outline-primary btn-sm">
														   <i class="bi bi-journal-text me-1"></i> Open Validation Guide
													   </a>
												   </p>
												   <p class='text-muted small mb-3'>If you find any discrepancies, kindly send us a note detailing what does not match your expectations (as per the task description).</p>
												   <div class="btn btn-info validate mt-2"><i class="bi bi-check-circle"></i> Validate</div>
												   <div class="mt-3 pt-3 border-top">
													   <label class="small"><strong>Rejection Reason (if applicable):</strong></label>
													   <textarea name="reject_reason" rows="3" class="form-control mt-2" placeholder="Please explain why this task is being rejected to help us make necessary corrections"></textarea>
													   <div class="btn btn-danger mt-2 reject"><i class="bi bi-x-circle"></i> Reject</div>
												   </div>
											   </div>
										   </div>
										   <?php elseif($task->stage != 'validated'):?>
										   <div class="alert alert-info mb-3">
											   <p class='mb-0 small'>This task will be available for validation once it reaches the <span class="stage-button stage-button-staging" id="task-stage-badge" style="display:inline-block; padding:0px 5px !important;border-radius:0px !important;">Staging</span> stage. Your validation is required before deploying to the Production Server.</p>
										   </div>
										   <?php endif;?>
										   
										   <!-- Notes Section: Only in Details Tab -->
										   <div class="card card-secondary shadow-sm mt-4">
											 <div class="card-header bg-info text-white"><i class="bi bi-chat-dots"></i> Notes</div>
											 <div class="card-body">
											<form id="task_notes" method="POST" enctype="multipart/form-data" action="javascript:void(0)" novalidate>
												   <input type="hidden" name="task_id" value="<?php echo $task->id;?>">
												   <div class="form-group">
													   <label for="">Add a Note</label>
													   <textarea name="notes" rows="4" class="summernote form-control" placeholder="Please make sure to enter notes that are related to the task description and scope to avoid being flagged as out of scope and rejected." required></textarea>
												   </div>
												   <div class="form-group mt-2">
													   <label for="note_file">Attach a file (optional):</label>
													   <input type="file" name="note_file" id="note_file" class="form-control">
													   <div id="note_file_preview" class="mt-2" style="display:none;">
														   <img src="" alt="Image Preview" id="note_file_img" style="max-width:200px; max-height:200px; border:1px solid #ccc;" />
													   </div>
												   </div>
												   <div class="form-group mt-2">
													   <button type="submit" class="btn btn-info" id=""><img class='ionicon' src='assets/ionicons/save-outline.svg'></i> Save Note</button>
												   </div>
											   </form>
											   <hr class='mt-4'>
											   <!-- Display Previous Notes Here -->
											   <?php if(!empty($task->notes)):?>
												   <table id='previous_notes' class="table table-bordered">
													   <tbody>
													   <?php foreach($task->notes as $i => $notes):?>
														   <tr class='<?php echo ($notes->out_of_scope == '1') ? 'out-of-scope' : '';?>'>
															   <!-- <td><?php echo $i+1;?></td> -->
															   <td>
															   <?php echo nl2br($notes->notes);?>
															   <?php if (!empty($notes->file)): ?>
																   <br><a href="<?php echo base_url('uploads/notes/' . $notes->file); ?>" target="_blank" class="badge bg-secondary"><i class="bi bi-paperclip"></i> Download Attachment</a>
															   <?php endif; ?>
															   <div class="float-end developer" style='' title="<?php echo $notes->country_code;?>">
															   <?php echo "by {$notes->developer}{$notes->customer} <i class='flag flag-".$notes->country_code."'></i> on " . date_format(date_create($notes->created_on),'Y m d @ H:i');?>
															   </div>
															   </td>
															   <td >
																   <?php if($notes->created_by_customer == $_SESSION['customer_access_id']):?>
																	   <div class="btn btn-sm btn-danger deleteNote" data-note-id='<?php echo $notes->id;?>'><i class="bi bi-trash"></i></div>
																   <?php endif;?>
																   <?php if($notes->out_of_scope == '1'):?>
																	   <img style='width:24px; height:24px;' src="<?php echo base_url('assets/images/OUT-OF-SCOPE-36PX.png');?>" alt="">
																   <?php endif;?>
															   </td>
														   </tr>
														   <?php endforeach;?>
													   </tbody>
												   </table>
											   <?php endif;?>
											 </div>
										   </div>
									   </div>
									<!-- Scope Tab -->
									<div class="tab-pane fade" id="scope" role="tabpanel">
										<div class="mb-3"><strong>Scope:</strong><br><span class="text-muted"><?php echo !empty($task->scope) ? nl2br(htmlspecialchars($task->scope)) : '<em>No scope specified.</em>'; ?></span></div>
										<div class="mb-3"><strong>Not Included:</strong><br><span class="text-muted"><?php echo !empty($task->not_included) ? nl2br(htmlspecialchars($task->not_included)) : '<em>Nothing specified.</em>'; ?></span></div>
										<div class="mb-3"><strong>Done When:</strong><br><span class="text-muted"><?php echo !empty($task->done_when) ? nl2br(htmlspecialchars($task->done_when)) : '<em>Not specified.</em>'; ?></span></div>
									</div>


				  <!-- Attachments Tab -->
				  <div class="tab-pane fade" id="attachments" role="tabpanel">
					<?php if(count($task->files) > 0):?>
					<div class="card card-secondary">
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
					<?php else: ?>
						<div class="text-muted">No attachments for this task.</div>
					<?php endif; ?>
				  </div>
				  <!-- History Tab -->
				  <div class="tab-pane fade" id="history" role="tabpanel">
					<div class="card card-secondary">
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
			</div>
			<div class="card-footer">
				<a href="<?php echo "portal/customers/tasks?sprint_id=".$task->sprint_id;?>"><div class="btn btn-warning btn-flat"><i class="bi bi-chevron-left"></i>Back</div></a>
			</div>
		</div>
	</div>
</div>