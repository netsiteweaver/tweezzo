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

					<p>The format required is a simple and lightweight csv file (explanation below) with six columns as follows:</p>
					<ul class="list-group mb-3">
						<li class="list-group-item">Task Name</li>
						<li class="list-group-item">Task Description</li>
						<li class="list-group-item">Section</li>
						<li class="list-group-item">What is Expected</li>
						<li class="list-group-item">What is Excluded</li>
						<li class="list-group-item">When is Completed</li>
					</ul>
					<p>We highly recommend enclosing text within double quotes to avoid getting errors since some of the fields submitted may contain commas. </p>
					<p>You can download a template <a href="<?php echo base_url("templates/importTasks.csv");?>">here</a></p>
					<!-- <p>Stage can have any of these values: <code>new, in progress, staging, validated, completed</code>, with a default value of <code>new</code> if it is empty or if data received does not match any of those mentioned.</p> -->
					<!-- <p>Section is highly recommended, however it is useful if the application have many sections, or it may refer to controller/method to help developers.</p> -->
					<p>A task number will be automatically added to each task. More options will be added in the next major release regarding the automatic numbering like pattern matching and others. </p>
					<hr>
					<p>A sprint is a short, fixed period of time (usually 1 - 4 weeks) during which a team works to complete a set of tasks or deliver a specific piece of a project. To read more about sprints, click on the image below:</p>
					<p class='text-center'><a target="_blank" href="https://en.wikipedia.org/wiki/Scrum_(software_development)#Sprint"><img src="assets/images/wikipedia.png" alt=""></a></p>
					<hr>

					<div class="form-group">
						<label for="">Select File</label>
						<form id="uploadForm" action="tasks/upload_file" method="post" enctype="multipart/form-data">
							<input type="file" class="form-control required" name="file" id="fileInput" accept=".csv" required>
							<input type="hidden" name="selected_sprint_id" value="">
							<button type="submit" class="btn btn-success btn-flat" id="parseFile"><i class='fa fa-upload'></i> Upload File</button>
						</form>
					</div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                    
                </div>
            
        </div>
    </div>
	<div class="col-md-6">
		<p>Columns required and recommended for import:</p>
		<table class="table table-bordered">
			<tbody>
				<tr>
					<th style='vertical-align:top !important;'>Task Name</th>
					<td>Give a brief description of the task</td>
					<td>Mandatory</td>
				</tr>
				<tr>
					<th style='vertical-align:top !important;'>Task Description</th>
					<td>Describe the task in more details</td>
					<td>Mandatory</td>
				</tr>
				<tr>
					<th style='vertical-align:top !important;'>Section</th>
					<td>Tell us where to implement this functionality. Typically this would be the url needed to access the page where this functionality will be implemented.</td>
					<td>Highly Recommended</td>
				</tr>
				<tr>
					<th style='vertical-align:top !important;'>What's expected from this task</th>
					<td>Tell us what you expect from this task. This can be in terms of display, print, performance or any other</td>
					<td>Recommended</td>
				</tr>
				<tr>
					<th style='vertical-align:top !important;'>What's not included</th>
					<td>To avoid confusion and delay, let us know what is not included in this task. If nothing is specified here, the scope of this task will be limited <u>strictly</u> to the task description.</td>
					<td>Recommended</td>
				</tr>
				<tr>
					<th style='vertical-align:top !important;'>When it's considered done</th>
					<td>Tell us what you expect from this task for it to be completed.</td>
					<td>Mandatory</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<a href="tasks/listing"><div class="btn btn-warning btn-flat"><i class="fa fa-chevron-left"></i> Back</div></a>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalPreview" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id='content'>
				<table id="preview_import" class="table table-bordered">
					<thead>
						<tr>
							<th>#</th>
							<th>Task Name</th>
							<th>Task Description</th>
							<th>Section</th>
							<th>What is Expected</th>
							<th>What is Excluded</th>
							<th>When is Completed</th>
						</tr>
					</thead>
					<tbody></tbody>
					<tfoot>
						<tr>
							<th colspan='6'></th>
						</tr>
					</tfoot>
				</table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-navy" data-dismiss="modal">
                    <div class="fa fa-times"></div> Cancel
                </button>
				<div class="btn btn-info"><i class="fa fa-save"></i> Proceed</div>
            </div>
        </div>
    </div>
</div>
