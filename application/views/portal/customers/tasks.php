<style>
    .task-ref-cell { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
    .task-ref-cell .copy-task-ref { cursor: pointer; opacity: 0.6; padding: 2px 4px; border: none; background: none; color: inherit; font-size: 14px; }
    .task-ref-cell .copy-task-ref:hover { opacity: 1; }
    .task-ref-cell .copy-task-ref.copied { opacity: 1; color: #28a745; }
</style>
<form method="get" id="taskFilterForm">
<div class="row mb-5">
    <div class="col-md-2">
        <input type="hidden" name="sprint_id" value="<?php echo $this->input->get("sprint_id");?>">
        <label for="">Sort By</label>
        <select name="sort_by" id="" class="form-control autosubmit">
            <option value="">Select</option>
            <option value="task_number" <?php echo ( (empty($this->input->get('sort_by'))) || ($this->input->get("sort_by") == 'task_number' ) )?'selected':'';?>>Task Number</option>
            <!-- <option value="project_id" <?php echo ($this->input->get("sort_by") == 'project_id' )?'selected':'';?>>Project</option>
            <option value="sprint_id" <?php echo ($this->input->get("sort_by") == 'sprint_id' )?'selected':'';?>>Sprint</option> -->
            <option value="project_name" <?php echo ($this->input->get("sort_by") == 'project_name' )?'selected':'';?>>Project Name</option>
            <option value="sprint_name" <?php echo ($this->input->get("sort_by") == 'sprint_name' )?'selected':'';?>>Sprint Name</option>
            <option value="section" <?php echo ($this->input->get("sort_by") == 'section' )?'selected':'';?>>Section</option>
            <option value="name" <?php echo ($this->input->get("sort_by") == 'name' )?'selected':'';?>>Task Name</option>
            <option value="due_date" <?php echo ($this->input->get("sort_by") == 'due_date' )?'selected':'';?>>Due Date</option>
            <option value="stage" <?php echo ($this->input->get("sort_by") == 'stage' )?'selected':'';?>>Stage</option>
        </select>
    </div>
    <div class="col-md-2">
        <label for="">Sort Direction</label>
        <select name="sort_dir" id="" class="form-control autosubmit">
            <option value="asc" <?php echo (in_array($this->input->get("sort_dir"),['asc','']) )?'selected':'';?>>
                Ascending</option>
            <option value="desc" <?php echo (in_array($this->input->get("sort_dir"),['desc']) )?'selected':'';?>>
                Descending</option>
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
        <label for="">Filter Stages</label>
        <input type="hidden" name="stages" id="selectedStages" value="<?php echo htmlspecialchars(implode(',', $stages)); ?>">
        <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#stageFilterModal">
            Select Stages
        </button>
    <!-- Stage Filter Modal -->
    <div class="modal fade" id="stageFilterModal" tabindex="-1" aria-labelledby="stageFilterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="stageFilterModalLabel">Select Stages</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="all" id="selectAllStages">
                        <label class="form-check-label" for="selectAllStages">Select All</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input stage-checkbox" type="checkbox" value="new" id="stageNew">
                        <label class="form-check-label" for="stageNew">NEW</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input stage-checkbox" type="checkbox" value="in_progress" id="stageInProgress">
                        <label class="form-check-label" for="stageInProgress">IN PROGRESS</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input stage-checkbox" type="checkbox" value="testing" id="stageTesting">
                        <label class="form-check-label" for="stageTesting">TESTING</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input stage-checkbox" type="checkbox" value="staging" id="stageStaging">
                        <label class="form-check-label" for="stageStaging">STAGING</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input stage-checkbox" type="checkbox" value="validated" id="stageValidated">
                        <label class="form-check-label" for="stageValidated">VALIDATED</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input stage-checkbox" type="checkbox" value="completed" id="stageCompleted">
                        <label class="form-check-label" for="stageCompleted">COMPLETED</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input stage-checkbox" type="checkbox" value="on_hold" id="stageOnHold">
                        <label class="form-check-label" for="stageOnHold">ON HOLD</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="applyStageFilter">Apply</button>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="col-md-2 mt-4">
        <button type="submit" class="btn btn-info apply"><img src="assets/ionicons/checkmark-done-outline.svg" alt="" class="ionicon">Apply</button>
    </div>
</div>
</form>
<div class="row table-responsive">
    <div class="col-md-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th># <img src="assets/images/sort.png" alt="" class='<?php echo ( (empty($this->input->get('sort_by'))) || ($this->input->get('sort_by') == 'task_number') ) ? '' :'d-none';?>'></th>
                    <th>Task ID</th>
                    <th>PROJECT NAME <img src="assets/images/sort.png" alt="" class='<?php echo ($this->input->get('sort_by') == 'project_name') ? '' :'d-none';?>'></th>
                    <th>SPRINT NAME <img src="assets/images/sort.png" alt="" class='<?php echo ($this->input->get('sort_by') == 'sprint_name') ? '' :'d-none';?>'></th>
                    <th>SECTION <img src="assets/images/sort.png" alt="" class='<?php echo ($this->input->get('sort_by') == 'section') ? '' :'d-none';?>'></th>
                    <th>TASK <img src="assets/images/sort.png" alt="" class='<?php echo ($this->input->get('sort_by') == 'name') ? '' :'d-none';?>'></th>
                    <th>DUE DATE <img src="assets/images/sort.png" alt="" class='<?php echo ($this->input->get('sort_by') == 'due_date') ? '' :'d-none';?>'></th>
                    <th>STAGE <img src="assets/images/sort.png" alt="" class='<?php echo ($this->input->get('sort_by') == 'stage') ? '' :'d-none';?>'></th>
                    <th>WORK TYPE</th>
                    <th class="text-center" title="Source"><i class="fas fa-sign-in-alt"></i></th>
                    <th>BILLABLE</th>
                    <th>Ref</th>
                    <th><i class="bi bi-chat-dots"></i></th>
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
                <?php $totals[$task->stage]++; ?>
                <tr data-id="<?php echo $task->id;?>">
                    <td class="task-number"><?php echo isset($task->task_number) ? htmlspecialchars($task->task_number) : '';?></td>
                    <td class="task-ref-cell">
                                <?php
                                $display_ref = (isset($task->task_ref) && $task->task_ref !== '') ? $task->task_ref : (isset($task->task_number) ? $task->task_number : '');
                                if ($display_ref === '' && function_exists('task_ref')) {
                                    $display_ref = task_ref(isset($task->project_code) ? $task->project_code : null, isset($task->sprint_code) ? $task->sprint_code : null, isset($task->task_number) ? $task->task_number : '');
                                }
                                $display_ref = $display_ref !== '' ? $display_ref : (isset($task->task_number) ? $task->task_number : '');
                                ?>
                                <span class="task-ref-text"><?php echo htmlspecialchars($display_ref); ?></span>
                                <button type="button" class="copy-task-ref" data-ref="<?php echo htmlspecialchars($display_ref); ?>" title="Copy task ID"><i class="bi bi-clipboard"></i></button>
                            </td>
                    <td><?php echo $task->project_name;?></td>
                    <td><?php echo $task->sprint_name;?></td>
                    <td class="task-section"><?php echo $task->section;?></td>
                    <td class="task-name"><?php echo $task->name;?></td>
                    <td><?php echo $task->due_date;?></td>
                    <td class="text-center">
                        <button type="button"
                            class="stage-button stage-button-<?php echo $task->stage;?>">
                            <?php echo strtoupper($task->stage);?>
                        </button>

                    </td>
                    <td><?php echo !empty($task->work_type) ? ucfirst($task->work_type) : '—';?></td>
                    <td class="text-center"><?php echo task_source_icon_html(isset($task->source) ? $task->source : ''); ?></td>
                    <td><?php echo isset($task->billable) && $task->billable == 1 ? 'Yes' : (isset($task->billable) && $task->billable == 0 ? 'No' : '—');?></td>
                    <td><?php echo isset($task->ref) && $task->ref !== '' ? htmlspecialchars($task->ref) : '—';?></td>
                    <td class=''><?php echo $task->notes_count;?><br><i class="bi bi-eye view-notes cursor-pointer"></i></td>
                    <td>
                        <a href="portal/customers/view?task_uuid=<?php echo $task->uuid;?>">
                            <div class="btn btn-outline-secondary" style="color:#fff; background-color: var(--customersPortalBackground)"><i class="bi bi-eye"></i> View</div>
                        </a>
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan='14' class='text-center'>
                        TOTAL:
                        <?php echo count($tasks) . " | NEW: " . $totals['new'] . " | IN PROGRESS: " . $totals['in_progress'] . " | TESTING: " . $totals['testing'] . " | STAGING: " . $totals['staging'] . " | VALIDATED: " . $totals['validated'] . " | COMPLETED: " . $totals['completed'] . " | ON HOLD: " . $totals['on_hold'];?>
                    </th>
                </tr>
            </tfoot>
        </table>
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