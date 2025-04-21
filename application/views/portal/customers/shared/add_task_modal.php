<style>
.list-group-item:hover {
    /* border: 1px solid #2cbfc6; */
    background-color: #2cbfc6;
}

.list-group-item.selected {
    background-color: #2cbfc6;
}

p.title {
    font-weight: bold;
    margin-bottom: 0px;
    margin-top: 20px;
}
</style>
<!-- Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTaskModalLabel">Add a Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        All submitted tasks will be reviewed for approval. Please be aware that:<br>
                        - Your task may not be included in the current sprint depending on priorities and capacity.<br>
                        - It might fall outside the current project scope, in which case an estimate will be provided for your review.<br>
                        - Approval and scheduling will be communicated after evaluation.<br>
                        Thank you for your understanding! <hr>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-5">
                            <p class='title'>Select a Project</p>
                            <input type="hidden" name='project_id' value="">
                            <input type="hidden" name='sprint_id' value="">
                            <ul class="list-group projects">
                                <?php foreach($projects as $project):?>
                                <li data-customer-id="<?php echo $project->customer_id;?>"
                                    data-project-id="<?php echo $project->id;?>"
                                    class="cursor-pointer list-group-item select-project">
                                    <?php echo $project->name;?>
                                </li>
                                <?php endforeach;?>
                            </ul>
                        </div>
                        <div class="form-group mb-5 d-none">
                            <p class='title'>Select Sprint</p>
                            <ul class="list-group sprints">
                                <?php foreach($sprints as $sprint):?>
                                <li data-project-id="<?php echo $sprint->project_id;?>"
                                    data-sprint-id="<?php echo $sprint->id;?>"
                                    class="list-group-item select-sprint d-none">
                                    <?php echo $sprint->name;?></li>
                                <?php endforeach;?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6 data-input d-none">
                        <div class="form-group">
                            <p class='title'>Section</p>
                            <input type="text" name='section' class="form-control">
                        </div>
                        <div class="form-group">
                            <p class='title'>Task Number</p>
                            <input type="text" name='task_number' class="form-control">
                        </div>
                        <div class="form-group">
                            <p class='title'>Task Name</p>
                            <input type="text" name='name' class="form-control">
                        </div>
                        <div class="form-group">
                            <p class='title'>Task Description</p>
                            <textarea type="text" rows='5' name='description' class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <p class='title'>Preferred due Date</p>
                            <input type="date" name='due_date' class="form-control">
                        </div>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i>
                    Close</button>
                <button type="button" class="btn btn-info create-task"><i class="bi bi-save"></i> Create Task</button>
            </div>
        </div>
    </div>
</div>