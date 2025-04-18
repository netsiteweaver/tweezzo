<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Access Prohibited<?php //echo isset($page_title)?$page_title:"";?> | <?php echo $company->name; ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/vendors/adminlte/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/vendors/adminlte/bower_components/select2/dist/css/select2.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/vendors/adminlte/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/vendors/adminlte/bower_components/Ionicons/css/ionicons.min.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/vendors/adminlte/bower_components/jvectormap/jquery-jvectormap.css">
    <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/vendors/adminlte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
<!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/vendors/adminlte/dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/vendors/adminlte/dist/css/skins/_all-skins.min.css">
  <!-- My Custom CSS based on AdminLTE -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/css/adminlte_custom.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/vendors/toastr/build/toastr.min.css">
  <link rel="shortcut icon" href="<?php echo base_url('favicon.ico'); ?>" type="image/x-icon" />
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <script>
    var base_url = "<?php echo base_url();?>";
    var flashSuccess = "<?php echo $flashSuccess; ?>"
    var flashDanger = "<?php echo $flashDanger; ?>"
  </script>
</head>
<body class="hold-transition <?php echo ($sidebar_collapse==1)?"sidebar-collapse":"";?> skin-red sidebar-mini">
<div id="overlay-wrapper" class="hidden">
    <div class='text-center'>
        <img src='<?php echo base_url('assets/images/loader.svg'); ?>'>
    </div>
</div>  
<div class="wrapper">

  <header class="main-header">

    <!-- Logo -->
    <a href="#" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini">CP</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg">Control Panel</span>
    </a>

    <!-- Header Navbar: style can be found in header.less -->
    <?php $this->load->view("shared/topnav",$this->data);  ?>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <?php $this->load->view("shared/leftnav",$this->data);  ?>
  <!-- <?php //echo isset($leftnav)?$leftnav:"";?> -->
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><?php echo isset($page_title)?$page_title:"&nbsp;"; ?></h1>
      <?php echo (isset($breadcrumbs))?$breadcrumbs:""; ?>
    </section>

    <!-- Main content -->
    <section class="content">
    <?php echo isset($content)?$content:"No Content";?>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <!-- Control Sidebar -->
  <?php $this->load->view("shared/rightaside",$this->data,true);?>
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>

</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="<?php echo base_url();?>assets/vendors/adminlte/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url();?>assets/vendors/adminlte/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Select2 -->
<script src="<?php echo base_url();?>assets/vendors/adminlte/bower_components/select2/dist/js/select2.full.min.js"></script>
<!-- Bootbox -->
<script src="<?php echo base_url("assets/js/bootbox.min.js"); ?>"></script>
<!-- DataTables -->
<script src="<?php echo base_url();?>assets/vendors/adminlte/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url();?>assets/vendors/adminlte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- FastClick -->
<script src="<?php echo base_url();?>assets/vendors/adminlte/bower_components/fastclick/lib/fastclick.js"></script>
<!-- Numeral Js -->
<script src="<?php echo base_url();?>assets/vendors/Numeral-js/numeral.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url();?>assets/vendors/adminlte/dist/js/adminlte.min.js"></script>
<!-- Sparkline -->
<script src="<?php echo base_url();?>assets/vendors/adminlte/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap  -->
<script src="<?php echo base_url();?>assets/vendors/adminlte/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="<?php echo base_url();?>assets/vendors/adminlte/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- SlimScroll -->
<script src="<?php echo base_url();?>assets/vendors/adminlte/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- ChartJS -->
<script src="<?php echo base_url();?>assets/vendors/adminlte/bower_components/chart.js/Chart.js"></script>
<!-- Toastr -->
<script src="<?php echo base_url();?>assets/vendors/toastr/build/toastr.min.js"></script>

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!-- <script src="<?php echo base_url();?>assets/vendors/adminlte/dist/js/pages/dashboard2.js"></script> -->
<!-- AdminLTE for demo purposes -->
<script src="<?php echo base_url();?>assets/vendors/adminlte/dist/js/demo.js"></script>
<script src="<?php echo base_url('assets/js/ekko-lightbox.min.js'); ?>"></script>
<script>
    $(document).delegate('*[data-toggle="lightbox"]', 'click', function(event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });            
</script>

    <!-- AUTO LOADER FOR JS -->
    <!-- Looks for a js whose name matches the controller + method and loads it if found -->
    <?php
        $jsFileControllerMethod = "assets/js/pages/".$controller."_".$method.".js";
        $jsFileController = "assets/js/pages/".$controller.".js";
    ?>
    <?php if(file_exists($jsFileControllerMethod)): ?>
        <script src="<?php echo base_url($jsFileControllerMethod)."?".date("YmdHis"); ?>"></script>
    <?php elseif(file_exists($jsFileController)):?>
        <script src="<?php echo base_url($jsFileController)."?".date("YmdHis"); ?>"></script>
    <?php endif; ?>
    <script src="<?php echo base_url('assets/js/main.js'); ?>"></script>
</body>
</html>
