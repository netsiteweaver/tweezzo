<div style="font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #4c4c4c; width:90%;max-width:800px; margin-left:5%;border:1px solid #eee;padding:25px;">
    <div style="width:100%;border-bottom:1px solid #ccc">
        <b><?php echo $userName;?></b> has moved <b><?php echo $productName;?> [<?php echo $stockref;?>]</b> in <b><?php echo $document_number;?></b> and is now at stage <b><?php echo $stageName;?> [<?php echo $stageAbbr;?>]</b>
<?php if(!empty($remarks)) :?>
            <div style='margin-top: 20px;margin-left:20px;margin-bottom:25px;font-weight:bold;'><?php echo "User also left the following message:"; ?>
                <div style='width:calc(100% - 60px);border:1px solid #ccc;padding:10px 20px;border-radius: 3px; font-weight: normal;'><?php echo nl2br($remarks);?></div>
            </div>
<?php endif;?>
    </div>
    <div style="margin-top:25px;">
        <a style='text-decoration: none; font-weight: bold;' href='<?php echo base_url("orders/view/".$orderUuid);?>'>
            <span style="margin-top:50px; margin-right:20px; font-family: Arial, Helvetica, sans-serif; font-size: 24px; background-color: cornflowerblue; color: #fff; text-align: center; padding: 10px 20px;">
                View Order <?php echo $document_number;?>
            </span>
        </a>
        <a style='text-decoration: none; font-weight: bold;' href='<?php echo base_url("users/signin");?>'>
            <span style="margin-top:50px; margin-right:20px; font-family: Arial, Helvetica, sans-serif; font-size: 24px; background-color: cornflowerblue; color: #fff; text-align: center; padding: 10px 20px;">
                Log In
            </span>
        </a>
    </div>
</div>