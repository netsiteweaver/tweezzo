<div class="row no-print mb-3">
    <div class="col-md-8">
        <a href="<?php echo base_url('reports/saved'); ?>" class="btn btn-sm <?php echo empty($type) ? 'btn-info' : 'btn-default'; ?>">All</a>
        <a href="<?php echo base_url('reports/saved?type=developer'); ?>" class="btn btn-sm <?php echo $type === 'developer' ? 'btn-info' : 'btn-default'; ?>">Developer</a>
        <a href="<?php echo base_url('reports/saved?type=client'); ?>" class="btn btn-sm <?php echo $type === 'client' ? 'btn-info' : 'btn-default'; ?>">Client</a>
    </div>
    <div class="col-md-4 text-right">
        <a href="<?php echo base_url('reports/developer'); ?>" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Developer report</a>
        <a href="<?php echo base_url('reports/client'); ?>" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Client report</a>
    </div>
</div>

<div class="box">
    <div class="box-body table-responsive no-padding">
        <table class="table table-bordered table-hover">
            <thead>
                <tr class="text-center text-uppercase">
                    <th>Code</th>
                    <th>Saved on</th>
                    <th>Type</th>
                    <th>Title / Subject</th>
                    <th>Period</th>
                    <th>Hours</th>
                    <th>Amount</th>
                    <th>By</th>
                    <th class="no-print">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reports)): ?>
                <tr><td colspan="9" class="text-center text-muted">No saved reports yet. Generate a report and click Save.</td></tr>
                <?php else: ?>
                <?php foreach ($reports as $r): ?>
                <tr>
                    <td class="text-center">
                        <a href="<?php echo base_url('reports/view_saved/' . $r->uuid); ?>">
                            <code><?php echo htmlspecialchars(!empty($r->report_code) ? $r->report_code : '—'); ?></code>
                        </a>
                    </td>
                    <td class="text-center"><?php echo htmlspecialchars($r->created_on); ?></td>
                    <td class="text-center">
                        <span class="badge badge-<?php echo $r->report_type === 'developer' ? 'primary' : 'secondary'; ?>">
                            <?php echo htmlspecialchars(ucfirst($r->report_type)); ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?php echo base_url('reports/view_saved/' . $r->uuid); ?>">
                            <?php echo htmlspecialchars(!empty($r->title) ? $r->title : $r->subject_name); ?>
                        </a>
                        <?php if (!empty($r->subject_name) && !empty($r->title)): ?>
                        <div style="font-size:11px;color:#888;"><?php echo htmlspecialchars($r->subject_name); ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo htmlspecialchars($r->date_from . ' → ' . $r->date_to); ?></td>
                    <td class="text-center"><?php echo number_format((float) $r->total_hours, 2); ?></td>
                    <td class="text-right"><?php echo htmlspecialchars($r->currency); ?> <?php echo number_format((float) $r->total_amount, 2); ?></td>
                    <td><?php echo htmlspecialchars(!empty($r->created_by_name) ? $r->created_by_name : '—'); ?></td>
                    <td class="text-center no-print">
                        <a class="btn btn-sm btn-info" href="<?php echo base_url('reports/view_saved/' . $r->uuid); ?>" title="View"><i class="fa fa-eye"></i></a>
                        <a class="btn btn-sm btn-danger" href="<?php echo base_url('reports/view_saved/' . $r->uuid . '?output=pdf'); ?>" title="PDF"><i class="fa fa-file-pdf"></i></a>
                        <a class="btn btn-sm btn-warning" href="<?php echo base_url('reports/delete_saved/' . $r->uuid); ?>" title="Delete"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
