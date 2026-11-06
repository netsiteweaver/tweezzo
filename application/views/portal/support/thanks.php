<?php $e = function ($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }; ?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card text-center">
            <div class="card-body py-5">
                <div class="display-4 text-success mb-3"><i class="bi bi-check2-circle"></i></div>
                <h3>Thanks, we've received your request</h3>
                <?php if (!empty($last)): ?>
                    <p class="lead mb-1">Your ticket reference is <b><?php echo $e($last['number']); ?></b>.</p>
                    <p class="text-muted">
                        We've sent a confirmation to <b><?php echo $e($last['email']); ?></b> with a link to follow your ticket and reply.
                    </p>
                    <?php if (!empty($last['upload_errors'])): ?>
                    <div class="alert alert-warning text-start">Some files were not attached: <?php echo $e(implode('; ', $last['upload_errors'])); ?></div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-muted">We'll get back to you by email shortly.</p>
                <?php endif; ?>
                <a href="support" class="btn btn-outline-secondary mt-2">Submit another request</a>
            </div>
        </div>
    </div>
</div>
