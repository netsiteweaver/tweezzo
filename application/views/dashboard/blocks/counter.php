<div class="info-box">
    <span class="info-box-icon <?php echo $item['class'];?>"><i class="fa <?php echo $item['icon'];?>"></i></span>
    <div class="info-box-content" style="position:relative">
        <span class="info-box-text"><?php echo $item['label'];?></span>
        <div class="info-box-number"><?php echo $item['count'];?></div>
        <div class="info-box-link">
            <a href="<?php echo base_url($item['link']);?>">More Info <i class='fa fa-arrow-circle-right'></i></a>
        </div>
    </div>
</div>
