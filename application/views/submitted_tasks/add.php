<style>
#users-list li.assigned {
    background-color: rgb(183, 221, 210);
    /* border:1px solid #ccc !important; */
}

#users-list li.assigned img {
    border: 4px solid #20c997 !important;
}
</style>
<form id="add_user" action="tasks/save" method="post" enctype="multipart/form-data">
    <input type="hidden" name="qs" value="<?php echo $_SERVER["QUERY_STRING"];?>">
    <input type="hidden" name="_customer_id" value="<?php echo $this->input->get("customer_id");?>">
    <input type="hidden" name="_project_id" value="<?php echo $this->input->get("project_id");?>">
    <input type="hidden" name="_sprint_id" value="<?php echo $this->input->get("sprint_id");?>">
    <input type="hidden" name="userIds" value="[]">
    <input type="hidden" name="deleted_images" value="[]">
    <div class="row">
        <div class="col-md-12">
            <div class="message" style='padding-top:20px; text-align:center; font-weight:bold; color:#ff0000;'></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-yellow">
                    <h3 class="card-title">Task</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="customer_id">Customer</label>
                        <select class="form-control required" name="customer_id" id="customer_id" required>
                            <option value="">Select</option>
                            <?php foreach($customers as $c):?>
                            <option value="<?php echo $c->customer_id;?>"
                                <?php echo ($c->customer_id == $this->input->get('customer_id')) ? 'selected' : '';?>>
                                <?php echo "{$c->company_name}";?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="project_id">Projects</label>
                        <select name="project_id" id="project_id" class="form-control" required>
                            <?php if(!empty($projects)) foreach($projects as $project):?>
                            <option value="<?php echo $project->id;?>"
                                <?php echo ($project->id == $this->input->get('project_id')) ? 'selected' : '';?>>
                                <?php echo $project->name;?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sprint_id">Sprint</label>
                        <select name="sprint_id" id="sprint_id" class="form-control" required>
                            <?php if(!empty($sprints)) foreach($sprints as $sprint):?>
                            <option value="<?php echo $sprint->id;?>"
                                <?php echo ($sprint->id == $this->input->get('sprint_id')) ? 'selected' : '';?>>
                                <?php echo $sprint->name;?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <div
                        class="ready <?php echo ( ((isset($projects)) && (count($projects) == 1)) && ((isset($sprints)) && (count($sprints) == 1)) ) ? '' : 'd-none';?>">
                        <div class="form-group">
                            <label>Task Number</label>
                            <input type="text" class="form-control" name="task_number" placeholder="e.g. #01.14"
                                value="" required>
                        </div>
                        <div class="form-group">
                            <label>Section</label>
                            <input type="text" class="form-control" name="section" placeholder="e.g. Reports" value=""
                                required autofocus>
                        </div>
                        <div class="form-group">
                            <label>Task Name</label>
                            <input type="text" class="form-control required" name="name" placeholder="Enter Task Name"
                                value="" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" id="" rows="5" class="form-control"
                                placeholder='Enter a description of the task'></textarea>
                        </div>
                        <div class="form-group">
                            <label>Due Date</label>
                            <input type="date" class="form-control" name="due_date" placeholder="" value="">
                        </div>
                        <div class="form-group">
                            <label>Estimated Hours</label>
                            <input type="number" step='0.25' min='0' class="form-control" name="estimated_hours"
                                value="1">
                        </div>

                        <!-- </div> -->
                        <div class="form-group d-none">
                            <label>Stage</label>
                            <select class="form-control required" name="stage" required readonly>
                                <option value="" disabled>Select</option>
                                <option value="new" selected>New</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="on_hold">On Hold</option>
                                <option value="stopped">Stopped</option>

                            </select>
                        </div>
                        <input type="hidden" name="progress" value="0">

                        <div class="form-group">
                            <label for="">Upload a file</label>
                            <input type="file" class="form-control" name="file1" id="file" accept=".jpg, .png, .jpeg"
                                placeholder="Upload File">
                            <input type="file" class="form-control" name="file2" id="file" accept=".jpg, .png, .jpeg"
                                placeholder="Upload File">
                            <input type="file" class="form-control" name="file3" id="file" accept=".jpg, .png, .jpeg"
                                placeholder="Upload File">
                            <input type="file" class="form-control" name="file4" id="file" accept=".jpg, .png, .jpeg"
                                placeholder="Upload File">
                            <input type="file" class="form-control" name="file5" id="file" accept=".jpg, .png, .jpeg"
                                placeholder="Upload File">
                            <small class="form-text text-muted">Upload a file related to the task. (.jpg, .png, .jpeg,
                                .pdf)</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="col-md-12 ready <?php echo ( ((isset($projects)) && (count($projects) == 1)) && ((isset($sprints)) && (count($sprints) == 1)) ) ? '' : 'd-none';?>"">
                        <button type="submit" class="btn btn-success" id="save"><i class='fa fa-save'></i> Save</button>
                        <input style='width:20px; height:20px;' type="checkbox" name='add_more' id='add_more' value='1'
                            <?php echo ($this->input->get('add_more') == 1) ? 'checked' : '';?>>
                        <label for="add_more"> <i class="fa fa-plus"></i> Add More</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-danger">
                    <h3 class="card-title">Scope Definition</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="">What's expected from this task</label>
                        <textarea name="scope_client_expectation" id="" rows='5' class="form-control" placeholder="Tell us what you expect from this task. This can be in terms of display, print, performance or any other"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">What's not included</label>
                        <textarea name="scope_not_included" id="" rows='5' class="form-control" placeholder="To avoid confusion and delay, let us know what is not included in this task. If nothing is specified here, the scope of this task will be limited strictly to the task description."></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">When it's considered done</label>
                        <textarea name="scope_when_done" id="" rows='5' class="form-control" placeholder="Tell us what you expect from this task for it to be completed."></textarea>
                    </div>
                </div>
                <div class="card-footer">

                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">Users</h3>
                </div>
                <div class="card-body">
                    <ul id="users-list" class="list-group">
                        <?php foreach($developers as $user):?>
                        <?php if($user->user_type != 'developer') continue;?>
                        <li data-id="<?php echo $user->id;?>" class="list-group-item cursor-pointer add-user">
                            <img style='width:50px;padding:2px;background-color:#eee;border:1px solid #ccc;border-radius: 50%;'
                                src="uploads/users/<?php echo $user->photo;?>" alt="">
                            <?php echo "{$user->email} ({$user->name})";?>
                        </li>
                        <?php endforeach;?>
                    </ul>
                </div>
                <div class="card-footer">

                </div>
            </div>
        </div>
    </div>
</form>