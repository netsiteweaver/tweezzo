<div style="margin:0 auto;max-width:800px;">
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
                <td>
                    <h3 style="margin-top:0;">Tasks Awaiting Validation</h3>
                    <p>
                        Hello,
                    </p>
                    <p>
                        This is a reminder that <strong><?php echo intval($staging_count); ?></strong>
                        task(s) are currently in <strong>staging</strong> for:
                    </p>
                    <ul>
                        <li><strong>Customer:</strong> <?php echo htmlspecialchars($customer_name); ?></li>
                        <li><strong>Project:</strong> <?php echo htmlspecialchars($project_name); ?></li>
                        <li><strong>Sprint:</strong> <?php echo htmlspecialchars($sprint_name); ?></li>
                    </ul>
                    <p style="padding:12px;background:#f8f9fa;border-left:4px solid #0d6efd;margin:16px 0;">
                        <strong>How we release:</strong> Changes are deployed to our <strong>staging</strong> environment first so you can review them.
                        We only push to the <strong>production</strong> server <strong>after</strong> you validate the task in the portal (or after we address any feedback from a rejection).
                    </p>
                    <p>
                        Please review and validate or reject the tasks from the customer portal:
                    </p>
                    <p>
                        <a href="<?php echo $tasks_link; ?>" style="display:inline-block;padding:10px 14px;background:#0d6efd;color:#ffffff;text-decoration:none;border-radius:4px;">
                            Review Staging Tasks
                        </a>
                    </p>
                    <p style="margin-bottom:0;">
                        If validation is already in progress, you can ignore this reminder.
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
