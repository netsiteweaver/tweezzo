<div class="card">
    <div class="card-header">
        <h2><?= htmlspecialchars($note->customer_name) ?> <small class="text-muted">(<?= htmlspecialchars($note->meeting_datetime) ?>)</small></h2>
    </div>
    <div class="card-body">
        <p><b>Lieu:</b> <?= ucwords($note->lieu) ?></p>
        <div class="row">
            <div class="col-md-9">
                <p class='text-bold border-bottom'>Notes</p>
                <p><?= nl2br($note->notes) ?></p>
            </div>
            <div class="col-md-3 border-left">
                <p class='text-bold border-bottom'>Attendees</p>
                <p><?= nl2br($note->attendees) ?></p>
            </div>
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
                                <img src="<?= base_url($file->file_path) ?>" class="img-fluid rounded border" style="height: 120px; object-fit: cover;">
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
    </div>
    <div class="card-footer">
        <a href="<?= site_url('meeting_notes') ?>" class="btn btn-back mt-4"><i class="fa fa-chevron-left"></i> Back to List</a>
    </div>
</div>