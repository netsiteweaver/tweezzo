<?php if($perms['add']): ?>
<a title='Add User' href="<?php echo base_url("developers/add/"); ?>"><button class="btn btn-flat btn-success"><i class="fa fa-plus"></i> Add</button></a>
<?php endif; ?>

<label class="pull-right" style="font-weight:normal; cursor:pointer; margin-top:6px;">
    <input type="checkbox" id="hideSuspended" checked> Hide suspended
</label>
<div class="clearfix"></div>

<?php
    $totalDevelopers     = (isset($developers) && !empty($developers)) ? count($developers) : 0;
    $activeDevelopers    = 0;
    $suspendedDevelopers = 0;
    if($totalDevelopers > 0){
        foreach($developers as $dev){
            if($dev->status == '1'){
                $activeDevelopers++;
            }elseif($dev->status == '2'){
                $suspendedDevelopers++;
            }
        }
    }
?>
<div class="row"><!-- Normal Level -->
    <div class="col-xs-12 col-sm-12">
        <div class="box">
            <?php if( (isset($developers)) && (!empty($developers)) ): ?>
            <div class="box-body table-responsive no-padding">
                <table id="tbl1" class="table table-border table-hover extended-bottom-margin">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($developers as $developer): ?>
                        <tr <?php echo ($_SESSION['user_id'] == $developer->id)?' class="active"':''; ?>>
                            <td>
                                <?php if(!empty($developer->photo)):?>
                                    <img style='width:30px' src="<?php echo base_url("uploads/users/".$developer->photo);?>" alt="">
                                <?php else:?>
                                    <img style='width:30px' src="<?php echo base_url("assets/images/alphabets/".strtolower(substr($developer->name,0,1)).".png");?>" alt="">
                                <?php endif;?>
                            </td>
                            <td class="<?php echo ($developer->status!=1)?"inactive":"";?>"><?php echo $developer->name; ?></td>
                            <td class="<?php echo ($developer->status!=1)?"inactive":"";?>"><?php echo $developer->email; ?></td>
                            <td>
                                <?php if($developer->status == '1'): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php elseif($developer->status == '2'): ?>
                                    <span class="badge badge-warning">Suspended</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="<?php echo ($developer->status!=1)?"inactive":"";?>">
                            <a title='View Timesheets' href="<?php echo base_url('timesheets/listing?developer_id=' . (int) $developer->id); ?>"><div class="btn btn-flat btn-md btn-info"><i class='fas fa-clock'></i></div></a>
                            <?php if($perms['edit']): ?>
                                <a title='Update User' href="<?php echo base_url('developers/edit/' . $developer->id); ?>"><div class="btn btn-flat btn-md btn-primary"><i class='fas fa-edit'></i></div></a>
                            <?php endif; ?>
                            <?php if($developer->status == '1' && $perms['edit'] && $_SESSION['user_id'] != $developer->id): ?>
                                <button title='Suspend User' data-url="<?php echo base_url("developers/suspend"); ?>" data-id="<?php echo $developer->id;?>" data-name="<?php echo htmlspecialchars($developer->name);?>" class="suspendDeveloper btn btn-md btn-flat bg-orange"><i class='fas fa-stop-circle'></i></button>
                            <?php endif; ?>
                            <?php if($developer->status == '2' && $perms['edit']): ?>
                                <button title='Unsuspend User' data-url="<?php echo base_url("developers/unsuspend"); ?>" data-id="<?php echo $developer->id;?>" data-name="<?php echo htmlspecialchars($developer->name);?>" class="unsuspendDeveloper btn btn-md btn-flat btn-success"><i class='fas fa-unlock'></i></button>
                            <?php endif; ?>
                            <?php if($perms['permission']): ?>
                                <!-- <a title='Grant Permission' href="<?php echo base_url('developers/permission/' . $developer->id); ?>"><div class="btn btn-flat btn-md btn-warning"><i class='fa fa-lock'></i></div></a> -->
                            <?php endif; ?>
                            <?php if($perms['delete']): ?>
                                <button title='Delete User' data-url="<?php echo base_url("developers/deleteAjax"); ?>" data-id="<?php echo $developer->id;?>" class="deleteAjax btn btn-md btn-flat btn-danger"><i class='fa fa-trash'></i></button>
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

<div class="row summary-badges extended-bottom-margin">
    <div class="col-xs-12 text-center">
        <span class="badge bg-blue" style="font-size:14px; padding:8px 12px; margin:0 4px;"><i class="fas fa-users"></i> Total: <span id="count-total"><?php echo $totalDevelopers; ?></span></span>
        <span class="badge badge-success" style="font-size:14px; padding:8px 12px; margin:0 4px;"><i class="fas fa-check-circle"></i> Active: <span id="count-active"><?php echo $activeDevelopers; ?></span></span>
        <span class="badge badge-warning" style="font-size:14px; padding:8px 12px; margin:0 4px;"><i class="fas fa-pause-circle"></i> Suspended: <span id="count-suspended"><?php echo $suspendedDevelopers; ?></span></span>
    </div>
</div>

<div class="row hidden">
    <div class="col-md-12">
        <p class='text-center'><b>Action Button</b></p>
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td><div class="btn btn-block btn-xs btn-info"><i class='fas fa-2x fa-edit'></i></div></td>
                    <td><div class="btn btn-block btn-xs btn-warning"><i class='fas fa-2x fa-lock'></i></div></td>
                    <td><div class="btn btn-block btn-xs bg-orange"><i class='fas fa-2x fa-stop-circle'></i></div></td>
                    <td><div class="btn btn-block btn-xs btn-success"><i class='fas fa-2x fa-play-circle'></i></div></td>
                    <td><div class="btn btn-block btn-xs btn-danger"><i class='fas fa-2x fa-trash'></i></div></td>
                </tr>
                <tr class='text-center text-bold'>
                    <td>Update User</td>
                    <td>Grant Permissions</td>
                    <td>De - Activate</td>
                    <td>Re - Activate</td>
                    <td class='red'>Delete</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>