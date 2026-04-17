<div class="row justify-content-center mt-3">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-person-gear me-2"></i>My Profile
            </div>
            <div class="card-body">
                <form method="post" action="<?php echo base_url('portal/customers/updateMyProfile'); ?>" class="mb-3">
                    <h6>Profile Details</h6>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($profile->name ?? $_SESSION['customer_name']); ?>" minlength="4" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($profile->phone_number1 ?? ''); ?>" placeholder="Enter phone number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Country</label>
                        <?php $selected_country = strtolower((string) ($profile->country_code ?? 'mu')); ?>
                        <select name="country_code" class="form-control" required>
                            <option value="mu" <?php echo ($selected_country === 'mu') ? 'selected' : ''; ?>>Mauritius</option>
                            <option value="in" <?php echo ($selected_country === 'in') ? 'selected' : ''; ?>>India</option>
                            <option value="ua" <?php echo ($selected_country === 'ua') ? 'selected' : ''; ?>>Ukraine</option>
                            <option value="pl" <?php echo ($selected_country === 'pl') ? 'selected' : ''; ?>>Poland</option>
                            <option value="br" <?php echo ($selected_country === 'br') ? 'selected' : ''; ?>>Brazil</option>
                            <option value="cn" <?php echo ($selected_country === 'cn') ? 'selected' : ''; ?>>China</option>
                            <option value="mx" <?php echo ($selected_country === 'mx') ? 'selected' : ''; ?>>Mexico</option>
                            <option value="pk" <?php echo ($selected_country === 'pk') ? 'selected' : ''; ?>>Pakistan</option>
                            <option value="ma" <?php echo ($selected_country === 'ma') ? 'selected' : ''; ?>>Morocco</option>
                            <option value="cr" <?php echo ($selected_country === 'cr') ? 'selected' : ''; ?>>Costa Rica</option>
                            <option value="ru" <?php echo ($selected_country === 'ru') ? 'selected' : ''; ?>>Russia</option>
                            <option value="fr" <?php echo ($selected_country === 'fr') ? 'selected' : ''; ?>>France</option>
                            <option value="de" <?php echo ($selected_country === 'de') ? 'selected' : ''; ?>>Germany</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($profile->email ?? $_SESSION['customer_email']); ?>" readonly>
                        <small class="text-muted">Email can only be changed by an administrator.</small>
                    </div>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-person-check me-1"></i>Update Profile
                    </button>
                </form>
                <hr>
                <h6>Change Password</h6>
                <p class="text-muted mb-3">If this account was created with a generated password, change it now for security.</p>
                <form method="post" action="<?php echo base_url('portal/customers/updateMyPassword'); ?>">
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" minlength="6" required>
                        <small class="text-muted">Minimum 6 characters</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" minlength="6" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-shield-lock me-1"></i>Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>