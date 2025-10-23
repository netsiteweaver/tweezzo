<style>
    .info-box-text {

    }
    .info-box-number {
        position: absolute;
        right: 10px;
        top: 0px;
        font-size: 2em;
        float: right;
        background-color: #000;
        color: #fff;
        padding: 0px 15px;
        border-radius: 5px;
        box-shadow: 5px 5px 5px #ccc;
    }
    .info-box-link {

    }
    
    /* Online Users Dashboard Styles */
    .user-list {
        max-height: 300px;
        overflow-y: auto;
    }
    .online-user-item {
        padding: 8px;
        margin-bottom: 5px;
        background: #f4f4f4;
        border-radius: 4px;
        display: flex;
        align-items: center;
    }
    .online-user-item img {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        margin-right: 10px;
    }
    .online-user-item .user-info {
        flex: 1;
    }
    .online-user-item .user-name {
        font-weight: bold;
        font-size: 14px;
    }
    .online-user-item .user-email {
        font-size: 11px;
        color: #666;
    }
    .online-user-item .user-status {
        color: #28a745;
        font-size: 10px;
    }
</style>

<!-- Online Users Widget -->
<div class="row">
    <div class="col-md-12">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users"></i> Online Users</h3>
                <div class="card-tools">
                    <span class="badge badge-light" id="dashboard-online-count">0</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Admins -->
                    <div class="col-md-4">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-user-shield"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Admins</span>
                                <span class="info-box-number" id="admin-count">0</span>
                            </div>
                        </div>
                        <div id="admin-list" class="user-list"></div>
                    </div>
                    
                    <!-- Developers -->
                    <div class="col-md-4">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-code"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Developers</span>
                                <span class="info-box-number" id="developer-count">0</span>
                            </div>
                        </div>
                        <div id="developer-list" class="user-list"></div>
                    </div>
                    
                    <!-- Customers -->
                    <div class="col-md-4">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Customers</span>
                                <span class="info-box-number" id="customer-count">0</span>
                            </div>
                        </div>
                        <div id="customer-list" class="user-list"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="" id='dashboard-block'>
<?php foreach($dashboardItems as $db):?>
    <div class="row">
    <?php foreach($db as $item):?>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
            <span class="info-box-icon <?php echo $item['class'];?>"><i class="fa <?php echo $item['icon'];?>"></i></span>

            <div class="info-box-content" style="position:relative">
                <span class="info-box-text"><?php echo $item['label'];?></span>
                <div class="info-box-number"><?php echo $item['count'];?></div>
                <div class="info-box-link">
                    <a href="<?php echo base_url($item['link']);?>">More Info <i class='fa fa-arrow-circle-right'></i></a>
                </div>
                
            </div>
            <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>                
    <?php endforeach;?>
    </div>
<?php endforeach;?>
</div>
<!-- /.row -->          
