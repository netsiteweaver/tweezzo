<?php
/**
 * Generic helpdesk email body. Expects: title, intro, ticket, message (nullable), link, link_label,
 * show_details (optional).
 */
$e = function ($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); };
?>
<div style="margin:0px auto;max-width:800px;font-family:Helvetica, Arial, sans-serif;color:#434245;">
    <h3 style="text-align:center;text-transform:uppercase;"><?php echo $e($title); ?></h3>

    <p style="font-size:16px;line-height:24px;"><?php echo $e($intro); ?></p>

    <table align="center" border="1" cellpadding="8" cellspacing="0" role="presentation" style="width:100%;border-color:#ddd;font-size:15px;">
        <tbody>
            <tr>
                <th style="text-align:left;width:160px;background:#f6f6f6;">TICKET</th>
                <td><?php echo $e($ref); ?> — <?php echo $e($ticket->subject); ?></td>
            </tr>
            <?php if (!empty($show_details)): ?>
            <tr>
                <th style="text-align:left;background:#f6f6f6;">PRIORITY</th>
                <td><?php echo $e(Tickets_model::priorityLabel($ticket->priority)); ?></td>
            </tr>
            <tr>
                <th style="text-align:left;background:#f6f6f6;">STATUS</th>
                <td><?php echo $e(Tickets_model::statusLabel($ticket->status)); ?></td>
            </tr>
            <?php if (!empty($ticket->category_name)): ?>
            <tr>
                <th style="text-align:left;background:#f6f6f6;">CATEGORY</th>
                <td><?php echo $e($ticket->category_name); ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <th style="text-align:left;background:#f6f6f6;">FROM</th>
                <td>
                    <?php echo $e(Tickets_model::requesterName($ticket) ?: '—'); ?>
                    <?php if (!empty($ticket->contact_email)): ?>&lt;<?php echo $e($ticket->contact_email); ?>&gt;<?php endif; ?>
                    <?php if (!empty($ticket->company_name)): ?><br><?php echo $e($ticket->company_name); ?><?php elseif (!empty($ticket->requester_company)): ?><br><?php echo $e($ticket->requester_company); ?><?php endif; ?>
                </td>
            </tr>
            <?php endif; ?>
            <?php if (!empty($message)): ?>
            <tr>
                <th style="text-align:left;vertical-align:top;background:#f6f6f6;">MESSAGE</th>
                <td><?php echo Tickets_model::formatBody($message); ?></td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if (!empty($link)): ?>
    <p style="text-align:center;margin:30px 0;">
        <a class="btn" href="<?php echo $e($link); ?>" style="text-decoration:none;color:#ffffff;">
            <span class="label" style="background-color:rgb(31, 89, 156);padding:10px 20px;border:1px solid #091b36;border-radius:3px;text-transform:uppercase;"><?php echo $e($link_label); ?></span>
        </a>
    </p>
    <?php endif; ?>

    <p style="font-size:13px;color:#888;text-align:center;">Please keep the reference <?php echo $e($ref); ?> in any correspondence about this request.</p>
</div>
