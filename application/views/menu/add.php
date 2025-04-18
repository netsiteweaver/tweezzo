<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
			<form id='resource' class="form-horizontal editing" action="<?php echo base_url('menu/save/'); ?>" method='post' name='resource_form'>
				<input type="hidden" name="parent_menu" value="">
                <div class="box-body">
                	<!-- Type -->
					<input type="hidden" name="type" value="menu">
					<!-- Display Name -->
					<div class="form-group">
						<label for="stockref" class="col-sm-2 control-label">Display Name</label>
						<div class="col-sm-4">
						  <input type="text" class="form-control required" maxlength="30" name='nom' value='' autofocus>
						</div>
					</div>
					<!-- Visible -->
					<div class="form-group">
						<label for="stockref" class="col-sm-2 control-label">Visible</label>
						<div class="col-sm-4">
						  <input type='radio' name="visible" id="visible_yes" value='1' checked>Yes
						  <input type='radio' name="visible" id="visible_no" value='0'>No
						</div>
					</div>
					<!-- Controller -->
					<div class="form-group">
						<label for="category" class="col-sm-2 control-label">Controller</label>
						<div class="col-sm-4">
							<select class="form-control required" name='controller' id='controller'>
								<option value=''>Select</option>
								<?php foreach($controllers as $controller): ?>
								<!-- <option value='<?php echo $controller; ?>' <?php echo ($controller=='home')?'selected':''; ?>><?php echo ucwords($controller); ?></option> -->
								<option value='<?php echo strtolower($controller); ?>' <?php echo ($parent_controller==$controller)?"selected":"";?>><?php echo ucwords($controller); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<!-- Action / Method -->
					<div class="form-group">
						<label for="price" class="col-sm-2 control-label">Action</label>
						<div class="col-sm-4">
						  <input type="text" name="action" class="form-control" id='action' value=''>
						</div>
					</div>
					<!-- Parent Menu -->
					<div class="form-group">
						<label for="brand" class="col-sm-2 control-label">Parent Menu</label>
						<div class="col-sm-4">
							<select class="form-control required" name='parent_menu' id='parent_menu'>
								<option value='0'>Root</option>
								<?php foreach($all_root_menu as $item): ?>
								<option value='<?php echo $item->id; ?>'><?php echo $item->nom; ?></option>
								<?php endforeach; ?>
							</select>	  
						</div>
					</div>
					<!-- Access Level -->
					<div class="form-group">
						<label for="brand" class="col-sm-2 control-label">Access Level<span class='asterix'></span></label>
			    			<div class="col-sm-6">
	                            <div class='checkbox'>
									<label>
	                					<input type="checkbox" name="normal" value="1">
				                        Normal
			                        </label>
								</div>
	                            <div class='checkbox'>
							  		<label>
	                                  	<input type="checkbox" name="admin" value="1">
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
						<input type='hidden' name='icon' id='icon' value='<?php echo (!empty($resource->class))?$resource->class:''; ?>'>
						<div class="col-sm-4 preview_container">
						<span class="fa fa-2x"></span>
						
						</div>
					</div>
					<div class="form-group choose-icon">
						<label for="icon" class="col-sm-2 control-label">Choose</label>
						
						<div class="col-sm-8 icons">
							<?php foreach($glyphicons as $icon): ?>
								<span data-classname='<?php echo $icon->classname; ?>' class="fa fa-border <?php echo $icon->classname; ?>"></span>
							<?php endforeach; ?>
						</div>
					</div>
					<!-- Display Order -->
					<div class="form-group">
						<label for="price" class="col-sm-2 control-label">Display Order</label>
						<div class="col-sm-4">
						  <input type="number" name="display_order" step='10' class="form-control numeric required" id="display_order" value='50'>
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
