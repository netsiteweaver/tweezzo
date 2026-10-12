<div class="card">
    <div class="card-header">
        <h2>Import from HMS</h2>
        <p class="text-muted mb-0">Approved quotations in HMS. Open one to review the tasks its line items will become; nothing is created until you confirm.</p>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <a href="<?= base_url('hms_import/index') ?>" class="btn btn-sm btn-flat <?= $show_all ? 'btn-default' : 'btn-info' ?>">Not imported yet</a>
            <a href="<?= base_url('hms_import/index?show=all') ?>" class="btn btn-sm btn-flat <?= $show_all ? 'btn-info' : 'btn-default' ?>">All approved</a>
        </div>

        <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div>
        <?php elseif (empty($quotations)): ?>
        <div class="alert alert-info mb-0"><i class="fa fa-info-circle"></i> <?= $show_all ? 'There are no approved quotations in HMS.' : 'Every approved quotation has already been imported.' ?></div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Number</th>
                        <th>Client</th>
                        <th>Title</th>
                        <th class="text-right">Amount</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quotations as $q): ?>
                    <tr>
                        <td class="text-nowrap"><?= htmlspecialchars($q['date']) ?></td>
                        <td class="text-nowrap"><a href="<?= htmlspecialchars($q['url']) ?>" target="_blank" title="Open in HMS"><?= htmlspecialchars($q['number']) ?></a></td>
                        <td><?= htmlspecialchars(!empty($q['client']['company']) ? $q['client']['company'] : $q['client']['name']) ?></td>
                        <td><?= htmlspecialchars($q['title']) ?></td>
                        <td class="text-right text-nowrap"><?= number_format($q['total'], 2) ?></td>
                        <td class="text-nowrap">
                            <?php if (!empty($q['exported_on'])): ?>
                            <span class="badge badge-success" title="Imported on <?= htmlspecialchars(date('d M Y', strtotime($q['exported_on']))) ?>"><?= (int) $q['task_count'] ?> task(s) imported</span>
                            <?php else: ?>
                            <span class="badge badge-warning">Not imported</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right text-nowrap">
                            <a href="<?= base_url('hms_import/preview/' . rawurlencode($q['uuid'])) ?>" class="btn btn-sm btn-flat btn-info"><i class="fa fa-tasks"></i> Review tasks</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
