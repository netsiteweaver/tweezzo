<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info">
                <h3 class="card-title">Notes</h3>
            </div>
            <div class="card-body">
				<!-- Display Previous Notes Here -->
				<?php if(!empty($task->notes)):?>
					<table id='previousNotes' class="table table-bordered table-hover">
						<tbody>
						<?php foreach($task->notes as $i => $note):?>
							<tr style="<?php echo ( (count($task->notes)>1) && ($note->id == $this->input->get('note_id')) ) ? 'border:2px solid #17a2b8;' : '';?>" class='<?php echo ($note->out_of_scope == '1') ? 'out-of-scope' : '';?>'>
								<td style='color:#ccc;font-size:0.8em;'><?php echo count($task->notes) - $i;?></td>
								<td><?php echo nl2br($note->notes);?><br>
									<span class="float-right" style='color:#4c4c4c; padding:3px 8px; font-size:0.8em; font-style:italic;'>
										<?php echo $note->name.$note->customer;?> - <?php echo date('d-M-Y h:i A',strtotime($note->created_on));?>
									</span>
								</td>
								<!-- <td>
									<?php if($note->created_by == $_SESSION['user_id']):?>
										<div class="btn btn-xs btn-danger deleteNote" data-note-id='<?php //echo $note->id;?>'><i class="fa fa-trash"></i></div>
									<?php endif;?>	
								</td> -->
							</tr>
						<?php endforeach;?>
						</tbody>
					</table>
				<?php endif;?>

                <div id="task_notes" class="form-group mt-5">
                    <label for="">Notes</label>
                    <textarea name="notes" id="" rows="5" class="summernote form-control" placeholder="Enter your notes. Other users will be able to view your notes." ></textarea>
                </div>
                <div class="form-group">
                    <div data-task-id="<?php echo $task->id;?>" class="btn btn-flat btn-info" id="saveNote"><i class='fa fa-save'></i> Save Note</div>
                </div>

            </div>
            <div class="card-footer">

            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-teal">
                <h3 class="card-title">Task Details</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Section</label>
                            <input type="text" class="form-control" name="section" placeholder="e.g. #01.14"
                                value="<?php echo $task->section;?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Task Number</label>
                            <input type="text" class="form-control" name="task_number" placeholder="e.g. #01.14"
                                value="<?php echo $task->task_number;?>" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Task Name</label>
                    <div class="form-control textarea textarea-100"><?php echo $task->name;?></div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <div class="form-control textarea textarea-100" style=''><?php echo $task->description;?></div>
                </div>
                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Due Date</label>
                            <input type="date" class="form-control" name="due_date" placeholder=""
                                value="<?php echo $task->due_date;?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Estimated Hours</label>
                            <input type="number" step='0.25' min='0' class="form-control" name="estimated_hours"
                                value="<?php echo $task->estimated_hours;?>">
                        </div>
                    </div>

                </div>
                <div class="form-group">
                    <p>Current Stage:</p>
                    <div class="stage-button stage-button-<?php echo $task->stage;?>"><?php echo strtoupper(str_replace("_"," ",$task->stage));?></div>
                </div>
            </div>
            <div class="card-footer">

            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-danger">
                <h3 class="card-title">Scope</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <p class="title">What's expected from this task</p>
                        <div class="form-control textarea textarea-200" name=""
                            id=""><?php echo nl2br($task->scope_client_expectation);?></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <p class="title">What's not included</p>
                        <div class="form-control textarea textarea-200" name=""
                            id=""><?php echo nl2br($task->scope_not_included);?></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <p class="title">When it's considered done</p>
                        <div class="form-control textarea textarea-200" name=""
                            id=""><?php echo nl2br($task->scope_when_done);?></div>
                    </div>
                </div>

            </div>
            <div class="card-footer">

            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <a href="<?php echo base_url("notes/listing?".$qs);?>">
            <div class="btn btn-warning"><i class="fa fa-chevron-left"></i> Back</div>
        </a>
    </div>
</div>