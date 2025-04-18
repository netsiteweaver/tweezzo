<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box box-primary">
            <div class="box-body">
				<nav>
					<div class="nav nav-tabs" id="nav-tab" role="tablist">
						<a class="nav-item nav-link active" id="nav-rows_per_page-tab" data-toggle="tab" href="#my_profile" role="tab" aria-controls="nav-home" aria-selected="true"><i class="fa fa-angle-down"></i> My Profile</a>
						<a class="nav-item nav-link" id="nav-theme-tab" data-toggle="tab" href="#change_password" role="tab" aria-controls="nav-theme" aria-selected="false"><i class="fa fa-lock"></i> Change Password</a>
						<!-- <a class="nav-item nav-link" id="nav-mailer-tab" data-toggle="tab" href="#dashboard" role="tab" aria-controls="nav-mailer" aria-selected="false"><i class="fa fa-envelope"></i> My Dashboard</a> -->
					</div>
				</nav>				

				<div class="tab-content">
                    <div id="my_profile" class="tab-pane show active">
                        <form id="edit_user" name="form" method="post" enctype="multipart/form-data" action="<?php echo base_url('users/updateprofile/'); ?>">
					        <input type="hidden" name="id" value="<?php echo $user->id; ?>">
							<input type="hidden" name="user_level" value="<?php echo $user->user_level;?>">
							<div class="row">
								<div class="col-md-6">
									<h3>Name</h3>
									<input type="text" class="form-control required" name="name" placeholder="" value="<?php echo $user->name; ?>" autofocus>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<h3>Job Title</h3>
									<input type="text" class="form-control required" name="job_title" placeholder="" value="<?php echo $user->job_title; ?>">
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<h3>Username</h3>
									<input type="text" class="form-control required" name="username" placeholder="" value="<?php echo $user->username; ?>" readonly>
								</div>
							</div>

							<div class="row <?php echo ( ($user->user_level != 'Normal') && (empty($user->email)) )?'has-error':'';?>">
								<div class="col-md-6">
									<h3>Email</h3>
									<input type="email" class="form-control <?php echo ($user->user_level == 'Normal')?'':'required';?>" name="email" placeholder="" value="<?php echo $user->email; ?>">
								</div>
							</div>

							<br>
							
							<div class="row <?php echo (empty($user->photo))?'d-none':'';?>">
								<div class="col-xs-4">
									<img src="<?php echo base_url("uploads/users/".$user->photo);?>" style="clip-path: circle();width:200px;" alt="">
									<!-- <div><i class="fa fa-trash"></i></div> -->
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<label for="userPhoto">Photo</label>
									<input type="file" name="image" accept=".jpg,.png,.jpeg">
								</div>
							</div>

                            <div class="row">
                                <div class="col-md-6">
                                    <br>
                                    <button type="submit" class="btn btn-xs btn-flat btn-info"><i class="fa fa-save"></i> Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="change_password" class="tab-pane fade">
                        <form id="edit_user" name="form" method="post" action="<?php echo base_url('users/updateprofile/password'); ?>">
					        <input type="hidden" name="id" value="<?php echo $user->id; ?>">
							<div class="row">
								<div class="col-md-4">
										<h3>New Password</h3>
										<input type="password" class="form-control required2" minlength="6" name="pswd" placeholder="Please enter a valid password" value="">
								</div>
							</div>
							<div class="row">
								<div class="col-md-4">
										<h3>Confirm Password</h3>
										<input type="password" class="form-control required2" minlength="6" name="pswd2" placeholder="Enter your password again here to confirm" value="">
								</div>
							</div>
                            <div class="row">
                                <div class="col-md-6">
                                    <br>
                                    <button type="submit" class="btn btn-xs btn-flat btn-info"><i class="fa fa-save"></i> Update</button>
                                </div>
                            </div>
                        </form>
                        
                    </div>
                    <!-- <div id="dashboard" class="tab-pane fade">
                        <h2></h2>
                        <p class='notes'>Currently not available. This will allow each user to customise his/her dashboard</p>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</div>


