<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
			<form id='resource' class="form-horizontal editing" action="<?php echo base_url('menu/save_crud/'); ?>" method='post' name='resource_form'>
                <div class="box-body">
					<!-- Display Name -->
					<div class="form-group">
						<label for="stockref" class="col-sm-2 control-label">Display Name <p class="notes">This will be the parent menu item name that will displayed in the left navbar.</p></label>
						<div class="col-sm-4">
						  <input type="text" class="form-control required" maxlength="30" name='nom' value='' data-name="Display Name" autofocus>
						</div>
					</div>
					<!-- Controller -->
					<div class="form-group">
						<label for="category" class="col-sm-2 control-label">Controller</label>
						<div class="col-sm-4">
							<select class="form-control required" name='controller' id='controller'>
								<option value=''>Select</option>
								<?php foreach($controllers as $controller): ?>
								<option value='<?php echo strtolower($controller); ?>'><?php echo ucwords($controller); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					
					<!-- Access Level -->
					<div class="form-group">
						<label for="brand" class="col-sm-2 control-label">Access Level<span class='asterix'></span><p class="notes">The access level will be applied to parent element only. The other elements will have default levels, which you can change afterwards by editing each manually.</p></label>
			    			<div class="col-sm-6">
	                            <div class='checkbox'>
									<label>
	                					<input type="checkbox" name="normal" value="1">
				                        Normal
			                        </label>
								</div>
	                            <div class='checkbox'>
							  		<label>
	                                  	<input type="checkbox" name="admin" value="1" checked>
	                                  	Admin
	                              	</label>
	                            </div>
								<div class='checkbox disabled hidden'>
								  	<label>
										<input type="checkbox" value="1" checked disabled>
										<input type="hidden" name="root" value="1" checked>
										Root
	                          		</label>
	                            </div>
							</div>
					</div>
					<!-- Icon -->
					<div class="form-group choose-icon">
						<label for="icon" class="col-sm-2 control-label">Icon<span class='asterix'></span></label>
						<input type='hidden' class='required' name='icon' id='icon' data-name="Selecting an Icon" value='<?php echo (!empty($resource->class))?$resource->class:''; ?>'>
						<div class="col-sm-4 preview_container">
						<span class="fa fa-2x"></span>
						
						</div>
					</div>
					<div class="form-group choose-icon">
						<label for="icon" class="col-sm-2 control-label">Please Choose an Icon <p class="notes">This will displayed beside the parent element in the navbar. All the icons displayed are from <a href='https://fontawesome.com/v4.7/icons/' target='_blank'>Font Awesome v4.7</a></p></label>
						
						<div class="col-sm-8 icons">
							<?php foreach($icons as $icon): ?>
								<span data-classname='<?php echo $icon->classname; ?>' class="fa fa-border <?php echo $icon->classname; ?>"></span>
							<?php endforeach; ?>
						</div>
					</div>
					<!-- Display Order -->
					<div class="form-group">
						<label for="price" class="col-sm-2 control-label">Display Order</label>
						<div class="col-sm-4">
						  <input type="number" name="display_order" step='10' min='10' class="form-control numeric required" id="display_order" value='<?php echo $next_order;?>'>
						</div>
					</div>
					<!-- Button --->
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-4">
						  <button type="submit" name='submit' class="btn btn-xs btn-flat btn-success">Save</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
