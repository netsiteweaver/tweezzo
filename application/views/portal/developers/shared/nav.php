<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a href="portal/developers/tasks">
            <img src="assets/images/<?php echo $logo;?>" alt="" style='height:25px;'>
        </a>
        <!-- <a class="navbar-brand" href="#">Task Manager <span class="notes">v1.0</span></a> -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo (!in_array($this->uri->segment(3),['myCustomers','myProjects','mySprints']))?'active':'';?>" href="portal/developers/tasks"><i class="bi bi-list-task"></i> Tasks</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($this->uri->segment(3)=='myCustomers')?'active':'';?>" href="portal/developers/myCustomers"><i class="bi bi-people"></i> Customers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($this->uri->segment(3)=='myProjects')?'active':'';?>" href="portal/developers/myProjects"><i class="bi bi-list-task"></i> Projects</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($this->uri->segment(3)=='mySprints')?'active':'';?>" href="portal/developers/mySprints"><i class="bi bi-123"></i> Sprints</a>
                </li>
                <!-- <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Dropdown
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#">Action</a></li>
                        <li><a class="dropdown-item" href="#">Another action</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                </li> -->
                <!-- <li class="nav-item">
                    <a class="nav-link" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
                </li> -->
                <div class="nav-item">
                    <a href="portal/developers/signout" class="nav-link"><i class="bi bi-door-open"></i> Signout</a>
                </div>
            </ul>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item cursor-pointer add-task" style='margin-right:25px;'>
                    <img src="assets/images/more-32px.png" style='width:32px;height:32px;' alt="">
                </li>
                <!-- <li class="nav-item cursor-pointer add-user-access">
                    <img src="assets/images/contact-32px.png" style='width:32px;height:32px;' alt="">
                </li> -->
            </ul>
            <!-- <form class="d-flex">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form> -->
        </div>
    </div>
</nav>
<div class="row">
    <div class="col-md-12" style='position:relative;'>
        <p class="logged-in-user"><?php echo "{$_SESSION['developer_name']} &lt;{$_SESSION['developer_email']}&gt";?></p>
    </div>
</div>