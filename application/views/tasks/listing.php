<style>
    img.user {
        width: 25px;
        border-radius: 50%;
        border: 1px solid #4c4c4c;
        /* padding:1px; */
        background-color: #fff;
        cursor: crosshair;
        margin-bottom: 3px;
    }

    img.user:hover {
        transform: scale(4);
        -webkit-transition: all 0.5s ease;
        -moz-transition: all 0.5s ease;
        -ms-transition: all 0.5s ease;
        transition: all 0.5s ease;
    }

    #users-list li {
        margin-bottom: 5px;
        border: 1px solid #ccc;
    }

    #users-list li.assigned {
        background-color: rgb(183, 221, 210);
        /* border:1px solid #ccc !important; */
    }

    #users-list li.assigned img {
        border: 4px solid #20c997 !important;
    }

    table.small-text tr th, table.small-text tr td {
        font-size:0.9em;
    }

    .task-ref-cell { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
    .task-ref-cell .copy-task-ref { cursor: pointer; opacity: 0.6; padding: 2px 4px; border: none; background: none; color: inherit; font-size: 14px; }
    .task-ref-cell .copy-task-ref:hover { opacity: 1; }
    .task-ref-cell .copy-task-ref.copied { opacity: 1; color: #28a745; }

    #users-list-remove li.select-user-remove { cursor: pointer; }
    #users-list-remove li.select-user-remove.assigned {
        background-color: rgb(255, 236, 210);
        border: 2px solid #f0ad4e;
    }

</style>
<?php 
// Parse the query string into an array
parse_str($qs, $queryArray);
if(isset($queryArray['task_uuid'])) unset($queryArray['task_uuid']);
$cleanQuery = http_build_query($queryArray);
?>
<div class="row no-print">
    <div class="col-xs-2 mt-4">
        <?php if($perms['add']): ?>
        <a href="<?php echo base_url("tasks/add?customer_id=".$this->input->get('customer_id')."&sprint_id=".$this->input->get("sprint_id")."&project_id=".$this->input->get("project_id")); ?>"><button
                class="btn btn-flat btn-success"><i class="fa fa-plus"></i> Add</button></a>
        <?php endif; ?>
        <?php if($perms['import']): ?>
            <a href="<?php echo base_url("tasks/import/"); ?>"><button class="btn btn-flat btn-info"><i
                    class="fa fa-upload"></i> Import</button></a>
        <?php endif; ?>
    </div>
</div>
<div class="row no-print">
    <div class="col-md-2">
        <label for="">Customer</label>
        <select class="form-control monitor" id="customer_id">
            <option value="">Select Customer</option>
            <?php foreach($customers as $customer): ?>
            <option value="<?php echo $customer->customer_id; ?>"
                <?php echo ($this->input->get("customer_id") == $customer->customer_id) ? "selected" : ""; ?>>
                <?php echo "{$customer->company_name}"; ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2 <?php echo (empty($this->input->get("customer_id"))) ? 'd-none' : '';?>">
        <label for="">Project</label>
        <select class="form-control monitor" id="project_id">
            <option value="">Select Project</option>
            <?php foreach($projects as $project): ?>
            <option data-customer-id="<?php echo $project->customer_id; ?>" value="<?php echo $project->id; ?>"
                <?php echo ($this->input->get("project_id") == $project->id) ? "selected" : ""; ?>
                <?php //echo ($this->input->get('customer_id') == $project->customer_id) ? '' : 'disabled';?>>
                <?php echo "{$project->name}"; ?>
            </option>
            <?php //endif;?>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2 <?php echo (empty($this->input->get("project_id"))) ? 'd-none' : '';?>">
        <label for="">Sprint</label>
        <select class="form-control monitor" id="sprint_id">
            <option value="">Select Sprint</option>
            <?php foreach($sprints as $sprint): ?>
            <option data-project-id="<?php echo $sprint->project_id; ?>" value="<?php echo $sprint->id; ?>"
                <?php echo ($this->input->get("sprint_id") == $sprint->id) ? "selected" : ""; ?>
                <?php //echo ($this->input->get('project_id') == $sprint->project_id) ? '' : 'disabled';?>>
                <?php echo "{$sprint->name}"; ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2 mt-4">
        <input type="hidden" id="stage" name="stage" value='<?php echo (!empty($this->input->get("stage")))?$this->input->get("stage"):'[]';?>'>
        <div class="btn btn-block btn-outline-info choose-stages">Select Stages <?php
            $stageParam = $this->input->get("stage");
            $stageCount = 0;
            if (!empty($stageParam)) {
                $decodedStages = json_decode($stageParam, true);
                $stageCount = is_array($decodedStages) ? count($decodedStages) : 0;
            }
            echo '[' . $stageCount . ']';
        ?></div>
    </div>
    <!-- <div class="col-md-2">
        <label for="">Stage</label>
        <select name="" id="stage" class="form-control monitor">
            <option value="">Select Stage</option>
            <option value="new" <?php //echo ($this->input->get("stage") == "new") ? "selected" : ""; ?>>New</option>
            <option value="in_progress" <?php //echo ($this->input->get("stage") == "in_progress") ? "selected" : ""; ?>>
                In Progress</option>
            <option value="testing" <?php //echo ($this->input->get("stage") == "testing") ? "selected" : ""; ?>>Testing
            </option>
            <option value="staging" <?php //echo ($this->input->get("stage") == "staging") ? "selected" : ""; ?>>Staging
            </option>
            <option value="validated" <?php //echo ($this->input->get("stage") == "validated") ? "selected" : ""; ?>>
                Validated</option>
            <option value="completed" <?php //echo ($this->input->get("stage") == "completed") ? "selected" : ""; ?>>
                Completed</option>
            <option value="on_hold" <?php //echo ($this->input->get("stage") == "on_hold") ? "selected" : ""; ?>>On Hold
            </option>
            <option value="stopped" <?php //echo ($this->input->get("stage") == "stopped") ? "selected" : ""; ?>>Stopped</option>
        </select>
    </div> -->

    <div class="col-md-2">
        <label for="only_with_notes">Notes</label>
        <select name="notes_only" class="form-control monitor" id="notes_only">
            <option value="" <?php //echo ( (empty($this->input->get("notes_only"))) || ($this->input->get("notes_only") == "no") ) ? "selected" : ""; ?>>All</option>
            <option value="without" <?php echo ($this->input->get("notes_only") == "without") ? "selected" : ""; ?>>Only Without</option>
            <option value="with" <?php echo ($this->input->get("notes_only") == "with") ? "selected" : ""; ?>>Only With</option>
        </select>
    </div>
    <div class="col-md-2">
        <label for="work_type">Work type</label>
        <select name="work_type" class="form-control monitor" id="work_type">
            <option value="">All</option>
            <option value="development" <?php echo $this->input->get("work_type") == "development" ? "selected" : ""; ?>>Development</option>
            <option value="maintenance" <?php echo $this->input->get("work_type") == "maintenance" ? "selected" : ""; ?>>Maintenance</option>
            <option value="support" <?php echo $this->input->get("work_type") == "support" ? "selected" : ""; ?>>Support</option>
            <option value="bugfix" <?php echo $this->input->get("work_type") == "bugfix" ? "selected" : ""; ?>>Bugfix</option>
            <option value="other" <?php echo $this->input->get("work_type") == "other" ? "selected" : ""; ?>>Other</option>
        </select>
    </div>
    <div class="col-md-2">
        <label for="billable">Billable</label>
        <select name="billable" class="form-control monitor" id="billable">
            <option value="">All</option>
            <option value="1" <?php echo $this->input->get("billable") === "1" ? "selected" : ""; ?>>Yes</option>
            <option value="0" <?php echo $this->input->get("billable") === "0" ? "selected" : ""; ?>>No</option>
        </select>
    </div>
    <div class="col-md-2">
        <label for="closed_filter">Closed tasks</label>
        <select name="closed_filter" class="form-control monitor" id="closed_filter">
            <option value="open" <?php echo (isset($closed_filter) ? $closed_filter : 'open') === 'open' ? 'selected' : ''; ?>>Open only</option>
            <option value="closed" <?php echo (isset($closed_filter) ? $closed_filter : 'open') === 'closed' ? 'selected' : ''; ?>>Closed only</option>
            <option value="all" <?php echo (isset($closed_filter) ? $closed_filter : 'open') === 'all' ? 'selected' : ''; ?>>Open and closed</option>
        </select>
    </div>
    <input type="password" name="fake-password" autocomplete="new-password" style="position:absolute; top:-1000px; left:-1000px;">
    <div class="col-md-2">
        <label for="search">Search</label>
        <div class="input-group mb-3">
            <input type="text" class="form-control task-listing-search" name="search_text" id="search_text" placeholder="Search in tasks..." aria-label="Search in tasks...e" aria-describedby="basic-addon2" value="<?php echo $this->input->get("search_text");?>">
            <div class="input-group-append search cursor-pointer">
                <span class="input-group-text" id="basic-addon2"><i class="fa fa-binoculars"></i></span>
            </div>
        </div>

        <!-- <label for="search">Search</label> -->
        <!-- <input class='form-control' type="search" name="search" placeholder="Search in tasks..." value=""> -->
    </div>

    


</div>
<div class="row no-print">

    <div class="col-md-2">
        <label for="">Order By</label>
        <select name="" id="order_by" class="form-control monitor">
            <option value="">Select Order By</option>
            <option value="task_number" <?php echo ( (empty($this->input->get("order_by"))) || ($this->input->get("order_by") == "task_number") ) ? "selected" : ""; ?>>Task Number</option>
            <option value="section" <?php echo ($this->input->get("order_by") == "section") ? "selected" : ""; ?>> Section</option>
            <option value="name" <?php echo ($this->input->get("order_by") == "name") ? "selected" : ""; ?>>Task </option>
            <option value="sprint_name" <?php echo ($this->input->get("order_by") == "sprint_name") ? "selected" : ""; ?>>Sprint</option>
            <option value="project_name" <?php echo ($this->input->get("order_by") == "project_name") ? "selected" : ""; ?>>Project</option>
            <option value="company_name" <?php echo ($this->input->get("order_by") == "company_name") ? "selected" : ""; ?>>Customer</option>
            <option value="stage" <?php echo ($this->input->get("order_by") == "stage") ? "selected" : ""; ?>>Stage </option>
            <option value="created_on" <?php echo ($this->input->get("order_by") == "created_on") ? "selected" : ""; ?>>Created Date</option>
            <option value="due_date" <?php echo ($this->input->get("order_by") == "due_date") ? "selected" : ""; ?>> Due Date</option>
        </select>
    </div>
    <div class="col-md-1">
        <label for="">Direction</label>
        <select name="" id="order_dir" class="form-control monitor">
            <option value="asc" <?php echo ($this->input->get("order_dir") == "asc") ? "selected" : ""; ?>>Asc</option>
            <option value="desc" <?php echo ($this->input->get("order_dir") == "desc") ? "selected" : ""; ?>>Desc
            </option>
        </select>
    </div>
    <div class="col-md-1">
        <label for="">Display</label>
        <select name="" id="display" class="form-control monitor">
            <option value="">Select</option>
            <option value="10" <?php echo ($this->input->get("display") == "10") ? "selected" : ""; ?>>10 Rows</option>
            <option value="25" <?php echo ($this->input->get("display") == "25") ? "selected" : ""; ?>>25 Rows</option>
            <option value="50" <?php echo ($this->input->get("display") == "50") ? "selected" : ""; ?>>50 Rows</option>
            <option value="100" <?php echo ($this->input->get("display") == "100") ? "selected" : ""; ?>>100 Rows
            </option>
        </select>
    </div>
    <div class="col-md-2">
        <label for="">Developers</label>
        <select name="assigned_to" id="assigned_to" class="form-control monitor">
            <option value="">Select</option>
            <?php foreach($developers as $d):?>
            <option value="<?php echo $d->id;?>" <?php echo ($this->input->get("assigned_to") == $d->id) ? "selected" : ""; ?>><?php echo "{$d->email} ({$d->name})";?></option>
            <?php endforeach;?>
        </select>
    </div>
    <?php if( ($perms['edit']) || ($perms['delete'])):?>
    <div class="col-md-2 mt-4">
        <div class="btn-group btn-block" role="group">
            <button type="button" class="btn btn-default text-left dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-at"></i> Email List
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item email-developer"><i class="fa fa-user"></i> To Developer</a>
                <?php if ( (isset($tasks[0])) && (!empty($this->input->get("customer_id"))) ) :?>
                <?php
                    $notifyMode = isset($sprint_client_notify['mode']) ? $sprint_client_notify['mode'] : 'none';
                    $clientLabel = 'To Client';
                    $clientEmails = [];
                    if (!empty($client_recipient_emails) && is_array($client_recipient_emails)) {
                        $clientEmails = $client_recipient_emails;
                    } elseif (isset($tasks[0]) && !empty($tasks[0]->email)) {
                        $clientEmails = [$tasks[0]->email];
                    }
                    if ($notifyMode === 'staging_validation') {
                        $clientLabel = 'Inform Client (Ask Validation)';
                    } elseif ($notifyMode === 'completed_update') {
                        $clientLabel = 'Inform Client (Completed)';
                    }
                ?>
                <a data-email="<?php echo htmlspecialchars(implode(', ', $clientEmails));?>"
                   data-notify-mode="<?php echo $notifyMode;?>"
                   class="dropdown-item email"><i class="fa fa-user"></i> <?php echo $clientLabel;?></a>
                <?php endif;?>
            </div>
        </div>
    </div>
    <div class="col-md-1 mt-4">
        <div data-target="task-list" data-skip-columns="[0,-1,-2,-3,-4]" data-include-columns="" id="downloadTableAsCSV" data-filename="tasks" class="btn btn-success export"><i class="fa fa-download"></i></div>
    </div>
    <!-- <div class="col-md-2 mt-4">
        <div class="btn btn-block btn-default email-developer">
            <i class="fa fa-at"></i> Email Developer
        </div>
    </div>
    <div class="col-md-2 mt-4">
        <div data-email="<?php echo (isset($tasks[0])) ? $tasks[0]->email : '';?>"
            title="<?php echo (empty($this->input->get("customer_id"))) ? 'Please select a Customer first' : '';?>"
            class="btn btn-block btn-default email <?php echo ( (!isset($tasks[0])) || (empty($this->input->get("customer_id"))) ) ? 'disabled' : '';?>">
            <i class="fa fa-at"></i> Email Client
        </div>
    </div> -->
    <div class="col-md-1 mt-4">
        <div class="btn btn-default btn-block print"><i class="fa fa-print"></i> Print</div>
        <!-- <div class="btn btn-default"><i class="fa fa-cog"></i></div> -->
    </div>

    <div class="col-md-1 mt-4">
        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
            <div class="btn-group" role="group">
                <button id="withSelectedBtn" type="button" class="btn btn-default disabled dropdown-toggle"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    With Selected
                </button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                    <?php if($perms['edit']):?>
                    <a class="dropdown-item assign-multiple"><i class="fa fa-user"></i> Assign Users</a>
                    <a class="dropdown-item remove-assignees-multiple"><i class="fa fa-user-times"></i> Remove assignees</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item due-date-multiple"><i class="fa fa-calendar"></i> Set Due Date</a>
                    <a class="dropdown-item clear-due-date-multiple"><i class="fa fa-calendar-times-o"></i> Clear Due Date</a>
                    <a class="dropdown-item stage-multiple"><i class="fa fa-truck"></i> Change Stage</a>
                    <a class="dropdown-item move-sprint-multiple"><i class="fa fa-arrow-right"></i> Move Sprint</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item work-type-multiple"><i class="fa fa-tag"></i> Set work type</a>
                    <a class="dropdown-item billable-multiple"><i class="fa fa-money"></i> Set billable</a>
                    <a class="dropdown-item estimated-hours-multiple"><i class="fa fa-clock-o"></i> Set estimated hours</a>
                    <a class="dropdown-item section-multiple"><i class="fa fa-folder-open"></i> Set section</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item reopen-multiple"><i class="fa fa-undo"></i> Reopen</a>
                    <a class="dropdown-item close-multiple"><i class="fa fa-window-close"></i> Close</a>
                    <?php endif;?>
                    <?php if($perms['delete']):?>
                    <a class="dropdown-item delete-multiple"><i class="fa fa-trash"></i> Delete</a>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>
</div>

<?php if( (!empty($tasks)) && (!empty($this->input->get("customer_id"))) ):?>
<div class="row print-only listing">
    <p class='page-title'>
        <?php echo (!empty($this->input->get("customer_id"))) ? "<b>Customer</b>: {$tasks[0]->company_name}" : '';?>
        <?php echo (!empty($this->input->get("project_id"))) ? " <b>Project</b>: {$tasks[0]->project_name}" : '';?>
        <?php echo (!empty($this->input->get("sprint_id"))) ? " <b>Sprint</b>: {$tasks[0]->sprint_name}" : '';?>
        <?php
            $st = json_decode($this->input->get("stage"), true);
            if (is_array($st) && !empty($st)) {
                echo " <b>Stages</b>: ";
                $stStr = "";
                foreach ($st as $stage) {
                    $stStr .= strtoupper(str_replace("_", " ", $stage)) . ', ';
                }
                echo substr($stStr, 0, strlen($stStr) - 2);
            }
        ?>
    </p>
</div>
<?php endif;?>

<div class="row">
    <div class="col-md-12 text-right font-italic text-italic" style='font-size:0.8em;color:#999'>
        <?php echo "Displaying " . count($tasks) . " of " . $total_rows . " tasks";?>
        <?php if (isset($total_estimated_hours)): ?>
            <span class="ml-3"><strong>Total est. hours (filtered):</strong> <?php echo number_format((float) $total_estimated_hours, 2); ?></span>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box">
            <?php if( (isset($tasks)) && (!empty($tasks)) ): ?>
            <div class="box-body table-responsive no-padding">
                <table id="task-list" uuid="tbl1" class="table table-border extended-bottom-margin">
                    <thead>
                        <tr class='text-center' style='text-transform:uppercase;'>
                            <th class='no-print'></th>
                            <th># <?php echo ( (empty($this->input->get("order_by"))) || ($this->input->get("order_by") == "task_number") ) ? "<i class='fa fa-sort'></i>" : '';?></th>
                            <th>Section <?php echo ($this->input->get("order_by") == "section") ? "<i class='fa fa-sort'></i>" : '';?></th>
                            <th>Task <?php echo ($this->input->get("order_by") == "name") ? "<i class='fa fa-sort'></i>" : '';?></th>
                            <?php if(empty($this->input->get("sprint_id"))):?>
                            <th>Sprint <?php echo ($this->input->get("order_by") == "sprint_name") ? "<i class='fa fa-sort'></i>" : '';?></th>
                            <?php endif;?>
                            <?php if(empty($this->input->get("project_id"))):?>
                            <th>Project <?php echo ($this->input->get("order_by") == "project_name") ? "<i class='fa fa-sort'></i>" : '';?></th>
                            <?php endif;?>
                            <?php if(empty($this->input->get("customer_id"))):?>
                            <th>Customer <?php echo ($this->input->get("order_by") == "company_name") ? "<i class='fa fa-sort'></i>" : '';?></th>
                            <?php endif;?>
                            <th>Stage <?php echo ($this->input->get("order_by") == "stage") ? "<i class='fa fa-sort'></i>" : '';?></th>
                            <th class='no-print'>Work type</th>
                            <th class='no-print'>Billable</th>
                            <th class='no-print'>Settled</th>
                            <th class='no-print'>Date settled</th>
                            <th class='no-print'>Ref</th>
                            <th>Created Date <?php echo ($this->input->get("order_by") == "created_on") ? "<i class='fa fa-sort'></i>" : '';?></th>
                            <th>Created By</th>
                            <th class='no-print'>Due Date <?php echo ($this->input->get("order_by") == "due_date") ? "<i class='fa fa-sort'></i>" : '';?></th>
                            <th class='no-print'>Hours</th>
                            <th class='no-print'>Developers</th>
                            <th class='no-print'><i class="fa fa-comments"></i></th>
                            <th class='no-print'>Actions</th>
                            <?php if( ($perms['edit']) || ($perms['delete'])):?>
                            <th class='no-print'>
                                <input type="checkbox" name="select_all_tasks" class='select_all_tasks'>
                            </th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $totals = [
                            'new'           =>  0,
                            'in_progress'   =>  0,
                            'testing'       =>  0,
                            'staging'       =>  0,
                            'validated'     =>  0,
                            'completed'     =>  0,
                            'on_hold'       =>  0
                        ];?>
                        <?php foreach($tasks as $i => $task): ?>
                        <?php $totals[$task->stage]++; $page=$this->uri->segment(3,1); $display=!empty($this->input->get("display"))?$this->input->get("display"):8;?>
                        <tr data-id="<?php echo $task->id;?>">
                            <td class='no-print' style='width:20px;font-size:14px;vertical-align:middle;color:#ccc;text-align:center;'>
                                <?php echo ($i+1)+( ($page-1)*$display );?>
                            </td>
                            <td class='task-number task-ref-cell'>
                                <?php
                                $display_ref = (isset($task->task_ref) && $task->task_ref !== '') ? $task->task_ref : (isset($task->task_number) ? $task->task_number : '');
                                if ($display_ref === '' && function_exists('task_ref')) {
                                    $display_ref = task_ref(isset($task->project_code) ? $task->project_code : null, isset($task->sprint_code) ? $task->sprint_code : null, isset($task->task_number) ? $task->task_number : '');
                                }
                                $display_ref = $display_ref !== '' ? $display_ref : (isset($task->task_number) ? $task->task_number : '');
                                ?>
                                <span class="task-ref-text"><?php echo htmlspecialchars($display_ref); ?></span>
                                <button type="button" class="copy-task-ref" data-ref="<?php echo htmlspecialchars($display_ref); ?>" title="Copy reference"><i class="fa fa-copy"></i></button>
                            </td>
                            <td class='task-section'><?php echo $task->section; ?></td>
                            <td class='task-name'>
                                <?php if (isset($task->closed) && (string) $task->closed === '1'): ?>
                                <span class="badge badge-secondary font-weight-normal mr-1">Closed</span>
                                <?php endif; ?>
                                <div style='border-bottom:1px dashed #ccc;padding-bottom:3px;margin-bottom:-5px;'><?php echo $task->name; ?></div>
                                <?php echo ( (!empty($task->description)) && ($task->description != $task->name) )? "<br><i class='delius-regular'><span>" . nl2br($task->description) . "</span></i>": '<span></span>';?>
                            </td>
                            <?php if(empty($this->input->get("sprint_id"))):?>
                            <td><?php echo $task->sprint_name; ?></td>
                            <?php endif;?>
                            <?php if(empty($this->input->get("project_id"))):?>
                            <td><?php echo $task->project_name; ?></td>
                            <?php endif;?>
                            <?php if(empty($this->input->get("customer_id"))):?>
                            <td>
                                <?php if (isset($task->customer_id) && $task->customer_id !== null && $task->customer_id !== ''): ?>
                                <a style='color:#4c4c4c; text-decoration:none;'
                                    href='tasks/listing?customer_id=<?php echo $task->customer_id;?>'><?php echo isset($task->company_name) ? $task->company_name : '—'; ?></a>
                                <?php else: ?>
                                <?php echo isset($task->company_name) && $task->company_name !== '' ? $task->company_name : '—'; ?>
                                <?php endif; ?>
                            </td>
                            <?php endif;?>
                            <td class='stage text-center'>
                                <div class="stage-button stage-button-<?php echo $task->stage;?>" style="display:inline-block;">
                                    <?php echo ucwords(str_replace("_"," ",$task->stage)); ?>
                                </div>
                                <?php if($task->stage == 'completed' && !empty($task->completed_date)): ?>
                                <i class="fa fa-info-circle text-info cursor-pointer completed-date-info" 
                                   style="margin-left: 5px; font-size: 14px;" 
                                   data-toggle="tooltip" 
                                   data-placement="top" 
                                   title="Completed: <?php echo date('Y-m-d H:i', strtotime($task->completed_date)); ?>"
                                   data-completed-date="<?php echo date('Y-m-d H:i', strtotime($task->completed_date)); ?>"></i>
                                <?php endif; ?>
                            </td>
                            <td class='no-print'><?php echo !empty($task->work_type) ? ucfirst($task->work_type) : '—';?></td>
                            <td class='no-print text-center'><?php
                                $__bill = isset($task->billable) ? (int) $task->billable : null;
                                if ($__bill === 1) {
                                    echo '<i class="fa fa-check text-success" title="Billable" aria-label="Billable"></i>';
                                } else {
                                    $__bill_title = ($__bill === 0) ? 'Not billable' : 'Not specified';
                                    echo '<i class="fa fa-times text-danger" title="' . htmlspecialchars($__bill_title) . '" aria-label="' . htmlspecialchars($__bill_title) . '"></i>';
                                }
                            ?></td>
                            <td class='no-print'><?php echo isset($task->settled) && $task->settled == 1 ? 'Yes' : (isset($task->settled) && $task->settled == 0 ? 'No' : '—');?></td>
                            <td class='no-print'><?php echo isset($task->settled_on) && $task->settled_on ? date('Y-m-d', strtotime($task->settled_on)) : '—';?></td>
                            <td class='no-print'><?php echo isset($task->ref) && $task->ref !== '' ? htmlspecialchars($task->ref) : '—';?></td>
                            <td><?php echo !empty($task->created_on) ? date('Y-m-d', strtotime($task->created_on)) : '';?></td>
                            <td><?php echo !empty($task->created_by_name) ? $task->created_by_name : '';?></td>
                            <td class='no-print text-center <?php echo ( (!empty($task->due_date)) && ( strtotime($task->due_date) <= time()) ) ? 'red text-bold' : ''?>'>
                                <?php echo (!empty($task->due_date)) ? date_format(date_create($task->due_date),'Y-m-d') : '';?>
                            </td>
                            <td class='text-center no-print'><?php echo $task->estimated_hours;?></td>
                            <td class='no-print'>
                                <?php foreach($task->users as $user):?>
                                <img title="<?php echo $user->name;?>" class='user'
                                    src="uploads/users/<?php echo $user->photo;?>" alt="">
                                <?php endforeach;?>
                            </td>

                            <td class='no-print'><?php echo $task->notes;?><br><i class="fa fa-eye view-notes cursor-pointer"></i></td>

                            <td class='no-print' style='width:150px;'>
                                <?php if($perms['view']): ?>
                                <a
                                    href="<?php echo base_url('tasks/view?task_uuid=' . $task->uuid."&".$cleanQuery); ?>">
                                    <div class="btn btn-flat btn-default"><i class='fas fa-eye'></i><span
                                            class='ButtonLabel'></span></div>
                                </a>
                                <?php endif; ?>
                                <?php if($perms['edit']): ?>
                                <a
                                    href="<?php echo base_url('tasks/edit?task_uuid=' . $task->uuid."&".$cleanQuery); ?>">
                                    <div class="btn btn-flat btn-primary"><i class='fas fa-edit'></i><span
                                            class='ButtonLabel'></span></div>
                                </a>
                                <?php endif; ?>
                                <?php if($perms['delete']): ?>
                                <button class="btn btn-danger close-task">&#10006</button>
                                <button data-url="<?php echo base_url("tasks/delete"); ?>"
                                    data-uuid="<?php echo $task->uuid;?>" class="deleteAjax btn btn-flat btn-danger"><i
                                        class='fa fa-trash'></i><span class='ButtonLabel'></span></button>
                                <?php endif; ?>

                            </td>
                            <?php if( ($perms['edit']) || ($perms['delete'])):?>
                            <td class='no-print'>
                                <input type="checkbox" name="select_task" class='select_task'>
                            </td>
                            <?php endif;?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                   

                </table>
                <table class="table table-bordered small-text">
                    <tfoot>
                        <tr class='text-center'>
                            <th>NEW</th>
                            <th>IN PROGRESS</th>
                            <th>TESTING</th>
                            <th>STAGING</th>
                            <th>VALIDATED</th>
                            <th>COMPLETED</th>
                            <th>ON HOLD</th>
                            <th>TOTAL</th>
                        </tr>
                        <tr class='text-center'>
                            <td><?php echo $totals['new'];?></td>
                            <td><?php echo $totals['in_progress'];?></td>
                            <td><?php echo $totals['testing'];?></td>
                            <td><?php echo $totals['staging'];?></td>
                            <td><?php echo $totals['validated'];?></td>
                            <td><?php echo $totals['completed'];?></td>
                            <td><?php echo $totals['on_hold'];?></td>
                            <td><?php echo count($tasks);?></td>
                        </tr>

                    </tfoot>
                </table>
            </div>
            <?php else: ?>
            <p>No records</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row mb-5 no-print">
    <div class="col-md-4"></div>
    <div class="col-md-4 text-center">
        <img class='img-thumbnail' src="assets/images/stageColors.png" alt="">
    </div>
</div>

<?php if( (isset($pagination)) && (!empty($pagination)) ) echo $pagination;?>

<!-- Modal -->
<div class="modal fade" id="modalAssignUsers" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Assign Users To Selected Tasks</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>By Assigning in bulk, any previously assigned users will be removed from the selected tasks.</p>
                <ul id="users-list" class="list-group">
                    <?php foreach($users as $user):?>
                    <?php if($user->user_type != 'developer') continue;?>
                    <li data-id="<?php echo $user->id;?>"
                        class="list-group-item select-user <?php //echo in_array($user->id, $task->assigned_users) ? 'assigned':'';?>">
                        <img style='width:50px;padding:2px;background-color:#eee;border:1px solid #ccc;border-radius: 50%;'
                            src="uploads/users/<?php echo $user->photo;?>" alt="">
                        <?php echo "{$user->email} ({$user->name})";?>
                    </li>
                    <?php endforeach;?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                    Cancel</button>
                <button type="button" class="btn btn-primary proceed"><i class="fa fa-check"></i> Assign</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalDeleteConfirmation" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Verify the list of tasks selected for deletion before
                    proceeding</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h1>TEST</h1>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                    Cancel</button>
                <button type="button" class="btn btn-danger proceedWithDeletion"><i class="fa fa-trash"></i>
                    Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalSetStage" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Assign Users To Selected Tasks</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- <p>By Assigning in bulk, any previously assigned users will be removed from the selected tasks.</p> -->
                <ul id="users-list" class="list-group">
                    <li data-stage='new' class="list-group-item select-stage">New</li>
                    <li data-stage='in_progress' class="list-group-item select-stage">In Progress</li>
                    <li data-stage='testing' class="list-group-item select-stage">Testing</li>
                    <li data-stage='staging' class="list-group-item select-stage">Staging</li>
                    <li data-stage='validated' class="list-group-item select-stage">Validated</li>
                    <li data-stage='completed' class="list-group-item select-stage">Completed</li>
                    <li data-stage='on_hold' class="list-group-item select-stage">On Hold</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                    Cancel</button>
                <button type="button" class="btn btn-primary changeStage"><i class="fa fa-check"></i> Proceed</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalChangeSprint" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Move Selected Tasks to Sprint</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- <p>By Assigning in bulk, any previously assigned users will be removed from the selected tasks.</p> -->
                <ul id="users-list" class="list-group">
                    <?php foreach($sprints as $sprint):?>
                    <li data-sprint='<?php echo $sprint->id;?>' class="list-group-item select-sprint"><?php echo "{$sprint->customer_name} / {$sprint->project_name} / {$sprint->name}";?></li>
                    <?php endforeach;?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                    Cancel</button>
                <button type="button" class="btn btn-primary changeSprint"><i class="fa fa-check"></i> Proceed</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalDueDate" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Set Due Date</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>By setting <b>Due Date</b> in bulk, any previously set <b>Due Date</b> <span class='red text-bold'>will be overwritten</span>.</p>
                <div class="form-group">
                    <label for=""></label>
                    <input name='due_date' type="date" min="<?php echo date("Y-m-d");?>" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                    Cancel</button>
                <button type="button" class="btn btn-primary setDueDate"><i class="fa fa-check"></i> Proceed</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRemoveAssignees" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Remove assignees from selected tasks</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Remove developers from the <b>selected tasks only</b>. Assignments on other tasks are unchanged.</p>
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="remove_all_assignees" name="remove_all_assignees" value="1">
                    <label class="form-check-label" for="remove_all_assignees">Remove <b>all</b> assignees from these tasks</label>
                </div>
                <p class="small mb-2">Or pick specific users to remove (ignored if “remove all” is checked):</p>
                <ul id="users-list-remove" class="list-group">
                    <?php foreach($users as $user):?>
                    <?php if($user->user_type != 'developer') continue;?>
                    <li data-id="<?php echo $user->id;?>" class="list-group-item select-user-remove cursor-pointer">
                        <img style='width:50px;padding:2px;background-color:#eee;border:1px solid #ccc;border-radius: 50%;'
                            src="uploads/users/<?php echo $user->photo;?>" alt="">
                        <?php echo "{$user->email} ({$user->name})";?>
                    </li>
                    <?php endforeach;?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                <button type="button" class="btn btn-warning proceedRemoveAssignees"><i class="fa fa-user-times"></i> Remove</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalBulkWorkType" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set work type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Work type</label>
                    <select class="form-control" id="bulk_work_type" name="bulk_work_type">
                        <option value="">Clear (not set)</option>
                        <option value="development">Development</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="support">Support</option>
                        <option value="bugfix">Bugfix</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                <button type="button" class="btn btn-primary applyBulkWorkType"><i class="fa fa-check"></i> Apply</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalBulkBillable" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set billable</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Billable</label>
                    <select class="form-control" id="bulk_billable_mode" name="bulk_billable_mode">
                        <option value="1">Billable</option>
                        <option value="0">Not billable</option>
                        <option value="unset">Clear (not set)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                <button type="button" class="btn btn-primary applyBulkBillable"><i class="fa fa-check"></i> Apply</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalBulkEstimatedHours" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set estimated hours</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Mode</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="bulk_est_hours_mode" id="bulk_est_mode_set" value="set" checked>
                        <label class="form-check-label" for="bulk_est_mode_set">Set to a value (leave hours empty to clear)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="bulk_est_hours_mode" id="bulk_est_mode_add" value="add">
                        <label class="form-check-label" for="bulk_est_mode_add">Add to current estimate</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="bulk_est_hours_value">Hours</label>
                    <input type="number" step="0.25" min="0" class="form-control" id="bulk_est_hours_value" name="bulk_est_hours_value" placeholder="">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                <button type="button" class="btn btn-primary applyBulkEstimatedHours"><i class="fa fa-check"></i> Apply</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalBulkSection" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set section</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">The same section text is applied to every selected task. Leave empty to clear the section.</p>
                <div class="form-group">
                    <label for="bulk_section_value">Section</label>
                    <input type="text" class="form-control" id="bulk_section_value" name="bulk_section_value" maxlength="255" placeholder="">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                <button type="button" class="btn btn-primary applyBulkSection"><i class="fa fa-check"></i> Apply</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalClearDueDateConfirmation" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Clear due date for selected tasks</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                <button type="button" class="btn btn-warning proceedWithClearDueDate"><i class="fa fa-calendar-times-o"></i> Clear due dates</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalReopenConfirmation" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reopen selected tasks</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted">This clears the closed flag on the tasks you select. Use this if they were closed by mistake or need to appear as open again.</p>
                <div class="bulk-reopen-task-list"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                <button type="button" class="btn btn-primary proceedWithReopen"><i class="fa fa-undo"></i> Reopen</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Notes-->
<div class="modal fade" id="modalNotes" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNotesTitle">Notes</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style='width:25px; font-size:10px; color: #ccc;'>#</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                    Close</button>
                <!-- <button type="button" class="btn btn-primary setDueDate"><i class="fa fa-check"></i> Proceed</button> -->
            </div>
        </div>
    </div>
</div>
<style>
    #stages-list li span{
        display: none;
    }
    #stages-list li.selected span{
        display: block;
    }
</style>
<!-- Modal -->
<div class="modal fade" id="modalChooseStages" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Select Stages</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul id="stages-list" class="list-group">
                    <li data-stage='' class='list-group-item text-center'>
                        <div class="btn btn-default select-all">Select / Deselect All</div>
                    </li>
                    <?php foreach($stages as $stage):?>
                    <li data-stage="<?php echo $stage;?>" class="list-group-item cursor-pointer choose-stage <?php //echo in_array($user->id, $task->assigned_users) ? 'assigned':'';?>">
                        <?php echo str_replace("_"," ",strtoupper($stage));?>
                        <span class='float-right'><i class="fa fa-check-square green"></i></span>
                    </li>
                    <?php endforeach;?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                    Cancel</button>
                <button type="button" class="btn btn-success applyChosenStages"><i class="fa fa-check"></i> Proceed</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalCloseConfirmation" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Please verify the tasks before
                    proceeding</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                    Cancel</button>
                <button type="button" class="btn btn-info proceedWithClosing"><i class="fa fa-window-close"></i>
                    Close Tasks</button>
            </div>
        </div>
    </div>
</div>