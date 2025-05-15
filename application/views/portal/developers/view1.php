        <style>
#users-list li {
    margin-bottom: 5px;
    border: 1px solid #ccc;
}

#users-list li.assigned {
    background-color: rgb(183, 221, 210);
    /* border:1px solid #ccc !important; */
}

#users-list li.assigned img {
    border: 4px solid #20c997 !important;
}

.note-editable p {
    font-weight: normal !important;
}

.note-editable>*:first-child {
    margin-top: 0 !important;
    padding-top: 0 !important;
}

.note-editable {
    line-height: 1;
}
.preview-container {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 10px;
}

.preview-item {
  width: 100px;
  height: 100px;
  border: 1px solid #ccc;
  position: relative;
  overflow: hidden;
  text-align: center;
  font-size: 12px;
  padding: 5px;
  background: #f9f9f9;
}

.preview-item img {
  max-width: 100%;
  max-height: 100%;
}

.preview-item .file-icon {
  font-size: 30px;
  margin-top: 20px;
  color: #777;
}


        </style>

        <div class="row">
            <div class="col-md-6">
                <div class="card border-secondary mb-4"><!-- TASK INFORMATION -->
                    <div class="card-header ">
                        TASK INFORMATION
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <colgroup>
                                <col style='width:11.11%' />
                                <col style='width:22.22%' />
                                <col style='width:11.11%' />
                                <col style='width:22.22%' />
                                <col style='width:11.11%' />
                                <col style='width:22.22%' />
                            </colgroup>

                            <tr>
                                <th>Task #:</th>
                                <td><?php echo $task->task_number;?></td>
                                <th>Sprint:</th>
                                <td><?php echo $task->sprint_name;?></td>
                                <th>Project:</th>
                                <td><?php echo $task->project_name;?></td>

                            </tr>

                            <tr>
                                <th>Customer:</th>
                                <td colspan='5'><?php echo $task->company_name;?></td>
                            </tr>

                            <tr>
                                <th>Section:</th>
                                <td colspan='3'><?php echo $task->section;?></td>

                                <th>Stage</th>
                                <td>
                                    <div class="stage-button stage-button-<?php echo $task->stage;?>">
                                        <?php echo strtoupper(str_replace("_"," ",$task->stage));?>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <th>Task:</th>
                                <td colspan='5'><?php echo $task->name;?></td>
                            </tr>

                            <tr>
                                <th>Task Description:</th>
                                <td colspan='5'><?php echo nl2br($task->description);?></td>
                            </tr>

                        </table>
                    </div>
                </div>
                <div class="card border-secondary text-center mb-4"><!-- MOVE STAGE TO -->
                    <div class="card-header">
                        <img src="./assets/images/file-transfer.png" alt=""> MOVE STAGE TO
                    </div>
                    <div class="card-body">
                        <div data-stage='new'
                            class="btn stage-button stage-button-new <?php echo ($task->stage=='new') ? 'transparent' :'changeStage'?>">
                            New
                        </div>
                        <div data-stage='in_progress'
                            class="btn stage-button stage-button-in_progress <?php echo ($task->stage=='in_progress') ? 'transparent' :'changeStage'?>">
                            In Progress
                        </div>
                        <div data-stage='testing'
                            class="btn stage-button stage-button-testing <?php echo ($task->stage=='testing') ? 'transparent' :'changeStage'?>">
                            Testing </div>
                        <div data-stage='staging'
                            class="btn stage-button stage-button-staging <?php echo ($task->stage=='staging') ? 'transparent' :'changeStage'?>">
                            Staging </div>
                        <div data-stage='completed'
                            class="btn stage-button stage-button-completed <?php echo ($task->stage=='completed') ? 'transparent' :'changeStage'?>">
                            Completed </div>
                        <div data-stage='on_hold'
                            class="btn stage-button stage-button-on_hold <?php echo ($task->stage=='on_hold') ? 'transparent' :'changeStage'?>">
                            On Hold</div>
                    </div>

                </div>
                <div class="card border-secondary mb-4"><!-- PREVIOUS NOTES -->
                    <div class="card-header">
                        PREVIOUS NOTES
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <?php foreach($task->notes as $i =>  $notes):?>
                            <tr class='<?php echo ($notes->out_of_scope == '1') ? 'out-of-scope' : '';?>'>
                                <td><?php echo $i+1;?></td>
                                <td>
                                    <?php echo nl2br(strip_tags($notes->notes));?>
                                    <span class="float-end developer" title="<?php echo $notes->country_code;?>">
                                        <?php echo "by {$notes->developer}{$notes->customer} <i class='flag flag-{$notes->country_code}'></i> on " . date_format(date_create($notes->created_on),'Y m d @ H:i');?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($notes->created_by == $_SESSION['developer_id']):?>
                                    <div class="btn btn-sm btn-danger deleteNote"
                                        data-note-id='<?php echo $notes->id;?>'><i class="bi bi-trash"></i>
                                    </div>
                                    <?php endif;?>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </table>
                    </div>
                </div>
                <div class="card border-secondary mb-4"><!-- NEW NOTE -->
                    <div class="card-header">
                        NEW NOTE
                    </div>
                    <div class="card-body">
                        <form method="post" action='portal/developers/saveNotes'>
                            <input type="hidden" name="task_id" value="<?php echo $task->id;?>">
                            <input type="hidden" name="task_uuid" value="<?php echo $task->uuid;?>">
                            <div class="form-group">
                                <!-- <label for="">Notes</label> -->
                                <textarea name="notes" id="" rows='5' class="summernote form-control" minlength='5'
                                    required></textarea>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <label for="display_type">
                                        <input type="checkbox" id="display_type" name="display_type" checked>
                                        Visible to All</label>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-primary"><i class="bi bi-save"></i> Save
                                        Notes</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

            <div class="col-md-6">
                <?php if(count($task->files) > 0):?>
                <div class="card border-secondary mb-4">
                    <div class="card-header">ATTACHMENTS</div>
                    <div class="card-body">
                        <div id="attachments">
                            <div class="row">
                                <?php foreach($task->files as $file):?>
                                <div class="col-md-4">
                                    <a href="<?php echo base_url("uploads/tasks/{$file->file_name}");?>"
                                        data-lightbox="test">
                                        <img style='width:100%;' class='img-thumbnail img-responsize'
                                            src="<?php echo base_url("uploads/tasks/{$file->thumb_name}");?>"
                                            alt="image missing">
                                    </a>
                                </div>
                                <?php endforeach;?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif;?>
                <div class="card border-secondary mb-4">
                    <form method="post" action="portal/developers/uploadFiles" enctype="multipart/form-data">
                        <input type="hidden" name="uuid" value="<?php echo $task->uuid;?>">
                    <div class="card-header">ADD ATTACHMENTS</div>
                    <div class="card-body">
                        <input type="file" id="fileInput1" name="file1" class="form-control mb-2">
                        <div id="preview1" class="mb-3"></div>

                        <input type="file" id="fileInput2" name="file2" class="form-control mb-2">
                        <div id="preview2" class="mb-3"></div>

                        <input type="file" id="fileInput3" name="file3" class="form-control mb-2">
                        <div id="preview3" class="mb-3"></div>

                        <input type="file" id="fileInput4" name="file4" class="form-control mb-2">
                        <div id="preview4" class="mb-3"></div>

                        <input type="file" id="fileInput5" name="file5" class="form-control mb-2">
                        <div id="preview5" class="mb-3"></div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-info">Upload Files</button>
                    </div>

                    </form>
                </div>

                <?php if( count($task->stage_history) > 0 ):?>
                <div class="card border-secondary">
                    <div class="card-header">STAGE HISTORY</div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>DATE</th>
                                    <th>USER</th>
                                    <th>STAGE CHANGE</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($task->stage_history as $history):?>
                                <tr>
                                    <td><?php echo date('d-M-Y h:i A',strtotime($history->created_on));?></td>
                                    <td><?php echo "{$history->created_by_email}<span class='text-muted'> [{$history->user_type}]</span>";?>
                                    </td>
                                    <td><?php echo "From <b>" . strtoupper(str_replace("_"," ",$history->old_stage)) . "</b> to <b>" . strtoupper(str_replace("_"," ",$history->new_stage))."</b>";?>
                                    </td>
                                </tr>
                                <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif;?>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-6">
                <a href="portal/developers/tasks<?php echo "?customer_id={$this->input->get('customer_id')}&project_id={$this->input->get('project_id')}&sprint_id={$this->input->get('sprint_id')}";?>">
                    <div class="btn btn-warning">
                        <img src="assets/ionicons/chevron-back-sharp.svg" alt="" class="ionicon">Back
                    </div>
                </a>
            </div>
        </div>

<script>
const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

function handlePreview(inputId, previewId) {
  const input = document.getElementById(inputId);
  const preview = document.getElementById(previewId);

  input.addEventListener('change', function () {
    preview.innerHTML = ''; // Clear previous preview

    const file = input.files[0];
    if (!file) return;

    if (!allowedTypes.includes(file.type)) {
      preview.innerHTML = `<span class="text-danger">Invalid file type: ${file.type}</span>`;
      return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
      const img = document.createElement('img');
      img.src = e.target.result;
      img.style.maxWidth = '150px';
      img.style.maxHeight = '150px';
      img.className = 'img-thumbnail';
      preview.appendChild(img);
    };
    reader.readAsDataURL(file);
  });
}

// Attach to each file input
handlePreview('fileInput1', 'preview1');
handlePreview('fileInput2', 'preview2');
handlePreview('fileInput3', 'preview3');
handlePreview('fileInput4', 'preview4');
handlePreview('fileInput5', 'preview5');
</script>

<!-- Font Awesome (for icons) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
