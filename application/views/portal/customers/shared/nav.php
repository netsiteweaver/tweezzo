<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid portal-navbar-inner">
        <a class="portal-logo-link" href="portal/customers/dashboard">
            <img src="assets/images/<?php echo $logoDark;?>" alt="" class="portal-logo-img">
        </a>
        <!-- <a class="navbar-brand" href="#">Task Manager <span class="notes">v1.0</span></a> -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="portal-navbar-search-wrapper d-flex align-items-center">
            <div class="position-relative" data-portal-search-url="<?php echo base_url('portal/customers/searchTasks'); ?>">
                <input class="form-control form-control-sm portal-global-search-input" type="search" placeholder="Search tasks (e.g. WR-S3-001)…" aria-label="Search tasks" autocomplete="off" style="min-width: 180px;">
                <div class="portal-global-search-results dropdown-menu shadow position-absolute start-0 mt-1" style="max-height: 70vh; overflow-y: auto; display: none;"></div>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mb-2 mb-lg-0">
                
                <li class="nav-item">
                    <a class="nav-link <?php echo ($this->uri->segment(3)=='dashboard')?'active':'';?>" href="portal/customers/dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo in_array($this->uri->segment(3), ['projects','sprints','tasks','validationGuide']) ? 'active' : '';?>" href="#" id="workMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-kanban me-2"></i>Work
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="workMenu">
                        <li><a class="dropdown-item <?php echo ($this->uri->segment(3)=='projects')?'active':'';?>" href="portal/customers/projects"><i class="bi bi-folder2-open me-2"></i>Projects</a></li>
                        <li><a class="dropdown-item <?php echo ($this->uri->segment(3)=='sprints')?'active':'';?>" href="portal/customers/sprints"><i class="bi bi-trophy me-2"></i>Sprints</a></li>
                        <li><a class="dropdown-item <?php echo ($this->uri->segment(3)=='tasks')?'active':'';?>" href="portal/customers/tasks"><i class="bi bi-list-check me-2"></i>Tasks</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item <?php echo ($this->uri->segment(3)=='validationGuide')?'active':'';?>" href="portal/customers/validationGuide"><i class="bi bi-journal-check me-2"></i>Validation guide</a></li>
                    </ul>
                </li>
                <?php if(!empty($project_environments)):?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="environmentsMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-globe2 me-2"></i>Environments
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="environmentsMenu">
                        <?php $multiple = (count($project_environments) > 1); $firstProject = true;?>
                        <?php foreach($project_environments as $env):?>
                            <?php if($multiple):?>
                                <?php if(!$firstProject):?><li><hr class="dropdown-divider"></li><?php endif;?>
                                <li><h6 class="dropdown-header"><?php echo htmlspecialchars($env->name);?></h6></li>
                            <?php endif;?>
                            <?php if(!empty($env->production_url)):?>
                                <li><a class="dropdown-item" href="<?php echo htmlspecialchars($env->production_url);?>" target="_blank" rel="noopener noreferrer"><i class="bi bi-rocket-takeoff me-2"></i>Production<i class="bi bi-box-arrow-up-right ms-2 small text-muted"></i></a></li>
                            <?php endif;?>
                            <?php if(!empty($env->staging_url)):?>
                                <li><a class="dropdown-item" href="<?php echo htmlspecialchars($env->staging_url);?>" target="_blank" rel="noopener noreferrer"><i class="bi bi-cone-striped me-2"></i>Staging<i class="bi bi-box-arrow-up-right ms-2 small text-muted"></i></a></li>
                            <?php endif;?>
                            <?php $firstProject = false;?>
                        <?php endforeach;?>
                    </ul>
                </li>
                <?php endif;?>
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
            </ul>
            <ul class="navbar-nav mb-2 mb-lg-0 align-items-center">
                <li class="nav-item portal-nav-separator" aria-hidden="true"></li>
                <li class="nav-item dropdown portal-nav-action">
                    <a class="nav-link dropdown-toggle portal-nav-action-label <?php echo ($this->uri->segment(3)=='submittedTasks')?'active':'';?>" href="#" id="submitTaskMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i id="submitTask" class="bi bi-plus-circle-fill portal-nav-icon" aria-hidden="true"></i><span class="portal-nav-action-text">Submit task</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="submitTaskMenu">
                        <li><button type="button" class="dropdown-item add-task"><i class="bi bi-plus-circle me-2"></i>New request</button></li>
                        <li><a class="dropdown-item" href="portal/customers/submittedTasks"><i class="bi bi-card-checklist me-2"></i>View submitted</a></li>
                    </ul>
                </li>
                <li class="nav-item cursor-pointer add-user-access portal-nav-action">
                    <span class="portal-nav-action-label"><i id="addUser" class="bi bi-person-plus-fill portal-nav-icon" aria-hidden="true"></i><span class="portal-nav-action-text">Add user</span></span>
                </li>
            </ul>
            <ul class="navbar-nav mb-2 mb-lg-0 ms-auto align-items-center portal-user-info">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle me-2"></i>
                        <span class="d-none d-md-inline">
                            <?php echo htmlspecialchars($_SESSION['customer_name']); ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                        <?php if (!empty($_SESSION['customer_company_name'])): ?>
                        <li class="dropdown-item-text">
                            <strong><?php echo htmlspecialchars($_SESSION['customer_company_name']); ?></strong>
                        </li>
                        <?php endif; ?>
                        <li class="dropdown-item-text text-muted small">
                            <?php echo htmlspecialchars($_SESSION['customer_name']); ?><br>
                            &lt;<?php echo htmlspecialchars($_SESSION['customer_email']); ?>&gt;
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item <?php echo ($this->uri->segment(3)=='myaccount')?'active':'';?>" href="portal/customers/myaccount">
                                <i class="bi bi-person-gear me-2"></i>My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-danger" href="portal/customers/signout">
                                <i class="bi bi-box-arrow-right me-2"></i>Sign out
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- <form class="d-flex">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form> -->
        </div>
    </div>
</nav>