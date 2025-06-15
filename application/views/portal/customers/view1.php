        <style>
#users-list li {
    margin-bottom: 5px;
    border: 1px solid #ccc;
}

#users-list li.assigned {
    background-color: rgb(183, 221, 210);
    /* border:1px solid #ccc !important; */
}

#users-list li.assigned img {
    border: 4px solid #20c997 !important;
}

.note-editable p {
    font-weight: normal !important;
}

.note-editable>*:first-child {
    margin-top: 0 !important;
    padding-top: 0 !important;
}

.note-editable {
    line-height: 1;
}

.changeStage {
    cursor: pointer;
}
.transparent {
    opacity:0.3;
    cursor: unset;
}
        </style>

        <div class="row">
            <div class="col-md-6">
                <div class="card border-secondary mb-4"><!-- TASK INFORMATION -->
                    <div class="card-header ">
                        TASK INFORMATION
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <colgroup>
                                <col style='width:11.11%' />
                                <col style='width:22.22%' />
                                <col style='width:11.11%' />
                                <col style='width:22.22%' />
                                <col style='width:11.11%' />
                                <col style='width:22.22%' />
                            </colgroup>

                            <tr>
                                <th>Task #:</th>
                                <td><?php echo $task->task_number;?></td>
                                <th>Sprint:</th>
                                <td><?php echo $task->sprint_name;?></td>
                                <th>Project:</th>
                                <td><?php echo $task->project_name;?></td>

                            </tr>

                            <tr>
                                <th>Customer:</th>
                                <td colspan='5'><?php echo $task->company_name;?></td>
                            </tr>

                            <tr>
                                <th>Section:</th>
                                <td colspan='3'><?php echo $task->section;?></td>

                                <th>Stage</th>
                                <td>
                                    <div class="stage-button stage-button-<?php echo $task->stage;?>">
                                        <?php echo strtoupper(str_replace("_"," ",$task->stage));?>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <th>Task:</th>
                                <td colspan='5'><?php echo $task->name;?></td>
                            </tr>

                            <tr>
                                <th>Task Description:</th>
                                <td colspan='5'><?php echo nl2br($task->description);?></td>
                            </tr>

                        </table>
                    </div>
                </div>
                <div class="card border-secondary text-center mb-4"><!-- MOVE STAGE TO -->
                    <div class="card-header">
                        <img src="./assets/images/file-transfer.png" alt=""> MOVE STAGE TO
                    </div>
                    <div class="card-body">
						<?php if($task->stage == 'staging'):?>
				<div class="row">
					<div class="col-md-12 mt-4">
						<p class='notes'>This task has successfully passed our internal testing and is now available on the <b>Staging Server</b> for your review.</p>
						<p class='notes'>Please verify it against the requirements and click the <b>Validate</b> button below if everything meets your expectations.</p>
						<p class='notes'>If you find any discrepancies, kindly send us a note <i>(in the space provided below or beside depending whether you are on mobile or desktop respectively)</i> detailing what does not match your expectations (as per the task description).</p>
						<div class="btn btn-info mt-4 validate"><i class="bi bi-check-circle"></i> Validate</div>
						
					</div>
				</div>
				<div class="row mt-2" style='border:1px solid #ccc; padding:5px;'>
					<div class="col-md-12">
						<textarea name="reject_reason" id="" rows="3" class="form-control" placeholder="Please explain why this task is being rejected to help us make necessary corrections"></textarea>
						<div class="btn btn-danger mt-4 reject"><i class="bi bi-x-circle"></i> Reject</div>
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



                        
                    </div>

                </div>
                <div class="card border-secondary mb-4"><!-- PREVIOUS NOTES -->
                    <div class="card-header">
                        PREVIOUS NOTES
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <?php foreach($task->notes as $i =>  $notes):?>
                            <tr class='<?php echo ($notes->out_of_scope == '1') ? 'out-of-scope' : '';?>'>
                                <td><?php echo $i+1;?></td>
                                <td>
                                    <?php echo nl2br(strip_tags($notes->notes));?>
                                    <span class="float-end developer" title="<?php echo $notes->country_code;?>">
                                        <?php echo "by {$notes->developer}{$notes->customer} <i class='flag flag-{$notes->country_code}'></i> on " . date_format(date_create($notes->created_on),'Y m d @ H:i');?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($notes->created_by == $_SESSION['developer_id']):?>
                                    <div class="btn btn-sm btn-danger deleteNote"
                                        data-note-id='<?php echo $notes->id;?>'><i class="bi bi-trash"></i>
                                    </div>
                                    <?php endif;?>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </table>
                    </div>
                </div>
                <div class="card border-secondary mb-4"><!-- NEW NOTE -->
                    <div class="card-header">
                        NEW NOTE
                    </div>
                    <div class="card-body">
                        <form method="post" action='portal/developers/saveNotes'>
                            <input type="hidden" name="task_id" value="<?php echo $task->id;?>">
                            <input type="hidden" name="task_uuid" value="<?php echo $task->uuid;?>">
                            <div class="form-group">
                                <!-- <label for="">Notes</label> -->
                                <textarea name="notes" id="" rows='5' class="summernote form-control" minlength='5'
                                    required></textarea>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <label for="display_type">
                                        <input type="checkbox" id="display_type" name="display_type" checked>
                                        Visible to All</label>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-primary"><i class="bi bi-save"></i> Save
                                        Notes</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

            <div class="col-md-6">
                <?php if(count($task->files) > 0):?>
                <div class="card border-secondary mb-4">
                    <div class="card-header">ATTACHMENTS</div>
                    <div class="card-body">
                        <div id="attachments">
                            <div class="row">
                                <?php foreach($task->files as $file):?>
                                <div class="col-md-4">
                                    <a href="<?php echo base_url("uploads/tasks/{$file->file_name}");?>"
                                        data-lightbox="test">
                                        <img style='width:100%;' class='img-thumbnail img-responsize'
                                            src="<?php echo base_url("uploads/tasks/{$file->thumb_name}");?>"
                                            alt="image missing">
                                    </a>
                                </div>
                                <?php endforeach;?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif;?>

                <?php if( count($task->stage_history) > 0 ):?>
                <div class="card border-secondary">
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
                                    <td><?php echo "{$history->created_by_email}<span class='text-muted'> [{$history->user_type}]</span>";?>
                                    </td>
                                    <td><?php echo "From <b>" . strtoupper(str_replace("_"," ",$history->old_stage)) . "</b> to <b>" . strtoupper(str_replace("_"," ",$history->new_stage))."</b>";?>
                                    </td>
                                </tr>
                                <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif;?>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-6">
                <a href="portal/developers/tasks<?php echo "?customer_id={$this->input->get('customer_id')}&project_id={$this->input->get('project_id')}&sprint_id={$this->input->get('sprint_id')}";?>">
                    <div class="btn btn-warning">
                        <img src="assets/ionicons/chevron-back-sharp.svg" alt="" class="ionicon">Back
                    </div>
                </a>
            </div>
        </div>