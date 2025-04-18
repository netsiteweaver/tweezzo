<!-- <div class="row">
    <div class="col-xs-12"> -->
        <div class="box box-primary">
            <form role="form" action="<?php echo base_url('settings/updatecompany'); ?>" method="post">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h3><i class="fa fa-building"></i> Company Details</h3>
                            <div class="form-group">
                                <label><i class="fa fa-info-circle"></i> Company Name</label>
                                <input class="form-control" name="name" placeholder="" value="<?php echo $company->name; ?>">
                                <p class="help-block"></p>
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-info-circle"></i> Company Legal Name</label>
                                <input class="form-control" name="legal_name" placeholder="" value="<?php echo $company->legal_name; ?>">
                                <p class="help-block"></p>
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-edit"></i> BRN</label>
                                <input class="form-control" name="brn" placeholder="" value="<?php echo $company->brn; ?>">
                                <p class="help-block"></p>
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-edit"></i> VAT Reg No.</label>
                                <input class="form-control" name="vat" placeholder="" value="<?php echo $company->vat; ?>">
                                <p class="help-block"></p>
                            </div>  
                            <div class="form-group">
                                <label><i class="fa fa-map-marker"></i> Address</label>
                                <input class="form-control" name="address1" placeholder="" value="<?php echo $company->address1; ?>">
                                <p class="help-block"></p>
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-map-marker"></i> Address (contd)</label>
                                <input class="form-control" name="address2" placeholder="" value="<?php echo $company->address2; ?>">
                                <p class="help-block"></p>
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-map-marker"></i> City</label>
                                <input class="form-control" name="city" placeholder="" value="<?php echo $company->city; ?>">
                                <p class="help-block"></p>
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-map-marker"></i> Country</label>
                                <input class="form-control" name="country" placeholder="" value="<?php echo $company->country; ?>">
                                <p class="help-block"></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h3><i class="fa fa-sticky-note"></i> Contacts</h3>
                                <!-- <p class='help-block'>Empty fields will not be displayed</p> -->
                                <div class="form-group">
                                    <label><i class="fa fa-phone"></i> Phone</label>
                                    <input class="form-control" name="phone" placeholder="Enter your Business Phone" value="<?php echo $company->phone; ?>">
                                    <p class="help-block"></p>
                                </div>
                                <div class="form-group ">
                                    <label><i class="fa fa-mobile"></i> Mobile</label>
                                    <input class="form-control" name="mobile" placeholder="Enter a Professional mobile number" value="<?php echo $company->mobile; ?>">
                                    <p class="help-block"></p>
                                </div>
                                <div class="form-group">
                                    <label><i class="fa fa-fax"></i> Fax</label>
                                    <input class="form-control" name="fax" placeholder="Enter your business fax number" value="<?php echo $company->fax; ?>">
                                    <p class="help-block"></p>
                                </div>
                                <div class="form-group">
                                    <label><i class="fa fa-envelope-o"></i> Email</label>
                                    <input class="form-control" name="email" placeholder="Enter your business email address" value="<?php echo $company->email; ?>">
                                    <p class="help-block"></p>
                                </div>
                                <h3 class=""><i class="fa fa-clock-o"></i> Working Hours</h3>
                                <div class="form-group ">
                                    <label for=""></label>
                                    <textarea name="working_hours" id="" class="form-control summernote" rows="10"><?php //echo $company->working_hours;?></textarea>
                                </div>
                        </div>
                        <div class="col-md-4 hidden">
                            <h3><i class="fa fa-sticky-note"></i> Social Media</h3>
                            <!-- <p class="help-block">All the social media fields are optional.</p> -->
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label><i class="fab fa-facebook-square fa-2x"></i></label>
                                    </div>
                                    <div class="col-md-8">
                                        <input class="form-control" name="facebook" placeholder="Enter your Facebook page" value="<?php echo $company->facebook; ?>">
                                        <p class="help-block">Please enter the full URL to your facebook account</p>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label><i class="fab fa-twitter-square fa-2x"></i></label>
                                    </div>
                                    <div class="col-md-8">
                                        <input class="form-control" name="twitter" placeholder="Enter your Twitter page" value="<?php echo $company->twitter; ?>">
                                        <p class="help-block">Please enter the full URL to your Twitter account</p>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label><i class="fab fa-linkedin fa-2x"></i></label>
                                    </div>
                                    <div class="col-md-8">
                                        <input class="form-control" name="linkedin" placeholder="Enter your LinkedIn page" value="<?php echo $company->linkedin; ?>">
                                        <p class="help-block">Please enter the full URL to your LinkedIn account</p>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label><i class="fab fa-instagram-square fa-2x"></i></label>
                                    </div>
                                    <div class="col-md-8">
                                        <input class="form-control" name="instagram" placeholder="Enter your Instagram page" value="<?php echo $company->instagram; ?>">
                                        <p class="help-block">Please enter the full URL to your Instagram page</p>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label><i class="fab fa-youtube-square fa-2x"></i></label>
                                    </div>
                                    <div class="col-md-8">
                                        <input class="form-control" name="youtube" placeholder="Enter your Youtube channel" value="<?php echo $company->youtube; ?>">
                                        <p class="help-block">Please enter the full URL for your Youtube channel</p>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label><i class="fab fa-skype fa-2x"></i></label>
                                    </div>
                                    <div class="col-md-8">
                                        <input class="form-control" name="skype" placeholder="Enter your Skype ID" value="<?php echo $company->skype; ?>">
                                        <p class="help-block">Please enter your Skype ID</p>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label><i class="fab fa-whatsapp-square fa-2x"></i></label>
                                    </div>
                                    <div class="col-md-8">
                                        <input class="form-control" name="whatsapp" placeholder="Enter your WhatsApp number" value="<?php echo $company->whatsapp; ?>">
                                        <p class="help-block">Please enter your WhatsApp number</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-xs btn-flat btn-primary">Update</button>
                </div>
                </div>
            </form>
        </div>
    <!-- </div>
</div> -->
    