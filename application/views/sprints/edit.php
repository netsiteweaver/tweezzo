<div class="row">
    <div class="col-md-6">
        <div class="card card-secondary">
            <!-- <div class="card-header">
				<h3 class="card-title">Task</h3>
			</div> -->
            <!-- /.card-header -->
            <!-- form start -->
            <form id="add_user" action="sprints/save" method="post">
				<input type="hidden" name="uuid" value="<?php echo $sprint->uuid;?>">
                <div class="card-body">
					<div class="form-group">
						<label for="">Project</label>
						<select class="form-control required" name="project_id" required>
							<option value="">Select</option>
							<?php foreach($projects as $p):?>
							<option value="<?php echo $p->id;?>" <?php echo ($p->id == $sprint->project_id) ? 'selected' : '';?>><?php echo "{$p->name} / {$p->company_name}";?></option>
							<?php endforeach;?>
						</select>
					</div>
                    <div class="form-group">
						<label for="">Code</label>
						<div class="input-group">
							<input type="text" class="form-control" name="code" placeholder="e.g. S3 (unique per project)" value="<?php echo isset($sprint->code) ? htmlspecialchars($sprint->code) : '';?>" maxlength="20">
							<div class="input-group-append">
								<button type="button" class="btn btn-outline-secondary" id="generate_sprint_code">
									<i class="fa fa-magic"></i> Generate
								</button>
							</div>
						</div>
						<small class="form-text text-muted">Short code for task references (e.g. WR-S3-001). Unique per project.</small>
						<small class="form-text text-danger"><i class="fa fa-exclamation-triangle"></i> Warning: If this code has already been shared, changing it can break existing references.</small>
                    </div>
                    <div class="form-group">
						<label for="">Sprint Name</label>
						<input type="text" class="form-control required" name="name" placeholder="Enter Sprint Name" value="<?php echo $sprint->name;?>" required autofocus>
                    </div>

                    <div class="form-group">
						<label for="">Active</label>
						<input type="checkbox" name="active" value="" <?php echo ($sprint->active=='1')?'checked':'';?>>
					</div>
                    <div class="form-group">
                        <label for="ready_for_validation">Ready For Customer Validation</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="ready_for_validation"
                                   name="ready_for_validation"
                                   value="1"
                                   <?php echo (!empty($validationReminder) && (int)$validationReminder->ready_for_validation === 1) ? 'checked' : ''; ?>>
                            <label class="custom-control-label" for="ready_for_validation">
                                Enable weekly staging reminders for this sprint
                            </label>
                        </div>
                        <small id="ready_for_validation_help" class="form-text text-muted">
                            Keep this OFF while tasks are still being pushed to staging.
                        </small>
                        <?php if(!empty($validationReminder) && !empty($validationReminder->last_sent_on)): ?>
                            <small class="form-text text-info">
                                Last reminder sent on: <?php echo date('d M Y H:i', strtotime($validationReminder->last_sent_on)); ?>
                            </small>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
					<a href="<?php echo "sprints/listing?customer_id=".$this->input->get('customer_id')."&stage=".$this->input->get("stage");?>""><div class="btn btn-warning btn-flat"><i class="fa fa-chevron-left"></i> Back</div></a>
                    <button type="submit" class="btn btn-success btn-flat" id="save"><i class='fa fa-save'></i> Save</button>
                    <div class="btn btn-danger btn-flat float-right"><i class="fa fa-trash"></i> Delete</div>
                </div>
            </form>
        </div>
    </div>
</div>
