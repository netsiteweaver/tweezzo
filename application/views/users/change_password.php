<style>
#user .main-container{
	background: none;
}
</style>

    <div class="col-xs-10 col-xs-offset-1 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
      <form id="form_signin" class="form-signin" action="<?php echo base_url('user/saveNewPassword'); ?>" method="post">
        <?php if($status == "error"): ?>
          <div class="label">

          </div>
        <?php endif; ?>
        <h2 class="form-signin-heading">Please enter new password</h2>
        <label for="inputEmail" class="sr-only">Password</label>
        <input type="hidden" name="reset_token" value="<?php echo $reset_token; ?>">
        <input type="text" id="newPassword" name="newPassword" class="form-control required" value="" placeholder="New Password" autofocus>
        <button class="btn btn-lg btn-default btn-block" type="submit" id="signin">Proceed</button>
      </form>
	  
    </div>



