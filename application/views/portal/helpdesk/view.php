<?php
/**
 * Ticket view for the customer and developer portals and the public tracking page.
 * Expects: $ticket, $thread, $fileUrl, $replyUrl, $portal (customers|developers|public),
 * optional $resolveUrl, $backUrl.
 */
$e = function ($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); };
$isDev = $portal === 'developers';
$isClosed = $ticket->status === 'closed';
$backUrl = isset($backUrl) ? $backUrl : "portal/{$portal}/tickets";
?>
<div class="row mt-4 justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
                <h4 class="mb-1"><?php echo $e($ticket->subject); ?></h4>
                <div class="text-muted small">
                    <?php echo $e($isDev ? Tickets_model::staffRef($ticket) : $ticket->public_ref); ?> ·
                    <?php echo Tickets_model::statusBadge($ticket->status, true); ?>
                    <?php echo Tickets_model::priorityBadge($ticket->priority, true); ?>
                    <?php if ($ticket->category_name): ?> · <?php echo $e($ticket->category_name); ?><?php endif; ?>
                    <?php if ($ticket->project_name): ?> · <?php echo $e($ticket->project_name); ?><?php endif; ?>
                    <?php if ($isDev && $ticket->company_name): ?> · <?php echo $e($ticket->company_name); ?><?php endif; ?>
                    <?php if ($isDev && Tickets_model::isOverdue($ticket)): ?><span class="badge bg-danger">SLA overdue</span><?php endif; ?>
                </div>
            </div>
            <div class="d-flex gap-2">
                <?php if (!empty($resolveUrl) && !in_array($ticket->status, Tickets_model::CLOSED_STATUSES, true)): ?>
                <form method="post" action="<?php echo $e($resolveUrl); ?>" onsubmit="return confirm('Mark this ticket as resolved?');">
                    <button type="submit" class="btn btn-sm btn-outline-success"><i class="bi bi-check2-circle me-1"></i>Mark resolved</button>
                </form>
                <?php endif; ?>
                <?php if ($portal !== 'public'): ?>
                <a href="<?php echo $e($backUrl); ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>All tickets</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($ticket->status === 'pending_customer' && !$isDev): ?>
        <div class="alert alert-warning py-2"><i class="bi bi-hourglass-split me-1"></i>We're waiting for your reply on this ticket.</div>
        <?php elseif ($ticket->status === 'resolved' && !$isDev): ?>
        <div class="alert alert-success py-2"><i class="bi bi-check2-circle me-1"></i>This ticket is resolved. Still having the problem? Reply below and it will be reopened.</div>
        <?php endif; ?>

        <?php $this->load->view('helpdesk/_thread', ['ticket' => $ticket, 'thread' => $thread, 'fileUrl' => $fileUrl]); ?>

        <?php if ($isClosed && !$isDev): ?>
            <div class="alert alert-secondary mt-3">This ticket is closed. If you need more help, please open a new ticket.</div>
        <?php else: ?>
        <form method="post" action="<?php echo $e($replyUrl); ?>" enctype="multipart/form-data" class="card mt-3" id="ticket-reply-form">
            <div class="card-body">
                <?php if ($isDev): ?>
                <div class="btn-group btn-group-sm mb-2" role="group">
                    <input type="radio" class="btn-check" name="is_internal" id="mode-reply" value="0" checked>
                    <label class="btn btn-outline-primary" for="mode-reply"><i class="bi bi-reply me-1"></i>Reply to customer</label>
                    <input type="radio" class="btn-check" name="is_internal" id="mode-internal" value="1">
                    <label class="btn btn-outline-warning" for="mode-internal"><i class="bi bi-sticky me-1"></i>Internal note</label>
                </div>
                <?php endif; ?>
                <label for="reply-body" class="form-label"><?php echo $isDev ? 'Message' : 'Add a reply'; ?></label>
                <textarea class="form-control hd-editor" id="reply-body" name="body" rows="5" data-height="160" placeholder="Write your message…"></textarea>
                <div class="row mt-2 g-2 align-items-end">
                    <div class="col-md">
                        <input class="form-control form-control-sm" type="file" name="attachments[]" multiple>
                        <div class="form-text">Up to <?php echo Tickets_model::MAX_FILES; ?> files, <?php echo Tickets_model::MAX_FILE_KB / 1024; ?> MB each.</div>
                    </div>
                    <?php if ($isDev): ?>
                    <div class="col-md-3">
                        <label class="form-label small mb-0" for="reply-status">Then set status</label>
                        <select class="form-select form-select-sm" name="status" id="reply-status">
                            <option value="">Keep (<?php echo $e(Tickets_model::statusLabel($ticket->status)); ?>)</option>
                            <?php foreach (Tickets_model::statuses() as $k => $s): ?>
                            <?php if ($k === $ticket->status) continue; ?>
                            <option value="<?php echo $k; ?>"><?php echo $s['label']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-primary" id="ticket-reply-submit"><i class="bi bi-send me-1"></i>Send</button>
                    </div>
                </div>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>
<script src="assets/js/helpdesk-editor.js?v=2" defer></script>
<script>
    (function () {
        var form = document.getElementById('ticket-reply-form');
        if (!form) return;
        form.addEventListener('submit', function (ev) {
            var bodyEl = document.getElementById('reply-body');
            var empty = window.hdEditor ? hdEditor.isEmpty(bodyEl) : !bodyEl.value.trim();
            var files = form.querySelector('input[type=file]').files.length;
            if (empty && !files) {
                ev.preventDefault();
                alert('Please write a message.');
                return;
            }
            document.getElementById('ticket-reply-submit').disabled = true;
        });
    })();
</script>
