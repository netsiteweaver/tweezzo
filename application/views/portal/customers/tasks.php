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
        <label for="">Filter Stages <img class='toggle-filter ionicon' src="assets/ionicons/arrow-down-circle.svg"
                alt=""> <span style='font-size:14px; color:#4c4c4c;'>(Ctrl+Click for multiple selection)</span></label>
        <select name="stages" id="" class="autosubmit <?php echo (count($stages)==0)?'d-none':'';?> form-control"
            style='height:170px;' multiple>
            <option value="new" <?php echo (in_array('new',$stages) )?'selected':'';?>>NEW</option>
            <option value="in_progress" <?php echo (in_array('in_progress',$stages) )?'selected':'';?>>IN PROGRESS
            </option>
            <option value="testing" <?php echo (in_array('testing',$stages) )?'selected':'';?>>TESTING</option>
            <option value="staging" <?php echo (in_array('staging',$stages) )?'selected':'';?>>STAGING</option>
            <option value="validated" <?php echo (in_array('validated',$stages) )?'selected':'';?>>VALIDATED</option>
            <option value="completed" <?php echo (in_array('completed',$stages) )?'selected':'';?>>COMPLETED</option>
            <option value="on_hold" <?php echo (in_array('on_hold',$stages) )?'selected':'';?>>ON HOLD</option>
        </select>
    </div>
    <div class="col-md-2 mt-4">
        <div class="btn btn-info apply"><img src="assets/ionicons/checkmark-done-outline.svg" alt=""
                class="ionicon">Apply</div>
    </div>
</div>
<div class="row table-responsive">
    <div class="col-md-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th># <img src="assets/images/sort.png" alt="" class='<?php echo ( (empty($this->input->get('sort_by'))) || ($this->input->get('sort_by') == 'task_number') ) ? '' :'d-none';?>'></th>
                    <th>PROJECT NAME <img src="assets/images/sort.png" alt="" class='<?php echo ($this->input->get('sort_by') == 'project_name') ? '' :'d-none';?>'></th>
                    <th>SPRINT NAME <img src="assets/images/sort.png" alt="" class='<?php echo ($this->input->get('sort_by') == 'sprint_name') ? '' :'d-none';?>'></th>
                    <th>SECTION <img src="assets/images/sort.png" alt="" class='<?php echo ($this->input->get('sort_by') == 'section') ? '' :'d-none';?>'></th>
                    <th>TASK <img src="assets/images/sort.png" alt="" class='<?php echo ($this->input->get('sort_by') == 'name') ? '' :'d-none';?>'></th>
                    <th>DUE DATE <img src="assets/images/sort.png" alt="" class='<?php echo ($this->input->get('sort_by') == 'due_date') ? '' :'d-none';?>'></th>
                    <th>STAGE <img src="assets/images/sort.png" alt="" class='<?php echo ($this->input->get('sort_by') == 'stage') ? '' :'d-none';?>'></th>
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
                    <td class='tast-number''><?php echo $task->task_number;?></td>
                    <td><?php echo $task->project_name;?></td>
                    <td><?php echo $task->sprint_name;?></td>
                    <td><?php echo $task->section;?></td>
                    <td><?php echo $task->name;?></td>
                    <td><?php echo $task->due_date;?></td>
                    <td class="text-center">
                        <button type="button"
                            class="stage-button stage-button-<?php echo $task->stage;?>">
                            <?php echo strtoupper($task->stage);?>
                        </button>

                    </td>
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
                    <th colspan='7' class='text-center'>
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