<div style='max-width:800px%; text-align: center;'>
    <h3>MEETING NOTES</h3>
</div>
<div style="margin:0px auto;max-width:800px;">
    <p>Please find below details of our meeting:</p>
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
                <th style='text-align:left; width: 150px;'>DATE & TIME</th>
                <td><?php echo date_format(date_create($meeting->meeting_datetime),'Y-m-d @ H:i');?></td>
            </tr>
            <tr>
                <th style='text-align:left; width: 150px;'>CUSTOMER</th>
                <td><?php echo $meeting->customer_name;?></td>
            </tr>
            <tr>
                <th style='text-align:left; width: 150px;'>LIEU</th>
                <td><?php echo $meeting->lieu;?></td>
            </tr>
            <tr>
                <th style='text-align:left; width: 150px;'>ATTENDEES</th>
                <td><?php echo nl2br($meeting->attendees);?></td>
            </tr>
            <tr>
                <th style='text-align:left; width: 150px;'>MEETING NOTES</th>
                <td><?php echo nl2br($meeting->notes);?></td>
            </tr>
        </tbody>
    </table>
</div>