<div class="row">
    <div class="col-md-6">
        <div class="card card-secondary">
            <!-- <div class="card-header">
				<h3 class="card-title">Task</h3>
			</div> -->
            <!-- /.card-header -->
            <!-- form start -->
            <form id="add_user" action="sprints/save" method="post">
                <div class="card-body">
                    <div class="form-group">
						<label for="">Sprint Name</label>
						<input type="text" class="form-control required" name="name" placeholder="Enter Sprint Name" value="" required autofocus>
                    </div>
                    <div class="form-group">
						<label for="">Code</label>
						<input type="text" class="form-control" name="code" placeholder="e.g. S3 (unique per project)" value="" maxlength="20">
						<small class="form-text text-muted">Short code for task references (e.g. WR-S3-001). Unique per project. Leave empty to use task number only.</small>
                    </div>
					<div class="form-group">
						<label for="">Project</label>
						<select class="form-control required" name="project_id" required>
							<option value="">Select</option>
							<?php foreach($projects as $p):?>
							<option value="<?php echo $p->id;?>"><?php echo "{$p->name} / {$p->company_name}";?></option>
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