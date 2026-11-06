<?php
/**
 * Conversation thread, shared by the admin view, the customer/developer portals and the public tracking page.
 * Expects: $ticket, $thread (from Tickets_model::getThread), $fileUrl (string with "{id}" placeholder).
 */
$e = function ($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); };
$attachmentList = function ($attachments) use ($e, $fileUrl) {
    if (empty($attachments)) return '';
    $html = '<div class="hd-attachments">';
    foreach ($attachments as $a) {
        $url = str_replace('{id}', (int) $a->id, $fileUrl);
        if (Tickets_model::isImageAttachment($a)) {
            $html .= '<a class="hd-att hd-att-img" href="' . $e($url) . '" data-lightbox="ticket-' . (int) $a->ticket_id . '" data-title="' . $e($a->original_name) . '">'
                . '<img src="' . $e($url) . '" alt="' . $e($a->original_name) . '" loading="lazy"></a>';
        } else {
            $html .= '<a class="hd-att" href="' . $e($url) . '" target="_blank" rel="noopener"><span class="hd-att-icon">&#128206;</span> '
                . $e($a->original_name) . ' <small>(' . Tickets_model::humanSize($a->size) . ')</small></a>';
        }
    }
    return $html . '</div>';
};
$authorLabel = ['admin' => 'Support', 'developer' => 'Support', 'customer' => 'Customer', 'guest' => 'Customer'];
?>
<style>
    .hd-thread { display:flex; flex-direction:column; gap:14px; }
    .hd-msg { border:1px solid #e3e6ea; border-left:4px solid #6c757d; border-radius:6px; background:#fff; }
    .hd-msg-head { display:flex; justify-content:space-between; flex-wrap:wrap; gap:6px; padding:8px 14px; border-bottom:1px solid #eef0f2; font-size:13px; color:#6c757d; }
    .hd-msg-head b { color:#343a40; }
    .hd-msg-body { padding:12px 14px; white-space:normal; word-wrap:break-word; overflow-wrap:anywhere; }
    .hd-msg.customer, .hd-msg.guest { border-left-color:#17a2b8; }
    .hd-msg.admin, .hd-msg.developer { border-left-color:#28a745; }
    .hd-msg.internal { border-left-color:#ffc107; background:#fffbea; }
    .hd-msg.original { border-left-color:#007bff; }
    .hd-tag { display:inline-block; font-size:11px; padding:1px 6px; border-radius:3px; background:#eef0f2; color:#495057; margin-left:6px; text-transform:uppercase; letter-spacing:.03em; }
    .hd-tag.internal { background:#ffc107; color:#212529; }
    .hd-system { text-align:center; font-size:12px; color:#888; }
    .hd-attachments { display:flex; flex-wrap:wrap; gap:8px; padding:0 14px 12px; }
    .hd-att { display:inline-flex; align-items:center; gap:4px; padding:4px 8px; border:1px solid #dee2e6; border-radius:4px; font-size:13px; background:#f8f9fa; text-decoration:none; }
    .hd-att-img { padding:2px; }
    .hd-att-img img { height:72px; width:auto; max-width:140px; object-fit:cover; border-radius:3px; display:block; }
    body.dark-mode .hd-msg { background:#343a40; border-color:#4b545c; }
    body.dark-mode .hd-msg-head { border-color:#4b545c; }
    body.dark-mode .hd-msg.internal { background:#4a4120; }
    body.dark-mode .hd-msg-head b { color:#f8f9fa; }
    body.dark-mode .hd-att { background:#3f474e; border-color:#4b545c; }
</style>

<div class="hd-thread">
    <div class="hd-msg original">
        <div class="hd-msg-head">
            <span><b><?php echo $e($ticket->contact_name ?: ($ticket->contact_email ?: 'Customer')); ?></b>
                <span class="hd-tag">Original request</span></span>
            <span><?php echo $e(date('d M Y H:i', strtotime($ticket->created_on))); ?></span>
        </div>
        <div class="hd-msg-body"><?php echo Tickets_model::formatBody($ticket->body); ?></div>
        <?php echo $attachmentList($thread['ticket_attachments']); ?>
    </div>

    <?php foreach ($thread['replies'] as $r): ?>
        <?php if ($r->author_type === 'system'): ?>
            <div class="hd-system" id="reply-<?php echo (int) $r->id; ?>">— <?php echo $e($r->body); ?> · <?php echo $e(date('d M Y H:i', strtotime($r->created_on))); ?> —</div>
            <?php continue; ?>
        <?php endif; ?>
        <div class="hd-msg <?php echo $e($r->author_type); ?> <?php echo $r->is_internal ? 'internal' : ''; ?>" id="reply-<?php echo (int) $r->id; ?>">
            <div class="hd-msg-head">
                <span><b><?php echo $e($r->author_name); ?></b>
                    <span class="hd-tag"><?php echo $e($authorLabel[$r->author_type] ?? $r->author_type); ?></span>
                    <?php if ($r->is_internal): ?><span class="hd-tag internal">Internal note</span><?php endif; ?></span>
                <span><?php echo $e(date('d M Y H:i', strtotime($r->created_on))); ?></span>
            </div>
            <?php if (trim($r->body) !== ''): ?>
            <div class="hd-msg-body"><?php echo Tickets_model::formatBody($r->body); ?></div>
            <?php endif; ?>
            <?php echo $attachmentList($r->attachments); ?>
        </div>
    <?php endforeach; ?>
</div>
