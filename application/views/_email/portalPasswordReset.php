<div style='margin:30px auto; max-width:800px;'>
    <h3>Your Portal Password Has Been Reset</h3>
    <p>Hello <?php echo $name;?>,</p>
    <p>This is to inform you that your password to access the <strong><?php echo $company_name;?></strong> customer portal has been reset.</p>
    <div style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #007bff; margin: 20px 0;">
        <p style="margin: 0;"><strong>Your New Password:</strong></p>
        <p style="font-size: 18px; font-family: 'Courier New', monospace; margin: 10px 0 0 0; color: #007bff;"><b><?php echo $password;?></b></p>
    </div>
    <p>Please use this password to log in to the customer portal. For security reasons, we recommend changing your password after logging in.</p>
</div>
<div style='margin:30px auto; max-width:800px;'>
    <a class='btn' href="<?php echo $link;?>">
        <div class="label"><?php echo $link_label;?></div>
    </a>
</div>

