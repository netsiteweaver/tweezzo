<?php
$cur = htmlspecialchars(trim((string) $outstanding['currency']) . ' ');
$due = !empty($outstanding['invoices']) || !empty($outstanding['quotations']);
$headings = ['validated' => 'All Tasks Validated', 'reminder' => 'Payment Reminder Sent', 'paid' => 'Payment Received'];
?>
<div style="margin:0 auto;max-width:800px;">
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
                <td>
                    <h3 style="margin-top:0;"><?php echo $headings[$event]; ?></h3>
                    <ul>
                        <li><strong>Customer:</strong> <?php echo htmlspecialchars($customer_name); ?></li>
                        <li><strong>Project:</strong> <?php echo htmlspecialchars($project_name); ?></li>
                        <li><strong>Sprint:</strong> <?php echo htmlspecialchars($sprint_name); ?></li>
                        <li><strong>HMS documents:</strong> <?php echo empty($outstanding['refs']) ? 'none referenced by the tasks' : htmlspecialchars(implode(', ', $outstanding['refs'])); ?></li>
                    </ul>
                    <?php if ($outstanding['error'] !== ''): ?>
                    <p style="padding:12px;background:#fff8db;border-left:4px solid #f4d03f;">
                        <strong>Payment could not be checked:</strong> <?php echo htmlspecialchars($outstanding['error']); ?><br>
                        The client was not contacted. This is retried daily; check HMS before pushing live.
                    </p>
                    <?php elseif ($due): ?>
                    <p style="padding:12px;background:#fdecee;border-left:4px solid #dc3545;">
                        <strong>Payment outstanding.</strong> Do not push live yet.
                    </p>
                    <ul>
                        <?php foreach ($outstanding['invoices'] as $inv): ?>
                        <li>Invoice <?php echo htmlspecialchars($inv['number']); ?>: <?php echo $cur . number_format((float) $inv['balance'], 2); ?> due</li>
                        <?php endforeach; ?>
                        <?php foreach ($outstanding['quotations'] as $quo): ?>
                        <li>Quotation <?php echo htmlspecialchars($quo['number']); ?> (not invoiced yet): <?php echo $cur . number_format((float) $quo['total'], 2); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php foreach ($outstanding['emails'] as $mail): ?>
                    <p>
                        <?php if (empty($mail['recipients'])): ?>
                        <strong style="color:#dc3545;">HMS client <?php echo htmlspecialchars($mail['client']); ?> has no email address, so the client was NOT emailed.</strong> Contact them directly.
                        <?php else: ?>
                        <?php if (!empty($mail['sent'])): ?>
                        HMS emailed <?php echo htmlspecialchars($mail['client']); ?> (<?php echo htmlspecialchars(implode(', ', $mail['sent'])); ?>) that payment must be done to proceed.
                        <?php endif; ?>
                        <?php if (!empty($mail['failed'])): ?>
                        <br><strong style="color:#dc3545;">Email FAILED for <?php echo htmlspecialchars(implode(', ', $mail['failed'])); ?>.</strong>
                        <?php endif; ?>
                        <?php endif; ?>
                    </p>
                    <?php endforeach; ?>
                    <p>The client is reminded automatically until payment is recorded in HMS.</p>
                    <?php elseif (empty($outstanding['refs'])): ?>
                    <p style="padding:12px;background:#fff8db;border-left:4px solid #f4d03f;">
                        No task in this sprint references a quotation or invoice, so payment was not checked. Confirm billing before pushing live.
                    </p>
                    <?php else: ?>
                    <p style="padding:12px;background:#eaf8ec;border-left:4px solid #198754;">
                        <strong>Fully paid.</strong> Ready to push live and set the tasks to completed.
                    </p>
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
