<?php if($perms['add']): ?>
<!-- <div class="row">
    <div class="col-xs-1"> -->
        <a href="<?php echo base_url("users/add/"); ?>"><button class="btn btn-xs btn-flat btn-success"><i class="fa fa-plus"></i> Add</button></a>
    <!-- </div>
</div> -->
<?php endif; ?>
<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box">
            <?php if( (isset($users)) && (!empty($users)) ): ?>
            <div class="box-body table-responsive no-padding">
                <table id="tbl1" class="table table-border table-hover extended-bottom-margin">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Level</th>
                            <th>Status</th>
                            <th>Last Access</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                        <tr <?php echo ($_SESSION['user_id'] == $user->id)?' class="active"':''; ?>>
                            <td>
                                <?php if(!empty($user->photo)):?>
                                    <img style='width:30px' src="<?php echo base_url("uploads/users/".$user->photo);?>" alt="">
                                <?php else:?>
                                    <img style='width:30px' src="<?php echo base_url("assets/images/alphabets/".strtolower(substr($user->name,0,1)).".png");?>" alt="">
                                <?php endif;?>
                            </td>
                            <td class="<?php echo ($user->status!=1)?"inactive":"";?>"><?php echo $user->name; ?></td>
                            <td class="<?php echo ($user->status!=1)?"inactive":"";?>"><?php echo $user->username; ?></td>
                            <td class="<?php echo ($user->status!=1)?"inactive":"";?>"><?php echo $user->email; ?></td>
                            <td class="<?php echo ($user->status!=1)?"inactive":"";?>"><?php echo $user->department; ?></td>
                            <td class="<?php echo ($user->status!=1)?"inactive":"";?>"><?php echo $user->user_level; ?></td>
                            <td class="<?php echo ($user->status!=1)?"inactive":"";?>"><?php echo $user_status[$user->status]; ?></td>
                            <td class="<?php echo ($user->status!=1)?"inactive":"";?>"><?php echo (!empty($user->last_login))?$user->last_login:"Never"; ?></td>
                            <td class="<?php echo ($user->status!=1)?"inactive":"";?>">
                            <?php if($perms['edit']): ?>
                                <a href="<?php echo base_url('users/edit/' . $user->id); ?>"><div class="btn btn-flat btn-xs btn-primary"><i class='fas fa-edit'></i><span class='ButtonLabel'> Edit</span></div></a>
                            <?php endif; ?>
                            <?php if($perms['permission']): ?>
                                <a href="<?php echo base_url('users/permission/' . $user->id); ?>"><div class="btn btn-flat btn-xs btn-warning"><i class='fa fa-lock'></i><span class='ButtonLabel'> Permissions</span></div></a>
                            <?php endif; ?>
                            <?php if($user->status=="1"): ?>
                            <?php if($perms['deactivate']): ?>
                                <a href='<?php echo base_url('users/deactivate/' . $user->uuid); ?>'><div class="btn btn-flat btn-xs btn-danger"><i class='fa fa-lock'></i><span class='ButtonLabel'> De-Activate</span></div></a>
                            <?php endif; ?>
                            <?php else: ?>
                            <?php if($perms['activate']): ?>
                                <a href='<?php echo base_url('users/activate/' . $user->uuid); ?>'><div class="btn btn-flat btn-xs btn-success"><i class='fa fa-lock'></i><span class='ButtonLabel'> Activate</span></div></a>
                            <?php endif; ?>
                            <?php endif; ?>
                            <?php if($perms['delete']): ?>
                                <button data-url="<?php echo base_url("users/deleteAjax"); ?>" data-id="<?php echo $user->id;?>" class="deleteAjax btn btn-xs btn-flat btn-danger"><i class='fa fa-trash'></i><span class='ButtonLabel'> Delete</span></button>
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
