<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <form role="form" action="<?php echo base_url('settings/updateparams'); ?>" method="post">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <a class="nav-item nav-link active" id="nav-rows_per_page-tab" data-toggle="tab" href="#nav-rows_per_page" role="tab" aria-controls="nav-home" aria-selected="true"><i class="fa fa-angle-down"></i> Rows Per Page</a>
                                    <a class="nav-item nav-link" id="nav-theme-tab" data-toggle="tab" href="#nav-theme" role="tab" aria-controls="nav-theme" aria-selected="false"><i class="fa fa-bars"></i> Sidebar</a>
                                    <a class="nav-item nav-link" id="nav-mailer-tab" data-toggle="tab" href="#nav-mailer" role="tab" aria-controls="nav-mailer" aria-selected="false"><i class="fa fa-envelope"></i> Mailer</a>
                                    <a class="nav-item nav-link" id="nav-barcode-tab" data-toggle="tab" href="#nav-barcode" role="tab" aria-controls="nav-barcode" aria-selected="false"><i class="fa fa-barcode"></i> Barcode</a>
                                    <a class="nav-item nav-link" id="nav-testing-tab" data-toggle="tab" href="#nav-testing" role="tab" aria-controls="nav-testing" aria-selected="false"><i class="fa fa-paper-plane"></i> Testing Mode</a>
                                </div>
                            </nav>

                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-rows_per_page" role="tabpanel" aria-labelledby="nav-rows_per_page-tab">
                                    <div class="form-group">
                                        <label><i class="fa fa-info-circle"></i> General</label>
                                        <select class="form-control" name="rows_per_page">
                                            <option value="10" <?php echo ($rows_per_page==10)?'selected':''; ?>>10</option>
                                            <option value="25" <?php echo ($rows_per_page==25)?'selected':''; ?>>25</option>
                                            <option value="50" <?php echo ($rows_per_page==50)?'selected':''; ?>>50</option>
                                            <option value="100" <?php echo ($rows_per_page==100)?'selected':''; ?>>100</option>
                                        </select>
                                        <p class="help-block">This value is used for the general listing of items.</p>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-theme" role="tabpanel" aria-labelledby="nav-theme-tab">
                                    <div class="form-group d-none">
                                        <label><i class="fa fa-info-circle"></i> Admin Theme</label>
                                        <select class="form-control" name="theme_color">
                                            <option value="purple" <?php echo ($theme_color=="purple")?'selected':''; ?>>Purple</option>
                                            <option value="red" <?php echo ($theme_color=="red")?'selected':''; ?>>Red</option>
                                            <option value="blue" <?php echo ($theme_color=="blue")?'selected':''; ?>>Blue</option>
                                            <option value="yellow" <?php echo ($theme_color=="yellow")?'selected':''; ?>>Yellow</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="radios"><i class="fa fa-info-circle"></i> Collapse Sidebar</label>
                                            <label class="radio-inline" for="radios-0">
                                            <input name="sidebar_collapse" id="radios-0" class='sidebar_collapse' value="1" <?php echo($sidebar_collapse==1)?"checked='checked'":"";?> type="radio">
                                            Yes
                                            </label> 
                                            <label class="radio-inline" for="radios-1">
                                            <input name="sidebar_collapse" id="radios-1" class='sidebar_collapse' value="0" <?php echo($sidebar_collapse==0)?"checked='checked'":"";?> type="radio">
                                            No
                                        </label>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-mailer" role="tabpanel" aria-labelledby="nav-mailer-tab">
                                    <div class="col-md-12">
                                        <p><i class="fa fa-info-circle"></i> SMTP Settings</p>
                                        <?php if($_SESSION['user_level']=='Root'):?>
                                        <div class="form-group">
                                            <label for="">Outgoing Server</label>
                                            <input type="text" class='form-control save-state' name="smtp_settings[hostname]" value="<?php echo $smtp_settings->hostname;?>" placeholder="Please enter the outgoing server">
                                        </div>
                                        <div class="form-group">
                                            <label for="">Username</label>
                                            <input type="text" class='form-control save-state' name="smtp_settings[username]" value="<?php echo $smtp_settings->username;?>" placeholder="Please enter the username">
                                        </div>
                                        <?php else:?>
                                            <input type="hidden" class='save-state' name="smtp_settings[hostname]" value="<?php echo $smtp_settings->hostname;?>">
                                            <input type="hidden" class='save-state' name="smtp_settings[username]" value="<?php echo $smtp_settings->username;?>">
                                        <?php endif;?>
                                        <div class="form-group">
                                            <label for="">From</label>
                                            <input type="text" class='form-control save-state' name="smtp_settings[from]" value="<?php echo (isset($smtp_settings->from))?$smtp_settings->from:'';?>" placeholder="Please enter from email">
                                        </div>
                                        <div class="form-group">
                                            <label for="">Display Name</label>
                                            <input type="text" class='form-control save-state' name="smtp_settings[displayname]" value="<?php echo $smtp_settings->displayname;?>" placeholder="Please enter name that will be displayed">
                                        </div>
                                        <?php if($_SESSION['user_level']=='Root'):?>
                                        <div class="form-group">
                                            <label for="">Password</label>
                                            <input type="text" class='form-control save-state' name="smtp_settings[password]" value="<?php echo $smtp_settings->password;?>" placeholder="Please enter the password">
                                        </div>
                                        <div class="form-group">
                                            <label for="">Port</label>
                                            <input type="text" class='form-control save-state' name="smtp_settings[port]" value="<?php echo $smtp_settings->port;?>" placeholder="Please enter port, for example 465 or 567">
                                        </div>
                                        <?php else:?>
                                            <input type="hidden" class='save-state' name="smtp_settings[password]" value="<?php echo $smtp_settings->password;?>">
                                            <input type="hidden" class='save-state' name="smtp_settings[port]" value="<?php echo $smtp_settings->port;?>">
                                        <?php endif;?>
                                        <div class="form-group">
                                            <!-- <label for="">&nbsp;</label> -->
                                            <div class="btn btn-default test-email"><i class="fa fa-send"></i> Test Email</div>
                                            <p style="font-size:14px;color:darkgreen;" id="test-result"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-testing" role="tabpanel" aria-labelledby="nav-testing-tab">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><i class="fa fa-info-circle"></i> Testing Mode</label>
                                            <p style='margin-top:20px;'>Setting <b>Testing Mode</b> to <u>Yes</u> will prevent the application from sending email. The emails will still be saved in the queue, but marked as sent.</p>
                                            <select class="form-control" name="testing_mode">
                                                <option value="no" <?php echo ($testing_mode=="no")?'selected':''; ?>>No</option>
                                                <option value="yes" <?php echo ($testing_mode=="yes")?'selected':''; ?>>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-barcode" role="tabpanel" aria-labelledby="nav-barcode-tab">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><i class="fa fa-info-circle"></i> Use Barcode</label>
                                            <select class="form-control" name="barcode">
                                                <option value="no" <?php echo ($barcode=="no")?'selected':''; ?>>No</option>
                                                <option value="yes" <?php echo ($barcode=="yes")?'selected':''; ?>>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 display-help"></div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-xs btn-flat btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>