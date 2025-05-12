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
    <div class="col-md-2 <?php echo (empty($this->input->get("project_id"))) ? 'd-none' : '';?>"">
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
        <div class="btn btn-block btn-outline-info choose-stages">Select Stages <?php echo (!empty($this->input->get("stage")))? "[".count(json_decode($this->input->get("stage")))."]":'[0]';?></div>
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
                <a data-email="<?php echo (isset($tasks[0])) ? $tasks[0]->email : '';?>" class="dropdown-item email"><i class="fa fa-user"></i> To Client</a>
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
                    <a class="dropdown-item due-date-multiple"><i class="fa fa-user"></i> Set Due Date</a>
                    <a class="dropdown-item stage-multiple"><i class="fa fa-truck"></i> Change Stage</a>
                    <a class="dropdown-item move-sprint-multiple"><i class="fa fa-arrow-right"></i> Move Sprint</a>
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
        <?php echo (!empty($this->input->get("stage"))) ? " <b>Stage</b>: " . strtoupper(str_replace("_"," ",$tasks[0]->stage)) : '';?>
    </p>
</div>
<?php endif;?>

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
                            <td class='task-number'><?php echo $task->task_number;?></td>
                            <td class='task-section'><?php echo $task->section; ?></td>
                            <td class='task-name'>
                                <div style='border-bottom:1px dashed #ccc;padding-bottom:3px;margin-bottom:-5px;'><?php echo $task->name; ?></div>
                                <?php echo ( (!empty($task->description)) && ($task->description != $task->name) )? "<br><i class='delius-regular'>" . nl2br($task->description) . "</i>": '';?>
                            </td>
                            <?php if(empty($this->input->get("sprint_id"))):?>
                            <td><?php echo $task->sprint_name; ?></td>
                            <?php endif;?>
                            <?php if(empty($this->input->get("project_id"))):?>
                            <td><?php echo $task->project_name; ?></td>
                            <?php endif;?>
                            <?php if(empty($this->input->get("customer_id"))):?>
                            <td><a style='color:#4c4c4c; text-decoration:none;'
                                    href='tasks/listing?customer_id=<?php echo $task->customer_id;?>'><?php echo "{$task->company_name}"; ?></a>
                            </td>
                            <?php endif;?>
                            <td class='text-center'>
                                <div class="stage-button stage-button-<?php echo $task->stage;?>">
                                    <?php echo ucwords(str_replace("_"," ",$task->stage)); ?>
                                </div>
                            </td>
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
                <table class="table table-bordered">
                <tfoot>
                        <tr>
                            <th colspan='14'>
                                <?php echo "NEW: {$totals['new']}, IN PROGRESS: {$totals['in_progress']}, TESTING: {$totals['testing']}, STAGING: {$totals['staging']}, VALIDATED: {$totals['validated']}, COMPLETED: {$totals['completed']}, ON HOLD: {$totals['on_hold']}. TOTAL DISPLAYED: " . count($tasks);?>
                            </th>
                        </tr>
                        <tr>
                            <th colspan='14'>TOTAL ROWS: <?php echo $total_rows;?></th>
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
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Select Stages</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul id="stages-list" class="list-group">
                    <?php foreach($stages as $stage):?>
                    <li data-stage="<?php echo $stage;?>" class="list-group-item cursor-pointer choose-stage <?php //echo in_array($user->id, $task->assigned_users) ? 'assigned':'';?>">
                        <?php echo "{$stage}";?>
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