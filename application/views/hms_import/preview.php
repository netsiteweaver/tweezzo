<?php
$total_tasks = 0;
foreach ($lines as $line) {
    $total_tasks += count($line['tasks']);
}
$client_label = !empty($quotation['client']['company']) ? $quotation['client']['company'] : $quotation['client']['name'];
?>
<div class="card">
    <div class="card-header">
        <h2><?= ($quotation['type'] == 'estimate') ? 'Estimate' : 'Quotation' ?> <?= htmlspecialchars($quotation['number']) ?> &rarr; Tasks</h2>
        <p class="text-muted mb-0">
            <?= htmlspecialchars($client_label) ?> &middot; <?= htmlspecialchars($quotation['title']) ?> &middot; <?= htmlspecialchars($quotation['date']) ?>
            &middot; <a href="<?= htmlspecialchars($quotation['url']) ?>" target="_blank">Open in HMS</a>
        </p>
    </div>
    <div class="card-body">
        <?php if ($already_imported > 0 || !empty($quotation['exported_on'])): ?>
        <div class="alert alert-warning">
            <i class="fa fa-exclamation-triangle"></i>
            <strong>Already imported.</strong>
            <?php if ($already_imported > 0): ?>
            <?= (int) $already_imported ?> task(s) with reference <?= htmlspecialchars($quotation['number']) ?> exist.
            <a href="<?= base_url('tasks/listing?search_text=' . rawurlencode($quotation['number'])) ?>" target="_blank">View them</a>.
            <?php else: ?>
            HMS shows this quotation as sent on <?= htmlspecialchars(date('d M Y', strtotime($quotation['exported_on']))) ?>.
            <?php endif; ?>
            Saving again creates new tasks next to the existing ones.
        </div>
        <?php endif; ?>

        <form id="hmsImportForm" method="post" onsubmit="return false;">
            <input type="hidden" name="quotation_uuid" value="<?= htmlspecialchars($quotation['uuid']) ?>">

            <!-- Customer, Project, Sprint Selection -->
            <div class="row mb-2">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="customer_id">Customer <span class="text-danger">*</span></label>
                        <select class="form-control required" name="customer_id" id="customer_id" required>
                            <option value="">Select Customer</option>
                            <?php foreach($customers as $c):?>
                            <option value="<?php echo $c->customer_id;?>"
                                <?php echo ($c->customer_id == $matched_customer_id) ? 'selected' : '';?>>
                                <?php echo htmlspecialchars($c->company_name);?></option>
                            <?php endforeach;?>
                        </select>
                        <?php if (empty($matched_customer_id)): ?>
                        <small class="form-text text-warning">No customer matches "<?= htmlspecialchars($client_label) ?>"<?= !empty($quotation['client']['email']) ? ' (' . htmlspecialchars($quotation['client']['email']) . ')' : '' ?>. Please pick one.</small>
                        <?php else: ?>
                        <small class="form-text text-muted">Matched to HMS client "<?= htmlspecialchars($client_label) ?>".</small>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="project_id">Project <span class="text-danger">*</span></label>
                        <select name="project_id" id="project_id" class="form-control" required>
                            <option value="">Select Project</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="sprint_id">Sprint <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="sprint_id" id="sprint_id" class="form-control" required>
                                <option value="">Select Sprint</option>
                            </select>
                            <button type="button" class="btn btn-primary" id="createSprintBtn" title="Create New Sprint">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Applied to every task -->
            <div class="row mb-2">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="due_date">Due Date</label>
                        <input type="date" class="form-control" name="due_date" id="due_date" value="">
                        <small class="form-text text-muted">Optional. Cannot be later than the sprint end date.</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="developers">Developers</label>
                        <select class="form-control" name="developers[]" id="developers" multiple style="height: 100px;">
                            <?php foreach($developers as $developer): ?>
                            <?php if($developer->user_type != 'developer') continue; ?>
                            <option value="<?= $developer->id ?>">
                                <?= htmlspecialchars($developer->name . ' (' . $developer->email . ')') ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Optional. Hold Ctrl/Cmd to select several.</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Billing</label>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="billable" id="billable" value="1" checked>
                            <label class="form-check-label" for="billable">Mark tasks as billable</label>
                        </div>
                        <small class="form-text text-muted">Each task gets <?= htmlspecialchars($quotation['number']) ?> as its reference.</small>
                    </div>
                </div>
            </div>
        </form>

        <hr>

        <h4>Tasks to be created (<span class="selected-count">0</span> of <?= $total_tasks ?>)</h4>
        <p class="text-muted">Every list item or table row of a quotation line is a separate task. Edit anything below, and untick what should not be imported.</p>

        <?php foreach ($lines as $l => $line): ?>
        <div class="card card-outline card-secondary mb-3 hms-line">
            <div class="card-header">
                <div class="form-check d-inline-block">
                    <input type="checkbox" class="form-check-input line-toggle" id="line_<?= $l ?>" <?= $line['recurring'] ? '' : 'checked' ?>>
                    <label class="form-check-label font-weight-bold" for="line_<?= $l ?>">
                        <?= htmlspecialchars($line['product'] !== '' ? $line['product'] : 'Line ' . ($l + 1)) ?>
                    </label>
                </div>
                <span class="text-muted ml-2">
                    <?= rtrim(rtrim(number_format($line['qty'], 2), '0'), '.') ?> <?= htmlspecialchars($line['unit']) ?>
                    &middot; <?= count($line['tasks']) ?> task(s)
                    <?php if ($line['recurring']): ?>&middot; recurring service, left out by default<?php endif; ?>
                </span>
                <?php if ($line['original'] !== ''): ?>
                <a href="#" class="float-right toggle-original" data-target="#original_<?= $l ?>">Show quotation text</a>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if ($line['original'] !== ''): ?>
                <div id="original_<?= $l ?>" class="p-3 border-bottom text-muted" style="display:none; white-space:pre-line;"><?= htmlspecialchars($line['original']) ?></div>
                <?php endif; ?>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th style="width:36px;"></th>
                                <th style="width:24%;">Section</th>
                                <th>Task</th>
                                <th style="width:110px;">Est. hours</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($line['tasks'] as $task): ?>
                            <tr class="task-row">
                                <td class="text-center align-middle">
                                    <input type="checkbox" class="task-include" aria-label="Import this task" <?= $line['recurring'] ? '' : 'checked' ?>>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm task-section" maxlength="255" value="<?= htmlspecialchars($task['section']) ?>" placeholder="e.g. Reports">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm task-name" maxlength="255" value="<?= htmlspecialchars($task['name']) ?>" placeholder="Task name">
                                    <textarea class="form-control form-control-sm task-description mt-1" rows="<?= ($task['description'] !== '') ? min(4, substr_count($task['description'], "\n") + 1) : 1 ?>" placeholder="Description (optional)"><?= htmlspecialchars($task['description']) ?></textarea>
                                </td>
                                <td>
                                    <input type="number" step="0.25" min="0" class="form-control form-control-sm task-hours" value="<?= ($task['hours'] !== null) ? htmlspecialchars((string) $task['hours']) : '' ?>">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <div class="alert alert-info mb-0">
            <i class="fa fa-info-circle"></i>
            A link back to the quotation is added to each task description. Est. hours selected: <strong class="selected-hours">0</strong>
        </div>

        <!-- Create Sprint Modal -->
        <div class="modal fade" id="createSprintModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create New Sprint</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="createSprintForm" onsubmit="return false;">
                            <input type="hidden" name="project_id" id="modal_project_id" value="">
                            <div class="form-group">
                                <label for="sprint_name">Sprint Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="sprint_name" maxlength="50" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sprint_start_date">Start Date</label>
                                        <input type="date" class="form-control" name="start_date" id="sprint_start_date" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sprint_end_date">End Date</label>
                                        <input type="date" class="form-control" name="end_date" id="sprint_end_date" required>
                                    </div>
                                </div>
                            </div>
                            <small class="form-text text-muted">Maximum sprint length: 14 days.</small>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveSprintBtn">Create Sprint</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button type="button" id="createTasksBtn" class="btn btn-success" disabled>
            <i class="fa fa-tasks"></i> Create <span class="selected-count">0</span> Task(s)
        </button>
        <a href="<?= site_url('hms_import/index') ?>" class="btn btn-back">
            <i class="fa fa-times"></i> Cancel
        </a>
    </div>
