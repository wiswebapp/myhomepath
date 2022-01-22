<?php

$table = "med_users";
$pageTitle = "Register Users";
$methodUrl = admin_url('users/');
$addDataUrl = $methodUrl . 'add';
$editDataUrl = $methodUrl . 'edit';

$pageData = $data['usersList']['data'];
$count = (count($pageData) > 0) ? count($pageData) : 0;

$countAll = ($data['usersList']['totalAllData'] > 0) ? $data['usersList']['totalAllData'] : 0;
$multipage = empty($this->uri->segment(4)) ? 0 : $this->uri->segment(4);

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
	<link rel="stylesheet" href="<?= admin_assets() ?>vendor/datatables/css/dataTables.bootstrap4.css">
	<title><?= APP_TITLE ?> | <?= $pageTitle ?></title>
</head>

<body>
	<div class="dashboard-main-wrapper">

		<?php include_once('includes/header.php') ?>
		<?php include_once('includes/sidebar.php') ?>
		<?php include_once('includes/global_jslib.php'); ?>

		<div class="dashboard-wrapper">

			<div class="container-fluid  dashboard-content">

				<div class="row">

					<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

						<!-- Page Title Section -->
						<div class="page-header">
							<h1><?= $pageTitle ?>
								<!-- <a href="<?= $addDataUrl ?>" class="btn btn-default" style="float: right;"><i class="fa fa-plus"></i> Add Data</a>
                          </h1>-->
								<hr>
						</div>

						<!-- Alert Section Start -->
						<?php if (!empty($msg = $this->session->flashdata('feedback'))) : ?>
							<div class="alert <?= $this->session->flashdata('feedback_class') ?>">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								<strong><?= $msg ?></strong>
							</div>
						<?php endif; ?>
						<!-- Alert Section End -->

						<!-- Filter Data Section Start -->
						<div class="filter-data-box">
							<form action="" method="GET" id="_filter_data">
								<table width="100%" cellpadding="1" border="0">
									<tr>
										<th>Filter by : </th>
										<th><input class="form-control" type="text" name="name" placeholder="Name" value="<?= trim(@$_GET['name']) ?>"></th>
										<th><input class="form-control" type="text" name="email" placeholder="Email" value="<?= trim(@$_GET['email']) ?>"></th>
										<th><input class="form-control" type="text" name="mobile" placeholder="Mobile Number" value="<?= trim(@$_GET['mobile']) ?>"></th>
										<th>
											<select class="form-control" name="status">
												<option value="">Select Status</option>
												<option <?php if (trim(@$_GET['status']) == "Yes") echo 'selected'; ?> value="Yes">Active</option>
												<option <?php if (trim(@$_GET['status']) == "No") echo 'selected'; ?> value="No">InActive</option>
											</select>
										</th>
										<th>
											<button type="submit" class="btn btn-primary">Search</button>
										</th>
									</tr>
								</table>
							</form>
						</div>
						<!-- Filter Data Section End -->
					</div>
				</div>
				<br>

				<div class="row">
					<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
						<div class="card">
							<div class="card-body">
								<!-- For Bulk Action & Export Data -->
								<div class="action-and-export">
									<!-- <select class="form-control" style="float: left;width: auto;" onchange="bulkAction(this.value,'<?= $methodUrl ?>changeBulkStatus','<?= $table ?>')">
										<option value="">Select Action</option>
										<option value="Active">Make Active</option>
										<option value="InActive">Make InActive</option>
										<option value="Deleted">Delete</option>
									</select> -->
									<?php if ($count > 0) : ?>
										<!-- <button type="button" onclick="exportData('register_users','xls')" class="btn btn-default" style="float: right;">Export Data</button> -->
									<?php endif; ?>
								</div>
								<div class="clearfix"></div><br>

								<!-- Data Table Has Started -->
								<div class="table-responsive">
									<table class="table table-striped table-bordered" style="width:100%">
										<thead>
											<tr>
												<th><input type="checkbox" id="setAllCheck"></th>
												<th>Register Date</th>
												<th>Name</th>
												<th>Mobile</th>
												<th>Email</th>
												<th>Status</th>
												<!-- <th>Action</th> -->
											</tr>
										</thead>
										<tbody id="_data_rows">
											<?php
											if ($count > 0) :

												for ($i = 0; $i < $count; $i++) {

													$userIdd = $pageData[$i]['id'];
													if ($pageData[$i]['isActive'] == 'Yes') {
														$status = "<span class='label label-success'>Active</span>";
													} elseif ($pageData[$i]['isActive'] == 'No') {
														$status = "<span class='label label-danger'>InActive</span>";
													} else {
														$status = "<span class='label label-warning'>Deleted</span>";
													}
											?>
													<tr>
														<td>
															<input type="checkbox" value="<?= $userIdd ?>" name="">
														</td>
														<td><?= toDate($pageData[$i]['created_at']) ?></td>
														<td><?= $pageData[$i]['name'] ?></td>
														<td><?= $pageData[$i]['phone'] ?></td>
														<td><?= $pageData[$i]['email'] ?></td>
														<td><?= $status ?></td>
														<!-- <td>
                                        <a data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit Data" class="btn btn-sm btn-default" href="<?= $editDataUrl . '/' . $userIdd ?>"><i class="fa fa-edit"></i></a>
                                        <span data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete Data" class="btn btn-sm btn-default" onclick="changeUserStatus(3,<?= $userIdd ?>,'<?= $table ?>')"><i class="fa fa-trash"></i></span>
                                      </td> -->
													</tr>
												<?php }
											else : ?>
												<tr>
													<td colspan="100%" align="center">
														<h2 class="btn">No Data Found</h2>
													</td>
												</tr>
											<?php endif; ?>
											<?php if (count($_GET) > 0) : ?>
												<tr>
													<td colspan="100%" align="center">
														<h3><a href="<?= $methodUrl ?>" class="btn-sm">Reset Filter</a></h3>
													</td>
												</tr>
											<?php endif; ?>
										</tbody>
									</table>
								</div>
								<hr>
								<!-- For Pagination Area Start -->
								<div style="padding: 1px;" align="right">
									<?= $this->pagination->create_links(); ?>
									<p style='float:right' class="dataFooter">Showing <?= $multipage ?> to <?= ($count + $multipage) ?> from <?= $countAll ?> entries</p>
								</div>
								<!-- For Pagination Area End -->

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="<?= admin_assets() ?>vendor/bootstrap/js/bootstrap.bundle.js"></script>
	<script src="<?= admin_assets() ?>libs/js/main-js.js"></script>
	<script>
		$("#setAllCheck").on('click', function() {
			if ($(this).prop("checked")) {
				$('#_data_rows input[type=checkbox]').prop('checked', this.checked);
			} else {
				$('#_data_rows input[type=checkbox]').prop('checked', false)
			}
		});
	</script>

</body>

</html>
