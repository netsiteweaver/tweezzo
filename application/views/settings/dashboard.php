<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <form id="dashboard-access" role="form" action="#" method="post">
                <div class="box-body">
                    <?php foreach($dashboard as $item):?>
                    <div class="row">
                        <div class="col-md-2">
                            <label>Button Label</label>
                            <input class="form-control lbl" name="label" placeholder="" value="<?php echo $item->label; ?>">
                            <p class="help-block"></p>
                        </div>
                        <div class="col-md-2">
                            <label>URL</label>
                            <input class="form-control url" name="url" placeholder="" value="<?php echo $item->url; ?>">
                            <p class="help-block"></p>
                        </div>
                        <div class="col-md-2">
                            <label>Button Class</label>
                            <input class="form-control cls" name="class" placeholder="" value="<?php echo $item->class; ?>">
                            <p class="help-block"></p>
                        </div>
                        <div class="col-md-2">
                            <label>Icon</label>
                            <input class="form-control icon" name="icon" placeholder="" value="<?php echo $item->icon; ?>">
                            <p class="help-block"></p>
                        </div>
                        <div class="col-md-2">
                            <label>Width (1-12)</label>
                            <select class="form-control width" name="width" >
                                <?php for($i=1;$i<=12;$i++):?>
                                    <option value="<?php echo $i;?>" <?php echo ($i==$item->width)?'selected':'';?>><?php echo $i;?></option>
                                <?php endfor;?>
                            </select>
                            <p class="help-block"></p>
                        </div>
                        <div class="col-md-2">
                            <div style='margin-top:25px;' class="btn btn-danger  btn-flat remove"><i class="fa fa-times"></i> Remove</div>
                        </div>
                    </div>
                    <?php endforeach;?>
                    <div class="row">
                        <div class="col-md-2">
                            <label>Button Label</label>
                            <input class="form-control lbl" name="label" placeholder="" value="">
                            <p class="help-block"></p>
                        </div>
                        <div class="col-md-2">
                            <label>URL</label>
                            <input class="form-control url" name="url" placeholder="" value="">
                            <p class="help-block"></p>
                        </div>
                        <div class="col-md-2">
                            <label>Button Class</label>
                            <input class="form-control cls" name="class" placeholder="" value="">
                            <p class="help-block"></p>
                        </div>
                        <div class="col-md-2">
                            <label>Icon</label>
                            <input class="form-control icon" name="icon" placeholder="" value="">
                            <p class="help-block"></p>
                        </div>
                        <div class="col-md-2">
                            <label>Width (1-12)</label>
                            <select name="" name="width" class="form-control width">
                                <?php for($i=1;$i<=12;$i++):?>
                                    <option value="<?php echo $i;?>"><?php echo $i;?></option>
                                <?php endfor;?>
                            </select>
                            <p class="help-block"></p>
                        </div>
                        <div class="col-md-2">
                            <div style='margin-top:25px;' class="btn btn-danger  btn-flat remove"><i class="fa fa-times"></i> Remove</div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="btn btn-info btn-lg btn-flat" id="add"><i class="fa fa-plus"></i> Add New</div>
                        </div>
                        
                    </div>
                </div>
                <div class="box-footer">
                    <div id="update" class="btn btn-xs btn-flat btn-primary">Update</div>
                </div>
                </div>
            </form>
        </div>
    </div>
</div>
    