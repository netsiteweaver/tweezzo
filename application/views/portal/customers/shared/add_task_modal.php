<style>
.list-group-item:hover {
    /* border: 1px solid #2cbfc6; */
    /* background-color: #2cbfc6; */
}

.list-group-item.selected {
    /* background-color: #2cbfc6; */
    background-color: #4c4c4c;
    color: #fff;
}

p.title {
    font-weight: bold;
    margin-bottom: 0px;
    margin-top: 20px;
}
p.notes {
    font-size: 0.95em;
    font-weight: bold;
    color: #f66;
    margin-bottom: 0px;
    margin-top: 0px;
}
</style>
<!-- Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTaskModalLabel">Request for additional Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row submission">
                    <div class="col-md-12">
                        <p>Please note your request will be queued, by default, for a new sprint. We will send you an estimate of the sprint for your approval before starting it. Also note that, for better project management, sprints can allow only a limited number of tasks. </p>
                    </div>
                </div>
                <div class="row submission">
                    <div class="col-md-6">
                        <div class="form-group">
                            <p class='title'>Section</p>
                            <p class="notes">Tell us where to implement this functionality. Typically this would be the url needed to access the page where this functionality will be implemented</p>
                            <input type="text" name='section' class="form-control">
                        </div>
                        <div class="form-group">
                            <p class='title'>Task Name</p>
                            <p class="notes">Give a brief description of the task</p>
                            <input type="text" name='name' class="form-control">
                        </div>
                        <div class="form-group">
                            <p class='title'>Task Description</p>
                            <p class="notes">Describe the task in more details here</p>
                            <textarea type="text" rows='5' name='description' class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <p class="title">What's expected from this task</p>
                            <p class="notes">Tell us what you expect from this task. This can be in terms of display, print, performance or any other</p>
                            <textarea class="form-control" rows="5" name="scope_client_expectation" id="" placeholder="" required></textarea>
                        </div>
                        <div class="form-group">
                            <p class="title">What's not included</p>
                            <p class="notes">To avoid confusion and delay, let us know what is not included in this task. If nothing is specified here, the scope of this task will be limited <u>strictly</u> to the task description.</p>
                            <textarea class="form-control" rows="5" name="scope_not_included" id="" placeholder="" required></textarea>
                        </div>
                        <div class="form-group">
                            <p class="title">When it's considered done</p>
                            <p class="notes">Tell us what you expect from this task for it to be completed.</p>
                            <textarea class="form-control" rows="5" name="scope_when_done" id="" placeholder="" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="row thankyou d-none">
                    <div class="col-md-1"></div>
                    <div class="col-md-5">
                        <img style='width:100%'src="assets/images/thank-you-face.png" alt="">
                    </div>
                    <div class="col-md-5">
                        <p class='mt-5' style='font-size:2em; font-weight:bold;'>Thank you for your request</p>
                        <p>Our team will get back to you as soon as possible with an estimate of the sprint for your approval before starting it. </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i>
                    Close</button>
                <button type="button" class="btn btn-info submit-task"><i class="bi bi-save"></i> Create Task</button>
            </div>
        </div>
    </div>
</div>