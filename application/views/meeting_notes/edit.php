<style>
    .preview-image {
  cursor: context-menu;
}
</style>
<?= validation_errors('<div class="alert alert-danger">', '</div>') ?>
<?php
$is_live = ($note->status === 'live');
$is_planned = ($note->status === 'planned');
$is_shared_session = ($is_live || $is_planned);
?>
<?php if ($is_shared_session): ?>
<div id="live-session" class="alert alert-light border d-flex flex-wrap align-items-center justify-content-between" data-note-id="<?= (int) $note->id ?>" data-status="<?= htmlspecialchars($note->status) ?>">
    <?php if ($is_live): ?>
    <div>
        <span class="badge badge-warning mr-2 live-badge"><i class="fa fa-pen"></i> In progress</span>
        <strong>Meeting in progress</strong>
        <span class="text-muted ml-2">Shared users follow your notes as you type (autosaved).</span>
        <span class="text-muted ml-2 live-save-status"></span>
    </div>
    <button type="button" class="btn btn-sm btn-outline-danger end-live-session"><i class="fa fa-stop-circle"></i> End Meeting</button>
    <?php else: ?>
    <div>
        <span class="badge badge-info mr-2"><i class="fa fa-calendar-alt"></i> PLANNED</span>
        <strong><?= htmlspecialchars(date('l d M Y \a\t H:i', strtotime($note->meeting_datetime))) ?></strong>
        <span class="text-muted ml-2">Invitees see it as upcoming with the agenda. It goes live automatically at this time; a reminder goes out an hour before.</span>
    </div>
    <button type="button" class="btn btn-sm btn-danger start-live-now"><i class="fa fa-pen"></i> Start Now</button>
    <?php endif; ?>
