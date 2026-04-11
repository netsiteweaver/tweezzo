<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Global search (tasks) – always visible in top bar -->
    <div class="backoffice-global-search-wrapper" id="navbar-global-search">
      <div class="form-inline position-relative">
        <input class="form-control form-control-navbar" type="search" id="global-search-input" placeholder="Search tasks (e.g. WR-S3-001)…" aria-label="Search tasks" autocomplete="off">
        <div id="global-search-results" class="dropdown-menu dropdown-menu-lg shadow position-absolute w-100" style="top: 100%; left: 0; display: none; max-height: 70vh; overflow-y: auto; z-index: 1050;"></div>
      </div>
    </div>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Online Users Dropdown Menu -->
      <li class="nav-item dropdown" id="online-users-dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#" title="Online Users">
          <i class="fas fa-users"></i>
          <span class="badge badge-success navbar-badge" id="online-users-count">0</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="online-users-menu">
          <span class="dropdown-item dropdown-header">
            <i class="fas fa-circle text-success"></i> Online Users
          </span>
          <div class="dropdown-divider"></div>
          <div id="online-users-list">
            <div class="dropdown-item text-center text-muted">
              <i class="fas fa-spinner fa-spin"></i> Loading...
            </div>
          </div>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" id="admin-theme-toggle" role="button" aria-pressed="false" title="Switch theme">
          <i class="fas fa-moon" aria-hidden="true"></i><span class="sr-only">Toggle dark theme</span>
        </a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto d-none">
      <!-- Messages Dropdown Menu -->
      <li id="search-sn-result" class="nav-item dropdown d-none">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-comments"></i>
          <span class="badge badge-info navbar-badge"></span>
        </a>
        <div id="search-sn-results" class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <!-- <img src="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle"> -->
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Brad Diesel
                  <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">Call me whenever you can...</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  John Pierce
                  <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">I got your message bro</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Nora Silvester
                  <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">The subject goes here</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <!-- <a href="#" class="dropdown-item dropdown-footer">See All Messages</a> -->
        </div>
      </li>
      <!-- Notifications Dropdown Menu -->
      <li id='messages-dropdown-block' class="nav-item dropdown d-none">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-danger navbar-badge"><?php //echo count($unread_messages);?></span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header"><?php //echo count($unread_messages);?> Messages</span>
          <div class="messages-block">
          <?php //foreach($unread_messages as $message):?>
            <div class="dropdown-divider"></div>
            <a href="<?php //echo base_url("messages/view/".$message->uuid);?>" class="dropdown-item">
              <i class="fas fa-envelope mr-2"></i> <?php //echo $message->subject;?>
              <!-- <span class="float-right text-muted text-sm">3 mins</span> -->
            </a>
          <?php //endforeach;?>
          </div>
          <div class="dropdown-divider"></div>
          <a href="<?php echo base_url("messages/listing");?>" class="dropdown-item dropdown-footer">See All Messages</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <!-- <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li> -->
    </ul>
  </nav>