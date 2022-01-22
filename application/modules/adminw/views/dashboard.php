<?php
$totalCustomer = $data['totalCustomer'];
$totalCategory = $data['totalCategory'];
$totalProduct = $data['totalProduct'];
?>
<!DOCTYPE html>
<html lang="en">


<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="<?= admin_assets() ?>vendor/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= admin_assets() ?>vendor/fonts/circular-std/style.css">
	<link rel="stylesheet" href="<?= admin_assets() ?>libs/css/style.css">
	<link rel="stylesheet" href="<?= admin_assets() ?>vendor/fonts/fontawesome/css/fontawesome-all.css">
	<link rel="stylesheet" href="<?= admin_assets() ?>vendor/vector-map/jqvmap.css">
	<link rel="stylesheet" href="<?= admin_assets() ?>vendor/jvectormap/jquery-jvectormap-2.0.2.css">
	<link rel="stylesheet" href="<?= admin_assets() ?>vendor/fonts/flag-icon-css/flag-icon.min.css">
	<title><?= APP_TITLE ?> | Dashboard</title>
</head>

<body>
	<div class="dashboard-main-wrapper">

		<?php include_once('includes/header.php') ?>
		<?php include_once('includes/sidebar.php') ?>

		<!-- ============================================================== -->
		<!-- wrapper  -->
		<!-- ============================================================== -->
		<div class="dashboard-wrapper">
			<div class="container-fluid  dashboard-content">
				<!-- ============================================================== -->
				<!-- pagehader  -->
				<!-- ============================================================== -->
				<div class="row">
					<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
						<div class="page-header">
							<h3 class="mb-2">Super Admin</h3>
							<p class="pageheader-text">Lorem ipsum dolor sit ametllam fermentum ipsum eu porta consectetur adipiscing elit.Nullam vehicula nulla ut egestas rhoncus.</p>
							<div class="page-breadcrumb">
								<nav aria-label="breadcrumb">
									<ol class="breadcrumb">
										<li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
										<li class="breadcrumb-item active" aria-current="page">Super Admin</li>
									</ol>
								</nav>
							</div>
						</div>
					</div>
				</div>
				<!-- ============================================================== -->
				<!-- pagehader  -->
				<!-- ============================================================== -->
				<div class="row">
					<!-- metric -->
					<div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
						<div class="card">
							<div class="card-body">
								<h5 class="text-muted">Customers</h5>
								<div class="metric-value d-inline-block">
									<h1 class="mb-1 text-primary"><?= $totalCustomer ?></h1>
								</div>
							</div>
							<div id="sparkline-1"></div>
						</div>
					</div>
					<!-- /. metric -->
					<!-- metric -->
					<!-- <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
						<div class="card">
							<div class="card-body">
								<h5 class="text-muted">Provider</h5>
								<div class="metric-value d-inline-block">
									<h1 class="mb-1 text-primary"><?= $totalProvider ?></h1>
								</div>
							</div>
							<div id="sparkline-2"></div>
						</div>
					</div> -->
					<!-- /. metric -->
					<!-- metric -->
					<div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
						<div class="card">
							<div class="card-body">
								<h5 class="text-muted">Total Category</h5>
								<div class="metric-value d-inline-block">
									<h1 class="mb-1 text-primary"><?= $totalCategory ?></h1>
								</div>
							</div>
							<div id="sparkline-3">
							</div>
						</div>
					</div>
					<!-- /. metric -->
					<!-- metric -->
					<div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
						<div class="card">
							<div class="card-body">
								<h5 class="text-muted">Total Product</h5>
								<div class="metric-value d-inline-block">
									<h1 class="mb-1 text-primary"><?= $totalProduct ?></h1>
								</div>
							</div>
							<div id="sparkline-4"></div>
						</div>
					</div>
					<!-- /. metric -->
				</div>
			</div>
		</div>
		<!-- ============================================================== -->
		<!-- end wrapper  -->
		<!-- ============================================================== -->
	</div>

	<script src="<?= admin_assets() ?>vendor/jquery/jquery-3.3.1.min.js"></script>
	<script src="<?= admin_assets() ?>vendor/bootstrap/js/bootstrap.bundle.js"></script>
	<script src="<?= admin_assets() ?>vendor/slimscroll/jquery.slimscroll.js"></script>
	<script src="<?= admin_assets() ?>libs/js/main-js.js"></script>
</body>

</html>
