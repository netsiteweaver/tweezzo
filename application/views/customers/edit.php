<form id="save_customers" role="form" action="<?php echo base_url('customers/update/'); ?>" method="post" autocomplete="off">
<div class="row">
    <div class="col-md-12">

        <input type="hidden" name="referer" value="<?php echo $this->input->get("referer");?>">
        <div class="row">
            <!-- <div class="col-md-12"> -->
                <!-- <div class="row"> -->
                    <?php foreach($fields as $f):?>
                        <div class="<?php echo (strtolower(trim($f['type'])) == 'hidden') ? '' : 'col-lg-3 col-md-4 col-sm-6';?>">
                        <?php if( in_array( (strtolower(trim($f['type']))),['text','number','email','date','password'] ) ):?>
                            <div class="form-group">
                                <label class="<?php echo ($f['required']) ? 'asterisk' :'';?>"><?php echo $f['label'];?></label>
                                <input type="<?php echo $f['type'];?>" class="form-control <?php echo ($f['required']) ? 'required' :'';?>" name="<?php echo $f['field_name'];?>" id="<?php echo $f['field_name'];?>" placeholder="<?php echo $f['placeholder'];?>" value="<?php echo $f['value'];?>" <?php echo ($f['required']) ? 'required' :'';?>>
                            </div>
                        <?php elseif(strtolower(trim($f['type'])) == 'textarea'):?>
                            <div class="form-group">
                                <label class="<?php echo ($f['required']) ? 'asterisk' :'';?>">Remarks</label>
                                <textarea name="remarks" id="remarks" cols="30" rows="3" class="form-control" placeholder=""></textarea>
                            </div>
                        <?php elseif(strtolower(trim($f['type'])) == 'select'):?>
                            <div class="form-group">
                                <label class="<?php echo ($f['required']) ? 'asterisk' :'';?>"><?php echo $f['label'];?></label>
                                <select name="<?php echo $f['field_name'];?>" id="<?php echo $f['field_name'];?>" class="form-control <?php echo ($f['required']) ? 'required' :'';?>" <?php echo ($f['required']) ? 'required' :'';?>>
                                    <option value="">Select <?php echo $f['label'];?></option>
                                    <?php foreach($f['options'] as $i => $o):?>
                                    <option value="<?php echo $o['id'];?>" <?php echo (isset($f['value'])) ? (($f['value']==$o['id']) ? 'selected' : '') : '';?>><?php echo $o['name'];?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        <?php elseif(strtolower(trim($f['type'])) == 'hidden'):?>
                            <input type="hidden" id="<?php echo $f['field_name'];?>" name="<?php echo $f['field_name'];?>" value="<?php echo $f['value'];?>">
                        <?php endif;?>
                        </div>
                    <?php endforeach;?>
                <!-- </div> -->
            <!-- </div> -->
        </div>
        <div class="row">
            <div class="col-md-4">
                <a href="customers/portal_access/<?php echo $uuid;?>">
                    <div class="btn btn-info"><i class="fa fa-building"></i> Access to Portal</div>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <a href="<?php echo base_url("customers/listing");?>"><div class="btn btn-warning"><i class="fa fa-chevron-left"></i> Back</div></a>
        <button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
    </div>
</div>
</form>