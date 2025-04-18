<div class="row">
    <div class="col-md-3">
        <table class="table table-bordered">
            <tr>
                <td><b>Date: </b><?php echo $record->date;?></td>
            </tr>
            <tr>
                <td><b>User: </b><?php echo $record->name;?></td>
            </tr>
            <tr>
                <td><b>IP: </b><?php echo $record->ip;?></td>
            </tr>
            <tr>
                <td><b>Controller: </b><?php echo $record->controller;?></td>
            </tr>
            <tr>
                <td><b>Method: </b><?php echo $record->method;?></td>
            </tr>
            <tr>
                <td><b>URI: </b><?php echo $record->uri;?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-5">
        <label for="">METADATA</label>
        <textarea name="" id="" cols="30" rows="10" class="form-control" style='height:80vh;'><?php 
        $data = json_decode($record->meta);
        foreach($data as $item => $content){
            if(!empty($content)){
                echo $item."\r\n";
                print_r($content);
                echo "\r\n";
            }
        }
        ?></textarea>
    </div>
</div>
<div class="row">
    <div class="col-md-2">
        <a href="<?php echo base_url('audittrail/listing/'.$this->input->get('page',1));?>"><div class="btn btn-warning btn-lg"><i class="fa fa-chevron-left"></i> Back</div></a>
    </div>
</div>