<div class="row small-text">

    <div class="col-md-6 table-responsive">
        <h4 class='text-center'>LATEST LOGINS</h4>
        <div class="div" style='height:50vh;overflow:scroll;'>
            <table id="latest_logins_table" class="table table-bordered">
                <thead>
                    <tr class='text-center'>
                        <th>DATE</th>
                        <th>USERNAME / EMAIL</th>
                        <th>OS</th>
                        <th>RESULT</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($latest_logins as $row):?>
                    <tr title="<?php echo $row->ip . " | " . $row->os . " | " . $row->browser . " | " . $row->result_other;?>" class='text-center <?php echo (($row->result=='SUCCESS')?'':'red');?>' >
                        <td><?php echo $row->datetime;?></td>
                        <td><?php echo $row->username . (!empty($row->email)?' / '.$row->email:'');?></td>
                        <td><?php echo $row->os;?></td>
                        <td><?php echo $row->result;?></td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>
    
</div>
