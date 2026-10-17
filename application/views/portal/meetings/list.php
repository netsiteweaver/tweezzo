<?php $lieu_labels = ['our' => 'Our office', 'client' => 'Client office', 'online' => 'Online', 'other' => 'Other']; ?>
<div class="row mt-4">
    <div class="col-md-12">
        <h4 class="mb-3"><i class="bi bi-people me-2"></i>Meetings</h4>
        <?php if (empty($meetings)): ?>
            <div class="alert alert-info">No meeting notes have been shared with you yet.</div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Meeting</th>
                        <th>Lieu</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($meetings as $meeting): ?>
                    <tr>
                        <td><?php echo htmlspecialchars(date('d M Y H:i', strtotime($meeting->meeting_datetime))); ?></td>
                        <td><?php echo htmlspecialchars($meeting->customer_name); ?></td>
                        <td><?php echo htmlspecialchars($lieu_labels[$meeting->lieu] ?? ucfirst((string) $meeting->lieu)); ?></td>
                        <td>
                            <?php if ($meeting->status === 'live'): ?>
                                <?php echo $this->Meeting_note_model->host_state_badge($this->Meeting_note_model->host_state($meeting)); ?>
                                <?php if ($meeting->can_contribute == 1): ?><span class="badge bg-secondary ms-1" title="You can add entries"><i class="bi bi-pencil"></i></span><?php endif; ?>
                            <?php elseif ($meeting->status === 'planned'): ?>
                                <span class="badge bg-info text-dark"><i class="bi bi-calendar-event me-1"></i>Upcoming</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Ended</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-nowrap">
                            <a href="portal/<?php echo $portal; ?>/meeting/<?php echo htmlspecialchars($meeting->uuid); ?>" class="btn btn-sm <?php echo $meeting->status === 'live' ? 'btn-warning' : 'btn-outline-secondary'; ?>">
                                <i class="bi bi-<?php echo $meeting->status === 'live' ? 'pencil-square' : 'journal-text'; ?> me-1"></i><?php echo $meeting->status === 'live' ? 'Follow notes' : 'View'; ?>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
