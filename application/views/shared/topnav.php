    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- <?php if(empty($_SESSION['authenticated_user']->email)):?>
          <li><a href="<?php echo base_url("users/myprofile?email");?>"><div class='badge bg-orange animated heartBeat infinite slow delay-1s'>ERROR</div></a></li>
          <?php endif;?> -->
                   
          <!-- <li><a href="<?php echo front_url();?>" target="_blank"><i class="fa fa-home"></i><span class='hidden-xs'> Visit Site</span></a></li> -->
          <!-- User Account: style can be found in dropdown.less -->
          <?php if($_SESSION['user_level'] !== "Normal"):?>

            <?php if( (isset($_SESSION['regions_in_no_route'])) && ($_SESSION['regions_in_no_route']>0) ):?>
            <li class="dropdown user user-menu" id="view-issues">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-warning"></i> Issues <span class="label label-danger"><?php echo count($_SESSION['regions_in_no_route']);?></span>
              </a>
            </li>
            <?php endif;?>

          <!-- <li class="hidden-xs dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-database"></i> Reset DB <i class="fa fa-caret-down"></i>
            </a>
            <ul class="dropdown-menu">
              <li>
                <a href="./reset/index">Click To Truncate Tables</a>
              </li>
            </ul>
          </li> -->
          <?php endif;?>
          <?php if($_SESSION['user_level'] !== "Normal"):?>
          <li class="dropdown hidden" id="notif">
            <a id="discount_request_settings">
              <i class="fa fa-microphone-slash hidden"></i>
              <i class="fa fa-cog"></i> Discount Request(s) <span class="badge"></span>
            </a>
          </li>
          <?php endif;?>
          <li class="hidden-xs dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-user"></i> Hi <?php echo $authenticated_user->name;?> !<i class="fa fa-caret-down"></i>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header text-left" style="text-align:left !important; color:#fff !important;">
                <div class="row">
                  <div class="col-md-12 text-center"><?php echo isset($authenticated_user->name)?$authenticated_user->name:"";?></div>
                </div>
                <div class="row">
                  <div class="col-md-4">Job Title</div>
                  <div class="col-md-8"><?php echo $authenticated_user->job_title;?></div>
                </div>
                <div class="row">
                  <div class="col-md-4">Level</div>
                  <div class="col-md-8"><?php echo $authenticated_user->user_level;?></div>
                </div>
                <div class="row">
                  <div class="col-md-4">Last Login</div>
                  <div class="col-md-8"><?php echo date_format(date_create($authenticated_user->last_login),'Y-m-d H:i:s');?></div>
                </div>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
              <?php if($perms['myprofile']): ?>
                <div class="pull-left">
                  <a href="<?php echo base_url("users/myprofile"); ?>" class="btn btn-info btn-flat bg-aqua"><i class="fa fa-user"></i> Profile</a>
                </div>
                <?php endif; ?>
                <div class="pull-right">
                  <a href="<?php echo base_url("users/signout"); ?>" class="btn btn-warning btn-flat bg-yellow">Sign out <i class="fa fa-sign-out"></i></a>
                </div>
              </li>
            </ul>
          </li>
          <!-- <li class='hidden-xs' id='goFullScreen'><a href="#"><i class='fa fa-expand'></i></a></li> -->
          <li class='visible-xs'><a href="<?php echo base_url("users/signout"); ?>"><i class="fa fa-sign-out"></i></a></li>
        </ul>
      </div>
      <?php if(ENVIRONMENT != 'production'):?>
      <div class="navbar-custom-menu" style="float: unset;margin-top: 15px;">
          <p style="color: #fff;text-align: center;font-weight: bold;"><?php echo strtoupper(ENVIRONMENT);?> PLATFORM</p>
      </div>
      <?php endif;?>
    </nav>