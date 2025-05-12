<div class="box box-primary">
    <form id="edit_user" name="form" method="post" action="developers/update" enctype="multipart/form-data">
        <div class="box-body">
            <input type="hidden" name="id" value="<?php echo $user->id; ?>">
            <div class="row">
                <div class="col-md-4" style='border-right:2px solid #ccc;'>
                    <div class="col-md-12">
                        <h2>Developer Information</h2>
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

                    <input type="hidden" name="username" value="<?php echo $user->email;?>">
                    <input type="hidden" name="level" value="normal">
                    <input type="hidden" name="user_type" value="developer">
                    <input type="hidden" name="department_id" value="1">

                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Email</label>
                            <input type="email" class="form-control required" name="email" placeholder=""
                                value="<?php echo $user->email; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="">2 Letter Country Code</label>
						<input type="text" class="form-control required" name="country_code" data-name="Country Code" minlength='2' maxlength='2' placeholder="Enter 2 letter country code for developer" data-min-length='4' value="<?php echo $user->country_code; ?>">
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