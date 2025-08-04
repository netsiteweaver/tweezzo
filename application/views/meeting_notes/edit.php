<style>
    .preview-image {
  cursor: context-menu;
}
</style>
<?= validation_errors('<div class="alert alert-danger">', '</div>') ?>
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
            <label for="notes" class="form-label">Notes</label>
            <textarea name="notes" id="notes" class="form-control summernote" rows="3"
                required><?= set_value('notes', $note->notes) ?></textarea>
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