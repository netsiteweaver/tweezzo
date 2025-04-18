<?php echo $pagination;?>
<div class="row">
    <div class="col-md-6 col-sm-12">
        <table class="table dt">
            <thead>
                <tr class='text-center'>
                    <th style='width:20%;'>VERSION</th>
                    <th style='width:30%;'>DATE</th>
                    <th>DETAILS</th>
                    <?php if($_SESSION['user_level'] == 'Root'):?>
                    <th>AUTHOR</th>
                    <?php endif;?>
                </tr>
            </thead>
            <?php foreach($commits as $c):?>
            <tbody>
                <tr class='text-center'>
                    <td><?php echo $c->version;?></td>
                    <td><?php echo ($_SESSION['user_level'] == 'Root') ? $c->date : date_format(date_create($c->date),'d F Y');?></td>
                    <td class='text-left'><?php echo $c->details;?></td>
                    <?php if($_SESSION['user_level'] == 'Root'):?>
                    <td><?php echo strip_tags($c->author);?></td>
                    <?php endif;?>
                </tr>
            </tbody>
            <?php endforeach;?>
        </table>
    </div>
</div>
<?php echo $pagination;?>