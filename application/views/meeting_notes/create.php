<div class="card">
    <?= form_open_multipart('meeting_notes/create') ?>
    <!-- <div class="card-header">
        <h2></h2>
    </div> -->
    <div class="card-body">
        <?= validation_errors('<div class="alert alert-danger">', '</div>') ?>

        <div class="form-group">
            <label for="">Select Client</label>
            <select name="customer_id" id="" class="form-control required" required autofocus>
                <option value="">Select Customer</option>
                <?php foreach($customers as $customer):?>
                <option value="<?php echo $customer->customer_id;?>" <?php echo set_value('customer_id') == $customer->customer_id ? 'selected' : ''; ?>><?php echo $customer->company_name;?></option>
                <?php endforeach;?>
                <option value="other">Other</option>
            </select>
        </div>

        <div class="form-group d-none">
            <label for="customer_name" class="form-label">Customer</label>
            <input type="text" name="customer_name" id="customer_name" class="form-control"
                value="<?= set_value('customer_name') ?>">
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-8">
                    <label for="meeting_date" class="form-label">Meeting Date</label>
                    <input type="date" name="meeting_date" id="meeting_date" class="form-control required" min="<?php echo date('Y-m-d');?>"
                        value="<?= set_value('meeting_date', date('Y-m-d')) ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="meeting_time" class="form-label">Meeting Time</label>
                    <input type="time" name="meeting_time" id="meeting_time" class="form-control required"
                        value="<?= set_value('meeting_time', date('H:00')) ?>" required>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="lieu">Lieu of Meeting</label>
            <select name="lieu" id="lieu" class="form-control required" required>
                <option value="">Select</option>
                <option value="our">Our Office</option>
                <option value="client">Client Office</option>
                <option value="online">Online</option>
                <option value="other">Other</option>
            </select>
        </div>

        <div class="form-group">
            <label for="attendees" class="form-label">Attendees</label>
            <textarea name="attendees" id="attendees" class="form-control required"
                rows="3" required><?= set_value('attendees', isset($note) ? $note->attendees : '') ?></textarea>
        </div>


        <div class="form-group">
            <label for="notes" class="form-label">Notes</label>
            <textarea name="notes" id="notes" class="form-control summernote required" rows="5" required><?= set_value('notes') ?></textarea>
        </div>

        <div class="form-group">
            <label for="attachments" class="form-label">Attach Files</label>
            <input type="file" name="attachments[]" id="attachments" class="form-control" multiple
                accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.zip,.rar">
        </div>

        <div id="preview" class="d-flex flex-wrap gap-2"></div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-save"><i class="fa fa-save"></i> Save Note</button>
        <a href="<?= site_url('meeting_notes') ?>" class="btn btn-back ms-2"><i class="fa fa-times"></i> Cancel</a>
    </div>
    </form>
</div>
