<style>

</style>
<form action="">
<div class="row">
    <div class="col-md-2">
        <input type="hidden" name="project_id" value="<?php echo $this->input->get("project_id");?>">
        <input type="hidden" name="sprint_id" value="<?php echo $this->input->get("sprint_id");?>">
        <input type="hidden" name="customer_id" value="<?php echo $this->input->get("customer_id");?>">
        <label for="">From</label>
        <input type="date" name='start_date' class='form-control monitor' value="<?php echo (empty($this->input->get('start_date'))) ? date("Y-m-01") : $this->input->get('start_date')?>">
    </div>
    <div class="col-md-2">
        <label for="">To</label>
        <input type="date" name='end_date' class='form-control monitor' value="<?php echo (empty($this->input->get('end_date'))) ? date("Y-m-t") : $this->input->get('end_date')?>">
    </div>
    <?php if( (!empty($this->input->get("customer_id"))) && (isset($notes[0]->company_name)) ):?>
    <div class="col-md-2">
        <label for="">Customer</label>
        <div class="form-control"><?php echo $notes[0]->company_name;?></div>
    </div>
    <?php endif;?>
    <?php if( (!empty($this->input->get("project_id"))) && (isset($notes[0]->projectName)) ):?>
    <div class="col-md-2">
        <label for="">Project</label>
        <div class="form-control"><?php echo $notes[0]->projectName;?></div>
    </div>
    <?php endif;?>
    <?php if( (!empty($this->input->get("sprint_id"))) && isset($notes[0]->sprintName) ):?>
    <div class="col-md-2">
        <label for="">Sprint</label>
        <div class="form-control"><?php echo $notes[0]->sprintName;?></div>
    </div>
    <?php endif;?>
    <div class="col-md-2 mt-4">
        <div class="btn btn-warning resetFilter" title="Reset filters"><i class="bi bi-arrow-counterclockwise"></i></div>
        <div class="btn btn-info download" title="Download the list in csv format (for LibrOffice, Excel or otherwise)"><i class="bi bi-download"></i></div>
    </div>
</div>
</form>
<div class="row mt-5 table-responsive">
    <div class="col-md-12">
        List of notes related to your tasks. You can click on sprint, project or customer and select date range to filter the list.
        <table id='notes' class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Note</th>
                    <th>Date</th>
                    <th>Author</th>
                    <th>Task: [#] Section / Task Name / Customer</th>
                    <?php if(empty($this->input->get("sprint_id"))):?>
                    <?php if(empty($this->input->get("customer_id"))):?>
                    <th>Customer</th>
                    <?php endif;?>
                    <th>Sprint</th>
                    <?php endif;?>
                    <?php if(empty($this->input->get("project_id"))):?>
                    <th>Project</th>
                    <?php endif;?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notes as $i=>$note):?>
                <tr class='<?php echo ($note->out_of_scope == 1) ? "out-of-scope" : "";?>'>
                    <td style='color:#999;font-size:0.9em;text-align:center;'><?php echo $i+1;?></td>
                    <td><?php echo nl2br($note->notes);?></td>
                    <td><?php echo $note->created_on;?></td>
                    <td><?php echo $note->author;?></td>
                    <td><?php echo "[{$note->taskNumber}] {$note->taskSection} / {$note->taskName} / {$note->company_name}";?></td>
                    <?php if(empty($this->input->get("customer_id"))):?>
                    <td style='text-decoration:underline;' data-customer-id='<?php echo $note->customerId;?>' class='filter-customer cursor-pointer'><?php echo $note->company_name;?></td>
                    <?php endif;?>
                    <?php if(empty($this->input->get("project_id"))):?>
                    <td style='text-decoration:underline;' data-project-id='<?php echo $note->projectId;?>' class='filter-project cursor-pointer'><?php echo $note->projectName;?></td>
                    <?php endif;?>
                    <?php if(empty($this->input->get("sprint_id"))):?>
                    <td style='text-decoration:underline;' data-sprint-id='<?php echo $note->sprintId;?>' class='filter-sprint cursor-pointer'><?php echo $note->sprintName;?></td>
                    <?php endif;?>
                    <td>
                        <a href="portal/developers/view?task_uuid=<?php echo $note->task_uuid;?>">
                            <div class="btn" style='background-color: var(--developersPortalBackground)'><img src="assets/images/show.png" alt=""></div>
                        </a>
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>