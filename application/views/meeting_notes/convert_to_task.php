<div class="card">
    <div class="card-header">
        <h2>Convert Meeting Notes to Tasks</h2>
        <p class="text-muted mb-0">Meeting: <?= htmlspecialchars($note->customer_name) ?> - <?= htmlspecialchars($note->meeting_datetime) ?><?php if (!empty($note->next_meeting_date)): ?> · Next: <?= $this->Meeting_note_model->formatNextMeeting($note->next_meeting_date) ?><?php endif; ?></p>
    </div>
    <div class="card-body">
        <form id="convertToTaskForm" method="post">
            <input type="hidden" name="meeting_note_id" value="<?= $note->id ?>">
            
            <!-- Customer, Project, Sprint Selection -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="customer_id">Customer <span class="text-danger">*</span></label>
                        <select class="form-control required" name="customer_id" id="customer_id" required>
                            <option value="">Select Customer</option>
                            <?php foreach($customers as $c):?>
                            <option value="<?php echo $c->customer_id;?>"
                                <?php echo ($c->customer_id == $note->customer_id) ? 'selected' : '';?>>
                                <?php echo htmlspecialchars($c->company_name);?></option>
                            <?php endforeach;?>
                        </select>
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

            <hr>

            <!-- Task Defaults -->
            <h4>Task Defaults</h4>
            <p class="text-muted">These settings will apply to all tasks by default. You can override them for individual tasks below.</p>
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="default_due_date">Due Date</label>
                        <input type="date" class="form-control" name="default_due_date" id="default_due_date" 
                            value="<?= date('Y-m-d', strtotime('+5 days')) ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="default_estimated_hours">Estimated Hours</label>
                        <input type="number" step="0.25" min="0" class="form-control" name="default_estimated_hours" 
                            id="default_estimated_hours" value="1">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="default_developers">Developers</label>
                        <select class="form-control" name="default_developers[]" id="default_developers" multiple 
                            style="height: 100px;">
                            <?php foreach($developers as $developer): ?>
                            <?php if($developer->user_type != 'developer') continue; ?>
                            <option value="<?= $developer->id ?>">
                                <?= htmlspecialchars($developer->name . ' (' . $developer->email . ')') ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple developers</small>
                    </div>
                </div>
            </div>

            <hr>

            <!-- Tasks Preview -->
            <h4>Tasks to be Created (<?= count($list_items) ?> items)</h4>
            <p class="text-muted">Each list item from the meeting notes will become a separate task. You can edit the task names and sections below.</p>
            
            <div id="tasks-preview" class="mt-3">
                <?php foreach($list_items as $index => $item): ?>
                <div class="card mb-3 task-item" data-index="<?= $index ?>">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Section</label>
                                    <input type="text" class="form-control task-section" name="task_sections[]" 
                                        value="Meeting Notes" placeholder="e.g. Reports">
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label>Task Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control task-name required" name="task_names[]" 
                                        value="<?= htmlspecialchars($item) ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description (optional)</label>
                            <textarea class="form-control task-description" name="task_descriptions[]" rows="2" 
                                placeholder="Additional task description"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Due Date</label>
                                    <input type="date" class="form-control task-due-date" name="task_due_dates[]" 
                                        value="<?= date('Y-m-d', strtotime('+5 days')) ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Estimated Hours</label>
                                    <input type="number" step="0.25" min="0" class="form-control task-estimated-hours" 
                                        name="task_estimated_hours[]" value="1">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Developers</label>
                                    <select class="form-control task-developers" name="task_developers[<?= $index ?>][]" 
                                        multiple style="height: 80px;">
                                        <?php foreach($developers as $developer): ?>
                                        <?php if($developer->user_type != 'developer') continue; ?>
                                        <option value="<?= $developer->id ?>">
                                            <?= htmlspecialchars($developer->name . ' (' . $developer->email . ')') ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="form-text text-muted">Ctrl/Cmd to select multiple</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> 
                <strong>Note:</strong> A link to the meeting notes will be automatically added to each task description.
            </div>
        </form>

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
                        <form id="createSprintForm">
                            <input type="hidden" name="project_id" id="modal_project_id" value="">
                            <div class="form-group">
                                <label for="sprint_name">Sprint Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="sprint_name" required>
                                <small class="form-text text-muted">Format: yyyymmdd (e.g., <?= date('Ymd') ?>)</small>
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
            <i class="fa fa-tasks"></i> Create <?= count($list_items) ?> Task(s)
        </button>
        <a href="<?= site_url('meeting_notes/view/'.$note->id) ?>" class="btn btn-back">
            <i class="fa fa-times"></i> Cancel
        </a>
    </div>
