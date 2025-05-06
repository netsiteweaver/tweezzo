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
        .note-editable > *:first-child {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
        .note-editable {
            line-height: 1;
        }
        .changeStage {
            cursor: pointer;
        }
        </style>

        <div class="row">
            <div class="col-md-6">
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
                        <th>Project:</th>
                        <td><?php echo $task->project_name;?></td>
                        <th>Section:</th>
                        <td><?php echo $task->section;?></td>
                    </tr>

                    <tr>
                        <th>Customer:</th>
                        <td colspan='5'><?php echo $task->company_name;?></td>
                        
                    </tr>
                    <tr>
                        
                    </tr>
                    <tr>
                        <th>Sprint:</th>
                        <td><?php echo $task->sprint_name;?></td>
                        <th>Sprint:</th>
                        <td><?php echo $task->sprint_name;?></td>
                        <th>Stage</th>
                        <td>
                            <div class="stage-button stage-button-<?php echo $task->stage;?>">
                            <?php echo strtoupper(str_replace("_"," ",$task->stage));?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        
                    </tr>

                    <tr>
                        <th>Task:</th>
                        <td colspan='5'><?php echo $task->name;?></td>
                    </tr>

                
                    <tr>
                        <th>Task Description:</th>
                        <td colspan='5'><?php echo nl2br($task->description);?></td>
                    </tr>
                    <tr>
                        
                    </tr>
                    <tr>
                        
                    </tr>
                </table>

                <table class="table table-bordered">
                <?php foreach($task->notes as $i =>  $notes):?>
                <tr class='<?php echo ($notes->out_of_scope == '1') ? 'out-of-scope' : '';?>'>
                    <td><?php echo $i+1;?></td>
                    <td>
                        <?php echo nl2br($notes->notes);?>  
                        <span class="float-end developer">
                        <?php echo "by {$notes->developer}{$notes->customer} <i class='flag flag-mu'></i> on " . date_format(date_create($notes->created_on),'Y m d @ H:i');?>
                        </span>
                    </td>
                    <td>
                        <?php if($notes->created_by == $_SESSION['developer_id']):?>
                        <div class="btn btn-sm btn-danger deleteNote" data-note-id='<?php echo $notes->id;?>'><i class="bi bi-trash"></i></div>
                        <?php endif;?>
                    </td>
                </tr>                        
                <?php endforeach;?>
                </table>

                <form method="post" action='portal/developers/saveNotes'>
                    <input type="hidden" name="task_id" value="<?php echo $task->id;?>">
                    <input type="hidden" name="task_uuid" value="<?php echo $task->uuid;?>">
                    <div class="form-group">
                        <label for="">Notes</label>
                        <textarea name="notes" id="" rows='5' class="summernote form-control" minlength='5' required></textarea>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="display_type">
                            <input type="checkbox" id="display_type" name="display_type" checked> Public</label>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-primary"><i class="bi bi-save"></i>    Save Notes</button>
                        </div>
                    </div>
                </form>
                <?php if($task->stage != 'completed'):?>
                <h3 class='text-center mt-5 mb-2' style='border:1px solid #ccc;'>
                    Move Stage&nbsp;
                    <img src="./assets/images/file-transfer.png" alt="">
                </h3>
                <div class="row">
                    <div class="col-md-4">
                        <?php if($task->stage == 'new'):?>
                        <div data-stage='in_progress' class="stage-button stage-button-in_progress changeStage"><img src="./assets/images/file-transfer.png" alt=""> In Progress <i class="bi bi-chevron-right"></i></div>
                        <?php elseif($task->stage == 'in_progress'):?>
                        <div data-stage='testing' class="stage-button stage-button-testing changeStage"><img src="./assets/images/file-transfer.png" alt=""> Testing <i class="bi bi-chevron-right"></i></div>
                        <?php elseif($task->stage == 'testing'):?>
                        <div data-stage='staging' class="stage-button stage-button-staging changeStage"><img src="./assets/images/file-transfer.png" alt=""> Staging <i class="bi bi-chevron-right"></i></div>
                        <?php elseif($task->stage == 'validated'):?>
                        <div data-stage='completed' class="stage-button stage-button-completed changeStage"><img src="./assets/images/file-transfer.png" alt=""> Completed <i class="bi bi-chevron-right"></i></div>
                        <?php endif;?>
                    </div>
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <?php if($task->stage == 'on_hold'):?>
                        <div data-stage='in_progress' class="stage-button stage-button-in_progress changeStage"><img src="./assets/images/file-transfer.png" alt=""> In Progress <i class="bi bi-play-circle"></i></div>
                        <?php else:?>
                        <div data-stage='on_hold' class="stage-button stage-button-on_hold changeStage"><img src="./assets/images/file-transfer.png" alt=""> On Hold <i class="bi bi-stop-circle"></i></div>
                        <?php endif;?>
                    </div>
                </div>
                <?php endif;?>

            </div>

            <div class="col-md-6">
                <?php if(count($task->files) > 0):?>
                <div class="card card-secondary">
                    <div class="card-header">ATTACHMENTS</div>
                    <div class="card-body">
                        <div id="attachments">
                            <div class="row">
                                <?php foreach($task->files as $file):?>	
                                    <div class="col-md-4">
                                        <a href="<?php echo base_url("uploads/tasks/{$file->file_name}");?>" data-lightbox="test">
                                            <img style='width:100%;' class='img-thumbnail img-responsize' src="<?php echo base_url("uploads/tasks/{$file->thumb_name}");?>" alt="image missing">
                                        </a>
                                    </div>
                                <?php endforeach;?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif;?>

                <?php if( count($task->stage_history) > 0 ):?>
                <div class="card card-secondary mt-5">
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
                                    <td><?php echo "{$history->created_by_email}<span class='text-muted'> [{$history->user_type}]</span>";?></td>
                                    <td><?php echo "From <b>" . strtoupper(str_replace("_"," ",$history->old_stage)) . "</b> to <b>" . strtoupper(str_replace("_"," ",$history->new_stage))."</b>";?></td>
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
