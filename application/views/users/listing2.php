<?php if($perms['add']): ?>
<a title='Add User' href="<?php echo base_url("users/add/"); ?>"><button class="btn btn-flat btn-success"><i class="fa fa-plus"></i> Add</button></a>
<?php endif; ?>

<div class="row"><!-- Normal Level -->
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
                            <th>Level</th>
                            <!-- <th>Type</th> -->
                             <th>Landing Page</th>
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
                            <td class="<?php echo ($user->status!=1)?"inactive":"";?>"><?php echo $user->user_level; ?></td>
                            <!-- <td class="<?php echo ($user->status!=1)?"inactive":"";?>"><?php echo ucwords($user->user_type); ?></td> -->
                            <td class="<?php echo ($user->status!=1)?"inactive":"";?>"><?php echo $user->landing_page; ?></td>
                            <td class="<?php echo ($user->status!=1)?"inactive":"";?>"><?php echo $user_status[$user->status]; ?></td>
                            <td class="<?php echo ($user->status!=1)?"inactive":"";?>"><?php echo (!empty($user->last_login))?$user->last_login:"Never"; ?></td>
                            <td class="<?php echo ($user->status!=1)?"inactive":"";?>">
                            <?php if($perms['edit']): ?>
                                <a title='Update User' href="<?php echo base_url('users/edit/' . $user->id); ?>"><div class="btn btn-flat btn-md btn-primary"><i class='fas fa-edit'></i></div></a>
                            <?php endif; ?>
                            <?php if($perms['permission']): ?>
                                <a title='Grant Permission' href="<?php echo base_url('users/permission/' . $user->id); ?>"><div class="btn btn-flat btn-md btn-warning"><i class='fa fa-lock'></i></div></a>
                            <?php endif; ?>
                            <?php if($user->status=="1"): ?>
                            <?php if($perms['deactivate']): ?>
                                <a title='De-Activate' href='<?php echo base_url('users/deactivate/' . $user->uuid); ?>'><div class="btn btn-flat btn-md bg-orange"><i class='fa fa-stop-circle'></i></div></a>
                            <?php endif; ?>
                            <?php else: ?>
                            <?php if($perms['activate']): ?>
                                <a title='Re-Activate' href='<?php echo base_url('users/activate/' . $user->uuid); ?>'><div class="btn btn-flat btn-md btn-success"><i class='fa fa-play-circle'></i></div></a>
                            <?php endif; ?>
                            <?php endif; ?>
                            <?php if($perms['delete']): ?>
                                <button title='Delete User' data-url="<?php echo base_url("users/deleteAjax"); ?>" data-id="<?php echo $user->id;?>" class="deleteAjax btn btn-md btn-flat btn-danger"><i class='fa fa-trash'></i></button>
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

<div class="row">
    <div class="col-md-12">
        <p><b>Action Button Legend</b></p>
        <table class="table-bordered">
            <tbody>
                <tr>
                    <td><div class="btn btn-primary"><i class='fas fa-edit'></i> Update User</div></td>
                    <td><div class="btn btn-warning"><i class='fas fa-lock'></i> Grant Permissions</div></td>
                    <td><div class="btn bg-orange"><i class='fas fa-stop-circle'></i> De - Activate</div></td>
                    <td><div class="btn btn-success"><i class='fas fa-play-circle'></i> Re - Activate</div></td>
                    <td><div class="btn btn-danger"><i class='fas fa-trash'></i> Delete</div></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>