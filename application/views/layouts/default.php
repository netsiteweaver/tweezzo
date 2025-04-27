<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo isset($page_title)?strip_tags($page_title) . " | ":"";?><?php echo $company->name; ?></title>

  <base href="<?php echo base_url();?>">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Delius&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/dist/css/adminlte.min.css">
  <!-- My Custom CSS based on AdminLTE -->
  <link rel="stylesheet" href="<?php echo base_url('assets/css/adminlte_custom.min.css?').date('YmdHis');?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/css/stages.min.css?').date('YmdHis');?>">
  <!-- Toastr -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/vendors/toastr/build/toastr.min.css">
  <!-- AlertifyJS -->
  <link rel="stylesheet" href="<?php echo base_url();?>node_modules/alertifyjs/build/css/alertify.min.css">
  <link rel="stylesheet" href="<?php echo base_url();?>node_modules/alertifyjs/build/css/themes/bootstrap.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/vendors/adminlte/bower_components/select2/dist/css/select2.min.css">
  <!-- Summernote.js -->
  <link rel="stylesheet" href="<?php echo base_url();?>vendor/summernote/summernote.min.css" />
  <!-- daterange picker -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/vendors/adminlte/bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <!-- jQuery UI -->
  <link rel="stylesheet" href="<?php echo base_url("assets/vendors/adminlte/bower_components/jquery-ui/themes/base/jquery-ui.css");?>">  
  <!-- Lightbox2 -->
  <link rel="stylesheet" href="<?php echo base_url("node_modules/lightbox2/dist/css/lightbox.min.css");?>">  
  <!-- <link rel="stylesheet" href="<?php //echo base_url('assets/css/fonts.css?').date('YmdHis');?>"> -->
  
  <?php if(!empty($stylesheets)) foreach($stylesheets as $s):?>
  <!-- Additional StyleSheets -->
  <link rel="stylesheet" href="<?php echo $s;?>" />
  <!-- End of Additional StyleSheets -->
  <?php endforeach;?>

  <!-- Favicon -->
  <!-- https://www.favicon-generator.org/ -->
  <link rel="apple-touch-icon" sizes="57x57" href="assets/favicon/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="assets/favicon/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="assets/favicon/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="assets/favicon/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="assets/favicon/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="assets/favicon/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="assets/favicon/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="assets/favicon/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192"  href="assets/favicon/android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="assets/favicon/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
  <link rel="manifest" href="assets/favicon/manifest.json">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">

  <script>
    var base_url = "<?php echo base_url();?>";
    var front_url = base_url.substring(0,(base_url.length-6));
    var flashSuccess = "<?php echo $flashSuccess; ?>"
    var flashDanger = "<?php echo $flashDanger; ?>"
    var flashWarning = "<?php echo $flashWarning; ?>"
    var flashInfo = "<?php echo $flashInfo; ?>"
  </script>
</head>
<?php if($_SESSION['user_level'] == 'Normal'):?>
<body class="hold-transition sidebar-mini layout-fixed sidebar-collapse">
<?php else:?>
<body class="hold-transition sidebar-mini layout-fixed <?php echo ((isset($sidebar_collapse))&&($sidebar_collapse==1))?"sidebar-collapse":"";?>">
<?php endif;?>
<div id="overlay" class='hidden'><div class="loader"></div></div>
<!-- Site wrapper -->
<div class="wrapper">
  <audio id="error-sound" src="<?php echo base_url("assets/audio/error-sound.wav");?>" preload="auto"></audio>
  <audio id="notify-bell" src="<?php echo base_url("assets/audio/notify-bell.wav");?>" preload="auto"></audio>  
  <audio id="ding" src="<?php echo base_url("assets/audio/ding.wav");?>" preload="auto"></audio>  
  <!-- Navbar -->
  <?php $this->load->view("shared/topbar",$this->data);?>
  <!-- /.navbar -->

  <?php $this->load->view("shared/leftsidebar",$this->data);?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
      <?php $this->load->view("shared/alerts",$this->data);?>
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
            echo "\r\n";
            echo $block;
            echo "\r\n";
            echo "\r\n<!-- END OF BLOCK CONTENT ".($idx+1)." -->\r\n";
          }
        }else{
          echo "\r\n<!-- SINGLE BLOCK CONTENT STARTS HERE -->\r\n";
          echo "\r\n";
          echo $content;
          echo "\r\n";
          echo "\r\n<!-- END OF SINGLE BLOCK CONTENT -->\r\n";
        }
      }?>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php $this->load->view("shared/alerts",$this->data);?>

  <?php $this->load->view("shared/footer",$this->data);?>

  <?php $this->load->view("shared/_modalSelectUser",$this->data);?>

