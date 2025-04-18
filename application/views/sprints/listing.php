<?php if($perms['add']): ?>
<div class="row">
    <div class="col-xs-1">
        <a href="<?php echo base_url("sprints/add/"); ?>"><button class="btn btn-flat btn-success"><i class="fa fa-plus"></i> Add</button></a>
    </div>
    
    <div class="col-md-3">
        <!-- <label for="">Customer</label> -->
        <select class="form-control monitor" id="customer_id">
            <option value="">Select Customer</option>
            <?php foreach($customers as $customer): ?>
            <option value="<?php echo $customer->customer_id; ?>" <?php echo ($this->input->get("customer_id") == $customer->customer_id) ? "selected" : ""; ?>>
                <?php echo "{$customer->company_name}"; ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="col-md-2">
        <select name="" id="order_by" class="form-control monitor">
            <option value="">Order By</option>
            <!-- <option value="section" <?php echo ($this->input->get("order_by") == "section") ? "selected" : ""; ?>>Name</option> -->
            <option value="name" <?php echo ( (empty($this->input->get("order_by"))) || ($this->input->get("order_by") == "name") ) ? "selected" : ""; ?>>Name</option>
            <option value="project_name" <?php echo ($this->input->get("order_by") == "project_name") ? "selected" : ""; ?>>Project</option>
            <option value="company_name" <?php echo ($this->input->get("order_by") == "company_name") ? "selected" : ""; ?>>Customer</option>
        </select>
    </div>
    <div class="col-md-1">
        <select name="" id="order_dir" class="form-control monitor">
            <option value="asc" <?php echo ($this->input->get("order_dir") == "asc") ? "selected" : ""; ?>>Asc</option>
            <option value="desc" <?php echo ($this->input->get("order_dir") == "desc") ? "selected" : ""; ?>>Desc</option>
        </select>
    </div>
    <div class="col-md-2">
        <select name="" id="display" class="form-control monitor">
            <option value="">Display</option>
            <option value="10" <?php echo ($this->input->get("display") == "10") ? "selected" : ""; ?>>10 lines</option>
            <option value="25" <?php echo ($this->input->get("display") == "25") ? "selected" : ""; ?>>25 lines</option>
            <option value="50" <?php echo ($this->input->get("display") == "50") ? "selected" : ""; ?>>50 lines</option>
            <option value="100" <?php echo ($this->input->get("display") == "100") ? "selected" : ""; ?>>100 lines</option>
        </select>
    </div>
</div>
<?php endif; ?>
<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box">
            <?php if( (isset($sprints)) && (!empty($sprints)) ): ?>
            <div class="box-body table-responsive no-padding">
                <table uuid="tbl1" class="table table-border table-hover extended-bottom-margin">
                    <thead>
                        <tr class='text-center' style='text-transform:uppercase;'>
                            <th>Name <?php echo ( (empty($this->input->get("order_by"))) || ($this->input->get("order_by") == "name") ) ? "<i class='fa fa-sort'></i>" : '';?></th>
                            <th>Project <?php echo ($this->input->get("order_by") == "project_name") ? "<i class='fa fa-sort'></i>" : '';?></th>
                            <th>Customer <?php echo ($this->input->get("order_by") == "company_name") ? "<i class='fa fa-sort'></i>" : '';?></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($sprints as $task): ?>
                        <tr>
                            <td><?php echo $task->name;?></td>
                            <td><?php echo $task->project_name;?></td>
                            <td><?php echo "<b>{$task->company_name}</b><br>{$task->full_name}";?></td>
                            <td>
                            <?php if($perms['view']): ?>
                                <a href="<?php echo base_url('sprints/view/' . $task->uuid."?customer_id=".$this->input->get("customer_id")."&stage=".$this->input->get("stage")); ?>"><div class="btn btn-flat btn-default"><i class='fas fa-eye'></i><span class='ButtonLabel'> View</span></div></a>
                            <?php endif; ?>
                            <?php if($perms['edit']): ?>
                                <a href="<?php echo base_url('sprints/edit/' . $task->uuid."?customer_id=".$this->input->get("customer_id")."&stage=".$this->input->get("stage")); ?>"><div class="btn btn-flat btn-primary"><i class='fas fa-edit'></i><span class='ButtonLabel'> Edit</span></div></a>
                            <?php endif; ?>
                            <?php if($perms['delete']): ?>
                                <button data-url="<?php echo base_url("sprints/delete"); ?>" data-uuid="<?php echo $task->uuid;?>" class="deleteAjax btn btn-flat btn-danger"><i class='fa fa-trash'></i><span class='ButtonLabel'></span></button>
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
