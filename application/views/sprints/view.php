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
						<input type="text" class="form-control required" name="name" placeholder="Enter Sprint Name" value="<?php echo $sprint->name;?>" disabled>
                    </div>
                    
					<div class="form-group">
						<label for="">Project</label>
						<select class="form-control required" name="project_id" disabled>
							<option value="">Select</option>
							<?php foreach($projects as $p):?>
							<option value="<?php echo $p->id;?>" <?php echo ($p->id == $sprint->project_id) ? 'selected' : '';?>><?php echo "{$p->name} / {$p->company_name}";?></option>
							<?php endforeach;?>
						</select>
					</div>

                    <div class="form-group col-6">
                        <label for="">Contains No. of Tasks</label>
                        <input type="text" class="text-right form-control" value="<?php echo $tasks;?>" disabled>
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
					<a href="<?php echo "sprints/listing?customer_id=".$this->input->get('customer_id')."&stage=".$this->input->get("stage");?>""><div class="btn btn-warning btn-flat"><i class="fa fa-chevron-left"></i> Back</div></a>
                </div>
            </form>
        </div>
    </div>
</div>
