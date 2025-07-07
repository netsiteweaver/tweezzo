<div style='width:100%; text-align: center;'>
    <h3>USER HAS BEEN REMOVED ACCESS</h3>
</div>
<div style="margin:0px auto;max-width:800px;">
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
                <th class='text-left'>NAME</th>
                <td><?php echo $user['name'];?></td>
            </tr>
            <tr>
                <th class='text-left'>EMAIL</th>
                <td><?php echo $user['email'];?></td>
            </tr>
            <tr>
                <th class="text-left">REMOVED BY</th>
                <td><?php echo "{$author->name} &lt;{$author->email}&gt;";?></td>
            </tr>
        </tbody>
    </table>
</div>
<div style='margin:30px auto; max-width:800px;'>
    <a class='btn' href="<?php echo $link;?>">
        <div class="label"><?php echo $link_label;?></div>
    </a>
</div>