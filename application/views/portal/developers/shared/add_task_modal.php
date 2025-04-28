<style>
.list-group-item:hover {
    background-color: #eee;
}
.list-group-item{
    border-radius: 0px !important;
    border: 1px solid #ccc !important;
    margin-bottom: 3px;
}
.list-group-item.selected {
    /* background-color: rgb(124, 182, 185); */
    background-color: #4c4c4c;
    color: #fff;
}
p.text-bold{
  font-weight: bold;
  margin-bottom:0px;
  margin-top: 20px;
}
</style>
<!-- Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTaskModalLabel">Add a Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <!-- <div class="row"><div class="col-md-12"><p class="text-bold text-center"> -->
                <!-- Tasks submitted will be grouped in the next sprint. We will send an estimate for your approval before starting to work on it. Please ensure that the task is well defined and includes all the necessary information. -->
              <!-- </p></div></div> -->
               <div class="row d-none">
                <div class="col-md-12">
                    <table id="selection" class="table table-bordered">
                        <tbody>
                            <tr>
                                <td class='col-md-3' id='selected-customer'></td>
                                <td class='col-md-3' id='selected-project'></td>
                                <td class='col-md-3' id='selected-sprint'></td>
                                <td><div class="btn btn-warning" id="resetModalAddTask"><i class="bi bi-arrow-counterclockwise"></i> Reset</div></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
               </div>
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <div class="form-group mb-5">
                            <p class='text-bold'>Select a Customer</p>
                            <input type="hidden" name='customer_id' value="">
                            <input type="hidden" name='project_id' value="">
                            <input type="hidden" name='sprint_id' value="">
                            <ul class="list-group customers">
                                <?php foreach($customers as $customer):?>
                                <li data-customer-name="<?php echo $customer->company_name;?>" data-customer-id="<?php echo $customer->customer_id;?>"
                                    class="cursor-pointer list-group-item select-customer">
                                    <?php echo $customer->company_name;?>
                                </li>
                                <?php endforeach;?>
                            </ul>
                        </div>
                        <div class="form-group mb-5 d-none">
                            <p class='text-bold'>Now Select a Project</p>
                            <ul class="list-group projects">
                                <?php foreach($projects as $project):?>
                                <li data-customer-id="<?php echo $project->customer_id;?>"
                                    data-project-name="<?php echo $project->name;?>"
                                    data-project-id="<?php echo $project->id;?>"
                                    class="cursor-pointer list-group-item select-project d-none">
                                    <?php echo $project->name;?>
                                </li>
                                <?php endforeach;?>
                            </ul>
                        </div>
                        <div class="form-group mb-5 d-none">
                            <p class='text-bold'>Now Select a Sprint</p>
                            <ul class="list-group sprints">
                                <?php foreach($sprints as $sprint):?>
                                <li data-project-id="<?php echo $sprint->project_id;?>"
                                    data-sprint-id="<?php echo $sprint->id;?>"
                                    data-sprint-name="<?php echo $sprint->name;?>"
                                    class="list-group-item select-sprint d-none">
                                    <?php echo $sprint->name;?></li>
                                <?php endforeach;?>
                            </ul>
                        </div>
                    </div>
                </row>
                <div class='row'>
                    <div class="col-md-6 data-input-left d-none">
                        <div class="form-group">
                            <p class='text-bold'>Section</p>
                            <input type="text" name="section" class="form-control">
                        </div>
                        <div class="form-group">
                            <p class='text-bold'>Task Number</p>
                            <input type="text" name="task_number" class="form-control">
                        </div>
                        <div class="form-group">
                            <p class='text-bold'>Task Name</p>
                            <input type="text" name="name" class="form-control">
                        </div>
                        <div class="form-group">
                            <p class='text-bold'>Task Description</p>
                            <textarea type="text" rows='5' name="description" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <p class='text-bold'>Preferred due Date</p>
                            <input type="date" name="due_date" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6 data-input-right d-none">
                        <p class="text-bold">Scope</p>
                        <div class="form-group">
                            <p class="text-bold">What's expected from this task</p>
                            <p class="notes">Tell us what you expect from this task. This can be in terms of display, print, performance or any other</p>
                            <textarea class="form-control" rows="5" name="scope_client_expectation" id="" placeholder="" required></textarea>
                        </div>
                        <div class="form-group">
                            <p class="text-bold">What's not included</p>
                            <p class="notes">To avoid confusion and delay, let us know what is not included in this task. If nothing is specified here, the scope of this task will be limited <u>strictly</u> to the task description.</p>
                            <textarea class="form-control" rows="5" name="scope_not_included" id="" placeholder="" required></textarea>
                        </div>
                        <div class="form-group mb-4">
                            <p class="text-bold">When it's considered done</p>
                            <p class="notes">Tell us what you expect from this task for it to be completed.</p>
                            <textarea class="form-control" rows="5" name="scope_when_done" id="" placeholder="" required></textarea>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i>
                    Close</button>
                <button type="button" class="btn btn-info create-task"><i class="bi bi-save"></i> Create Task</button>
            </div>
        </div>
    </div>
</div>