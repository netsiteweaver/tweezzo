<?php
$from = !empty($filters['from']) ? $filters['from'] : date('Y-m-01');
$to = !empty($filters['to']) ? $filters['to'] : date('Y-m-t');
$developer_id = !empty($filters['developer_id']) ? $filters['developer_id'] : '';
$customer_id = !empty($filters['customer_id']) ? $filters['customer_id'] : '';
$project_id = !empty($filters['project_id']) ? $filters['project_id'] : '';
$sprint_id = !empty($filters['sprint_id']) ? $filters['sprint_id'] : '';
$task_uuid = !empty($filters['task_uuid']) ? $filters['task_uuid'] : '';
$task_id = !empty($filters['task_id']) ? $filters['task_id'] : '';
?>
<form action="<?php echo base_url('timesheets/listing'); ?>" method="get" class="no-print">
    <div class="row">
        <div class="col-md-2">
            <label for="from">From</label>
            <input class="form-control" id="from" name="from" type="date" value="<?php echo htmlspecialchars($from); ?>">
        </div>
        <div class="col-md-2">
            <label for="to">To</label>
            <input class="form-control" id="to" name="to" type="date" value="<?php echo htmlspecialchars($to); ?>">
        </div>
        <div class="col-md-2">
            <label for="developer_id">Developer</label>
            <select name="developer_id" id="developer_id" class="form-control <?php echo $developer_id !== '' ? 'bg-info text-white' : ''; ?>">
                <option value="">All developers</option>
                <?php foreach ($developers as $d): ?>
                <option value="<?php echo (int) $d->id; ?>" <?php echo ((string) $developer_id === (string) $d->id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($d->name . ' (' . $d->email . ')'); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="customer_id">Customer</label>
            <select name="customer_id" id="customer_id" class="form-control monitor <?php echo $customer_id !== '' ? 'bg-info text-white' : ''; ?>">
                <option value="">All customers</option>
                <?php foreach ($customers as $c): ?>
                <option value="<?php echo (int) $c->customer_id; ?>" <?php echo ((string) $customer_id === (string) $c->customer_id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($c->company_name); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="project_id">Project</label>
            <select name="project_id" id="project_id" class="form-control monitor <?php echo $project_id !== '' ? 'bg-info text-white' : ''; ?>">
                <option value="">All projects</option>
                <?php foreach ($projects as $project): ?>
                <option value="<?php echo (int) $project->id; ?>" <?php echo ((string) $project_id === (string) $project->id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($project->name); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 <?php echo empty($project_id) ? 'd-none' : ''; ?>" id="sprint_filter_wrap">
            <label for="sprint_id">Sprint</label>
            <select name="sprint_id" id="sprint_id" class="form-control <?php echo $sprint_id !== '' ? 'bg-info text-white' : ''; ?>">
                <option value="">All sprints</option>
                <?php foreach ($sprints as $sprint): ?>
                <option value="<?php echo (int) $sprint->id; ?>" <?php echo ((string) $sprint_id === (string) $sprint->id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($sprint->name); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="row mt-2">
        <?php if (!empty($filtered_task)): ?>
        <div class="col-md-4">
            <label>Task</label>
            <div class="form-control bg-info text-white">
                <?php
                $taskLabel = !empty($filtered_task->task_ref) ? $filtered_task->task_ref : $filtered_task->task_number;
                echo htmlspecialchars($taskLabel . ' — ' . $filtered_task->name);
                ?>
            </div>
            <input type="hidden" name="task_uuid" value="<?php echo htmlspecialchars($filtered_task->uuid); ?>">
            <input type="hidden" name="task_id" value="<?php echo (int) $filtered_task->id; ?>">
        </div>
        <?php elseif ($task_uuid !== '' || $task_id !== ''): ?>
            <input type="hidden" name="task_uuid" value="<?php echo htmlspecialchars($task_uuid); ?>">
            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task_id); ?>">
        <?php endif; ?>
        <div class="col-md-3 mt-4">
            <button type="submit" class="btn btn-info"><i class="fa fa-check"></i> Apply</button>
            <a href="<?php echo base_url('timesheets/listing'); ?>" class="btn btn-warning" title="Reset filters"><i class="fa fa-undo"></i></a>
            <div data-target="timesheets_list" data-include-columns="" data-skip-columns="[]" id="downloadTableAsCSV" data-filename="timesheets" class="btn btn-success"><i class="fa fa-download"></i></div>
            <div class="btn btn-default print"><i class="fa fa-print"></i></div>
        </div>
    </div>
</form>

<div class="row print-only">
    <div class="col-md-12">
        <p class="page-title">Timesheets from <?php echo htmlspecialchars($from); ?> to <?php echo htmlspecialchars($to); ?></p>
    </div>
</div>

<div class="row mt-3">
    <div class="col-xs-12 col-sm-12">
        <div class="box">
            <div class="box-body table-responsive no-padding">
                <table id="timesheets_list" class="table table-bordered table-hover extended-bottom-margin">
                    <thead>
                        <tr class="text-center" style="text-transform:uppercase;">
                            <th>Code</th>
                            <th>Task</th>
                            <th>Section</th>
                            <th>Developer</th>
                            <th class="<?php echo $sprint_id !== '' ? 'bg-info text-white' : ''; ?>">Sprint</th>
                            <th class="<?php echo $project_id !== '' ? 'bg-info text-white' : ''; ?>">Project</th>
                            <th class="<?php echo $customer_id !== '' ? 'bg-info text-white' : ''; ?>">Customer</th>
                            <th>Notes</th>
                            <th>Start</th>
                            <th>Finish</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $totalSeconds = 0; ?>
                        <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted">No timesheet entries found for the selected filters.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($rows as $row): ?>
                        <tr class="text-center">
                            <td class="text-start">
                                <a href="<?php echo base_url('tasks/view?task_uuid=' . urlencode($row->taskUuid)); ?>">
                                    <?php echo htmlspecialchars(!empty($row->taskRef) ? $row->taskRef : $row->taskNumber); ?>
                                </a>
                            </td>
                            <td class="text-start">
                                <a href="<?php echo base_url('tasks/view?task_uuid=' . urlencode($row->taskUuid)); ?>">
                                    <?php echo htmlspecialchars($row->taskName); ?>
                                </a>
                            </td>
                            <td class="text-start"><?php echo htmlspecialchars($row->taskSection); ?></td>
                            <td class="text-start">
                                <a href="<?php echo base_url('timesheets/listing?' . http_build_query(array_filter([
                                    'from' => $from,
                                    'to' => $to,
                                    'developer_id' => $row->developerId,
                                    'customer_id' => $customer_id,
                                    'project_id' => $project_id,
                                    'sprint_id' => $sprint_id,
                                ]))); ?>">
                                    <?php echo htmlspecialchars($row->developerName); ?>
                                </a>
                                <div style="font-size:11px;color:#888;"><?php echo htmlspecialchars($row->developerEmail); ?></div>
                            </td>
                            <td>
                                <a href="<?php echo base_url('timesheets/listing?' . http_build_query(array_filter([
                                    'from' => $from,
                                    'to' => $to,
                                    'developer_id' => $developer_id,
                                    'customer_id' => $row->customerId,
                                    'project_id' => $row->projectId,
                                    'sprint_id' => $row->sprintId,
                                ]))); ?>">
                                    <?php echo htmlspecialchars($row->sprintName); ?>
                                </a>
                            </td>
                            <td>
                                <a href="<?php echo base_url('timesheets/listing?' . http_build_query(array_filter([
                                    'from' => $from,
                                    'to' => $to,
                                    'developer_id' => $developer_id,
                                    'customer_id' => $row->customerId,
                                    'project_id' => $row->projectId,
                                ]))); ?>">
                                    <?php echo htmlspecialchars($row->projectName); ?>
                                </a>
                            </td>
                            <td>
                                <a href="<?php echo base_url('timesheets/listing?' . http_build_query(array_filter([
                                    'from' => $from,
                                    'to' => $to,
                                    'developer_id' => $developer_id,
                                    'customer_id' => $row->customerId,
                                ]))); ?>">
                                    <?php echo htmlspecialchars($row->customerName); ?>
                                </a>
                            </td>
                            <td class="text-start"><?php echo nl2br(htmlspecialchars($row->notes)); ?></td>
                            <td><?php echo htmlspecialchars($row->start_time); ?></td>
                            <td><?php echo htmlspecialchars($row->finish_time); ?></td>
                            <td>
                                <?php
                                if (!empty($row->start_time) && !empty($row->finish_time)) {
                                    $diff = strtotime($row->finish_time) - strtotime($row->start_time);
                                    if ($diff < 0) {
                                        $diff = 0;
                                    }
                                    $totalSeconds += $diff;
                                    printf('%02d:%02d', floor($diff / 3600), floor(($diff % 3600) / 60));
                                } elseif (!empty($row->duration_minutes)) {
                                    $mins = (int) $row->duration_minutes;
                                    $totalSeconds += $mins * 60;
                                    printf('%02d:%02d', floor($mins / 60), $mins % 60);
                                }
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($rows)): ?>
                    <tfoot>
                        <?php
                        $totalDays = floor($totalSeconds / 86400);
                        $remainingSeconds = $totalSeconds % 86400;
                        $totalHours = floor($remainingSeconds / 3600);
                        $totalMinutes = floor(($remainingSeconds % 3600) / 60);
                        ?>
                        <tr>
                            <th colspan="10" class="text-right">TOTAL TIME</th>
                            <th class="text-center">
                                <?php
                                if ((int) $totalDays > 0) {
                                    printf('%02d days %02d:%02d', $totalDays, $totalHours, $totalMinutes);
                                } else {
                                    printf('%02d:%02d', $totalHours, $totalMinutes);
                                }
                                ?>
                            </th>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var customerSelect = document.getElementById('customer_id');
    var projectSelect = document.getElementById('project_id');
    var sprintWrap = document.getElementById('sprint_filter_wrap');
    var sprintSelect = document.getElementById('sprint_id');

    if (!customerSelect || !projectSelect) {
        return;
    }

    customerSelect.addEventListener('change', function () {
        projectSelect.innerHTML = '<option value="">All projects</option>';
        sprintSelect.innerHTML = '<option value="">All sprints</option>';
        sprintWrap.classList.add('d-none');
        var customerId = customerSelect.value;
        if (!customerId) {
            return;
        }
        $.post(base_url + 'projects/getByCustomerId', { customer_id: customerId }, function (res) {
            var data = (typeof res === 'string') ? JSON.parse(res) : res;
            if (!data || !data.data) {
                return;
            }
            data.data.forEach(function (p) {
                var opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = p.name;
                projectSelect.appendChild(opt);
            });
        });
    });

    projectSelect.addEventListener('change', function () {
        sprintSelect.innerHTML = '<option value="">All sprints</option>';
        var projectId = projectSelect.value;
        if (!projectId) {
            sprintWrap.classList.add('d-none');
            return;
        }
        sprintWrap.classList.remove('d-none');
        $.post(base_url + 'sprints/getByProjectId', { project_id: projectId }, function (res) {
            var data = (typeof res === 'string') ? JSON.parse(res) : res;
            if (!data || !data.data) {
                return;
            }
            data.data.forEach(function (s) {
                var opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.name;
                sprintSelect.appendChild(opt);
            });
        });
    });
})();
</script>
