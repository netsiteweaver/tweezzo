<nav class="navbar sticky-nav navbar-expand-lg navbar-light bg-light portal-navbar-developers">
    <div class="container-fluid portal-navbar-inner">
        <a class="portal-logo-link" href="portal/developers/tasks">
            <img src="assets/images/<?php echo $logoDark;?>" alt="" class="portal-logo-img">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo (!in_array($this->uri->segment(3),['myCustomers','myProjects','mySprints','notes','timesheets']))?'active':'';?>" href="portal/developers/tasks"><i class="bi bi-list-check me-2"></i>Tasks</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($this->uri->segment(3)=='myCustomers')?'active':'';?>" href="portal/developers/myCustomers"><i class="bi bi-people me-2"></i>Customers</a>
                </li>
                <!--<li class="nav-item">
                    <a class="nav-link <?php echo ($this->uri->segment(3)=='myProjects')?'active':'';?>" href="portal/developers/myProjects"><i class="bi bi-folder2-open me-2"></i>Projects</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($this->uri->segment(3)=='mySprints')?'active':'';?>" href="portal/developers/mySprints"><i class="bi bi-trophy me-2"></i>Sprints</a>
                </li>-->
                <li class="nav-item">
                    <a class="nav-link <?php echo ($this->uri->segment(3)=='notes')?'active':'';?>" href="portal/developers/notes"><i class="bi bi-chat-dots me-2"></i>Notes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($this->uri->segment(3)=='timesheets')?'active':'';?>" href="portal/developers/timesheets"><i class="bi bi-clock-history me-2"></i>Timesheets</a>
                </li>
                <div class="nav-item">
                    <a href="portal/developers/signout" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i>Signout</a>
                </div>
            </ul>
            <ul class="navbar-nav mb-2 mb-lg-0 align-items-center">
                <li class="nav-item portal-nav-separator" aria-hidden="true"></li>
                <li class="nav-item cursor-pointer add-task portal-nav-action">
                    <span class="portal-nav-action-label"><i id="submitTask" class="bi bi-plus-circle-fill portal-nav-icon" aria-hidden="true"></i><span class="portal-nav-action-text">Submit Task</span></span>
                </li>
            </ul>
            <ul class="navbar-nav mb-2 mb-lg-0 ms-auto align-items-center portal-user-info">
                <li class="nav-item">
                    <span class="nav-link py-0"><?php echo htmlspecialchars($_SESSION['developer_name']); ?> &lt;<?php echo htmlspecialchars($_SESSION['developer_email']); ?>&gt;</span>
                </li>
            </ul>
            <!-- <form class="d-flex">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form> -->

            <ul id="running-task" class="navbar-nav mb-2 mb-lg-0 <?php echo (empty($running_task)) ? 'd-none' : '';?>">
                <a title='View running task' href="portal/developers/view?task_uuid=<?php echo $running_task;?>" style="color:red;">
                <i class="fa fa-stop-circle"></i>
                </a>
            </ul>
        </div>
    </div>
</nav>

<?php if(!empty($flash_danger)):?>
<div class="row">
    <div class="col-md-12 alert alert-danger text-center text-bold">
        <i class="fa fa-exclamation-triangle"></i> <?php echo $flash_danger;?>
    </div>
</div>
<?php endif;?>
<?php if(!empty($flash_success)):?>
<div class="row">
    <div class="col-md-12 alert alert-success text-center text-bold">
        <i class="fa fa-check-square"></i> <?php echo $flash_success;?>
    </div>
</div>
<?php endif;?>
<?php if(!empty($flash_warning)):?>
<div class="row">
    <div class="col-md-12 alert alert-warning text-center text-bold">
        <i class="fa fa-exclamation-circle"></i> <?php echo $flash_warning;?>
    </div>
</div>
<?php endif;?>
<?php if(!empty($flash_info)):?>
<div class="row">
    <div class="col-md-12 alert alert-info text-center text-bold">
        <i class="fa fa-info-circle"></i> <?php echo $flash_info;?>
    </div>
</div>
<?php endif;?>