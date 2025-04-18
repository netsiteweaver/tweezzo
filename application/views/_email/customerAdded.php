<div style='max-width:800px%; text-align: center;'>
    <h3>CUSTOMER CREATED</h3>
</div>
<div style="margin:0px auto;max-width:800px;">
    <p>A customer has been created, details follow:</p>
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
                <th style='text-align:left; width: 150px;'>COMPANY *</th>
                <td><?php echo $customer['company_name'];?></td>
            </tr>
            <tr>
                <th style='text-align:left; width: 150px;'>CONTACT PERSON</th>
                <td><?php echo $customer['full_name'];?></td>
            </tr>
            <tr>
                <th style='text-align:left; width: 150px;'>ADDRESS</th>
                <td><?php echo $customer['address'];?></td>
            </tr>
            <tr>
                <th style='text-align:left; width: 150px;'>PHONE 1</th>
                <td><?php echo $customer['phone_number1'];?></td>
            </tr>
            <tr>
                <th style='text-align:left; width: 150px;'>PHONE 2</th>
                <td><?php echo $customer['phone_number2'];?></td>
            </tr>
            <tr>
                <th style='text-align:left; width: 150px;'>EMAIL *</th>
                <td><?php echo $customer['email'];?></td>
            </tr>
            <tr>
                <th style='text-align:left; width: 150px;'>BRN</th>
                <td><?php echo $customer['brn'];?></td>
            </tr>
            <tr>
                <th style='text-align:left; width: 150px;'>VAT</th>
                <td><?php echo $customer['vat'];?></td>
            </tr>
            <tr>
                <th style='text-align:left; width: 150px;'>REMARKS</th>
                <td><?php echo nl2br($customer['remarks']);?></td>
            </tr>
        </tbody>
    </table>
    <p>And below is your login credentials:</p>
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
            <th style='text-align:left; width: 150px;'>Customer's Portal</th>
                <td>
                    <a href="<?php echo base_url("portal/customers/signin");?>">Access Portal</a>
                </td>
            </tr>
            <tr>
                <th style='text-align:left; width: 150px;'>Email</th>
                <td><?php echo $customer['email'];?></td>
            </tr>
            <tr>
                <th style='text-align:left; width: 150px;'>Password</th>
                <td><?php echo $password;?></td>
            </tr>
        </tbody>
    </table>
</div>
<?php if( (!empty($link)) && (!empty($link_label)) ):?>
<div style='margin:30px auto; max-width:800px;'>
    <a class='btn' href="<?php echo $link;?>">
        <div class="label"><?php echo $link_label;?></div>
    </a>
</div>
<?php endif;?>