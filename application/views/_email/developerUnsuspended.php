<div style='width:100%; text-align: center;'>
    <h3>Developer Account Reactivated</h3>
</div>
<div style="margin:0px auto;max-width:800px;">
    <p>Dear <?php echo htmlspecialchars($user->name ?? 'Developer'); ?>,</p>
    <p>
        Good news! Your developer account has been reactivated by the administrator.
        You can now access the developer portal and continue working on your tasks.
    </p>
    <p>
        If you have any questions or need assistance, please don't hesitate to contact us.
    </p>
    <p>Thank you.</p>
</div>

<div style='margin:30px auto; max-width:800px; text-align:center;'>
    <a style='text-decoration:none; display:inline-block; padding:15px 30px; background-color:#007bff; color:#fff; border-radius:5px; font-weight:bold;' href="<?php echo base_url('portal/developers/signin');?>">
        Sign In to Developer Portal
    </a>
</div>

