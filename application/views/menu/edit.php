<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <form id='resource' class="form-horizontal editing" action="<?php echo base_url('menu/save/' . $resource->id); ?>" method='post' name='resource_form'>
                <input type="hidden" name="parent_menu" value="<?php echo $parent_menu;?>">
                <input type="hidden" name="id" value="<?php echo $resource->id;?>">
                <div class="box-body">
                    <!-- ID -->
                    <!-- <div class="form-group">
                        <label for="id" class="col-sm-2 control-label">ID</label>
                        <div class="col-sm-4">
                            <p id='resource_id' class="form-control-static"><?php //echo $resource->id; ?></p>
                            <input class='required' type="hidden" name="id" value="<?php //echo $resource->id; ?>">
                        </div>
                    </div> -->
                    <!-- Display Name -->
                    <div class="form-group">
                        <label for="stockref" class="col-sm-2 control-label">Display Name<span class='asterix'></span></label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control required" maxlength="30" name='nom' value='<?php echo $resource->nom; ?>' autofocus>
                        </div>
                    </div>
                    <!-- Visible -->
                    <div class="form-group">
                        <label for="stockref" class="col-sm-2 control-label">Visible</label>
                        <div class="col-sm-4">
                            <input type='radio' name="visible" value='1' <?php echo ($resource->visible == 1) ? 'checked' : ''; ?>>Yes
                            <input type='radio' name="visible" value='0' <?php echo ($resource->visible == 0) ? 'checked' : ''; ?>>No
                        </div>
                    </div>
                    <!-- Controller -->
                    <div class="form-group">
                        <label for="category" class="col-sm-2 control-label">Controller</label>
                        <div class="col-sm-4">
                            <select class="form-control required" name='controller'>
                                <option value=''>Select</option>
                                <?php foreach ($controllers as $controller): ?>
                                    <option value='<?php echo $controller; ?>' <?php echo ($controller == $resource->controller) ? 'selected' : ''; ?>><?php echo ucwords($controller); ?></option>
                                <?php endforeach; ?>
                            </select>	  
                        </div>
                    </div>
                    <!-- Action / Method -->
                    <div class="form-group">
                        <label for="price" class="col-sm-2 control-label">Action<span class='asterix'></span></label>
                        <div class="col-sm-4">
                            <input type="text" name="action" class="form-control" value='<?php echo $resource->action; ?>'>
                        </div>
                    </div>
                    <!-- Parent Menu -->
                    <div class="form-group">
                        <label for="brand" class="col-sm-2 control-label">Parent Menu<span class='asterix'></span></label>
                        <div class="col-sm-4">
                            <select class="form-control required" name='parent_menu' id="parent_menu">
                                <option value='0'>Root</option>
                                <?php foreach ($all_root_menu as $item): ?>
                                    <option value='<?php echo $item->id; ?>' <?php echo ($item->id == $resource->parent_menu) ? 'selected' : ''; ?>><?php echo $item->nom; ?></option>
                                <?php endforeach; ?>
                            </select>	  
                        </div>
                    </div>
                    <!-- Access Level -->
                    <div class="form-group">
                        <label for="brand" class="col-sm-2 control-label">Access Level<span class='asterix'></span></label>
                        <div class="col-sm-4">
                            <div class='checkbox'>
                                <label><input type="checkbox" name="normal" value="1" <?php echo ($resource->Normal == 1) ? "checked" : ""; ?>>
                                    Normal
                                </label>
                            </div>
                            <div class='checkbox'>
                                <label>
                                    <input type="checkbox" name="admin" value="1" <?php echo ($resource->Admin == 1) ? "checked" : ""; ?>>
                                    Admin
                                </label>
                            </div>
                            <div class='checkbox disabled hidden'>
                                <label>
                                    <input type="checkbox" value="1" <?php echo ($resource->Root == 1) ? "checked" : ""; ?> disabled>
                                    <input type="hidden" name="root" value="1">
                                    Root
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- Icon -->
                    <div class="form-group choose-icon <?php echo ($resource->visible == 0)?'hidden':'';?>">
                        <label for="icon" class="col-sm-2 control-label">Icon<span class='asterix'></span></label>
                        <input type='hidden' name='icon' id='icon' value='<?php echo $resource->class; ?>'>
                        <div class="col-sm-4 preview_container" title="<?php echo $resource->class; ?>">
                            <span class="fa fa-2x <?php echo $resource->class; ?>"></span> [<span class='icon_name'><?php echo $resource->class; ?>]</span>
                            
                        </div>
                    </div>
                    <div class="form-group choose-icon <?php echo ($resource->visible == 0)?'hidden':'';?>">
                        <label for="icon" class="col-sm-2 control-label">Choose<span class='asterix'></span></label>
                        <div class="col-sm-8 icons">
                            <?php foreach ($glyphicons as $icon): ?>
                                <span data-classname='<?php echo $icon->classname; ?>' class="fa fa-border <?php echo $icon->classname; ?> <?php echo ($icon->classname == $resource->class) ? 'selected_icon' : ''; ?>"></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Is Backoffice? -->
                    <div class="form-group">
                        <label for="brand" class="col-sm-2 control-label">Backoffice<span class='asterix'></span></label>
                        <div class="col-sm-4">
                            <div class='checkbox'>
                                <label><input type="checkbox" name="backoffice" value="1" <?php echo ($resource->backoffice == 1) ? "checked" : ""; ?>>
                                    Yes
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- Display Order -->
                    <div class="form-group choose-icon <?php echo ($resource->visible == 0)?'hidden':'';?>">
                        <label for="price" class="col-sm-2 control-label">Display Order<span class='asterix'></span></label>
                        <div class="col-sm-4">
                            <input type="number" name="display_order" class="form-control required required numeric" id="min_stock" placeholder="Minum Stock Level" value='<?php echo $resource->display_order; ?>'>
                        </div>
                    </div>
                    <!-- Button --->
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-4">
                            <button type="submit" name='submit' class="btn btn-xs btn-flat btn-success">Update</button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
