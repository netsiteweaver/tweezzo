<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Customer Portal | Task Manager</title>
    <base href="<?php echo base_url();?>">
    <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.2.0/css/all.css'>
    <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.2.0/css/fontawesome.css'>
    <link rel="stylesheet" href="./assets/css/portal/customers/signin.css?t=<?php echo date('YmdHis');?>">
    <!-- AlertifyJS -->
    <link rel="stylesheet" href="<?php echo base_url();?>node_modules/alertifyjs/build/css/alertify.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>node_modules/alertifyjs/build/css/themes/bootstrap.min.css">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/assets/favicon/customerPortal/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/assets/favicon/customerPortal/favicon.svg" />
    <link rel="shortcut icon" href="/assets/favicon/customerPortal/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/favicon/customerPortal/apple-touch-icon.png" />
    <link rel="manifest" href="/assets/favicon/customerPortal/site.webmanifest" />

    <!-- DOwnloaded from https://freefrontend.com/css-login-forms/ -->
    <!-- https://realfavicongenerator.net/ -->
</head>

<body>
    <!-- partial:index.partial.html -->
    <div class="container">
        <div class="screen">
            <div class="screen__content">
                <img style='position: absolute;
                            top: 20px;
                            left: 20px;
                            opacity: 0.9;
                            box-shadow: 3px 3px 3px #4c4c4c;
                            width: calc(100% - 40px);' src="assets/images/TASK-MANAGER-LOGO v1.0.png" alt="">
                <form class="login" method="post">
                    <div class='header'>Customer's Portal</div>
                    <div class="login__field">
                        <i class="login__icon fas fa-user"></i>
                        <input type="text" class="login__input" name='email' placeholder="Email"
                            value="<?php echo (!empty($this->input->get("email"))) ? $this->input->get("email") : '';?>" autofocus>
                    </div>
                    <div class="login__field">
                        <i class="login__icon fas fa-lock"></i>
                        <input type="password" class="login__input" name='password' placeholder="Password" value="<?php echo (!empty($this->input->get("email"))) ? '' : '';?>">
                    </div>
                    <button class="button login__submit">
                        <span class="button__text">Log In Now</span>
                        <i class="button__icon fas fa-chevron-right"></i>
                    </button>
                </form>
                <div class="forgot-password">
                    <p>Forgot Password ?</p>
                </div>
            </div>
            <div class="screen__background">
                <span class="screen__background__shape screen__background__shape4"></span>
                <span class="screen__background__shape screen__background__shape3"></span>
                <span class="screen__background__shape screen__background__shape2"></span>
                <span class="screen__background__shape screen__background__shape1"></span>
            </div>
        </div>
    </div>
    <!-- partial -->

    <script>
    var base_url = "<?php echo base_url();?>";
    </script>
    <!-- jQuery -->
    <script src="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/plugins/jquery/jquery.min.js"></script>
    <!-- Alertify -->
    <script src="<?php echo base_url('node_modules/alertifyjs/build/alertify.min.js'); ?>"></script>

    <script src="<?php echo base_url('assets/js/portal/customers/signin.js')."?".date("YmdHis"); ?>"></script>

</body>

</html>