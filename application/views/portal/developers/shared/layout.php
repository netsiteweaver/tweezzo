<!doctype html>
<html lang="en">

<head>

    <title><?php echo $page_title;?> | Tweezzo</title>
    <base href="<?php echo base_url();?>">

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./assets/css/portal/developers/tasks.css?t=<?php echo date("YmdHis");?>">
    <link rel="stylesheet" href="./assets/css/portal/developers/styles.css?t=<?php echo date("YmdHis");?>">

    <!-- AlertifyJS -->
    <link rel="stylesheet" href="<?php echo base_url();?>node_modules/alertifyjs/build/css/alertify.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>node_modules/alertifyjs/build/css/themes/bootstrap.min.css">
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css" rel="stylesheet">

    <link rel="stylesheet" href="./node_modules/lightbox2/dist/css/lightbox.min.css">

    <link rel="stylesheet" href="./node_modules/bootstrap-icons/font/bootstrap-icons.css">

    <!-- Favicon -->
    <!-- https://www.favicon-generator.org/ -->
    <link rel="apple-touch-icon" sizes="57x57" href="/assets/faviconDeveloper/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/assets/faviconDeveloper/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/assets/faviconDeveloper/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/assets/faviconDeveloper/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/assets/faviconDeveloper/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/assets/faviconDeveloper/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/assets/faviconDeveloper/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/assets/faviconDeveloper/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/faviconDeveloper/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/assets/faviconDeveloper/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/faviconDeveloper/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/assets/faviconDeveloper/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/faviconDeveloper/favicon-16x16.png">
    <link rel="manifest" href="/assets/faviconDeveloper/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <!-- Option 1: Include in HTML -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css"> -->

</head>

<body>
<div id="overlay" class='d-none'><div class="loader"></div></div>
    <div class="container-fluid">
        <?php $this->load->view("portal/developers/shared/nav");?>

        <?php 

        if(isset($content)) foreach($content as $block){
            echo $block;
        }
            
        ?>

        <div class="fixed-bottom">
            <footer>Netsiteweaver Ltd</footer>
        </div>

        <?php $this->load->view("portal/developers/shared/add_task_modal");?>
        <!-- Optional JavaScript; choose one of the two! -->

        <!-- Option 1: Bootstrap Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
        </script>
    <script src="<?php echo base_url("assets/AdminLTE-3.2.0/");?>/plugins/jquery/jquery.min.js"></script>
    <script>
        var base_url = "<?php echo base_url();?>";
    </script>
    <script src="<?php echo base_url("node_modules/lightbox2/dist/js/lightbox.min.js");?>"></script>
    <!-- Alertify -->
    <script src="<?php echo base_url('node_modules/alertifyjs/build/alertify.min.js'); ?>"></script>
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.js"></script>

    <script src="<?php echo base_url("assets/js/portal/developers/script.js?t=".date("YmdHis"));?>"></script>

        <!-- Option 2: Separate Popper and Bootstrap JS -->
        <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
</body>

</html>