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
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notes as $note): ?>
                <tr id="note-<?= $note->id ?>">
                    <td><?= htmlspecialchars($note->meeting_datetime) ?></td>
                    <td><?= htmlspecialchars($note->customer_name) ?></td>
                    <td>
                        <a href="<?= site_url('meeting_notes/view/'.$note->id) ?>" class="btn btn-view me-1"><i class="fa fa-eye"></i> View</a>
                        <a href="<?= site_url('meeting_notes/edit/'.$note->id) ?>" class="btn btn-save me-1"><i class="fa fa-edit"></i> Edit</a>
                        <button class="btn btn-delete delete-note" data-id="<?= $note->id ?>">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    </form>
</div>
