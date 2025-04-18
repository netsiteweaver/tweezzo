<div class="row">
    <div class="col-md-6">
        <div class="card card-secondary">
            <!-- <div class="card-header">
				<h3 class="card-title">Task</h3>
			</div> -->
            <!-- /.card-header -->
            <!-- form start -->
			<div class="card-body">
				<div class="form-group">
					<label for="">Project Name</label>
					<input type="text" class="form-control required" name="name" placeholder="Enter Task Name" value="<?php echo $project->name;?>" required autofocus disabled>
				</div>
				<div class="form-group">
					<label for="">Description</label>
					<textarea name="description" id="" rows="5" class="form-control" disabled><?php echo $project->description;?></textarea>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Start Date</label>
							<input type="text" class="form-control" name="task_number" placeholder="" value="<?php echo $project->start_date;?>" required disabled>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="">End Date</label>
							<input type="text" class="form-control" name="sprint" placeholder="" value="<?php echo $project->end_date;?>" disabled>
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<label for="">Client</label>
					<select class="form-control required" name="customer_id" required disabled>
						<option value="">Select</option>
						<?php foreach($customers as $c):?>
						<option value="<?php echo $c->customer_id;?>" <?php echo ($project->customer_id == $c->customer_id) ? 'selected' : '';?>><?php echo "{$c->company_name} / {$c->full_name}";?></option>
						<?php endforeach;?>
					</select>
				</div>

				
			</div>
			<!-- /.card-body -->

			<div class="card-footer">
				<a href="<?php echo "projects/listing";?>"><div class="btn btn-warning btn-flat"><i class="fa fa-chevron-left"></i> Back</div></a>
				<!-- <div class="btn btn-danger btn-flat float-right"><i class="fa fa-trash"></i> Delete</div> -->
			</div>
        </div>
    </div>
</div>