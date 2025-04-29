<form action="">
    <div class="row">
        <div class="col-md-2">
            <label for="">Start Date</label>
            <input type="date" name="start_date" class='form-control'
                value="<?php echo !empty($this->input->get("start_date")) ? $this->input->get("start_date") : date('Y-m-d', strtotime('-1 week'));?>">
        </div>
        <div class="col-md-1">
            <label for="">For</label>
            <input type="number" name="for" min='0' class='form-control'
                value="<?php echo !empty($this->input->get("for")) ? $this->input->get("for") : 1;?>">
        </div>
        <div class="col-md-2">
            <label for="">Period</label>
            <select name="period" id="" class="form-control">
                <!-- <option value="">Select</option> -->
                <option value="day" <?php echo ($this->input->get("period") == "day") ? "selected" : ""; ?>>Day</option>
                <option value="week" <?php echo ( (empty($this->input->get("period"))) || ($this->input->get("period") == "week") ) ? "selected" : ""; ?>>Week</option>
                <option value="month" <?php echo ($this->input->get("period") == "month") ? "selected" : ""; ?>>Month</option>
                <option value="year" <?php echo ($this->input->get("period") == "year") ? "selected" : ""; ?> disabled>Year</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="">Display</label>
            <select name="display" id="display" class="form-control monitor">
                <option value="">Default</option>
                <option value="10" <?php echo ( ( (empty($this->input->get("display"))) && ($default_per_page == '10') ) || ($this->input->get("display") == "10")) ? "selected" : ""; ?>>10 lines
                </option>
                <option value="25" <?php echo ( ( (empty($this->input->get("display"))) && ($default_per_page == '10') ) || ($this->input->get("display") == "25")) ? "selected" : ""; ?>>25 lines
                </option>
                <option value="50" <?php echo ( ( (empty($this->input->get("display"))) && ($default_per_page == '10') ) || ($this->input->get("display") == "50")) ? "selected" : ""; ?>>50 lines
                </option>
                <option value="100" <?php echo ( ( (empty($this->input->get("display"))) && ($default_per_page == '10') ) || ($this->input->get("display") == "100")) ? "selected" : ""; ?>>100 lines
                </option>
            </select>
        </div>
        <div class="col-md-2 mt-4">
            <button class="btn btn-info"><i class="fa fa-check"></i> Apply</button>
            <div class="btn btn-warning resetFilter" title="Reset filters"><i class="fa fa-undo"></i></div>
            <div data-target="notes_list" id="exportToCsv" class="btn btn-success export"><i class="fa fa-download"></i></div>
        </div>
    </div>
</form>

<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box">
            <?php if( (isset($notes)) && (!empty($notes)) ): ?>
            <div class="box-body table-responsive no-padding">
                <table id="notes_list" uuid="tbl1" class="table table-border table-hover extended-bottom-margin">
                    <thead>
                        <tr class='text-center' style='text-transform:uppercase;'>
                            <th style='font-size:12px;color:#ccc;'>#</th>
                            <th>Date</th>
                            <th>Note</th>
                            <th>Author</th>
                            <th>Customer</th>
                            <th>Project</th>
                            <th>Sprint</th>
                            <th>Task</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($notes as $i => $note): ?>
                        <tr class="<?php echo ($note->out_of_scope == '1') ? "out-of-scope" : ""; ?>">
                            <td style='font-size:12px;color:#ccc;'><?php echo $note->id;?></td>
                            <td><?php echo $note->created_on;?></td>
                            <td><?php echo nl2br($note->notes);?></td>
                            <td><?php echo $note->author;?></td>
                            <td class='cursor-pointer active-filter' data-type='customer' style='text-decoration:underline' data-customer-id="<?php echo $note->customerId;?>"><?php echo $note->company_name;?></td>
                            <td class='cursor-pointer active-filter' data-type='project' style='text-decoration:underline' data-project-id="<?php echo $note->projectId;?>"><?php echo $note->projectName;?></td>
                            <td class='cursor-pointer active-filter' data-type='sprint' style='text-decoration:underline' data-sprint-id="<?php echo $note->sprintId;?>"><?php echo $note->sprintName;?></td>
                            <td><?php echo "[{$note->taskNumber}] {$note->taskSection} / {$note->taskName}";?>
                            </td>
                            <td>
                                <?php if($perms['view']): ?>
                                <a href="<?php echo base_url('notes/view/' . $note->task_uuid); ?>">
                                    <div class="btn btn-flat btn-default"><i class='fas fa-eye'></i><span
                                            class='ButtonLabel'> View</span></div>
                                </a>
                                <?php endif; ?>
                                <!-- Example single danger button -->
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false">
                                        Action
                                    </button>
                                    <div class="dropdown-menu">
                                        <?php if($perms['view_task']): ?>
                                        <a target="_blank" class="dropdown-item" href="<?php echo base_url("tasks/view?task_uuid=".$note->task_uuid);?>"><i class="fa fa-eye"></i> View Task</a>
                                        <?php endif;?>
                                        <?php if($perms['edit_task']): ?>
                                        <a target="_blank" class="dropdown-item" href="<?php echo base_url("tasks/edit?task_uuid=".$note->task_uuid);?>"><i class="fa fa-edit"></i> Edit Task</a>
                                        <?php endif;?>
                                        <!-- <a class="dropdown-item" href="#"><i class="fa fa-comment"></i> View Note</a> -->
                                        <?php if($perms['delete']): ?>
                                        <div class="dropdown-divider"></div>
                                        <a style='color:red;font-weight:bold;' class="dropdown-item" href="<?php echo base_url("notes/confirm_delete/".$note->note_id);?>"><i class="fa fa-trash"></i> Delete Note</a>
                                        <?php endif;?>
                                    </div>
                                </div>

                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
            <?php else: ?>
            <p>No records</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php if( (isset($pagination)) && (!empty($pagination)) ) echo $pagination;?>