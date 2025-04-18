<div style='width:100%; text-align: center;'>
    <h3>CUSTOMER DELETED</h3>
</div>
<div style="margin:0px auto;max-width:800px;">
    <p>Client <b><?php echo $deletedCustomer->company_name;?></b> has been deleted, together with the following associated items:</p>
    <p><b>No. Projects Deleted</b>: <?php echo $deletedProjects;?></p>
    <p><b>No. Sprints Deleted</b>: <?php echo $deletedSprints;?></p>
    <p><b>No. Tasks Deleted</b>: <?php echo $deletedTasks;?></p>
    <p>Action taken by: <b><?php echo "{$user->name} / {$user->email}";?></b></p>
</div>
<?php if( (!empty($link)) && (!empty($link_label)) ):?>
<div style='margin:30px auto; max-width:800px;'>
    <a class='btn' href="<?php echo $link;?>">
        <div class="label"><?php echo $link_label;?></div>
    </a>
</div>
<?php endif;?>

