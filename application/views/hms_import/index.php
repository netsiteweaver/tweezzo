<div class="card">
    <div class="card-header">
        <h2>Import from HMS</h2>
        <p class="text-muted mb-0">Approved quotations in HMS. Open one to review the tasks its line items will become; nothing is created until you confirm.<?php if ($start_date !== ''): ?> Quotations dated before <?= htmlspecialchars(date('d M Y', strtotime($start_date))) ?> are not listed.<?php endif; ?></p>
    </div>
    <div class="card-body">
        <?php
        $range_query = array_filter(['from' => $date_from, 'to' => $date_to], 'strlen');
        $tab_url = function ($all) use ($range_query) {
            $q = $all ? array_merge(['show' => 'all'], $range_query) : $range_query;
            return base_url('hms_import/index') . (!empty($q) ? '?' . http_build_query($q) : '');
        };
        ?>
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <div class="mb-2">
                <a href="<?= htmlspecialchars($tab_url(false)) ?>" class="btn btn-sm btn-flat <?= $show_all ? 'btn-default' : 'btn-info' ?>">Not imported yet</a>
                <a href="<?= htmlspecialchars($tab_url(true)) ?>" class="btn btn-sm btn-flat <?= $show_all ? 'btn-info' : 'btn-default' ?>">All approved</a>
            </div>
            <form method="get" action="<?= base_url('hms_import/index') ?>" class="form-inline mb-2">
                <?php if ($show_all): ?><input type="hidden" name="show" value="all"><?php endif; ?>
                <label class="mr-1 small text-muted" for="hms-from">From</label>
                <input type="date" id="hms-from" name="from" value="<?= htmlspecialchars($date_from) ?>"<?= $start_date !== '' ? ' min="' . htmlspecialchars($start_date) . '"' : '' ?> class="form-control form-control-sm mr-2">
                <label class="mr-1 small text-muted" for="hms-to">To</label>
                <input type="date" id="hms-to" name="to" value="<?= htmlspecialchars($date_to) ?>" class="form-control form-control-sm mr-2">
                <button type="submit" class="btn btn-sm btn-flat btn-info mr-1"><i class="fa fa-filter"></i> Filter</button>
                <?php if ($has_range): ?>
                <a href="<?= base_url('hms_import/index' . ($show_all ? '?show=all' : '')) ?>" class="btn btn-sm btn-flat btn-default">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div>
        <?php elseif (empty($quotations)): ?>
        <div class="alert alert-info mb-0"><i class="fa fa-info-circle"></i> <?php
            if ($has_range) {
                echo 'No ' . ($show_all ? 'approved' : 'unimported approved') . ' quotations in HMS for the selected dates.';
            } else {
                echo $show_all ? 'There are no approved quotations in HMS.' : 'Every approved quotation has already been imported.';
            }
        ?></div>
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
