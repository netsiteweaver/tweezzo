<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <form role="form" action="<?php echo base_url('settings/updateparams'); ?>" method="post">
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-8 col-sm-6">
                            <h3><i class="fa fa-cog"></i> System Settings</h3>
                            <div class="form-group">
                                <label><i class="fa fa-info-circle"></i> Rows per Page</label>
                                <select class="form-control" name="rows_per_page">
                                    <option value="10" <?php echo ($rows_per_page==10)?'selected':''; ?>>10</option>
                                    <option value="25" <?php echo ($rows_per_page==25)?'selected':''; ?>>25</option>
                                    <option value="50" <?php echo ($rows_per_page==50)?'selected':''; ?>>50</option>
                                    <option value="100" <?php echo ($rows_per_page==100)?'selected':''; ?>>100</option>
                                </select>
                                <p class="help-block">This value is used for the listing of vehicles on the stock list page.</p>
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-info-circle"></i> Low Mileage Threshold</label>
                                <input type="number" class="form-control" name="low_mileage_threshold" placeholder="" value="<?php echo $low_mileage_threshold; ?>">
                                <p class="help-block">This value is used when displaying the listing of vehicles, putting a special bagde on vehicles whose mileage falls below this value</p>
                            </div>
                            <div class="form-group hidden">
                                
                                <label><i class="fa fa-info-circle"></i> Inquiry</label>
                                <p class="help-block">Where to show inquiry button on vehicle page?</p>

                                <div class="checkbox">
                                    <input type="hidden" name="inquire_above_photo" value="0">
                                    <label><input type="checkbox" name="inquire_above_photo" value="1" <?php echo($inquire_above_photo)?"checked":"";?>> Above vehicle photo</label>
                                </div>
                                <div class="checkbox">
                                    <input type="hidden" name="inquire_below_photo" value="0">
                                    <label><input type="checkbox" name="inquire_below_photo" value="1" <?php echo($inquire_below_photo)?"checked":"";?>> Below vehicle photo</label>
                                </div>
                                <div class="checkbox">
                                    <input type="hidden" name="inquire_below_details" value="0">
                                    <label><input type="checkbox" name="inquire_below_details" value="1" <?php echo($inquire_below_details)?"checked":"";?>> Below vehicle information</label>
                                </div>
                                <div class="checkbox">
                                    <input type="hidden" name="inquire_below_options" value="0">
                                    <label><input type="checkbox" name="inquire_below_options" value="1" <?php echo($inquire_below_options)?"checked":"";?>> Below vehicle options</label>
                                </div>
                            </div>
                            <!-- <div class="form-group">
                                <label><i class="fa fa-info-circle"></i> Admin Theme</label>
                                <select class="form-control" name="theme">
                                    <option value="purple" <?php echo ($theme=="purple")?'selected':''; ?>>Purple</option>
                                    <option value="blue" <?php echo ($theme=="blue")?'selected':''; ?>>Blue</option>
                                    <option value="yellow" <?php echo ($theme=="yellow")?'selected':''; ?>>Yellow</option>
                                </select>
                            </div> -->
                            <div class="form-group">
                                <label><i class="fa fa-info-circle"></i> Profit Markup</label>
                                <input type="text" class="form-control" name="profit_markup" placeholder="" value="<?php echo $profit_markup; ?>">
                                <p class="help-block">Please enter your markup value for vehicles. For 10% enter only 10</p>
                            </div>

                            <!-- Multiple Radios (inline) -->
                            <div class="form-group">
                              <label for="radios"><i class="fa fa-info-circle"></i> Collapse Sidebar</label>
                                <label class="radio-inline" for="radios-0">
                                  <input name="sidebar_collapse" id="radios-0" value="1" <?php echo($sidebar_collapse==1)?"checked='checked'":"";?> type="radio">
                                  Yes
                                </label> 
                                <label class="radio-inline" for="radios-1">
                                  <input name="sidebar_collapse" id="radios-1" value="0" <?php echo($sidebar_collapse==0)?"checked='checked'":"";?> type="radio">
                                  No
                                </label>
                            </div>
                        
                        </div>
                        <div class="col-md-6 display-help"></div>
                    </div>
                </div>
                <div class="box-footer">
                    <!-- <div class="row"> -->
                        <!-- <div class="col-md-2"> -->
                            <button type="submit" class="btn btn-xs btn-flat btn-primary">Update</button>
                        <!-- </div> -->
                    <!-- </div> -->
                <!-- /.col-lg-6 (nested) -->
                </div>
                
            </form>
        </div>
    </div>
</div>