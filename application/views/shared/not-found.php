<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6 text-center" style="margin-top:25vh; border:2px solid #ff0000; padding:30px; border-radius: 3px;">
        <h1 class="text-center" style='color:red;'><i class="fa fa-exclamation-triangle"></i> <?php echo $error404['message'];?></h1>
        <?php if(!empty($error404['url'])):?>
        <a href="<?php echo $error404['url'];?>"><div class="btn btn-default"><i class="fa fa-chevron-left"></i> Go Back</div></a>
        <?php endif;?>
    </div>
</div>