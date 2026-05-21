<div class="card">
    <div class="card-header">
        <div class="row align-items-end">
            <div class="col-md-2">
                <?php if ($perms['add']): ?>
                <a href="<?= site_url('meeting_notes/create') ?>" class="btn btn-create"><i class="fa fa-plus"></i> New Note</a>
                <?php endif; ?>
            </div>
            <div class="col-md-3">
                <label for="customer_id_filter" class="form-label mb-0">Customer</label>
                <select class="form-control monitor-index" id="customer_id_filter">
                    <option value="">All customers</option>
                    <?php foreach ($customers as $customer): ?>
                    <option value="<?= (int) $customer->customer_id ?>"
                        <?= ((string) $customer_id === (string) $customer->customer_id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($customer->company_name) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="meeting_notes_start_date" class="form-label mb-0">From</label>
                <input type="date" class="form-control monitor-index" id="meeting_notes_start_date"
                    value="<?= htmlspecialchars($start_date) ?>">
            </div>
            <div class="col-md-2">
                <label for="meeting_notes_end_date" class="form-label mb-0">To</label>
                <input type="date" class="form-control monitor-index" id="meeting_notes_end_date"
                    value="<?= htmlspecialchars($end_date) ?>">
            </div>
            <div class="col-md-2">
                <a href="<?= site_url('meeting_notes/index') ?>" class="btn btn-secondary">Clear filters</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>DATE & TIME</th>
                    <th>NEXT MEETING</th>
                    <th>CUSTOMER</th>
                    <th>LAST UPDATED</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($notes)): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted">No meeting notes found for the selected filters.</td>
                </tr>
                <?php endif; ?>
                <?php foreach ($notes as $note): ?>
                <tr id="note-<?= $note->id ?>">
                    <td><?= $this->Meeting_note_model->formatMeetingDatetime($note->meeting_datetime) ?></td>
                    <td><?= !empty($note->next_meeting_date) ? $this->Meeting_note_model->formatNextMeeting($note->next_meeting_date) : '—' ?></td>
                    <td><?= htmlspecialchars($note->customer_name) ?></td>
                    <td>
                        <?php if($note->last_updated_by):?>
                        <?= $this->Meeting_note_model->formatMeetingDatetime($note->last_updated) . ( ($note->last_updated_by) ? ' by ' . $note->updatedBy : '' ) ?>
                        <?php endif;?>
                    </td>
                    <td>
                        <?php if($perms['view']):?>
                        <a href="<?= site_url('meeting_notes/view/'.$note->id) ?>" class="btn btn-view me-1"><i class="fa fa-eye"></i> View</a>
                        <?php endif;?>
                        <?php if($perms['edit']):?>
                        <a href="<?= site_url('meeting_notes/edit/'.$note->id) ?>" class="btn btn-save me-1"><i class="fa fa-edit"></i> Edit</a>
                        <?php endif;?>
                        <?php if($perms['pdf']):?>
                        <a href="<?= site_url('meeting_notes/pdf/'.$note->id) ?>" class="btn btn-danger me-1"><i class="fa fa-file-pdf"></i> PDF</a>
                        <?php endif;?>
                        <?php if($perms['delete']):?>
                        <button class="btn btn-delete delete-note" data-id="<?= $note->id ?>"><i class="fa fa-trash"></i> Delete</button>
                        <?php endif;?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
