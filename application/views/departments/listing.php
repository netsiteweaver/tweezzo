<?php if($perms['add']): ?>
<div class="row">
    <div class="col-xs-1">
        <a href="<?php echo base_url("departments/add"); ?>"><div class="btn btn-xs btn-flat btn-success"><i class="fa fa-plus"></i> Add</div></a>
    </div>
</div>
<?php endif; ?>
<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box">
        <?php if( (isset($departments)) && (!empty($departments)) ): ?>
            <div class="box-body table-responsive no-padding">
                <table id="departments_listing" class="table">                    
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Address</th>  
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($departments as $department): ?>
                        <tr data-id="<?php echo $department->uuid;?>">
                            <td><?php echo $department->name; ?></td>
                            <td><?php echo $department->address; ?></td>
                            <td><?php echo $department->email; ?></td>
                            <td><?php echo $department->phone; ?></td>
                            <td>
                                <?php if($perms['edit']): ?>
                                <a href="<?php echo base_url("departments/edit/".$department->uuid); ?>"><div class="btn btn-xs btn-flat btn-primary"><i class="fa fa-edit"></i></div></a>
                                <?php endif; ?>
                                <?php if( ($perms['delete']) && ($department->id != 1) ) echo DeleteButton2('departments','uuid',$department->uuid,'','','',false); ?>
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
