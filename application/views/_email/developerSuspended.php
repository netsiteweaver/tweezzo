<div style='width:100%; text-align: center;'>
    <h3>Developer Account Suspended</h3>
</div>
<div style="margin:0px auto;max-width:800px;">
    <p>Dear <?php echo htmlspecialchars($user->name ?? 'Developer'); ?>,</p>
    <?php if(!empty($thresholdDays)): ?>
    <p>
        We noticed no activity from your account for the past <?php echo (int)$thresholdDays; ?> days.
        Your developer account has been temporarily suspended. As a result, you will not be able to access
        the portal.
    </p>
    <?php else: ?>
    <p>
        Your developer account has been suspended by the administrator. As a result, you will not be able to
        access the portal.
    </p>
    <?php endif; ?>
    <p>
        If you believe this is a mistake or you would like to reactivate your access,
        please contact the administrator.
    </p>
    <p>Thank you.</p>
</div>

