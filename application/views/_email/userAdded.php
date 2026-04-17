<div style='width:100%; text-align: center;'>
    <h3>USER HAS BEEN GRANTED ACCESS</h3>
</div>
<div style="margin:0px auto;max-width:800px;">
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
                <th class='text-left'>NAME</th>
                <td><?php echo $user_created['name'];?></td>
            </tr>
            <tr>
                <th class='text-left'>EMAIL</th>
                <td><?php echo $user_created['email'];?></td>
            </tr>
            <tr>
                <th class='text-left'>PASSWORD</th>
                <td><?php echo $user_created['password'];?></td>
            </tr>
            <tr>
                <th class='text-left'>CUSTOMER</th>
                <td><?php echo nl2br($customer->company_name);?></td>
            </tr>
            <!-- <tr>
                <th class="text-left">CREATED BY</th>
                <td><?php //echo "{$author->name} &lt;{$author->email}&gt;";?></td>
            </tr> -->
        </tbody>
    </table>
</div>
<div style='margin:30px auto; max-width:800px;'>
    <?php if (!empty($is_generated_password)): ?>
    <div style="margin-bottom:15px; padding:12px; border:1px solid #ffe58f; background:#fffbe6; color:#614700;">
        This password was generated automatically. For security, please sign in and change it immediately from <strong>My Profile</strong>, located in the top right corner of the screen.
    </div>
    <?php endif; ?>
    <a class='btn' href="<?php echo $link;?>">
        <div class="label"><?php echo $link_label;?></div>
    </a>
</div>