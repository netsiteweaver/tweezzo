<style>

</style>
<div class="row mt-5 table-responsive">
    <div class="col-md-12">
        List of notes related to your tasks:
        <table class="table">
            <thead>
                <tr>
                    <th>Note</th>
                    <th>Date</th>
                    <th>Author</th>
                    <th>Sprint</th>
                    <th>Project</th>
                    <th>Customer</th>
                    <th>Task: [#] Section / Task Name / Customer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notes as $note):?>
                <tr>
                    <td><?php echo nl2br($note->notes);?></td>
                    <td><?php echo $note->created_on;?></td>
                    <td><?php 
                    if(!empty($note->customerName)){
                        echo $note->customerName;
                    }elseif(!empty($note->userName)){
                        echo $note->userName;
                    }?>
                    </td>
                    <td><?php echo $note->projectName;?></td>
                    <td><?php echo $note->sprintName;?></td>
                    <td><?php echo $note->company_name;?></td>
                    <td><?php echo "[{$note->taskNumber}] {$note->taskSection} / {$note->taskName} / {$note->company_name}";?></td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>