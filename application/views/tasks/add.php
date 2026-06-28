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
                            <?php if(!empty($sprints)) foreach($sprints as $sprint):
                                $sprint_past_end = !empty($sprint->end_date) && $sprint->end_date < date('Y-m-d');
                                $sprint_selected = !$sprint_past_end && ($sprint->id == $this->input->get('sprint_id'));
                            ?>
                            <option value="<?php echo $sprint->id;?>"
                                data-end-date="<?php echo !empty($sprint->end_date) ? htmlspecialchars($sprint->end_date) : ''; ?>"
                                <?php echo $sprint_selected ? 'selected' : ''; ?>
                                <?php echo $sprint_past_end ? 'disabled' : ''; ?>>
                                <?php echo htmlspecialchars($sprint->name) . ($sprint_past_end ? ' (ended)' : ''); ?>
                            </option>
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
                                value="<?php echo (!empty($meeting_note)) ? htmlspecialchars('Meeting Notes - ' . $meeting_note->customer_name . ' (' . date('d-M-Y', strtotime($meeting_note->meeting_datetime)) . ')') : ''; ?>"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" id="" rows="5" class="form-control"
                                placeholder='Enter a description of the task'><?php 
                                if(!empty($meeting_note)) {
                                    $description = "Meeting Date: " . $meeting_note->meeting_datetime . "\n";
                                    $description .= "Location: " . ucwords($meeting_note->lieu) . "\n";
                                    $description .= "Attendees: " . $meeting_note->attendees . "\n\n";
                                    $description .= "Meeting Notes:\n" . $meeting_note->notes;
                                    echo htmlspecialchars($description);
                                }
                                ?></textarea>
                            <?php if(!empty($meeting_note)): ?>
                            <small class="form-text text-muted">Pre-filled from meeting notes. You can edit as needed.</small>
                            <?php endif; ?>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Due Date</label>
                                    <input type="date" class="form-control" name="due_date" id="task_due_date" placeholder="" value="<?= date('Y-m-d', strtotime('+5 days')); ?>">
                                    <small class="form-text text-muted" id="task_due_date_hint"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Estimated Hours</label>
                                    <input type="number" step='0.25' min='0' class="form-control" name="estimated_hours" value="1">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Work type</label>
                                    <select class="form-control" name="work_type">
                                        <option value="">Select</option>
                                        <option value="development">Development</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="support">Support</option>
                                        <option value="bugfix">Bugfix</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Source</label>
                                    <select class="form-control" name="source">
                                        <?php foreach (task_source_options() as $source_value => $source_label): ?>
                                        <option value="<?php echo htmlspecialchars($source_value); ?>" <?php echo $source_value === 'admin' ? 'selected' : ''; ?><?php echo task_source_disabled_in_form($source_value) ? ' disabled' : ''; ?>><?php echo htmlspecialchars($source_label); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 billable-only" style="display:none;">
                                <div class="form-group">
                                    <label>Ref (Inv / Quote #)</label>
                                    <input type="text" class="form-control" name="ref" placeholder="e.g. INV-2026-001 or QUO-001" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check mt-4">
                                        <input type="hidden" name="billable" value="0">
                                        <input type="checkbox" class="form-check-input task-billable-cb" name="billable" value="1" id="add_billable">
                                        <label class="form-check-label" for="add_billable">Billable</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 billable-only" style="display:none;">
                                <div class="form-group">
                                    <div class="form-check mt-4">
                                        <input type="hidden" name="settled" value="0">
                                        <input type="checkbox" class="form-check-input" name="settled" value="1" id="add_settled">
                                        <label class="form-check-label" for="add_settled">Settled</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 billable-only" style="display:none;">
                                <div class="form-group">
                                    <label>Date settled</label>
                                    <input type="date" class="form-control" name="settled_on" value="">
                                </div>
                            </div>
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
<script>
(function(){
    var cb = document.querySelector('.task-billable-cb');
    var fields = document.querySelectorAll('.billable-only');
    function toggle() {
        var show = cb && cb.checked;
        fields.forEach(function(el) { el.style.display = show ? 'block' : 'none'; });
    }
    if (cb) {
        cb.addEventListener('change', toggle);
        toggle();
    }
})();
</script>