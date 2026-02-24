<div class="card">
    <form id="" name="" action="#" method="" enctype="multipart/form-data"></form>
    <div class="card-header">
        <a href="<?= site_url('meeting_notes/create') ?>" class="btn btn-create"><i class="fa fa-plus"></i> New Note</a>
    </div>
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>DATE & TIME</th>
                    <th>CUSTOMER</th>
                    <th>LAST UPDATED</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notes as $note): ?>
                <tr id="note-<?= $note->id ?>">
                    <td><?= htmlspecialchars($note->meeting_datetime) ?></td>
                    <td><?= htmlspecialchars($note->customer_name) ?></td>
                    <td>
                        <?php if($note->last_updated_by):?>
                        <?= $note->last_updated . ( ($note->last_updated_by) ? ' by ' . $note->updatedBy : '' ) ?>
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
    </form>
</div>
