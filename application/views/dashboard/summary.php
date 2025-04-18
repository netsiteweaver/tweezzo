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
</style>
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
    <hr style='border:3px solid #4c4c4c;'>
<?php endforeach;?>
</div>
<!-- /.row -->          