</div>
<div class="mb-3"><i class="fa fa-users text-muted mr-1"></i><span class="meeting-participants"><span class="text-muted small">Loading participants&hellip;</span></span></div>
<script type="application/json" id="live-meeting-data"><?= json_encode([
    'note_id'       => (int) $note->id,
    'status'        => $note->status,
    'viewer'        => ['type' => 'admin', 'id' => (int) $_SESSION['user_id']],
    'notes_version' => (int) $note->notes_version,
    'shares'        => $shares,
    'shareable'     => $shareable,
    'entries'       => $entries,
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
<div class="row">
<div class="col-lg-8">
<?php elseif ($note->status === 'closed' && !empty($note->closed_on)): ?>
<div class="alert alert-secondary"><i class="fa fa-flag-checkered"></i> Live meeting ended on <?= htmlspecialchars(date('d M Y H:i', strtotime($note->closed_on))) ?>. Shared users keep read-only access.</div>
<?php endif; ?>
<div class="card card-primary">
    <?= form_open_multipart('meeting_notes/update/'.$note->id) ?>
    <div class="card-body">
        <div class="form-group">
            <label for="">Select Client</label>
            <select name="customer_id" id="" class="form-control" autofocus>
                <option value="">Select Customer</option>
                <?php foreach($customers as $customer):?>
                <option value="<?php echo $customer->customer_id;?>" <?php echo $customer->customer_id == $note->customer_id ? 'selected' : ''; ?>><?php echo $customer->company_name;?></option>
                <?php endforeach;?>
                <option value="other" <?php echo $note->customer_id == null ? 'selected' : ''; ?>>Other</option>
            </select>
        </div>

        <div class="form-group <?php echo ($note->customer_id == null) ? '' : 'd-none';?>">
            <label for="customer_name" class="form-label">Client Name</label>
            <input type="text" name="customer_name" id="customer_name" class="form-control"
                value="<?= set_value('customer_name', $note->customer_name) ?>" required>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-8">
                    <label for="meeting_date" class="form-label">Meeting Date</label>
                    <input type="date" name="meeting_date" id="meeting_date" class="form-control"
                        value="<?= substr($note->meeting_datetime,0,10) ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="meeting_time" class="form-label">Meeting Time</label>
                    <input type="time" name="meeting_time" id="meeting_time" class="form-control"
                        value="<?= substr($note->meeting_datetime,11,5) ?>" required>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="lieu" class="form-label">Lieu of Meeting</label>
            <select name="lieu" id="lieu" class="form-control" required>
                <option value="">Select</option>
                <option value="our" <?php echo $note->lieu == 'our' ? 'selected' : ''; ?>>Our Office</option>
                <option value="client" <?php echo $note->lieu == 'client' ? 'selected' : ''; ?>>Client Office</option>
                <option value="online" <?php echo $note->lieu == 'online' ? 'selected' : ''; ?>>Online</option>
                <option value="other" <?php echo $note->lieu == 'other' ? 'selected' : ''; ?>>Other</option>
            </select>
        </div>

        <div class="form-group">
            <label for="attendees" class="form-label">Attendees</label>
            <textarea name="attendees" id="attendees" class="form-control"
                rows="5"><?= set_value('attendees', isset($note) ? $note->attendees : '') ?></textarea>
        </div>


        <div class="form-group">
            <label for="agenda" class="form-label">Agenda <span class="text-muted">(optional &ndash; shown to invitees)</span></label>
            <textarea name="agenda" id="agenda" class="form-control summernote2" rows="3"><?= set_value('agenda', isset($note->agenda) ? $note->agenda : '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="notes" class="form-label">Notes</label>
            <textarea name="notes" id="notes" class="form-control summernote2" rows="3"
                required><?= set_value('notes', $note->notes) ?></textarea>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-8">
                    <label for="next_meeting_date" class="form-label">Next Meeting Date <span class="text-muted">(optional)</span></label>
                    <input type="date" name="next_meeting_date" id="next_meeting_date" class="form-control"
                        value="<?= set_value('next_meeting_date', !empty($note->next_meeting_date) ? substr($note->next_meeting_date, 0, 10) : '') ?>">
                </div>
                <div class="col-md-4">
                    <label for="next_meeting_time" class="form-label">Next Meeting Time</label>
                    <input type="time" name="next_meeting_time" id="next_meeting_time" class="form-control"
                        value="<?= set_value('next_meeting_time', !empty($note->next_meeting_date) ? substr($note->next_meeting_date, 11, 5) : '') ?>">
                </div>
            </div>
            <small class="form-text text-muted">Schedule the follow-up meeting, if known. Time is required when a date is set.</small>
        </div>

        <?php if (!empty($attachments)): ?>
            <h4>Attachments:</h4>
            <div class="row g-3">
                <?php foreach ($attachments as $file): ?>
                    <?php
                        $ext = strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION));
                        $is_image = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        $is_pdf = ($ext === 'pdf');
                        $icon_class = 'fa-file-o';

                        if ($is_image) $icon_class = 'fa-file-image-o';
                        elseif ($is_pdf) $icon_class = 'fa-file-pdf';
                        elseif (in_array($ext, ['doc', 'docx'])) $icon_class = 'fa-file-word';
                        elseif (in_array($ext, ['xls', 'xlsx'])) $icon_class = 'fa-file-excel';
                        elseif (in_array($ext, ['zip', 'rar'])) $icon_class = 'fa-file-archive';
                    ?>
                    <div class="col-6 col-md-3 col-lg-2 text-center">
                        <?php if ($is_image): ?>
                            <a href="<?= base_url($file->file_path) ?>" target="_blank" class="d-block mb-2">
                                <img data-id="<?php echo $file->id;?>" src="<?= base_url($file->file_path) ?>" class="preview-image img-fluid rounded border" style="height: 120px; object-fit: cover;">
                            </a>
                            <small><?= htmlspecialchars($file->file_name) ?></small>
                        <?php else: ?>
                            <a href="<?= base_url($file->file_path) ?>" target="_blank" class="text-decoration-none text-dark">
                                <i class="fa <?= $icon_class ?>" style="font-size: 48px;"></i><br>
                                <small><?= htmlspecialchars($file->file_name) ?></small>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>  

        <div class="form-group">
            <label for="attachments" class="form-label">Attach Files</label>
            <input type="file" name="attachments[]" id="attachments" class="form-control" multiple
                accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.zip,.rar">
        </div>

        

    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-update"><i class="fa fa-save"></i> Update Note</button>
        <a href="<?= site_url('meeting_notes') ?>" class="btn btn-back ms-2"><i class="fa fa-list"></i> Back to List</a>
    </div>
    </form>
</div>
<?php if ($is_shared_session): ?>
<?php if ($is_live): ?>
<div class="card card-outline card-info">
    <div class="card-header"><h3 class="card-title"><i class="fa fa-comments"></i> Contributions</h3></div>
    <div class="card-body">
        <div class="live-entries"></div>
        <div class="mt-3">
            <textarea class="form-control live-entry-input" rows="2"></textarea>
            <button type="button" class="btn btn-info btn-sm mt-2 live-entry-add"><i class="fa fa-plus"></i> Add Entry</button>
        </div>
    </div>
</div>
<?php endif; ?>
</div><!-- /.col-lg-8 -->
<div class="col-lg-4">
<div class="card card-outline card-danger live-share-panel">
    <div class="card-header"><h3 class="card-title"><i class="fa fa-share-alt"></i> Shared With</h3></div>
    <div class="card-body">
        <div class="live-shares mb-3"></div>
        <button type="button" class="btn btn-danger btn-sm share-open"><i class="fa fa-user-plus"></i> Share with people&hellip;</button>
        <p class="text-muted small mt-3 mb-0">
            <?php if ($is_planned): ?>
            Invitees get an email with the date, agenda and calendar links, a reminder an hour before, and a "now live" email when it starts. They find it in their portal under <b>Meetings</b>.
            <?php else: ?>
            Shared users see your notes live in their portal, under <b>Meetings</b>, and receive an email with the link.
            <?php endif; ?>
            <i class="fa fa-circle" style="color:#28a745;"></i> viewing now &middot; <i class="fa fa-circle" style="color:#ffc107;"></i> page open in the background &middot; <i class="fa fa-circle" style="color:#adb5bd;"></i> not on the page.
        </p>
    </div>
</div>
</div><!-- /.col-lg-4 -->
</div><!-- /.row -->

<!-- Modal: pick several client users and developers at once -->
<div class="modal fade" id="modalShareMeeting" tabindex="-1" role="dialog" aria-labelledby="modalShareMeetingTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalShareMeetingTitle"><i class="fa fa-share-alt"></i> Share live notes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="search" class="form-control form-control-sm mb-3 share-search" placeholder="Search by name or email">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong><i class="fa fa-building"></i> Client users</strong>
                            <a href="#" class="small share-select-all" data-type="customer">Select all</a>
                        </div>
                        <div class="share-people" data-type="customer"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong><i class="fa fa-code"></i> Developers</strong>
                            <a href="#" class="small share-select-all" data-type="developer">Select all</a>
                        </div>
                        <div class="share-people" data-type="developer"></div>
                    </div>
                </div>
                <p class="text-muted small mt-3 mb-0">Tick the people to share with. Use <i class="fa fa-pen"></i> to also let someone add entries. Each person gets an email with a link to the note.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                <button type="button" class="btn btn-danger share-save" disabled><i class="fa fa-user-plus"></i> Share</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($notify_shares)): ?>
