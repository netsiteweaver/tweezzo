<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo isset($page_title)?$page_title:"";?> | <?php echo $company->name; ?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/dist/css/adminlte.min.css">
    <!-- Login Page -->
    <link rel="stylesheet" href="<?php echo base_url("assets/css/login.css");?>">
    <link rel="shortcut icon" href="<?php echo base_url('assets/favicon/favicon.ico'); ?>" type="image/x-icon" />
    <script>
    
    </script>

</head>

<body class="hold-transition login-page">

    <audio id="failed" src="<?php echo base_url("assets/audio/failed.wav");?>" preload="auto"></audio>  
    <audio id="success" src="<?php echo base_url("assets/audio/success.wav");?>" preload="auto"></audio>  

    <video id="background-video" autoplay loop muted poster="<?php echo base_url('assets/videos/'.$video['poster']);?>">
        <source src="<?php echo base_url("assets/videos/".$video['filename']);?>" type="video/mp4">
    </video>

    <div class="login-box">
        <div class="login-logo">
            <a href="https://tweezzo.online" target="_blank">
                <img src="<?php echo base_url("assets/images/".$logo);?>" alt="Logo" style='width:100%'>
            </a>
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body">
            <div class="login-container">
                <p class="login-box-msg">Sign in to start your session</p>
                <p id="message" class="text-center" style="display:none;"><span style='color:#ff0000;'><i
                            class='fa fa-warning'></i></span> CAUTION: Caps Lock is ON</p>
                <form name="signin" action="<?php echo base_url('users/authenticate'); ?>" method="post">
                    <div class="form-group has-feedback">
                        <label for="">Enter Username</label>
                        <input type="text" class="form-control required1" data-name="Username" placeholder="Enter Username"
                            name="inputEmail" <?php echo isset($username)?"":"autofocus";?>
                            value="<?php echo isset($username)?$username:"";?>">
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                        <p class="error" style="color:red"></p>
                    </div>
                    <div class="form-group has-feedback">
                        <label for="">Enter Password</label>
                        <input type="password" class="form-control required1" data-name="Password" placeholder="Enter Password"
                            name="inputPassword" <?php echo isset($username)?"autofocus":"";?>>
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        <p class="error" style="color:red"></p>
                    </div>
                    
                    <div class="row">
                        <div class="col-xs-4">
                            <button type="submit" id="signin" class="btn btn-primary btn-block btn-flat"><i
                                    class="fas fa-sign-in-alt"></i> Sign In
                            </button>
                        </div>
                        <div class="col-xs-8">
                            <div class="float-right">
                                <label for="remember-me">
                                <input type="checkbox" style='margin-left:50px; margin-top: 10px; width:20px;height:20px;' id="remember-me">Remember Me</label>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
                
                <a style="font-size:0.8em; color:#999;" href="<?php echo base_url('users/forget_password');?>"
                    id="forget_password">Forgot Password ?</a>

                <p id="result" class="text-center"></p>
            </div>
            <!-------------------------------------------------------------->
            <div class="forgetpassword-container d-none">
                <p class="login-box-msg">Enter your username to reset your password</p>
                <p id="result" class="text-center"></p>
                <form name="reset" action="<?php echo base_url('users/forget_password_process'); ?>" method="post">
                    <div class="form-group has-feedback">
                        <input type="text" class="form-control required" id="username_to_reset" placeholder="Username"
                            name="inputEmail" value="" autofocus>
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                        <p style='font-size:0.8em;color:#999;padding:10px;'>An email will be sent only if the email
                            provided exists in our active records. You will need the newly generated password to signin.
                            We strongly recommend that you change your password immediately by accessing your profile
                            from the dropdown on top-right.</p>
                        <p class="error" style="color:red"></p>
                    </div>
                    <div class="row">
                        <div class="col-xs-4">
                            <button type="submit" id="reset_password"
                                class="btn btn-success btn-block btn-flat">Proceed</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
                <a style="font-size:0.8em; color:#999;" href="<?php echo base_url('users/forget_password');?>"
                    id="back_to_signin"><i class="fa fa-angle-left"></i> Back to Sign In</a>
            </div>

        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->
    <div id="controls">
        <div class="btn btn-outline-info btn-flat"><i class="fa fa-play"></i></div>
        <div class="btn btn-info btn-flat"><i class="fa fa-pause"></i></div>
    </div>

    <!-- jQuery -->
    <script src="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/plugins/bootstrap/js/bootstrap.bundle.min.js">
    </script>
    <script>
      var base_url = "<?php echo base_url();?>";
    </script>
    <script src="<?php echo base_url("assets/js/pages/login.js?t=".date("YmdHis"));?>"></script>
</body>

</html>