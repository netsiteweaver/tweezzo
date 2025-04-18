<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($page_title)?strip_tags($page_title) . " | ":"";?><?php echo $company->name; ?></title>

    <base href="<?php echo base_url();?>">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/dist/css/adminlte.min.css">
    <!-- My Custom CSS based on AdminLTE -->
    <link rel="stylesheet" href="<?php echo base_url('assets/css/adminlte_custom.min.css?').date('YmdHis');?>">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180"
        href="<?php echo base_url('assets/favicon/'.ENVIRONMENT.'/apple-touch-icon.png');?>">
    <link rel="icon" type="image/png" sizes="32x32"
        href="<?php echo base_url('assets/favicon/'.ENVIRONMENT.'/favicon-32x32.png');?>">
    <link rel="icon" type="image/png" sizes="16x16"
        href="<?php echo base_url('assets/favicon/'.ENVIRONMENT.'/favicon-16x16.png');?>">
    <link rel="manifest" href="<?php echo base_url('assets/favicon/'.ENVIRONMENT.'/site.webmanifest');?>">

    <script>
    var base_url = "<?php echo base_url();?>";
    </script>
</head>

    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-2">
                    <h1>TEST</h1>
                </div>
            </div>
        </div>
        

        <!-- jQuery -->
        <script src="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/plugins/jquery/jquery.min.js"></script>
        <!-- Bootstrap 4 -->
        <script src="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/plugins/bootstrap/js/bootstrap.bundle.min.js">
        </script>
        <!-- AdminLTE App -->
        <script src="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/dist/js/adminlte.min.js"></script>
        <script
            src="<?php echo base_url();?>assets/vendors/adminlte/bower_components/select2/dist/js/select2.full.min.js">
        </script>
    </body>

</html>