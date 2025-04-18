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
						<label for="">Sprint Name</label>
						<input type="text" class="form-control required" name="name" placeholder="Enter Sprint Name" value="<?php echo $sprint->name;?>" required autofocus>
                    </div>
                    
					<div class="form-group">
						<label for="">Project</label>
						<select class="form-control required" name="project_id" required>
							<option value="">Select</option>
							<?php foreach($projects as $p):?>
							<option value="<?php echo $p->id;?>" <?php echo ($p->id == $sprint->project_id) ? 'selected' : '';?>><?php echo "{$p->name} / {$p->company_name}";?></option>
							<?php endforeach;?>
						</select>
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
