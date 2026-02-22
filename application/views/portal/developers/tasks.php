<style>
    #modalChooseStages .list-group-item.selected {
        background-color:transparent !important;
        color: #4c4c4c !important;
    }
    .fa {
        width:25px;
        height:25px;
        font-size:24px;
        color:green;
    }
    li span {
        display: none;
    }
    li.selected span {
        display: inline;
    }
    .task-ref-cell { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
    .task-ref-cell .copy-task-ref { cursor: pointer; opacity: 0.6; padding: 2px 4px; border: none; background: none; color: inherit; font-size: 14px; }
    .task-ref-cell .copy-task-ref:hover { opacity: 1; }
    .task-ref-cell .copy-task-ref.copied { opacity: 1; color: #28a745; }

</style>

<form id="tasks" method="get" action="./portal/developers/tasks">
            <div class="row">
                <div class="col-md-2">
                    <label for="">Customer <span style='color:#36b936;' class='<?php echo (empty(($this->input->get("customer_id")))) ? 'd-none' :'';?>'><bi class="bi-check-circle-fill"></bi></span></label>
                    <select class="form-control autosubmit" name="customer_id" id="customer_id">
                        <option value="">Select Customer</option>
                        <?php foreach($myCustomers as $customer): ?>
                        <option value="<?php echo $customer->customer_id; ?>"
                            <?php echo ($this->input->get("customer_id") == $customer->customer_id) ? "selected" : ""; ?>>
                            <?php echo "{$customer->company_name}"; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="">Project <span style='color:#36b936;' class='<?php echo (empty(($this->input->get("project_id")))) ? 'd-none' :'';?>'><bi class="bi-check-circle-fill"></bi></span></label>
                    <select class="form-control autosubmit" name="project_id" id="project_id">
                        <option value="">Select Project</option>
                        <?php foreach($myProjects as $project): ?>
                        <?php if($this->input->get('customer_id') != $project->customer_id) continue;?>
                        <option data-customer-id="<?php echo $project->customer_id; ?>"
                            value="<?php echo $project->id; ?>"
                            <?php echo ($this->input->get("project_id") == $project->id) ? "selected" : ""; ?>
                            <?php echo ($this->input->get('customer_id') == $project->customer_id) ? '' : 'disabled';?>>
                            <?php echo "{$project->name} - {$project->company_name}"; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="">Sprint <span style='color:#36b936;' class='<?php echo (empty(($this->input->get("sprint_id")))) ? 'd-none' :'';?>'><bi class="bi-check-circle-fill"></bi></span></label>
                    <select class="form-control autosubmit" name="sprint_id" id="sprint_id">
                        <option value="">Select Sprint</option>
                        <?php foreach($mySprints as $sprint): ?>
                        <?php if ($this->input->get('project_id') !== $sprint->project_id) continue;?>
                        <option data-project-id="<?php echo $sprint->project_id; ?>"
                            value="<?php echo $sprint->id; ?>"
                            <?php echo ($this->input->get("sprint_id") == $sprint->id) ? "selected" : ""; ?>
                            <?php echo ($this->input->get('project_id') == $sprint->project_id) ? '' : 'disabled';?>>
                            <?php echo "{$sprint->name} - {$sprint->project_name} - {$sprint->company_name}"; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="">Stages</label><br>
                    <input type="hidden" id="stage" name="stage" value='<?php echo (!empty($this->input->get("stage")))?$this->input->get("stage"):'[]';?>'>
                    <div style='border:1px solid #ccc;border-radius:5px !important;' class="btn w-100 choose-stages">Select Stages <?php echo (!empty($this->input->get("stage")))? "[".count(json_decode($this->input->get("stage")))."]":'[0]';?></div>
                </div>

                <!-- <div class="col-md-2">
                    <label for="">Stage <span style='color:#36b936;' class='<?php echo (empty(($this->input->get("stage")))) ? 'd-none' :'';?>'><bi class="bi-check-circle-fill"></bi></span></label>
                    <select name="stage" id="stage" class="form-control autosubmit">
                        <option value="">Select Stage</option>
                        <option value="new" <?php echo ($this->input->get("stage") == "new") ? "selected" : ""; ?>>New
                        </option>
                        <option value="in_progress"
                            <?php echo ($this->input->get("stage") == "in_progress") ? "selected" : ""; ?>>In Progress
                        </option>
                        <option value="testing"
                            <?php echo ($this->input->get("stage") == "testing") ? "selected" : ""; ?>>
                            Testing</option>
                        <option value="staging"
                            <?php echo ($this->input->get("stage") == "staging") ? "selected" : ""; ?>>
                            Staging</option>
                        <option value="validated"
                            <?php echo ($this->input->get("stage") == "validated") ? "selected" : ""; ?>>Validated
                        </option>
                        <option value="completed"
                            <?php echo ($this->input->get("stage") == "completed") ? "selected" : ""; ?>>Completed
                        </option>
                        <option value="on_hold"
                            <?php echo ($this->input->get("stage") == "on_hold") ? "selected" : ""; ?>>
                            On Hold</option>
                        <option value="stopped" <?php //echo ($this->input->get("stage") == "stopped") ? "selected" : ""; ?>>Stopped</option>
                    </select>
                </div> -->

                <div class="col-md-2">
                    <label for="">Order By <span style='color:#36b936;' class='<?php echo (empty(($this->input->get("order_by")))) ? 'd-none' :'';?>'><bi class="bi-check-circle-fill"></bi></span></label>
                    <select name="order_by" id="order_by" class="form-control autosubmit">
                        <option value="">Select Order By</option>
                        <option value="task_number"
                            <?php echo ( (empty($this->input->get("order_by"))) || ($this->input->get("order_by") == "task_number") ) ? "selected" : ""; ?>>Task
                            Number
                        </option>
                        <option value="section"
                            <?php echo ($this->input->get("order_by") == "section") ? "selected" : ""; ?>>Section
                        </option>
                        <option value="task_name"
                            <?php echo ($this->input->get("order_by") == "task_name") ? "selected" : ""; ?>>
                            Task Name
                        </option>
                        <option value="sprint_name"
                            <?php echo ($this->input->get("order_by") == "sprint_name") ? "selected" : ""; ?>>Sprint
                        </option>
                        <option value="project_name"
                            <?php echo ($this->input->get("order_by") == "project_name") ? "selected" : ""; ?>>Project
                        </option>
                        <option value="company_name"
                            <?php echo ($this->input->get("order_by") == "company_name") ? "selected" : ""; ?>>Customer
                        </option>
                        <option value="due_date"
                            <?php echo ($this->input->get("order_by") == "due_date") ? "selected" : ""; ?>>
                            Due Date</option>
                        <option value="estimated_hours"
                            <?php echo ($this->input->get("order_by") == "estimated_hours") ? "selected" : ""; ?>>
                            Estimated Hours</option>
                        <option value="stage"
                            <?php echo ($this->input->get("order_by") == "stage") ? "selected" : ""; ?>>
                            Stage</option>
                        <!-- <option value="progress"
                            <?php //echo ($this->input->get("order_by") == "progress") ? "selected" : ""; ?>>Progress
                        </option> -->
                    </select>
                </div>

                <div class="col-md-1">
                    <label for="">Direction <span style='color:#36b936;' class='<?php echo (empty(($this->input->get("order_dir")))) ? 'd-none' :'';?>'><bi class="bi-check-circle-fill"></bi></span></label>
                    <select name="order_dir" id="order_dir" class="form-control autosubmit">
                        <option value="asc" <?php echo ($this->input->get("order_dir") == "asc") ? "selected" : ""; ?>>
                            Asc
                        </option>
                        <option value="desc"
                            <?php echo ($this->input->get("order_dir") == "desc") ? "selected" : ""; ?>>
                            Desc</option>
                    </select>
                </div>

            </div>

            <div class="row mb-4">

                <div class="col-md-1 d-none">
                    <label for="">Display</label>
                    <select name="display" id="display" class="form-control">
                        <option value="">Select</option>
                        <option value="10" <?php echo ($this->input->get("display") == "10") ? "selected" : ""; ?>>10
                            Rows
                        </option>
                        <option value="25" <?php echo ($this->input->get("display") == "25") ? "selected" : ""; ?>>25
                            Rows
                        </option>
                        <option value="50" <?php echo ($this->input->get("display") == "50") ? "selected" : ""; ?>>50
                            Rows
                        </option>
                        <option value="100" <?php echo ($this->input->get("display") == "100") ? "selected" : ""; ?>>100
                            Rows</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="only_with_notes">Notes</label>
                    <select name="notes_only" class="form-control autosubmit" id="notes_only">
                        <option value="" <?php //echo ( (empty($this->input->get("notes_only"))) || ($this->input->get("notes_only") == "no") ) ? "selected" : ""; ?>>All</option>
                        <option value="without" <?php echo ($this->input->get("notes_only") == "without") ? "selected" : ""; ?>>Only Without</option>
                        <option value="with" <?php echo ($this->input->get("notes_only") == "with") ? "selected" : ""; ?>>Only With</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="due_in_days">Due In (Days) <span style='color:#36b936;' class='<?php echo (empty(($this->input->get("due_in_days")))) ? 'd-none' :'';?>'><bi class="bi-check-circle-fill"></bi></span></label>
                    <input type="number" name="due_in_days" id="due_in_days" class="form-control autosubmit" min="0" placeholder="e.g. 7" value="<?php echo $this->input->get("due_in_days"); ?>">
                </div>

                <div class="col-md-2 mt-4">
                    <button class='btn btn-block btn-outline-primary' style='width:100%' type='submit'>
                        <div class="bg-icon bg-send"></div> Submit
                    </button>
                </div>

                <!-- <div class="col-md-8 mb-5"></div> -->

                <div class="col-md-2 mt-4">
                    <div class="btn btn-block btn-outline-warning" style='width:100%' id="reset">
                        <div class="bg-icon bg-refresh"></div>Clear
                    </div>
                </div>
            </div>
        </form>

    </div>

    <div class="row table-responsive">
        <div class="col-md-12">
            <table id="task_list" class="table table-bordered">
                <thead>
                    <tr>
                        <th># <img src="assets/images/sort.png" alt="" class='<?php echo ( (empty($this->input->get('order_by'))) || ($this->input->get('order_by') == 'task_number') ) ? '' :'d-none';?>'></th>
                        <th>SECTION <img src="assets/images/sort.png" alt="" class='<?php echo ($this->input->get('order_by') == 'section') ? '' :'d-none';?>'></th>
                        <th>TASK <img src="assets/images/sort.png" alt="" class='<?php echo ($this->input->get('order_by') == 'task_name') ? '' :'d-none';?>'></th>
                        <th>SPRINT <img src="assets/images/sort.png" alt="" class='<?php echo ($this->input->get('order_by') == 'sprint_name') ? '' :'d-none';?>'></th>
                        <th>PROJECT <img src="assets/images/sort.png" alt="" class='<?php echo ($this->input->get('order_by') == 'project_name') ? '' :'d-none';?>'></th>
                        <th>CUSTOMER <img src="assets/images/sort.png" alt="" class='<?php echo ($this->input->get('order_by') == 'company_name') ? '' :'d-none';?>'></th>
                        <th>CREATED DATE</th>
                        <th>CREATED BY</th>
                        <th>DUE DATE <img src="assets/images/sort.png" alt="" class='<?php echo ($this->input->get('order_by') == 'due_date') ? '' :'d-none';?>'></th>
                        <th>ESTIMATED HOURS <img src="assets/images/sort.png" alt="" class='<?php echo ($this->input->get('order_by') == 'estimated_hours') ? '' :'d-none';?>'></th>
                        <th>STAGE <img src="assets/images/sort.png" alt="" class='<?php echo ($this->input->get('order_by') == 'stage') ? '' :'d-none';?>'></th>
                        <th><i class="bi bi-chat-dots"></i></th>
                        <th><i class="bi bi-stopwatch"></i></th>
                        <th></th>
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
                    <?php foreach($tasks as $task):?>
                    <?php $totals[$task->task_stage]++; ?>
                    <tr data-id="<?php echo $task->id;?>">
                        <td class='task-number task-ref-cell'>
                                <?php
                                $display_ref = (isset($task->task_ref) && $task->task_ref !== '') ? $task->task_ref : (isset($task->task_number) ? $task->task_number : '');
                                if ($display_ref === '' && function_exists('task_ref')) {
                                    $display_ref = task_ref(isset($task->project_code) ? $task->project_code : null, isset($task->sprint_code) ? $task->sprint_code : null, isset($task->task_number) ? $task->task_number : '');
                                }
                                $display_ref = $display_ref !== '' ? $display_ref : (isset($task->task_number) ? $task->task_number : '');
                                ?>
                                <span class="task-ref-text"><?php echo htmlspecialchars($display_ref); ?></span>
                                <button type="button" class="copy-task-ref" data-ref="<?php echo htmlspecialchars($display_ref); ?>" title="Copy reference"><i class="bi bi-clipboard"></i></button>
                            </td>
                        <td class='task-section'><?php echo $task->section;?></td>
                        <td class='task-name'><?php echo $task->task_name;?></td>
                        <td><?php echo $task->sprint_name;?></td>
                        <td><?php echo $task->project_name;?></td>
                        <td><?php echo $task->company_name;?></td>
                        <td><?php echo !empty($task->created_on) ? date('Y-m-d', strtotime($task->created_on)) : '';?></td>
                        <td><?php echo !empty($task->created_by_name) ? $task->created_by_name : '';?></td>
                        <td><?php echo $task->due_date;?></td>
                        <td><?php echo $task->estimated_hours;?></td>
                        <td class="text-center">
                            <div class="stage-button stage-button-<?php echo $task->task_stage;?>">
                                <?php echo strtoupper(str_replace("_"," ",$task->task_stage));?>
                            </div>

                        </td>
                        <td class=''><?php echo $task->notes_count;?><br><i class="bi bi-eye view-notes cursor-pointer"></i></td>
                        <td class='cursor-pointer '>
                            <i class="bi bi-stop-circle-fill timer_stop <?php echo ( ($task->start_time != "") && ($task->finish_time == "")) ? '' : 'd-none';?>" style="font-size:1.2em;color:#f00;"></i>
                            <i class="bi bi-play-circle-fill timer_start <?php echo ( ($task->start_time != "") && ($task->finish_time == "")) ? 'd-none' : '';?>" style="font-size:1.2em;color:#999;"></i>
                        </td>
                        <td>
                            <a href="portal/developers/view?task_uuid=<?php echo "{$task->uuid}&customer_id={$this->input->get('customer_id')}&project_id={$this->input->get('project_id')}&sprint_id={$this->input->get('sprint_id')}";?>">
                                <div class="btn" style="color:#fff; background-color: var(--developersPortalBackground)"><div class="bg-icon bg-view"></div> View</div>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan='11' class='text-center'>
                            TOTAL:
                            <?php echo count($tasks) . " | NEW: " . $totals['new'] . " | IN PROGRESS: " . $totals['in_progress'] . " | TESTING: " . $totals['testing'] . " | STAGING: " . $totals['staging'] . " | VALIDATED: " . $totals['validated'] . " | COMPLETED: " . $totals['completed'] . " | ON HOLD: " . $totals['on_hold'];?>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <a href="">
                <div class="btn btn-warning"><i class="bi bi-chevron-left"></i> Back</div>
            </a>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-4"></div>
        <div class="col-md-4 text-center">
            <img class='img-thumbnail' src="assets/images/stageColors.png" alt="">
        </div>
    </div>

 
<!-- Modal -->
<div class="modal fade" id="modalNotes" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalNotesTitle">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x"></i> Close</button>
        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalTaskTimer" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Start Timer <i class="bi bi-stopwatch"></i></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class='task-name'></p>
        <textarea name="notes" id="" class="form-control" placeholder="Please enter a description of what you are doing specifically. This will be useful in reports since, for the task, you have multiple entries in the timesheet."></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary insert-timer">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<!-- <div class="modal fade" id="" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
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
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                    Cancel</button>
                <button type="button" class="btn btn-success applyChosenStages"><i class="fa fa-check"></i> Proceed</button>
            </div>
        </div>
    </div>
</div> -->

<!-- Modal -->
<div class="modal fade" id="modalChooseStages" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Select Stages</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <ul id="stages-list" class="list-group">
                <li data-stage='' class='list-group-item text-center'>
                    <div class="btn btn-info select-all">Select / Deselect All</div>
                </li>
                <?php foreach($stages as $stage):?>
                <li data-stage="<?php echo $stage;?>" class="text-center list-group-item cursor-pointer choose-stage <?php //echo in_array($user->id, $task->assigned_users) ? 'assigned':'';?>">
                    <?php echo str_replace("_"," ",strtoupper($stage));?>
                    <span class='float-end'><i class="fa fa-check-square green"></i></span>
                </li>
                <?php endforeach;?>
            </ul>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-success applyChosenStages">Proceed</button>
        </div>
        </div>
    </div>
</div>