</div>

<script>
// Wait for jQuery to be available
(function() {
    function initHmsImport() {
        if (typeof jQuery === 'undefined' || typeof base_url === 'undefined') {
            setTimeout(initHmsImport, 100);
            return;
        }

        var $ = jQuery;
        var createLabel = $('#createTasksBtn').html();

        function overlay(state) {
            if(typeof Overlay !== 'undefined') Overlay(state);
        }

        function resetSelect($select, label) {
            $select.empty().append($('<option>').val('').text(label));
        }

        function selectedRows() {
            return $('.task-row').filter(function(){
                return $(this).find('.task-include').prop('checked') && $.trim($(this).find('.task-name').val()) !== '';
            });
        }

        function refresh() {
            var $rows = selectedRows();
            var hours = 0;
            $rows.each(function(){
                hours += parseFloat($(this).find('.task-hours').val()) || 0;
            });
            $('.selected-count').text($rows.length);
            $('.selected-hours').text(Math.round(hours * 100) / 100);
            $('.task-row').each(function(){
                var on = $(this).find('.task-include').prop('checked');
                $(this).toggleClass('text-muted', !on).find('input[type=text], input[type=number], textarea').prop('disabled', !on);
            });
            var ready = $rows.length > 0 && $('#customer_id').val() && $('#project_id').val() && $('#sprint_id').val();
            $('#createTasksBtn').prop('disabled', !ready);
        }

        function getByCustomerId(customer_id) {
            resetSelect($('#project_id'), 'Select Project');
            resetSelect($('#sprint_id'), 'Select Sprint');
            refresh();
            if(!customer_id) return;

            overlay("on");
            $.ajax({
                url: base_url + 'projects/getByCustomerId',
                type: 'POST',
                data: {customer_id: customer_id},
                dataType: 'json',
                success: function(response){
                    if(response.result && response.data && response.data.length > 0) {
                        $(response.data).each(function(index, item){
                            $('#project_id').append($('<option>').val(item.id).text(item.name));
                        });
                        // A customer with a single project needs no choice
                        if(response.data.length === 1) {
                            $('#project_id').val(response.data[0].id).trigger('change');
                        }
                    } else {
                        resetSelect($('#project_id'), 'No projects found for this customer');
                    }
                    overlay("off");
                },
                error: function(){
                    resetSelect($('#project_id'), 'Error loading projects');
                    overlay("off");
                }
            });
        }

        function getByProjectId(project_id) {
            resetSelect($('#sprint_id'), 'Select Sprint');
            refresh();
            if(!project_id) return;

            overlay("on");
            $.ajax({
                url: base_url + 'sprints/getByProjectId',
                type: 'POST',
                data: {project_id: project_id},
                dataType: 'json',
                success: function(response){
                    if(response.result && response.data && response.data.length > 0) {
                        $(response.data).each(function(index, item){
                            $('#sprint_id').append($('<option>').val(item.id).text(item.name + (item.past_end ? ' (ended)' : '')));
                        });
                    } else {
                        resetSelect($('#sprint_id'), 'No sprints found');
                    }
                    overlay("off");
                },
                error: function(){
                    resetSelect($('#sprint_id'), 'Error loading sprints');
                    overlay("off");
                }
            });
        }

        function isoDate(dt) {
            return dt.getFullYear() + '-' + String(dt.getMonth() + 1).padStart(2, '0') + '-' + String(dt.getDate()).padStart(2, '0');
        }

        function addDaysIso(dateStr, days) {
            var p = dateStr.split('-');
            var dt = new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
            dt.setDate(dt.getDate() + days);
            return isoDate(dt);
        }

        function useSprint(sprint) {
            if($('#sprint_id option[value="'+sprint.id+'"]').length === 0) {
                $('#sprint_id').append($('<option>').val(sprint.id).text(sprint.name));
            }
            $('#sprint_id').val(sprint.id);
            refresh();
        }

        $(document).ready(function(){
            $('#customer_id').on('change', function(){
                getByCustomerId($(this).val());
            });

            $('#project_id').on('change', function(){
                getByProjectId($(this).val());
            });

            $('#sprint_id').on('change', refresh);

            // Tick or untick a whole quotation line
            $('.line-toggle').on('change', function(){
                $(this).closest('.hms-line').find('.task-include').prop('checked', $(this).prop('checked'));
                refresh();
            });

            $('.task-include').on('change', function(){
                var $line = $(this).closest('.hms-line');
                $line.find('.line-toggle').prop('checked', $line.find('.task-include:checked').length > 0);
                refresh();
            });

            $('.task-name, .task-hours').on('input', refresh);

            $('.toggle-original').on('click', function(e){
                e.preventDefault();
                var $target = $($(this).data('target'));
                $target.toggle();
                $(this).text($target.is(':visible') ? 'Hide quotation text' : 'Show quotation text');
            });

            // Create new sprint
            $('#createSprintBtn').on('click', function(){
                var project_id = $('#project_id').val();
                if(!project_id) {
                    alert('Please select a project first');
                    return;
                }
                var startIso = isoDate(new Date());
                var endIso = addDaysIso(startIso, 14);
                $('#modal_project_id').val(project_id);
                $('#sprint_name').val('');
                $('#sprint_start_date').val(startIso);
                $('#sprint_end_date').val(endIso).attr('min', startIso).attr('max', endIso);
                $.post(base_url + 'sprints/suggestName', {project_id: project_id}, function(response){
                    if(response.result && response.name && $('#sprint_name').val() === '') {
                        $('#sprint_name').val(response.name);
                    }
                }, 'json');
                $('#createSprintModal').modal('show');
            });

            $('#sprint_start_date').on('change', function(){
                var startVal = $(this).val();
                if(!startVal) return;
                var maxEnd = addDaysIso(startVal, 14);
                $('#sprint_end_date').attr('min', startVal).attr('max', maxEnd);
                var endVal = $('#sprint_end_date').val();
                if(!endVal || endVal < startVal || endVal > maxEnd) {
                    $('#sprint_end_date').val(maxEnd);
                }
            });

            $('#saveSprintBtn').on('click', function(){
                var $form = $('#createSprintForm');
                if(!$form[0].checkValidity()) {
                    $form[0].reportValidity();
                    return;
                }

                var $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Creating...');

                $.ajax({
                    url: base_url + 'sprints/createAjax',
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function(response){
                        if(response.result) {
                            useSprint(response.sprint);
                            $('#createSprintModal').modal('hide');
                            if(response.existing) {
                                alert('Using existing sprint: ' + response.sprint.name);
                            }
                        } else if(response.listing_url && confirm((response.reason || 'This project already has empty sprint(s) with no tasks.') + '\n\nView the sprint list now?')) {
                            window.open(response.listing_url, '_blank');
                        } else if(!response.listing_url) {
                            alert('Error: ' + (response.reason || 'Failed to create sprint'));
                        }
                        $btn.prop('disabled', false).html('Create Sprint');
                    },
                    error: function(){
                        alert('An error occurred while creating the sprint. Please try again.');
                        $btn.prop('disabled', false).html('Create Sprint');
                    }
                });
            });

            // Create tasks
            $('#createTasksBtn').on('click', function(){
                if(!$('#hmsImportForm')[0].checkValidity()) {
                    $('#hmsImportForm')[0].reportValidity();
                    return;
                }

                var tasks = [];
                selectedRows().each(function(){
                    tasks.push({
                        section: $(this).find('.task-section').val(),
                        name: $(this).find('.task-name').val(),
                        description: $(this).find('.task-description').val(),
                        hours: $(this).find('.task-hours').val()
                    });
                });
                if(tasks.length === 0) return;

                if(!confirm('Create ' + tasks.length + ' task(s) in sprint "' + $('#sprint_id option:selected').text() + '"?')) {
                    return;
                }

                var $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Creating Tasks...');

                var data = $('#hmsImportForm').serializeArray();
                data.push({name: 'tasks_json', value: JSON.stringify(tasks)});

                $.ajax({
                    url: base_url + 'hms_import/save',
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(response){
                        if(response.result) {
                            alert(response.message + (response.errors && response.errors.length > 0 ? '\n\nErrors:\n' + response.errors.join('\n') : ''));
                            window.location.href = base_url + 'tasks/listing?customer_id=' + $('#customer_id').val() + '&project_id=' + $('#project_id').val() + '&sprint_id=' + $('#sprint_id').val();
                        } else {
                            alert('Error: ' + (response.reason || 'Unknown error'));
                            $btn.html(createLabel);
                            refresh();
                        }
                    },
                    error: function(){
                        alert('An error occurred while creating tasks. Please check the task list before trying again.');
                        $btn.html(createLabel);
                        refresh();
                    }
                });
            });

            refresh();

            // Load projects if a customer was matched
            if($('#customer_id').val()) {
                getByCustomerId($('#customer_id').val());
            }
        });
    }

    // Start initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHmsImport);
    } else {
        initHmsImport();
    }
})();
</script>
