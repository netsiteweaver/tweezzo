<!-- <div class="row">
    <div class="col-xs-12 col-sm-12"> -->
<div class="box box-primary">
    <form id="edit_user" name="form" method="post" action="users/update" enctype="multipart/form-data">
        <div class="box-body">
            <input type="hidden" name="id" value="<?php echo $user->id; ?>">
            <div class="row">
                <div class="col-md-4" style="border-right:2px solid #ccc;">
                    <div class="col-md-12">
                        <h2>User Information</h2>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Name</label>
                            <input type="text" class="form-control required" name="name" placeholder=""
                                value="<?php echo $user->name; ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Job Title</label>
                            <input type="text" class="form-control required" name="job_title" placeholder=""
                                value="<?php echo $user->job_title; ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Email</label>
                            <input type="email" class="form-control required" name="email" placeholder=""
                                value="<?php echo $user->email; ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Username</label>
                            <input type="text" class="form-control required" name="username" placeholder=""
                                value="<?php echo $user->username; ?>" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="">User Level</label>
                            <select class="form-control required" name="level" data-name="User Level">
                                <option value="">Select Level</option>
                                <option value="Normal" <?php echo ($user->user_level == "Normal")?'selected':''; ?>>
                                    Normal</option>
                                <option value="Admin" <?php echo ($user->user_level == "Admin")?'selected':''; ?>>Admin
                                </option>
                                <?php if($_SESSION['user_level']=='Root'):?>
                                <option value="Root" <?php echo ($user->user_level == "Root")?'selected':''; ?>>Root
                                </option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <input type="hidden" name="user_type" value="regular">
                    <!-- <div class="form-group">
						<label for="">User Type</label>
						<p class="notes"><b>Regular</b> is the default and are users who will be able to login and use the application, whereas <b>Developer</b> & <b>Client</b> won't be able to login. They are used for notification about tasks creation and progress.</p>
						<select class="form-control required" name="user_type" data-name="User Level">
							<option value="" disabled>Select Type</option>
							<option value="regular" <?php //echo ($user->user_type == "regular")?'selected':''; ?>>Regular</option>
							<option value="developer" <?php //echo ($user->user_type == "developer")?'selected':''; ?>>Developer</option>
							<option value="client" <?php //echo ($user->user_type == "client")?'selected':''; ?>>Client</option>
						</select>
					</div> -->

                    <div class="row d-none">
                        <div class="col-md-12">
                            <h2>Department</h2>
                            <!-- <select name="department_id" class="form-control required" data-name="Department" required>
                                <option value="" name="department_id" disabled>Select a Department</option>
                                <?php foreach ($dpt as $row) : ?>
                                <option value="<?php echo $row->id; ?>"
                                    <?php echo($row->id==$user->department_id)?"selected":"";?>><?php echo $row->name; ?>
                                </option>
                                <?php endforeach; ?>
                            </select> -->
                            <input type="hidden" name="department_id" value="1">
                        </div>
                    </div>

                    <br>

					<div class="row <?php echo (empty($user->photo))?'d-none':'';?>">
						<div class="col-xs-12 user-photo">
							<img src="<?php echo base_url("uploads/users/".$user->photo);?>" style="clip-path: circle();width:200px;" alt="">
							<div class='remove-photo'><i class="fa fa-times"></i></div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<label for="userPhoto">Photo</label>
                            <input type="hidden" name="delete_image" value="0">
							<input type="file" name="image" accept=".jpg,.png,.jpeg">
						</div>
					</div>

                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-12">
                            <h2>Password management</h2>
                            <p>Leave empty for no change in password</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="">New Password</label>
                            <p class='notes'>Please enter your new password</p>
                            <input type="password" class="form-control required2" minlength="6" name="pswd"
                                placeholder="" value="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Confirm New Password</label>
                            <p class='notes'>Please enter your password a second time to confirm</p>
                            <input type="password" class="form-control required2" minlength="6" name="pswd2"
                                placeholder="" value="">
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-xs-2 col-xs-offset-1">
                    <div class="btn btn-flat btn-success" id="update"><i class='fa fa-save'></i> Update</button>
                    </div>
                </div>
            </div>
    </form>
</div>
<!-- </div>
</div> -->