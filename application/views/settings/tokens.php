<?php $key = '<input type="hidden" name="form_key" value="' . html_escape($form_key) . '">'; ?>
<div class="row">
    <div class="col-md-8 col-xs-12">
        <div class="box box-primary">
            <div class="box-body">
                <?php if (!empty($new_token)): ?>
                <div class="alert alert-warning">
                    <p><strong>Copy this token now.</strong> It will not be shown again.</p>
                    <input type="text" class="form-control input-sm" value="<?php echo html_escape($new_token);?>" readonly onfocus="this.select()">
                    <p style="margin:6px 0 0;font-size:0.9em;">In Yawmee: Connections &rarr; Tweezzo, mode "Yawmee API", auth "Bearer token", base URL <?php echo html_escape(rtrim(base_url(), '/'));?></p>
                </div>
                <?php endif; ?>

                <p><i class="fa fa-info-circle"></i> A token lets the Yawmee desktop app read your Tweezzo tasks and tickets, with your permissions.
                    Tokens are personal: each user sees only their own, including their mobile app sign-ins.
                    A token stops working after <?php echo Api_tokens_model::TTL_DAYS;?> days without use.</p>

                <?php if (empty($tokens)): ?>
                <p class="text-muted">You have no active tokens.</p>
                <?php else: ?>
                <table class="table table-condensed">
                    <thead><tr><th>Name / device</th><th>Created</th><th>Last used</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($tokens as $t): ?>
                        <tr>
                            <td><?php echo html_escape($t->device_name ?: 'Token');?></td>
                            <td><?php echo html_escape(date('j M Y', strtotime($t->created_at)));?></td>
                            <td><?php echo $t->last_used_at ? html_escape(date('j M Y H:i', strtotime($t->last_used_at))) : 'never';?></td>
                            <td class="text-right">
                                <form action="<?php echo base_url('settings/tokens');?>" method="post" onsubmit="return confirm('Revoke this token? The app using it will be signed out.');">
                                    <?php echo $key;?>
                                    <input type="hidden" name="do" value="revoke">
                                    <input type="hidden" name="id" value="<?php echo (int) $t->id;?>">
                                    <button type="submit" class="btn btn-xs btn-danger"><i class="fa fa-ban"></i> Revoke</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
            <div class="box-footer">
                <form action="<?php echo base_url('settings/tokens');?>" method="post">
                    <?php echo $key;?>
                    <input type="hidden" name="do" value="create">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" name="label" maxlength="100" placeholder="Name, e.g. Yawmee on my laptop">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Create token</button>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
