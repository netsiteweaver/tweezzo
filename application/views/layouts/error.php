<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo isset($page_title)?$page_title:"";?> || <?php echo $company->name; ?></title>

  <base href="<?php echo base_url();?>">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/dist/css/adminlte.min.css">
  <!-- My Custom CSS based on AdminLTE -->
  <link rel="stylesheet" href="<?php echo base_url('assets/css/adminlte_custom.min.css?').date('YmdHis');?>">
  <!-- Favicon -->
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo base_url('assets/favicon/apple-touch-icon.png');?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url('assets/favicon/favicon-32x32.png');?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url('assets/favicon/favicon-16x16.png');?>">
  <link rel="manifest" href="<?php echo base_url('assets/favicon/site.webmanifest');?>">

  <script>
    var base_url = "<?php echo base_url();?>";
    var front_url = base_url.substring(0,(base_url.length-6));
  </script>
</head>
<body class="hold-transition sidebar-mini layout-fixed <?php echo ((isset($sidebar_collapse))&&($sidebar_collapse==1))?"sidebar-collapse":"";?>">
<!-- Site wrapper -->
<div class="wrapper">
  <audio id="error-sound" src="<?php echo base_url("assets/audio/error-sound.wav");?>" preload="auto"></audio>
  <audio id="notify-bell" src="<?php echo base_url("assets/audio/notify-bell.wav");?>" preload="auto"></audio>  
  <!-- Navbar -->
  <?php $this->load->view("shared/topbar",$this->data);?>
  <!-- /.navbar -->

  <?php $this->load->view("shared/leftsidebar",$this->data);?>


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><?php echo isset($page_title)?$page_title:"&nbsp;"; ?></h1>
          </div>
          <div class="col-sm-6">
            <?php echo (isset($breadcrumbs))?$breadcrumbs:""; ?>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
      <?php if( (isset($content)) && (!empty($content)) ){
        if(is_array($content)){
          foreach($content as $idx => $block){
            echo "\r\n<!-- BLOCK CONTENT ".($idx+1)." STARTS HERE -->\r\n";
            echo $block;
            echo "\r\n<!-- END OF BLOCK CONTENT ".($idx+1)." -->\r\n";
          }
        }else{
          echo $content;
        }
      }?>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view("shared/footer",$this->data);?>

</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/dist/js/adminlte.min.js"></script>
<!-- Bootbox -->
<script src="<?php echo base_url("assets/js/bootbox.min.js"); ?>"></script>

<script src="<?php echo base_url('assets/js/main.min.js')."?".date("YmdHis"); ?>"></script>

<!-- AUTO LOADER FOR JS -->
<!-- Looks for a js whose name matches the controller + method and loads it if found -->
<!-- 2019-04-08: Looks for a minified version first -->
<?php
    $jsFileControllerMethod         = "assets/js/pages/".$controller."_".$method.".js";
    $jsMinifiedFileControllerMethod = "assets/js/pages/".$controller."_".$method.".min.js";
    $jsFileController               = "assets/js/pages/".$controller.".js";
    $jsMinifiedFileController       = "assets/js/pages/".$controller.".min.js";
?>
<?php if(file_exists($jsMinifiedFileControllerMethod)): ?>
    <script src="<?php echo base_url($jsMinifiedFileControllerMethod)."?".date("YmdHis"); ?>"></script>
<?php elseif(file_exists($jsFileControllerMethod)):?>
    <script src="<?php echo base_url($jsFileControllerMethod)."?".date("YmdHis"); ?>"></script>
<?php elseif(file_exists($jsMinifiedFileController)):?>
    <script src="<?php echo base_url($jsMinifiedFileController)."?".date("YmdHis"); ?>"></script>
<?php elseif(file_exists($jsFileController)):?>
    <script src="<?php echo base_url($jsFileController)."?".date("YmdHis"); ?>"></script>
<?php endif; ?>
<?php if($_SESSION['user_level'] !== "Normal"):?>
<script src="<?php echo base_url('assets/js/monitor.js')."?".date("YmdHis"); ?>"></script>
<?php endif;?>
</body>
</html>
