<!doctype html>
<html lang="en">
<head>
    <title><?php echo htmlspecialchars($page_title); ?> | Tweezzo</title>
    <base href="<?php echo base_url(); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./node_modules/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./node_modules/lightbox2/dist/css/lightbox.min.css">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/faviconCustomer/favicon-32x32.png">
    <style>
        body { background:#f4f6f9; }
        .support-header { background:#fff; border-bottom:1px solid #e3e6ea; }
        .support-header img { height:40px; width:auto; }
        .support-main { padding-bottom:60px; }
    </style>
</head>
<body>
    <header class="support-header py-3 mb-3">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="portal/support"><img src="assets/images/<?php echo htmlspecialchars((string) $logoDark); ?>" alt="Logo"></a>
            <a href="portal/customers/signin" class="btn btn-sm btn-outline-secondary"><i class="bi bi-box-arrow-in-right me-1"></i>Customer portal</a>
        </div>
    </header>
    <main class="container support-main">
        <?php if (!empty($flash_danger)): ?><div class="alert alert-danger"><?php echo $flash_danger; ?></div><?php endif; ?>
        <?php if (!empty($flash_warning)): ?><div class="alert alert-warning"><?php echo $flash_warning; ?></div><?php endif; ?>
        <?php if (!empty($flash_success)): ?><div class="alert alert-success"><?php echo $flash_success; ?></div><?php endif; ?>
        <?php echo $content; ?>
    </main>
    <script src="<?php echo base_url("assets/AdminLTE-3.2.0/plugins/jquery/jquery.min.js"); ?>"></script>
    <script src="<?php echo base_url("node_modules/lightbox2/dist/js/lightbox.min.js"); ?>"></script>
</body>
</html>
