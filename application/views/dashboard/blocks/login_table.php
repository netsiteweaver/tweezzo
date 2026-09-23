<?php
/**
 * Shared login-history block.
 * Expects: $title, $header_class, $rows, $show_username (bool)
 */
?>
<h4 class='text-center'><?php echo $title;?></h4>
<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead>
            <tr class='text-center <?php echo $header_class;?>'>
                <th>DATE</th>
                <th>USERNAME / EMAIL</th>
                <th>OS</th>
                <th>RESULT</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($rows as $row):?>
            <tr title="<?php echo $row->ip . " | " . $row->os . " | " . $row->browser . " | " . $row->result_other;?>" class='text-center <?php echo (($row->result=='SUCCESS')?'':'red');?>' >
                <td><?php echo $row->datetime;?></td>
                <td><?php echo $show_username ? ($row->username . (!empty($row->email)?' / '.$row->email:'')) : $row->email;?></td>
                <td><?php echo $row->os;?></td>
                <td><?php echo $row->result;?></td>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</div>
