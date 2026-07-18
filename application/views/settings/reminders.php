<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <form role="form" action="<?php echo base_url('settings/updatereminders'); ?>" method="post">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p><i class="fa fa-info-circle"></i> Each reminder email only covers tasks in the stages ticked for it. Untick every stage to stop that reminder entirely.</p>
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <?php $first = true; foreach($reminders as $param => $meta):?>
                                        <a class="nav-item nav-link <?php echo $first ? 'active' : '';?>" id="nav-<?php echo $param;?>-tab" data-toggle="tab" href="#nav-<?php echo $param;?>" role="tab" aria-controls="nav-<?php echo $param;?>" aria-selected="<?php echo $first ? 'true' : 'false';?>"><i class="fa fa-clock-o"></i> <?php echo $meta['label'];?></a>
                                    <?php $first = false; endforeach;?>
                                </div>
                            </nav>

                            <div class="tab-content" id="nav-tabContent">
                                <?php $first = true; foreach($reminders as $param => $meta):?>
                                    <div class="tab-pane fade <?php echo $first ? 'show active' : '';?>" id="nav-<?php echo $param;?>" role="tabpanel" aria-labelledby="nav-<?php echo $param;?>-tab">
                                        <div class="col-md-6">
                                            <div class="form-group" style="margin-top:15px;">
                                                <label><i class="fa fa-info-circle"></i> <?php echo $meta['label'];?></label>
                                                <!-- NB: not .help-block — that class is absolutely positioned in this theme -->
                                                <p class="text-muted" style="font-size:0.85em;margin-bottom:12px;"><?php echo $meta['help'];?> Runs from <code><?php echo $meta['cron'];?></code>.</p>
                                                <!-- Placeholder so an all-unchecked state is still posted -->
                                                <input type="hidden" name="<?php echo $param;?>[]" value="">
                                                <?php foreach($all_stages as $stage):?>
                                                    <?php $id = $param . '_' . $stage;?>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="<?php echo $id;?>" name="<?php echo $param;?>[]" value="<?php echo $stage;?>" <?php echo in_array($stage, $meta['selected']) ? "checked='checked'" : "";?>>
                                                        <label class="form-check-label" for="<?php echo $id;?>"><?php echo ucwords(str_replace("_", " ", $stage));?></label>
                                                    </div>
                                                <?php endforeach;?>
                                            </div>
                                        </div>
                                    </div>
                                <?php $first = false; endforeach;?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-xs btn-flat btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
