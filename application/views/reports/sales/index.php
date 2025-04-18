<style>
    .view-order{
        color:#3c8dbc;
    }
    .view-order:hover{
        text-decoration: underline !important;
    }
</style>
<div class="row">
    <form id="filter" action="">
    <div class="col-sm-3 col-lg-2">
        <label for="">From Date</label>
        <input type="date" name="start_date"  class='form-control' value="<?php echo date('Y-m-d',strtotime('-1 month'));?>" min="" max="<?php echo date('Y-m-d');?>">
    </div>
    <div class="col-sm-3 col-lg-2">
        <label for="">To Date</label>
        <input type="date" name="end_date"  class='form-control' value="<?php echo date('Y-m-d');?>" min="" max="<?php echo date('Y-m-d');?>">
    </div>
    <div class="col-sm-3 col-lg-2">
        <label for="">Item Code</label>
        <input type="text" name="stockref"  class='form-control' placeholder="Item Code">
    </div>
    <div class="col-sm-3 col-lg-2">
        <label for="">Item Description</label>
        <input type="text" name="description"  class='form-control' placeholder="Item Description">
    </div>
    <div class="col-sm-3 col-lg-2">
        <label for="">Color</label>
        <input type="text" name="color"  class='form-control' placeholder="Color">
    </div>
    <div class="col-sm-3 col-lg-2">
        <label for="">Category</label>
        <input type="text" name="category"  class='form-control' placeholder="Product Category">
    </div>
    <div class="col-sm-3 col-lg-2">
        <div class="form-group">
            <label>Channel</label>
            <select name="channel_id" id="channel" class="form-control required" data-name="Channel">
                <option value="">Select</option>
                <?php foreach ($channels as $row) : ?>
                    <option value="<?php echo $row->id; ?>"><?php echo strtoupper($row->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    
    <div class="col-sm-3 col-lg-2">
        <div class="form-group">
            <label>Routes</label>
            <select name="route_id" id="route_id" class="form-control required" data-name="Route">
                <option value="">Select</option>
                <?php foreach ($routes as $row) : ?>
                    <option value="<?php echo $row->route_id; ?>"><?php echo strtoupper($row->description); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
    </div>
    <div class="col-sm-3 col-lg-2">
        <div class="form-group">
            <label>Region</label>
            <select name="region_id" id="region_id" class="form-control required" data-name="Region">
                <option value="">Select</option>
                <?php foreach ($regions as $row) : ?>
                    <option value="<?php echo $row->id; ?>"><?php echo strtoupper($row->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="col-sm-3 col-lg-2">
        <label for="">Stage</label>
        <select id="" name="stage"  class='form-control'>
            <option value="">Select</option>
            <option value="draft">DRAFT</option>
            <option value="delivery">DELIVERY</option>
            <option value="completed">COMPLETED</option>
            <option value="cancel">CANCELLED</option>
        </select>
    </div>
    <div class="col-sm-3 col-lg-2">
        <label for="search_text">Search</label>
        <input type="text" class="form-control" name="search_text" placeholder="Enter text to search">
    </div>
    <div class="col-sm-3 pull-right text-right" style='margin-top:20px;'>
        <div class="btn btn-info" id="apply_filter"><div class="fa fa-check"></div> Apply Filter</div>
        <div class="btn btn-success" id="export"><div class="fa fa-file-excel-o"></div> Export</div>
        <div class="btn btn-default print"><div class="fa fa-print"></div> Print</div>
    </div>
    </form>
</div>

<div class="row">
    <div class="col-sm-12 table-responsive">
        <table id="tbl_orders" class="table table-bordered">
            <thead>
                <tr>
                    <th>ORDER DATE</th>
                    <th>CUSTOMER</th>
                    <th>ORDER No.</th>
                    <th>ITEM CODE</th>
                    <th>DESCRIPTION</th>
                    <th>COLOR</th>
                    <th>CATEGORY</th>
                    <th>CHANNEL</th>
                    <th>REGION</th>
                    <th>ROUTE</th>
                    <th>STAGE</th>
                    <th>QTY</th>
                    <th>PRICE</th>
                    <th>AMOUNT</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>