<!-- Modal: notify shared people about this update? -->
<div class="modal fade" id="modalNotifyUpdate" tabindex="-1" role="dialog" aria-labelledby="modalNotifyUpdateTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNotifyUpdateTitle"><i class="fa fa-paper-plane"></i> Notify about this update?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">This note is shared with the people below. Tick who should get an email about the update<?= $note->status === 'planned' ? ' (with the new date, agenda and calendar links)' : '' ?>.</p>
                <div class="d-flex justify-content-end mb-1"><a href="#" class="small notify-toggle-all">Untick all</a></div>
                <ul class="list-group">
                    <?php foreach ($notify_shares as $sh): ?>
                    <li class="list-group-item py-2">
                        <div class="form-check mb-0">
                            <input type="checkbox" class="form-check-input notify-share" id="notify-<?= (int) $sh->id ?>" value="<?= (int) $sh->id ?>" checked>
                            <label class="form-check-label" for="notify-<?= (int) $sh->id ?>">
                                <?= htmlspecialchars($sh->name ?: '(removed user)') ?>
                                <span class="badge badge-<?= $sh->participant_type === 'customer' ? 'success' : 'secondary' ?>"><?= $sh->participant_type === 'customer' ? 'Client' : 'Developer' ?></span>
                                <br><small class="text-muted"><?= htmlspecialchars((string) $sh->email) ?></small>
                            </label>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                <button type="button" class="btn btn-secondary notify-skip"><i class="fa fa-save"></i> Update only</button>
                <button type="button" class="btn btn-primary notify-send"><i class="fa fa-paper-plane"></i> Update &amp; notify</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
