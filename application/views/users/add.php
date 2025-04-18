<div class="row">
    <div class="col-md-6">
        <div class="card card-secondary">
            <!-- <div class="card-header">
				<h3 class="card-title">Quick Example1</h3>
			</div> -->
            <!-- /.card-header -->
            <!-- form start -->
            <form id="add_user" action="users/save" method="post">
                <div class="card-body">
                    <div class="form-group">
						<label for="">Name</label>
						<input type="text" class="form-control required" name="name" data-name="Name" placeholder="Enter your name" data-min-length='4' value="" autofocus>
                    </div>
                    <div class="form-group">
                        <label for="">Job Title</label>
                        <input type="text" class="form-control required" name="job_title" data-name="Job Title" placeholder="Enter your job title" data-min-length='4' value="">
                    </div>
					<div class="form-group">
                        <label for="">Email</label>
						<input type="email" class="form-control required" name="email" data-name="Email" placeholder="Enter email" data-min-length='4' value="">
                    </div>
					<div class="form-group">
                        <label for="">Username</label>
						<input type="text" class="form-control required" name="username" data-name="Username" placeholder="Enter desired username" data-min-length='4' value="">
                    </div>
					<div class="form-group">
						<label for="">User Level</label>
						<select class="form-control required" name="level" data-name="User Level">
							<option value="" disabled>Select Level</option>
							<option value="Normal" selected>Normal</option>
							<option value="Admin">Admin</option>
							<?php if($_SESSION['user_level']=='Root'):?>
							<option value="Root">Root</option>
							<?php endif; ?>
						</select>
					</div>
					<input type="hidden" name="user_type" value="regular">
					<!-- <div class="form-group">
						<label for="">User Type</label>
						<p class="notes"><b>Regular</b> is the default and are users who will be able to login and use the application, whereas <b>Developer</b> & <b>Client</b> won't be able to login. They are used for notification about tasks creation and progress.</p>
						<select class="form-control required" name="user_type" data-name="User Level">
							<option value="" disabled>Select Type</option>
							<option value="regular" selected>Regular</option>
							<option value="developer">Developer</option>
							<option value="client">Client</option>
						</select>
					</div> -->
					<div class="form-group d-none">
						<label for="">Department</label>
						<!-- <select name="department_id" class="form-control required" data-name="Department" required>
							<option value="" disabled>Select</option>
							<?php foreach ($dpt as $row) : ?>
							<option value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option>
							<?php endforeach; ?>
						</select> -->
						<input type="hidden" name="department_id" value="1">
                    </div>

					<input type="hidden" name="generate_password" value="no">

					<div class="form-group">
						<!-- <label for="">Send Password to User?</label>
						<p class="notes">By selecting <b>Yes</b>, a random password will be generated and emailed to
							the news user's email. This is the preferred method as even the administrator creating
							the account won't see the password.</p>
						<p class="notes">By selecting <b>No</b>, you will have to enter or generate a password, then send it to the user.</p>
						<div class="form-group">
							<label for="gen_pwd_yes">
								<input type="radio" name="generate_password" minlength="6" value="yes" id="gen_pwd_yes" checked> Yes
							</label>
						</div>
						<div class="form-group">
							<label for="gen_pwd_no">
								<input type="radio" name="generate_password" minlength="6" value="no" id="gen_pwd_no"> No
							</label>
						</div> -->
						<div class="form-group">
							<label for="">Password</label>
							<div class="input-group">
								<input type="password" class="form-control" name="password" data-name="Password" placeholder="Type your password" data-min-length='8' value="">
								<div class="input-group-text generate-password cursor-pointer"><i class="fa fa-reset"></i> Generate</div>
							</div>
						</div>
						<div class="form-group">
							<label for="">Confirm Password</label>
							<input type="password" class="form-control" name="pswd2" data-name="Confirm Password" placeholder="Confirm your password" value="">
						</div>

					</div>
					<input type="hidden" name="status" value="ACTIVE" />
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                    <button type="submit" class="btn btn-success" id="save"><i class='fa fa-save'></i> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>