<?php $e = function ($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }; ?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card text-center">
            <div class="card-body py-5">
                <div class="display-5 text-primary mb-3"><i class="bi bi-building-check"></i></div>
                <h4>Ticket <?php echo $e($ticket->public_ref); ?> is now followed in the customer portal</h4>
                <p class="text-muted">Our team attached this request to your company's account, so updates are now available to your company's portal users.</p>
                <a href="portal/customers/signin" class="btn btn-primary"><i class="bi bi-box-arrow-in-right me-1"></i>Sign in to the customer portal</a>
            </div>
        </div>
    </div>
</div>
