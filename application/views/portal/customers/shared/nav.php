<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a href="portal/customers/tasks">
            <img src="assets/images/<?php echo $logoDark;?>" alt="" style='height:25px;'>
        </a>
        <!-- <a class="navbar-brand" href="#">Task Manager <span class="notes">v1.0</span></a> -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mb-2 mb-lg-0">
                
                <li class="nav-item">
                    <a class="nav-link <?php echo ($this->uri->segment(3)=='projects')?'active':'';?>" href="portal/customers/projects"><div class="bg-icon bg-project_management"></div> Projects</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($this->uri->segment(3)=='sprints')?'active':'';?>" href="portal/customers/sprints"><div class="bg-icon bg-race"></div>Sprints</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($this->uri->segment(3)=='tasks')?'active':'';?>" href="portal/customers/tasks"><div class="bg-icon bg-clipboard"></div>Tasks</a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link <?php //echo ($this->uri->segment(3)=='notes')?'active':'';?>" href="portal/customers/notes"><div class="bg-icon bg-chat"></div>Notes</a>
                </li> -->
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
                    <a href="portal/customers/signout" class="nav-link"><div class="bg-icon bg-logout"></div>Signout</a>
                </div>
            </ul>
            <ul class="navbar-nav mb-2 mb-lg-0">
                <li class="nav-item cursor-pointer add-task" style='margin-right:25px;'>
                    <img id="submitTask" style="width:24px;" src="assets/images/add_task_48px.png" alt="">
                    <!-- <div id="submitTask" class="bg-icon bg-add_task"></div> -->
                </li>
                <li class="nav-item cursor-pointer add-user-access">
                    <img id="addUser" style="width:24px;" src="assets/images/add-user-48px.png" alt="">
                </li>
            </ul>
            <ul class="navbar-nav mb-2 mb-lg-0 ms-auto align-items-center">
                <?php if (!empty($_SESSION['customer_company_name'])): ?>
                <li class="nav-item me-3">
                    <span class="nav-link py-0" style="font-weight:600;"><?php echo htmlspecialchars($_SESSION['customer_company_name']); ?></span>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <span class="nav-link py-0"><?php echo htmlspecialchars($_SESSION['customer_name']); ?> &lt;<?php echo htmlspecialchars($_SESSION['customer_email']); ?>&gt;</span>
                </li>
            </ul>
            <!-- <form class="d-flex">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form> -->
        </div>
    </div>
</nav>