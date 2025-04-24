<!doctype html>
<html lang="en">
  <head>
  	<title>Developer's Portal</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<base href="<?= base_url(); ?>" />

	<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	
	<link rel="stylesheet" href="assets/portal/css/style.css?t=<?php echo time(); ?>">

	<!-- AlertifyJS -->
    <link rel="stylesheet" href="<?php echo base_url();?>node_modules/alertifyjs/build/css/alertify.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>node_modules/alertifyjs/build/css/themes/bootstrap.min.css">

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

	</head>
	<body id='developersPortal'>
	<img src="./assets/images/bg-developers-portal.jpg" class='bg' alt="">
	<section class="ftco-section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-6 col-lg-5 text-center mb-5">
					<a href="https://tweezzo.online" target="_blank">
						<img src="assets/images/LOGO-TWEEZZO-HORIZONTAL.png" style='width:100%' alt="">
					</a>
					<!-- <h2 class="heading-section">Customer's Portal</h2> -->
				</div>
			</div>
			<div class="row justify-content-center">
				<div class="col-md-6 col-lg-5">
					<div class="login-wrap p-4 p-md-5">
		      	<div class="icon d-flex align-items-center justify-content-center">
		      		<span class="fa fa-user-o"></span>
		      	</div>
		      	<!-- <h3 class="text-center mb-4">Have an account?</h3> -->
						<form action="./portal/developers/authenticate" method='post' class="login-form">
		      		<div class="form-group">
		      			<input type="email" name="email" class="form-control rounded-left" placeholder="Email" value="<?php echo $this->input->get("email");?>" autofocus required>
		      		</div>
	            <div class="form-group d-flex">
	              <input type="password" name="password" class="form-control rounded-left" placeholder="Password" required>
	            </div>
	            <div class="form-group d-md-flex">
	            	<div class="w-50">
	            		<label class="checkbox-wrap checkbox-primary">Remember Me
									  <input type="checkbox" name="remember_me" checked>
									  <span class="checkmark"></span>
									</label>
								</div>
								<div class="w-50 text-md-right forgot-password">
									<a href="#">Forgot Password</a>
								</div>
	            </div>
	            <div class="form-group">
	            	<button type="submit" class="login__submit btn btn-primary rounded submit p-3 px-5">Access Developer's Portal</button>
	            </div>
	          </form>
	        </div>
				</div>
			</div>
		</div>
	</section>

	<script>
		var base_url = "<?= base_url(); ?>";
	</script>
	<script src="assets/portal/js/jquery.min.js"></script>
  <script src="assets/portal/js/popper.js"></script>
  <script src="assets/portal/js/bootstrap.min.js"></script>
  <!-- Alertify -->
  <script src="<?php echo base_url('node_modules/alertifyjs/build/alertify.min.js'); ?>"></script>
  <script src="assets/js/portal/developers/signin.js?t=<?php echo time();?>"></script>

	</body>
</html>

