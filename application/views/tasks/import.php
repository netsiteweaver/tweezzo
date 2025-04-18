<div class="row">
    <div class="col-md-6">
        <div class="card card-secondary">
            <!-- <div class="card-header">
				<h3 class="card-title">Task</h3>
			</div> -->
            <!-- /.card-header -->
            <!-- form start -->
            <form action="tasks/import" method="post" enctype="multipart/form-data">
                <div class="card-body">
					<div class="form-group">
						<label for=""> Select Customer</label>
						<select class="form-control required" name="customer_id" required>
							<option value="">Select</option>
							<?php foreach($customers as $c):?>
							<option value="<?php echo $c->customer_id;?>"><?php echo "{$c->company_name} / {$c->full_name}";?></option>
							<?php endforeach;?>
						</select>
					</div>
					<div class="form-group">
						<label for=""> Select Project</label>
						<select class="form-control required" name="project_id" required>
							<option data-customer-id="" value="">Select</option>
							<?php foreach($projects as $c):?>
							<option data-customer-id="<?php echo $c->customer_id;?>" value="<?php echo $c->id;?>" disabled><?php echo $c->name;?></option>
							<?php endforeach;?>
						</select>
					</div>
					<div class="form-group">
						<label for=""> Select Sprint</label>
						<select class="form-control required" name="sprint_id" required>
							<option data-project-id="" value="">Select</option>
							<?php foreach($sprints as $c):?>
							<option data-project-id="<?php echo $c->project_id;?>" value="<?php echo $c->id;?>" disabled><?php echo $c->name;?></option>
							<?php endforeach;?>
						</select>
					</div>

                    <p>
                        The format required is a csv file with at least four columns, namely, <code>task name, task description, stage and section</code>. <br>
                        Stage can have any of these values: <code>new, in progress, staging, validated, completed</code>, with a default value of <code>new</code> if it is empty or if data received does not match any of those mentioned.<br>
                        Section is optional, however it is useful if the application have many sections, or it may refer to controller/method to help developers.
                        A task number will be automatically added to each task.
                    </p>

					<div class="form-group">
						<label for="">Select File</label>
						<input type="file" class="form-control required" name="file" accept=".csv,.xlsx,.xls" required>
					</div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                    <a href="tasks/listing"><div class="btn btn-warning btn-flat"><i class="fa fa-chevron-left"></i> Back</div></a>
                    <button type="submit" class="btn btn-success btn-flat" id="save"><i class='fa fa-upload'></i> Import</button>
                </div>
            </form>
        </div>
    </div>
</div>