</div>

<script>
// Wait for jQuery to be available
(function() {
    function initConvertToTask() {
        if (typeof jQuery === 'undefined' || typeof base_url === 'undefined') {
            setTimeout(initConvertToTask, 100);
            return;
        }
        
        var $ = jQuery;
        
        // Define functions first
        function getByCustomerId(customer_id) {
            if(!customer_id) {
                $('#project_id').empty().append('<option value="">Select Project</option>');
                $('#sprint_id').empty().append('<option value="">Select Sprint</option>');
                return;
            }
            
            console.log('Loading projects for customer_id:', customer_id);
            if(typeof Overlay !== 'undefined') Overlay("on");
            $.ajax({
                url: base_url + 'projects/getByCustomerId',
                type: 'POST',
                data: {customer_id: customer_id},
                dataType: 'json',
                success: function(response){
                    console.log('Projects response:', response);
                    $('#project_id').empty();
                    if(response.result && response.data && response.data.length > 0) {
                        $('#project_id').append('<option value="">Select Project</option>');
                        $(response.data).each(function(index, item){    
                            $('#project_id').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });
                    } else {
                        $('#project_id').append('<option value="">No projects found for this customer</option>');
                    }
                    $('#sprint_id').empty().append('<option value="">Select Sprint</option>');
                    $('#createTasksBtn').prop('disabled', true);
                    if(typeof Overlay !== 'undefined') Overlay("off");
                },
                error: function(xhr, status, error){
                    console.error('Error loading projects:', error);
                    console.error('Response:', xhr.responseText);
                    $('#project_id').empty().append('<option value="">Error loading projects</option>');
                    if(typeof Overlay !== 'undefined') Overlay("off");
                }
            });
        }

        function getByProjectId(project_id) {
            if(!project_id) {
                $('#sprint_id').empty().append('<option value="">Select Sprint</option>');
                return;
            }
            
            if(typeof Overlay !== 'undefined') Overlay("on");
            $.ajax({
                url: base_url + 'sprints/getByProjectId',
                type: 'POST',
                data: {project_id: project_id},
                dataType: 'json',
                success: function(response){
                    $('#sprint_id').empty();
                    if(response.result && response.data && response.data.length > 0) {
                        $('#sprint_id').append('<option value="">Select Sprint</option>');
                        $(response.data).each(function(index, item){    
                            $('#sprint_id').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });
                    } else {
                        $('#sprint_id').append('<option value="">No sprints found</option>');
                    }
                    $('#createTasksBtn').prop('disabled', true);
                    if(typeof Overlay !== 'undefined') Overlay("off");
                },
                error: function(xhr, status, error){
                    console.error('Error loading sprints:', error);
                    $('#sprint_id').empty().append('<option value="">Error loading sprints</option>');
                    if(typeof Overlay !== 'undefined') Overlay("off");
                }
            });
        }
        
        // Now set up event handlers
        $(document).ready(function(){
            // Load projects when customer is selected
            $('#customer_id').on('change', function(){
                var customer_id = $(this).val();
                if(customer_id) {
                    getByCustomerId(customer_id);
                } else {
                    $('#project_id').empty().append('<option value="">Select Project</option>');
                    $('#sprint_id').empty().append('<option value="">Select Sprint</option>');
                    $('#createTasksBtn').prop('disabled', true);
                }
            });
            
            // Load sprints when project is selected
            $('#project_id').on('change', function(){
                var project_id = $(this).val();
                if(project_id) {
                    getByProjectId(project_id);
                } else {
                    $('#sprint_id').empty().append('<option value="">Select Sprint</option>');
                    $('#createTasksBtn').prop('disabled', true);
                }
            });

            // Enable create button when sprint is selected
            $('#sprint_id').on('change', function(){
                if($(this).val() && $('#customer_id').val() && $('#project_id').val()) {
                    $('#createTasksBtn').prop('disabled', false);
                } else {
                    $('#createTasksBtn').prop('disabled', true);
                }
            });

            // Sync default values to all tasks
            function syncDefaultsToTasks() {
                var defaultDueDate = $('#default_due_date').val();
                var defaultHours = $('#default_estimated_hours').val();
                var defaultDevelopers = $('#default_developers').val() || [];
                
                // Update all task due dates
                $('.task-due-date').val(defaultDueDate);
                
                // Update all task estimated hours
                $('.task-estimated-hours').val(defaultHours);
                
                // Update all task developers
                $('.task-developers').each(function(){
                    $(this).val(defaultDevelopers);
                });
            }

            // Sync defaults when default fields change
            $('#default_due_date, #default_estimated_hours, #default_developers').on('change', function(){
                syncDefaultsToTasks();
            });

            // Add button to manually sync defaults
            $('#default_developers').after('<button type="button" class="btn btn-sm btn-secondary mt-2" id="syncDefaultsBtn"><i class="fa fa-sync"></i> Apply to All Tasks</button>');
            $('#syncDefaultsBtn').on('click', function(){
                syncDefaultsToTasks();
                alert('Default values have been applied to all tasks. You can still edit individual task values.');
            });

            // Create tasks
            $('#createTasksBtn').on('click', function(){
                if(!$('#convertToTaskForm')[0].checkValidity()) {
                    $('#convertToTaskForm')[0].reportValidity();
                    return;
                }

                if(!confirm('Are you sure you want to create <?= count($list_items) ?> task(s)?')) {
                    return;
                }

                var $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Creating Tasks...');

                $.ajax({
                    url: base_url + 'meeting_notes/createTasksFromNote',
                    type: 'POST',
                    data: $('#convertToTaskForm').serialize(),
                    dataType: 'json',
                    success: function(response){
                        if(response.result) {
                            alert(response.message + (response.errors && response.errors.length > 0 ? '\n\nErrors:\n' + response.errors.join('\n') : ''));
                            window.location.href = base_url + 'tasks/listing?customer_id=' + $('#customer_id').val() + '&project_id=' + $('#project_id').val() + '&sprint_id=' + $('#sprint_id').val();
                        } else {
                            alert('Error: ' + (response.reason || 'Unknown error'));
                            $btn.prop('disabled', false).html('<i class="fa fa-tasks"></i> Create <?= count($list_items) ?> Task(s)');
                        }
                    },
                    error: function(){
                        alert('An error occurred while creating tasks. Please try again.');
                        $btn.prop('disabled', false).html('<i class="fa fa-tasks"></i> Create <?= count($list_items) ?> Task(s)');
                    }
                });
            });
            
            // Load projects if customer is pre-selected
            <?php if(!empty($note->customer_id)): ?>
            var preSelectedCustomer = $('#customer_id').val();
            if(preSelectedCustomer) {
                // Small delay to ensure DOM is ready
                setTimeout(function(){
                    getByCustomerId(preSelectedCustomer);
                }, 300);
            }
            <?php endif; ?>

            function ymdToIsoDate(ymd) {
                if (!ymd || String(ymd).length !== 8) {
                    return '';
                }
                var s = String(ymd);
                return s.substring(0, 4) + '-' + s.substring(4, 6) + '-' + s.substring(6, 8);
            }

            function addDaysIso(dateStr, days) {
                if (!dateStr) {
                    return '';
                }
                var p = dateStr.split('-');
                var dt = new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
                dt.setDate(dt.getDate() + days);
                var y = dt.getFullYear();
                var m = String(dt.getMonth() + 1).padStart(2, '0');
                var d = String(dt.getDate()).padStart(2, '0');
                return y + '-' + m + '-' + d;
            }

            function openCreateSprintModal(project_id, meetingDate) {
                $('#modal_project_id').val(project_id);
                $('#sprint_name').val(meetingDate);
                var startIso = ymdToIsoDate(meetingDate) || '<?= date('Y-m-d') ?>';
                var endIso = addDaysIso(startIso, 14);
                $('#sprint_start_date').val(startIso).attr('min', startIso).attr('max', endIso);
                $('#sprint_end_date').val(endIso).attr('min', startIso).attr('max', endIso);
                $('#createSprintModal').modal('show');
            }

            $('#sprint_start_date').on('change', function(){
                var startVal = $(this).val();
                if (!startVal) {
                    return;
                }
                var maxEnd = addDaysIso(startVal, 14);
                $('#sprint_end_date').attr('min', startVal).attr('max', maxEnd);
                var endVal = $('#sprint_end_date').val();
                if (!endVal || endVal < startVal || endVal > maxEnd) {
                    $('#sprint_end_date').val(maxEnd);
                }
            });

            function promptEmptySprintsListing(emptyResponse) {
                var hasEmpty = emptyResponse && (
                    emptyResponse.has_empty
                    || (emptyResponse.empty_sprints && emptyResponse.empty_sprints.length)
                );
                if (!hasEmpty) {
                    return false;
                }
                var message = emptyResponse.reason || 'This project already has empty sprint(s) with no tasks.';
                var confirmMessage = message + '\n\nView the sprint list now?';
                if (emptyResponse.listing_url && window.confirm(confirmMessage)) {
                    window.location.href = emptyResponse.listing_url;
                    return true;
                }
                alert(message);
                return true;
            }

            // Create new sprint functionality
            $('#createSprintBtn').on('click', function(){
                var project_id = $('#project_id').val();
                if(!project_id) {
                    alert('Please select a project first');
                    return;
                }
                
                // Get meeting date and format as yyyymmdd
                var meetingDate = '<?= !empty($note->meeting_datetime) ? date("Ymd", strtotime($note->meeting_datetime)) : date("Ymd") ?>';
                
                if(typeof Overlay !== 'undefined') Overlay("on");
                $.ajax({
                    url: base_url + 'sprints/checkEmptySprints',
                    type: 'POST',
                    data: { project_id: project_id },
                    dataType: 'json',
                    success: function(emptyResponse){
                        if(emptyResponse.result && emptyResponse.has_empty) {
                            if(typeof Overlay !== 'undefined') Overlay("off");
                            promptEmptySprintsListing(emptyResponse);
                            return;
                        }

                        // Check if sprint with this name already exists
                        $.ajax({
                    url: base_url + 'sprints/checkSprintExists',
                    type: 'POST',
                    data: {
                        project_id: project_id,
                        name: meetingDate
                    },
                    dataType: 'json',
                    success: function(response){
                        if(typeof Overlay !== 'undefined') Overlay("off");
                        if(response.result && response.exists) {
                            // Sprint exists, use it
                            var sprintId = response.sprint.id;
                            var sprintName = response.sprint.name;
                            
                            // Check if sprint is already in dropdown
                            if($('#sprint_id option[value="'+sprintId+'"]').length === 0) {
                                // Add it to dropdown
                                $('#sprint_id').append('<option value="'+sprintId+'">'+sprintName+'</option>');
                            }
                            
                            // Select it
                            $('#sprint_id').val(sprintId);
                            
                            // Enable create tasks button
                            if($('#customer_id').val() && $('#project_id').val()) {
                                $('#createTasksBtn').prop('disabled', false);
                            }
                            
                            alert('Using existing sprint: ' + sprintName);
                        } else {
                            openCreateSprintModal(project_id, meetingDate);
                        }
                    },
                    error: function(){
                        if(typeof Overlay !== 'undefined') Overlay("off");
                        openCreateSprintModal(project_id, meetingDate);
                    }
                });
                    },
                    error: function(){
                        if(typeof Overlay !== 'undefined') Overlay("off");
                        alert('Could not verify sprint status. Please try again.');
                    }
                });
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
                            var sprintId = response.sprint.id;
                            var sprintName = response.sprint.name;
                            
                            // Check if sprint is already in dropdown
                            if($('#sprint_id option[value="'+sprintId+'"]').length === 0) {
                                // Add new sprint to dropdown
                                $('#sprint_id').append('<option value="'+sprintId+'">'+sprintName+'</option>');
                            }
                            
                            // Select it
                            $('#sprint_id').val(sprintId);
                            
                            // Enable create tasks button
                            if($('#customer_id').val() && $('#project_id').val()) {
                                $('#createTasksBtn').prop('disabled', false);
                            }
                            
                            $('#createSprintModal').modal('hide');
                            $form[0].reset();
                            
                            if(response.existing) {
                                alert('Using existing sprint: ' + sprintName);
                            }
                        } else {
                            if (response.empty_sprints && response.empty_sprints.length && promptEmptySprintsListing(response)) {
                                // redirect offered via confirm, or user declined
                            } else {
                                alert('Error: ' + (response.reason || 'Failed to create sprint'));
                            }
                        }
                        $btn.prop('disabled', false).html('Create Sprint');
                    },
                    error: function(){
                        alert('An error occurred while creating the sprint. Please try again.');
                        $btn.prop('disabled', false).html('Create Sprint');
                    }
                });
            });
        });
    }
    
    // Start initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initConvertToTask);
    } else {
        initConvertToTask();
    }
})();
</script>

