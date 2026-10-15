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
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="">Start Date</label>
								<input type="date" class="form-control sprint-date" name="start_date" data-initial="<?php echo !empty($sprint->start_date) ? htmlspecialchars($sprint->start_date) : ''; ?>" value="<?php echo !empty($sprint->start_date) ? htmlspecialchars($sprint->start_date) : ''; ?>">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="">End Date</label>
								<input type="date" class="form-control sprint-date" name="end_date" data-initial="<?php echo !empty($sprint->end_date) ? htmlspecialchars($sprint->end_date) : ''; ?>" value="<?php echo !empty($sprint->end_date) ? htmlspecialchars($sprint->end_date) : ''; ?>">
							</div>
						</div>
					</div>
					<small class="form-text text-muted">Maximum duration: 14 days. Clear both dates to keep this sprint without a time box (legacy).</small>

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

        <?php
        $settlementLabels = [
            'due'   => ['danger', 'Payment outstanding: the client is reminded automatically'],
            'paid'  => ['success', 'Fully paid: ready to go live'],
            'none'  => ['warning', 'No quotation or invoice referenced by the tasks'],
            'error' => ['warning', 'HMS could not be reached: retried daily'],
        ];
        $settlement = (!empty($sprint->settlement_checked_on) && isset($settlementLabels[$sprint->settlement_status])) ? $settlementLabels[$sprint->settlement_status] : null;
        ?>
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title"><i class="fa fa-money-bill"></i> Go-live payment</h3>
            </div>
            <div class="card-body">
                <?php if ($settlement): ?>
                    <p class="mb-1"><span class="badge badge-<?php echo $settlement[0]; ?>"><?php echo strtoupper($sprint->settlement_status); ?></span> <?php echo $settlement[1]; ?></p>
                    <small class="form-text text-muted">All tasks validated on <?php echo date('d M Y H:i', strtotime($sprint->settlement_checked_on)); ?>.</small>
                    <?php if (!empty($sprint->settlement_reminded_on)): ?>
                        <small class="form-text text-info">Payment request last sent on: <?php echo date('d M Y H:i', strtotime($sprint->settlement_reminded_on)); ?></small>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-muted mb-1">Checked automatically once the client has validated every task of this sprint.</p>
                <?php endif; ?>
                <small class="form-text text-muted">
                    HMS emails the client's billing addresses what is still owed on the quotations/invoices referenced by this sprint's tasks. Nothing is sent if everything is paid.
                </small>
            </div>
            <div class="card-footer">
                <form action="sprints/send_payment_request" method="post" class="d-inline"
                      onsubmit="return confirm('Email the client a payment request for this sprint now?');">
                    <input type="hidden" name="uuid" value="<?php echo htmlspecialchars($sprint->uuid); ?>">
                    <button type="submit" class="btn btn-primary btn-flat">
                        <i class="fa fa-paper-plane"></i> <?php echo !empty($sprint->settlement_reminded_on) ? 'Resend payment request' : 'Send payment request'; ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
