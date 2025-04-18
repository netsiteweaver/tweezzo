<div class="panel panel-info">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-4 table-responsive">
                <h4>Customer Information</h4>
                <table class="table table-bordered table-hover">
                    <colgroup>
                        <col width="25%" style="color:red;">
                        <col width="75%">
                    </colgroup>
                    <tr>
                        <td>Last Name</td>
                        <td><?php echo $customer->last_name; ?></td>
                    </tr>
                    <tr>
                        <td>First Name</td>
                        <td><?php echo $customer->first_name; ?></td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td><?php echo $customer->address; ?></td>
                    </tr>
                    <tr>
                        <td>City</td>
                        <td><?php echo $customer->city; ?></td>
                    </tr>
                    <tr>
                        <td>Region</td>
                        <td><?php echo $customer->region_name; ?></td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td><?php echo $customer->email; ?></td>
                    </tr>
                    <tr>
                        <td>Phone 1</td>
                        <td><?php echo $customer->phone_number1; ?></td>
                    </tr>
                    <tr>
                        <td>Phone 2</td>
                        <td><?php echo $customer->phone_number2; ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-8 table-responsive">
                <h4>Order History</h4>
                <table class="table table-bordered table-hover">
                    <thead>
                        <th>DATE</th>
                        <th>ITEM</th>
                        <th>QUANTITY</th>
                        <th>PRICE</th>
                        <th>AMOUNT</th>
                        <th>STATUS</th>
                        <th>NOTES</th>
                    </thead>
                    <tbody>
                    <?php foreach($history as $transaction):?>
                        <tr>
                            <td class="text-center"><?php echo date_format(date_create($transaction->invoice_date),'Y-m-d');?></td>
                            <td><?php echo $transaction->description;?></td>
                            <td class="text-right"><?php echo $transaction->quantity;?></td>
                            <td class="text-right"><?php echo number_format($transaction->price,0);?></td>
                            <td class="text-right"><?php echo number_format($transaction->total,0);?></td>
                            <td class="text-center"><?php echo strtoupper($transaction->deliver_status);?></td>
                            <td><?php echo $transaction->reason;?></td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<div class="panel-body">
    <a href="customers/listing">
        <div class="btn btn-warning"><i class="fa fa-chevron-left"></i> Back</div>
    </a>
</div>