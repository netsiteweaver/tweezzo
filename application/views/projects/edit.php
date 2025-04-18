<div class="row">
    <div class="col-md-6">
        <div class="card card-secondary">
            <!-- <div class="card-header">
				<h3 class="card-title">Task</h3>
			</div> -->
            <!-- /.card-header -->
            <!-- form start -->
            <form id="add_user" action="projects/save" method="post">
                <input type="hidden" name="uuid" value="<?php echo $project->uuid;?>">
				<input type="hidden" name="customer_id" value="<?php echo $this->input->get("customer_id");?>">
                <div class="card-body">
                    <div class="form-group">
						<label for="">Project Name</label>
						<input type="text" class="form-control required" name="name" placeholder="Enter Task Name" value="<?php echo $project->name;?>" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="">Description</label>
						<textarea name="description" id="" rows="5" class="form-control"><?php echo $project->description;?></textarea>
                    </div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="">Start Date</label>
								<input type="date" class="form-control" name="start_date" placeholder="" value="<?php echo $project->start_date;?>">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="">End Date</label>
								<input type="date" class="form-control" name="end_date" placeholder="" value="<?php echo $project->end_date;?>">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="">Customer</label>
						<select class="form-control required" name="customer_id" required>
							<option value="">Select</option>
							<?php foreach($customers as $c):?>
							<option value="<?php echo $c->customer_id;?>" <?php echo ($project->customer_id == $c->customer_id) ? 'selected' : '';?>><?php echo "{$c->company_name} / {$c->full_name}";?></option>
							<?php endforeach;?>
						</select>
					</div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                    <a href="<?php echo "projects/listing";?>""><div class="btn btn-warning btn-flat"><i class="fa fa-chevron-left"></i> Back</div></a>
                    <button type="submit" class="btn btn-success btn-flat" id="save"><i class='fa fa-save'></i> Save</button>
                    <div class="btn btn-danger btn-flat float-right"><i class="fa fa-trash"></i> Delete</div>
                </div>
            </form>
        </div>
    </div>
</div>