<?php
$is_live = ($note->status === 'live');
$is_planned = ($note->status === 'planned');
?>
<div class="row mt-4">
    <div class="col-md-12">
        <a href="portal/<?php echo $portal; ?>/meetings" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-chevron-left me-1"></i>Meetings</a>

        <div id="portal-meeting"
             data-note-ref="<?php echo htmlspecialchars($note->uuid); ?>"
             data-portal="<?php echo $portal; ?>"
             data-notes-version="<?php echo (int) $note->notes_version; ?>"
             data-status="<?php echo htmlspecialchars($note->status); ?>"
             data-viewer-type="<?php echo htmlspecialchars($share->participant_type); ?>"
             data-viewer-id="<?php echo (int) $share->participant_id; ?>"
             data-can-contribute="<?php echo ($is_live && $share->can_contribute == 1) ? 1 : 0; ?>">

            <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">
                <h4 class="mb-0">
                    <?php echo htmlspecialchars($note->customer_name); ?>
                    <small class="text-muted fs-6 ms-2"><?php echo htmlspecialchars(date('d M Y H:i', strtotime($note->meeting_datetime))); ?> &middot; <?php echo htmlspecialchars($lieu); ?></small>
                </h4>
                <span class="meeting-status-badge">
                    <?php if ($is_live): ?>
                        <span class="meeting-host-badge"><?php echo $this->Meeting_note_model->host_state_badge($this->Meeting_note_model->host_state($note)); ?></span>
                        <small class="text-muted ms-2 meeting-updated"></small>
                    <?php elseif ($is_planned): ?>
                        <span class="badge bg-info text-dark"><i class="bi bi-calendar-event me-1"></i>Upcoming</span>
                    <?php endif; ?>
                </span>
            </div>

            <?php if ($is_planned): ?>
            <div class="alert alert-info">
                <i class="bi bi-calendar-event me-1"></i>This meeting is planned for <strong><?php echo htmlspecialchars(date('l d M Y \a\t H:i', strtotime($note->meeting_datetime))); ?></strong>.
                The notes open here automatically when it starts &ndash; this page will refresh by itself.
                <?php if (!empty($calendar)): ?>
                <div class="mt-2">
                    <i class="bi bi-calendar-plus me-1"></i>Add to calendar:
                    <a href="<?php echo htmlspecialchars($calendar['google']); ?>" target="_blank" rel="noopener">Google</a> &middot;
                    <a href="<?php echo htmlspecialchars($calendar['outlook']); ?>" target="_blank" rel="noopener">Outlook</a>
                    <?php if (!empty($calendar['ics'])): ?> &middot; <a href="<?php echo htmlspecialchars($calendar['ics']); ?>">Apple / other (.ics)</a><?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="alert alert-secondary meeting-ended-banner <?php echo ($is_live || $is_planned) ? 'd-none' : ''; ?>">
                <i class="bi bi-flag me-1"></i>This meeting has ended<?php if (!empty($note->closed_on)): ?> on <?php echo htmlspecialchars(date('d M Y H:i', strtotime($note->closed_on))); ?><?php endif; ?>. The notes are read-only.
            </div>
            <div class="alert alert-danger meeting-removed-banner d-none">
                <i class="bi bi-slash-circle me-1"></i>This meeting is no longer shared with you.
            </div>

            <?php if ($is_live || $is_planned): ?>
            <div class="mb-3 meeting-participants-row"><i class="bi bi-people text-muted me-1"></i><span class="meeting-participants"><span class="text-muted small">Loading participants&hellip;</span></span></div>
            <?php endif; ?>

            <?php if (!empty($note->agenda) && trim(strip_tags($note->agenda)) !== ''): ?>
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-list-ol me-1"></i>Agenda</div>
                <div class="card-body"><?php echo $note->agenda; ?></div>
            </div>
            <?php endif; ?>

            <?php if (!$is_planned): ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-3">
                        <div class="card-header"><i class="bi bi-journal-text me-1"></i>Notes</div>
                        <div class="card-body meeting-notes-body"><?php echo $note->notes; ?></div>
                    </div>

                    <?php if (!empty($attachments)): ?>
                    <div class="card mb-3">
                        <div class="card-header"><i class="bi bi-paperclip me-1"></i>Attachments</div>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($attachments as $file): ?>
                            <li class="list-group-item"><a href="<?php echo base_url($file->file_path); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($file->file_name); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-3">
                        <div class="card-header"><i class="bi bi-chat-dots me-1"></i>Contributions</div>
                        <div class="card-body">
                            <div class="meeting-entries"></div>
                            <div class="meeting-entry-form mt-3 <?php echo ($is_live && $share->can_contribute == 1) ? '' : 'd-none'; ?>">
                                <textarea class="form-control meeting-entry-input" rows="2"></textarea>
                                <button type="button" class="btn btn-primary btn-sm mt-2 meeting-entry-add"><i class="bi bi-plus-lg me-1"></i>Add Entry</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <script type="application/json" id="portal-meeting-entries"><?php echo json_encode($entries, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
    </div>
</div>
