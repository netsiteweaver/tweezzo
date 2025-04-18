<div class="row">
    <div class="col-md-6">
        <div class="card card-secondary">
            <!-- <div class="card-header">
				<h3 class="card-title">Task</h3>
			</div> -->
            <!-- /.card-header -->
            <!-- form start -->
            <form id="add_user" action="projects/save" method="post">
                <div class="card-body">
                    <div class="form-group">
						<label for="">Project Name</label>
						<input type="text" class="form-control required" name="name" placeholder="Enter Task Name" value="" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="">Description</label>
						<textarea name="description" id="" rows="3" class="form-control"></textarea>
                    </div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="">Start Date</label>
								<input type="date" class="form-control" name="start_date" placeholder="" value="">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="">End Date</label>
								<input type="date" class="form-control" name="end_date" placeholder="" value="">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="">Customer</label>
						<select class="form-control required" name="customer_id" required>
							<option value="">Select</option>
							<?php foreach($customers as $c):?>
							<option value="<?php echo $c->customer_id;?>"><?php echo "{$c->company_name} / {$c->full_name}";?></option>
							<?php endforeach;?>
						</select>
					</div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                    <button type="submit" class="btn btn-success" id="save"><i class='fa fa-save'></i> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>