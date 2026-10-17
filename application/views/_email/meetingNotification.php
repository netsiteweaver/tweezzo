<?php
// $kind: invite | reminder | live
$is_planned = ($meeting->status === 'planned');
$when = date('l d M Y \a\t H:i', strtotime($meeting->meeting_datetime));
$headings = [
    'invite'    => $is_planned ? "You're invited to a meeting" : "You've been invited to follow live meeting notes",
    'reminder'  => "Your meeting starts soon",
    'live'      => "The meeting has started",
];
$button = ($kind === 'live' || !$is_planned) ? 'Follow Live Notes' : 'Open Meeting';
?>
<div style="margin:0 auto;max-width:800px;">
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
                <td>
                    <h3 style="margin-top:0;"><?php echo $headings[$kind]; ?></h3>
                    <p>Hello <?php echo htmlspecialchars($name); ?>,</p>
                    <?php if ($kind === 'invite' && $is_planned): ?>
                        <p>A meeting has been planned and you're invited. You can follow the notes live in the portal while the meeting takes place.</p>
                    <?php elseif ($kind === 'invite'): ?>
                        <p>The meeting notes below have been shared with you. You can follow them live while they are being written.</p>
                    <?php elseif ($kind === 'reminder'): ?>
                        <p>This is a reminder that the meeting below starts at <strong><?php echo date('H:i', strtotime($meeting->meeting_datetime)); ?></strong>.</p>
                    <?php else: ?>
                        <p>The meeting below has just started. Open the portal to follow the notes as they are written.</p>
                    <?php endif; ?>
                    <ul>
                        <li><strong>Meeting:</strong> <?php echo htmlspecialchars($meeting->customer_name); ?></li>
                        <li><strong>When:</strong> <?php echo htmlspecialchars($when); ?></li>
                        <li><strong>Where:</strong> <?php echo htmlspecialchars($lieu); ?></li>
                    </ul>
                    <?php if (!empty($meeting->agenda) && $kind !== 'live'): ?>
                        <p style="margin-bottom:4px;"><strong>Agenda</strong></p>
                        <div style="padding:8px 12px;background:#f8f9fa;border-left:4px solid #0d6efd;margin-bottom:16px;"><?php echo $meeting->agenda; ?></div>
                    <?php endif; ?>
                    <p>
                        <a href="<?php echo $link; ?>" style="display:inline-block;padding:10px 14px;background:#0d6efd;color:#ffffff;text-decoration:none;border-radius:4px;"><?php echo $button; ?></a>
                    </p>
                    <?php if (!empty($calendar)): ?>
                        <p style="margin-bottom:0;">
                            Add to your calendar:
                            <a href="<?php echo htmlspecialchars($calendar['google']); ?>">Google</a> &middot;
                            <a href="<?php echo htmlspecialchars($calendar['outlook']); ?>">Outlook</a>
                            <?php if (!empty($calendar['ics'])): ?> &middot; <a href="<?php echo htmlspecialchars($calendar['ics']); ?>">Apple / other (.ics)</a><?php endif; ?>
                        </p>
                    <?php else: ?>
                        <p style="margin-bottom:0;">Once the meeting ends, the notes stay available to you in the portal under <strong>Meetings</strong>.</p>
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
