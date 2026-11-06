<?php /* Compact counter: the whole box links to the matching listing. Expects: $item (label, icon, class, count, link) */ ?>
<a class="info-box dashboard-counter" href="<?php echo base_url($item['link']);?>" title="<?php echo htmlspecialchars($item['label']);?>">
    <span class="info-box-icon <?php echo $item['class'];?>"><i class="fa <?php echo $item['icon'];?>"></i></span>
    <span class="info-box-content">
        <span class="info-box-text"><?php echo $item['label'];?></span>
        <span class="dashboard-counter-number"><?php echo $item['count'];?></span>
    </span>
</a>
