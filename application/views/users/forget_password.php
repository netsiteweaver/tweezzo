<?php
$status = $this->uri->segment(3);
?>
<?php if( (!empty($status)) && ($status == "error101") ): ?>
<div class="row">
    <div class="col-xs-10 col-xs-offset-1">
        <div class="alert alert-danger"><i class="glyphicon glyphicon-remove-sign"></i> Sorry, there was an error processing your request.</div>
    </div>
</div>
<?php endif; ?>

<?php if( (!empty($status)) && ($status == "success") ): ?>
<div class="row">
    <div class="col-xs-10 col-xs-offset-1">
        <div class="alert alert-success"><i class="glyphicon glyphicon-ok-sign"></i> Instructions has been sent to your email address.</div>
        <div class="btn btn-success"><a href="<?php echo base_url('user'); ?>">Proceed to Login</a></div>
    </div>
</div>
<?php else: ?>
    <div class="col-xs-10 col-xs-offset-1 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
      <form id="form_resetpassword" class="form-signin" action="<?php echo base_url('user/forget_password_process'); ?>" method="post">
        <?php if($status == "error"): ?>
          <div class="label">

          </div>
        <?php endif; ?>
        <h2 class="form-signin-heading">Please enter your email</h2>
        <p class="notes">and we will send you an email containing a link to reset your password</p>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="email" id="inputEmail" name="inputEmail" class="form-control required" value="" placeholder="Email address" autofocus>
        <button class="btn btn-lg btn-info pull-right" type="submit" id="signin">Proceed</button>
      </form>
	  
    </div>
<?php endif; ?>


