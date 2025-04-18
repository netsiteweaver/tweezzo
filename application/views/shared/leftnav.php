  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header"><?php echo $company->name;?><div id="version-history" class='cursor-pointer'>build <?php echo $current_version;?></div></li>
        <?php if( (!is_null($menu_items['main'])) && (count($menu_items['main'])>0) ):?>
        <?php foreach($menu_items['main'] as $item): ?>
          <?php if(isset($menu_items['sub'][$item->id])): ?>

          <li class="treeview">
            <a href="#">
              <i class="fa <?php echo $item->class; ?>"></i>
              <span><?php echo $item->nom; ?></span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <?php foreach($menu_items['sub'][$item->id] as $sub_item): ?>
              <li <?php echo (($controller==$sub_item->controller)&&($method==$sub_item->action))?"class='active'":"";?>><a href="<?php echo base_url($sub_item->controller.'/'.$sub_item->action); ?>"><i class="fa <?php echo $sub_item->class; ?>"></i> <?php echo $sub_item->nom; ?></a></li>
              <?php endforeach;?>
            </ul>
          </li>
          <?php else:?>
          <li>
            <a href="<?php echo base_url($item->controller.'/'.$item->action); ?>">
              <i class="fa <?php echo $item->class; ?>"></i> <span><?php echo $item->nom; ?></span>
            </a>
          </li>            
          <?php endif;?>        
        <?php endforeach;?>
        <?php endif;?>
        <?php if($_SESSION['user_level'] != 'Normal'):?>
          <hr>
        <?php if( (isset($_SESSION['backoffice'])) && ($_SESSION['backoffice']=='on') ):?>
        <li>
          <a href="<?php echo base_url('backoffice/off'); ?>">
            <i class="fa fa-chevron-left"></i> <span>Return</span>
          </a>
        </li>   
        <?php else:?>
          <li>
          <a href="<?php echo base_url('backoffice/on'); ?>">
            <i class="fa fa-cog"></i> <span>Back Office</span>
          </a>
        </li>   
        <?php endif;?>
        <?php endif;?>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
