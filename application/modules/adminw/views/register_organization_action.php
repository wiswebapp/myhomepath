<?php
  $action = ucfirst(strtolower($data['action']));
  $pageTitle = $action." Provider";
  $methodUrl = admin_url('users/');

  if($action == 'Edit'){
    $userData = $data['usersData'][0];
  }  

?>
<!DOCTYPE html>
<html lang="en">
 
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?=APP_TITLE?> | <?=$pageTitle?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?=admin_assets()?>vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?=admin_assets()?>vendor/fonts/circular-std/style.css">
    <link rel="stylesheet" href="<?=admin_assets()?>/libs/css/style.css">
    <link rel="stylesheet" href="<?=admin_assets()?>/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <!-- For Single Use -->
    <link rel="stylesheet" href="<?=admin_assets()?>vendor/bootstrap-select/css/bootstrap-select.css">
    <style type="text/css">
      .card-toolbar-tabs .nav-pills .nav-link.active, .nav-pills .show > .nav-link,.nav-pills .nav-link.active, .nav-pills .show > .nav-link{
        color:#fff!important;
        background-color:black!important;;
      }
    </style>
</head>

<body>

    <div class="dashboard-main-wrapper">

        <?php include_once('includes/header.php') ?>
        <?php include_once('includes/sidebar.php') ?>   
        <?php include_once('includes/global_jslib.php'); ?>

        <div class="dashboard-wrapper">

          <div class="container-fluid  dashboard-content">
            

            <!-- ALL CONTENT INSIDE THIS DIV START -->
            <div class="row">
              <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                  <div class="page-header">
                      <!-- Page Title Section -->
                      <div class="page-header">
                          <h1><?=$pageTitle?>
                          <a href="<?=$methodUrl?>organization" class="btn btn-default" style="float: right;"> Back to listing</a>
                          </h1><hr>
                      </div>
                  </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card">
                    <div class="card-body">

                      <!-- Alert Section Start -->
                      <?php 
                        if(validation_errors()){
                          echo "<div class='alert alert-danger'><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>".validation_errors()."</div>"; 
                        }elseif(!empty($msg = $this->session->flashdata('feedback'))){ ?>
                        <div class="alert <?=$this->session->flashdata('feedback_class')?>">
                          <button type="button" class="close" data-dismiss="alert">&times;</button>
                          <strong><?=$msg?></strong>
                        </div>
                      <?php } ?>
                      <!-- Alert Section End -->

                      <!-- Form Section Starts Here -->
                      <form action="<?=$methodUrl.strtolower($action).'-organization/'.$userData['iOrgId']?>" enctype="multipart/form-data" method="post" accept-charset="utf-8" id="regform">

                        <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex">
                                        <h4 class="card-header-title"><i class="fas fa-pencil-alt"></i> Add / Update Data</h4>
                                        <div class="toolbar card-toolbar-tabs  ml-auto">
                                            <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active show" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="false">Provider Details</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content mb-3" id="pills-tabContent">
                                            <!-- Provider Profile Section -->
                                            <div class="tab-pane fade active show" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">

                                              <?php 

                                                if(empty($userData['vLogoImg'])) { 
                                                  $userData['vLogoImg'] = "no-image.png";
                                                } else{
                                                  echo "<input type='hidden' name='oldvImage' value='".$userData['vLogoImg']."'>";
                                                }
                                              ?>

                                              <div class="form-group col-xl-7 col-lg-7 col-md-7 col-sm-12 col-12">
                                                <img src="<?=base_url('webimages/organization/'.$userData['vLogoImg'])?>" height="150" width="150" class="img-thumbnail">
                                              </div>

                                              <?= buildInputText('Provider Name','vUserName','text',set_value('vUserName',$userData['vUserName']))?>
                                              <?= buildInputText('Mobile Number','vUserMobile','text',set_value('vUserMobile',$userData['vUserMobile']))?>
                                              <?= buildInputText('Email Address','vUserEmail','text',set_value('vUserEmail',$userData['vUserEmail']))?>
                                              <?php
                                                if($action == "Add"){
                                                  echo buildInputText('Password','vPassword','password','');
                                                }else{
                                                  echo buildInputText('Password','vPassword','password','','');
                                                }
                                              ?>

                                              
                                              <div class="form-group col-xl-7 col-lg-7 col-md-7 col-sm-12 col-12">
                                                <label>User Country <span class="text-danger">*</span></label><br>
                                                <select name="vCountry" onChange="setState(this.value)" class="form-control selectpicker" required="" data-live-search="true">
                                                  <option value="">Select Country</option>
                                                  <?php 
                                                    foreach ($data['country'] as $value) { 
                                                      $countrychk = "";
                                                      if($userData['vCountry'] == $value['iCountryId']){
                                                        $countrychk = "selected";
                                                      }
                                                  ?>
                                                    <option <?=$countrychk?> value="<?=$value['iCountryId']?>"><?=$value['vCountry']?></option>
                                                  <?php } ?>
                                                </select>
                                                <!-- pages/bootstrap-select.html -->
                                                <span class="error"><?=form_error('vCountry');?></span>
                                              </div>
                                              <!--============State============-->
                                              <div class="form-group col-xl-7 col-lg-7 col-md-7 col-sm-12 col-12">
                                                <label>Select State <span class="text-danger">*</span></label><br>
                                                <select name="vState" class="form-control" onChange="setCity(this.value)" required="" id="vState">
                                                  <option value="">Select State</option>
                                                </select>
                                              </div>
                                              <!--============City============-->
                                              <div class="form-group col-xl-7 col-lg-7 col-md-7 col-sm-12 col-12">
                                                <label>Select City <span class="text-danger">*</span></label><br>
                                                <select name="vCity" class="form-control" required="" id="vCity">
                                                  <option value="">Select State</option>
                                                </select>
                                              </div>

                                              <?= buildInputText('Address','vAddress','text',set_value('vAddress',$userData['vAddress']))?>

                                              <?= buildInputText('Zip Code','vZipCode','text',set_value('vZipCode',$userData['vZipCode']))?>
                                              
                                              <input type="hidden" name="vLocationLat" id="vLocationLat" value="<?=$userData['vLocationLat']?>">
                                              
                                              <input type="hidden" name="vLocationLong" id="vLocationLong" value="<?=$userData['vLocationLong']?>">

                                              <div class="form-group col-xl-7 col-lg-7 col-md-7 col-sm-12 col-12">
                                                <label>Profile Picture</label><br>
                                                <input type="file" name="vLogoImg" class="form-control">
                                              </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <hr>
                        <div class="form-group col-xl-7 col-lg-7 col-md-7 col-sm-12 col-12">
                          <button type="submit" class="btn btn-sm btn-dark"><?=$action?> Data</button>
                          <button type="reset" class="btn btn-sm btn-primary">Reset</button>
                          <a href="<?=$methodUrl?>organization" type="submit" class="btn btn-sm btn-light">Cancel</a><br>
                          <small class="text-danger">Note: All Fields (Profile Details + Business Details) are mandatory</small>
                        </div>
                      </form> 
                      <!-- Form Section Ends Here -->

                    </div>
                </div>
              </div>
            </div>
            <!-- ALL CONTENT INSIDE THIS DIV END -->

          </div>

        </div>

    </div>
    
    <script src="<?=admin_assets()?>vendor/jquery/jquery-3.3.1.min.js"></script>
    <script src="<?=admin_assets()?>vendor/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="<?=admin_assets()?>libs/js/main-js.js"></script>
    <!-- For Single Use -->
    <script src="<?=admin_assets()?>vendor/bootstrap-select/js/bootstrap-select.js"></script>
    <script>
      //Some Javascript Code 
      setState('<?=$userData['vCountry']?>','<?=$userData['vState']?>');
      setCity('<?=$userData['vState']?>','<?=$userData['vCity']?>');
    </script>
</body>
 
</html>