</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/dist/js/demo.js"></script>
<!-- Toastr -->
<script src="<?php echo base_url();?>assets/vendors/toastr/build/toastr.min.js"></script>
<!-- Moment.js -->
<script src="<?php echo base_url();?>assets/vendors/adminlte/bower_components/moment/min/moment.min.js"></script>
<!-- date-range-picker -->
<script src="<?php echo base_url();?>assets/vendors/adminlte/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- Select2 -->
<script src="<?php echo base_url();?>assets/vendors/adminlte/bower_components/select2/dist/js/select2.full.min.js"></script>
<!-- Bootbox -->
<script src="<?php echo base_url("assets/js/bootbox.min.js"); ?>"></script>
<!-- Alertify -->
<script src="<?php echo base_url('node_modules/alertifyjs/build/alertify.min.js'); ?>"></script>
<!-- Summernote -->
<script src="<?php echo base_url("vendor/summernote/summernote.min.js");?>"></script>
<!-- jQuery UI -->
<script src="<?php echo base_url("assets/vendors/adminlte/bower_components/jquery-ui/jquery-ui.min.js");?>"></script>
<!-- Lightbox2 -->
<script src="<?php echo base_url("node_modules/lightbox2/dist/js/lightbox.min.js");?>"></script>
<!-- html2pdf -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- ZOOM IMAGE -->
<!-- <script src="<?php echo base_url();?>assets/js/zoom_image.js"></script> -->

<script src="<?php echo base_url('assets/js/createModal.js')."?".date("YmdHis"); ?>"></script>
<script src="<?php echo base_url('assets/js/main.js')."?".date("YmdHis"); ?>"></script>
<script src="<?php echo base_url("assets/js/downloadTableAsCSV.js?t=".date("YmdHis"));?>"></script>
<script src="<?php echo base_url('assets/js/userSelectModal.js')."?".date("YmdHis"); ?>"></script>
<!-- <script src="<?php echo base_url('assets/js/messages.min.js')."?".date("YmdHis"); ?>"></script> -->

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

<?php if(!empty($scripts)) foreach($scripts as $sc):?>
<!-- Additional StyleSheets -->
<script src="<?php echo $sc;?>"></script>
<!-- End of Additional StyleSheets -->
<?php endforeach;?>

<?php if($_SESSION['user_level'] !== "Normal"):?>
<script src="<?php echo base_url('assets/js/monitor.js')."?".date("YmdHis"); ?>"></script>
<?php endif;?>

<!-- Modal -->
<div class="modal fade" id="version-history-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style="min-height:50vh;">
    <div id="modal-overlay" class="hidden"><div class="lds-dual-ring"></div></div>
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title text-center" id="exampleModalLongTitle">Version History</h3>
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->
      </div>
      <div class="modal-body">
        <table id="commit-history" class="table table-bordered">
          <thead>
            <tr>
              <th>DETAILS</th>
              <th>DATE</th>
              <?php if($_SESSION['user_level'] == 'Root'):?>
              <th>AUTHOR</th>
              <?php endif;?>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <div class="pull-left pagination"></div>
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
      </div>
    </div>
  </div>
</div>    

<!-- Modal Login -->
<div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <!-- <div id="modal-overlay" class="hidden"><div class="lds-dual-ring"></div></div> -->
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title text-center" id="exampleModalLongTitle">Login</h3>
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->
      </div>
      <div class="modal-body">
        <div id="result"></div>
        <div class="form-group">
          <label>Username or Email</label>
          <input type="text" class="form-control" name="modal-email" placeholder="Username or Email">
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" class="form-control" name="modal-password" placeholder="Password">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
        <div class="btn btn-info" id="modal-signin"><i class="fa fa-sign-in"></i> Sign In</div>
      </div>
    </div>
  </div>
</div>    

<!-- Modal Search Result -->
<div class="modal fade" id="search-result-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <!-- <div id="modal-overlay" class="hidden"><div class="lds-dual-ring"></div></div> -->
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title text-center" id="exampleModalLongTitle">Search Result</h3>
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
      </div>
    </div>
  </div>
</div> 

</body>
</html>
