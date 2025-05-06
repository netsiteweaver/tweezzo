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

<div class="row no-print">
    <div class="col-xs-2 mt-4">
        <?php if($perms['add']): ?>
        <a href="<?php echo base_url("submitted_tasks/add?customer_id=".$this->input->get('customer_id')."&sprint_id=".$this->input->get("sprint_id")."&project_id=".$this->input->get("project_id")); ?>"><button
                class="btn btn-flat btn-success"><i class="fa fa-plus"></i> Add</button></a>
        <?php endif; ?>
        <?php if($perms['import']): ?>
            <a href="<?php echo base_url("submitted_tasks/import/"); ?>"><button class="btn btn-flat btn-info"><i
                    class="fa fa-upload"></i> Import</button></a>
        <?php endif; ?>
    </div>

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


    <div class="col-md-2">
        <label for="search">Search</label>
        <div class="input-group mb-3">
            <input type="text" class="form-control submitted_task-listing-search" name="search_text" id="search_text" placeholder="Search in submitted_tasks..." aria-label="Search in submitted_tasks...e" aria-describedby="basic-addon2" value="<?php echo $this->input->get("search_text");?>">
            <div class="input-group-append search cursor-pointer">
                <span class="input-group-text" id="basic-addon2"><i class="fa fa-binoculars"></i></span>
            </div>
        </div>

        <!-- <label for="search">Search</label> -->
        <!-- <input class='form-control' type="search" name="search" placeholder="Search in submitted_tasks..." value=""> -->
    </div>

    


</div>
<div class="row no-print">

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
    
    <?php if( ($perms['edit']) || ($perms['delete'])):?>

    <div class="col-md-1 mt-4">
        <div data-target="submitted_task-list" data-skip-columns="[0,-1,-2,-3,-4]" data-include-columns="" id="downloadTableAsCSV" data-filename="submitted_tasks" class="btn btn-success export"><i class="fa fa-download"></i></div>
    </div>
    <!-- <div class="col-md-2 mt-4">
        <div class="btn btn-block btn-default email-developer">
            <i class="fa fa-at"></i> Email Developer
        </div>
    </div>
    <div class="col-md-2 mt-4">
        <div data-email="<?php echo (isset($submitted_tasks[0])) ? $submitted_tasks[0]->email : '';?>"
            title="<?php echo (empty($this->input->get("customer_id"))) ? 'Please select a Customer first' : '';?>"
            class="btn btn-block btn-default email <?php echo ( (!isset($submitted_tasks[0])) || (empty($this->input->get("customer_id"))) ) ? 'disabled' : '';?>">
            <i class="fa fa-at"></i> Email Client
        </div>
    </div> -->
    <div class="col-md-1 mt-4">
        <div class="btn btn-default btn-block print"><i class="fa fa-print"></i> Print</div>
        <!-- <div class="btn btn-default"><i class="fa fa-cog"></i></div> -->
    </div>

    
    <?php endif;?>
</div>

<?php if( (!empty($submitted_tasks)) && (!empty($this->input->get("customer_id"))) ):?>
<div class="row print-only listing">
    <p class='page-title'>
        <?php echo (!empty($this->input->get("customer_id"))) ? "<b>Customer</b>: {$submitted_tasks[0]->company_name}" : '';?>
        <?php echo (!empty($this->input->get("project_id"))) ? " <b>Project</b>: {$submitted_tasks[0]->project_name}" : '';?>
        <?php echo (!empty($this->input->get("sprint_id"))) ? " <b>Sprint</b>: {$submitted_tasks[0]->sprint_name}" : '';?>
        <?php echo (!empty($this->input->get("stage"))) ? " <b>Stage</b>: " . strtoupper(str_replace("_"," ",$submitted_tasks[0]->stage)) : '';?>
    </p>
</div>
<?php endif;?>

<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box">
            <?php if( (isset($submitted_tasks)) && (!empty($submitted_tasks)) ): ?>
            <div class="box-body table-responsive no-padding">
                <table id="submitted_task-list" uuid="tbl1" class="table table-border extended-bottom-margin">
                    <thead>
                        <tr class='text-center' style='text-transform:uppercase;'>
                            <th class='no-print'></th>
                            <th style='width:60px;'><i class='fa fa-building'></i> / <i class='fa fa-user'></i></th>
                            <th style='width:150px;'>Submitted By</th>
                            <th>Task <?php echo ($this->input->get("order_by") == "name") ? "<i class='fa fa-sort'></i>" : '';?></th>
                            <th class='no-print'>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($submitted_tasks as $i => $submitted_task): ?>
                        <?php $page=$this->uri->segment(3,1); $display=!empty($this->input->get("display"))?$this->input->get("display"):8;?>
                        <tr data-id="<?php echo $submitted_task->id;?>">
                            <td class='no-print' style='width:20px;font-size:14px;vertical-align:middle;color:#ccc;text-align:center;'>
                                <?php echo ($i+1)+( ($page-1)*$display );?>
                            </td>
                            <td class='text-center'>
                                <?php echo (!empty($submitted_task->created_by)) ? "<i class='fa fa-building'></i>" : "<i class='fa fa-user'></i>";?>
                            </td>
                            <td><?php echo $submitted_task->submitted_by; ?></td>
                            <td class='submitted_task-name'>
                                <div style='border-bottom:1px dashed #ccc;padding-bottom:3px;margin-bottom:5px;'><b>Section:</b> <?php echo $submitted_task->name; ?></div>
                                <div style='border-bottom:1px dashed #ccc;padding-bottom:3px;margin-bottom:5px;'><b>Task:</b> <?php echo $submitted_task->section; ?></div>
                                <div style='border-bottom:1px dashed #ccc;padding-bottom:3px;margin-bottom:5px;'><b>Task Details:</b> <?php echo nl2br($submitted_task->description); ?></div>
                            </td>
                            <td class='no-print' style='width:150px;'>
                                <?php if($perms['edit']): ?>
                                <a
                                    href="<?php echo base_url('submitted_tasks/view?task_uuid=' . $submitted_task->uuid."&".$qs); ?>">
                                    <div class="btn btn-flat btn-default"><i class='fas fa-eye'></i><span
                                            class='ButtonLabel'></span></div>
                                </a>
                                <?php endif; ?>
                                <?php if($perms['delete']): ?>
                                <button data-url="<?php echo base_url("submitted_tasks/delete"); ?>"
                                    data-uuid="<?php echo $submitted_task->uuid;?>" class="deleteAjax btn btn-flat btn-danger"><i
                                        class='fa fa-trash'></i><span class='ButtonLabel'></span></button>
                                <?php endif; ?>
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
                <p>By Assigning in bulk, any previously assigned users will be removed from the selected submitted_tasks.</p>
                <ul id="users-list" class="list-group">
                    <?php foreach($users as $user):?>
                    <?php if($user->user_type != 'developer') continue;?>
                    <li data-id="<?php echo $user->id;?>"
                        class="list-group-item select-user <?php //echo in_array($user->id, $submitted_task->assigned_users) ? 'assigned':'';?>">
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
                <h5 class="modal-title" id="exampleModalLongTitle">Verify the list of submitted_tasks selected for deletion before
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
