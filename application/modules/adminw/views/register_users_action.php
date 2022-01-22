<?php
  $action = ucfirst(strtolower($data['action']));
  $pageTitle = $action." Users";
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
                          <a href="<?=$methodUrl?>" class="btn btn-default" style="float: right;"> Back to listing</a>
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
                      <form action="<?=$methodUrl.strtolower($action).'/'.$userData['iUserId']?>" enctype="multipart/form-data" method="post" accept-charset="utf-8" id="regform">

                        <?php 

                          if(empty($userData['vImgName'])) { 
                            $userData['vImgName'] = "no-image.png";
                          } else{
                            echo "<input type='hidden' name='oldvImage' value='".$userData['vImgName']."'>";
                          }
                          
                        ?>
                        <div class="form-group">
                          <img src="<?=base_url('webimages/user/'.$userData['vImgName'])?>" height="150" width="150" class="img-thumbnail">
                        </div>

                        <?= buildInputText('Full Name','vName','text',set_value('vName',$userData['vName']))?>
                        <?= buildInputText('Mobile Number','vPhone','text',set_value('vPhone',$userData['vPhone']))?>
                        <?= buildInputText('Email Address','vEmail','email',set_value('vEmail',$userData['vEmail']))?>
                        <?php
                          if($action == "Add"){
                            echo buildInputText('Password','vPassword','password','');
                          }else{
                            echo buildInputText('Password','vPassword','password','','');
                          }
                        ?>
                        <?php
                          $genderChek = "checked";
                          $genderfemale = "";
                          if($userData['eGender'] == "Female"){
                            $genderfemale = "checked";
                            $genderChek = "";
                          }
                        ?>
                        <div class="form-group col-xl-7 col-lg-7 col-md-7 col-sm-12 col-12">
                          <label>Gender</label><br>
                          <!-- <input type="radio" name="eGender" <?=$genderChek?> value="Male"> Male &nbsp;&nbsp;
                          <input type="radio" name="eGender" <?=$genderfemale?> value="Female"> Female -->
                          <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" name="eGender" <?=$genderChek?> class="custom-control-input" value="Male">
                            <span class="custom-control-label">Male</span>
                          </label>
                          <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" name="eGender" <?=$genderfemale?> class="custom-control-input" value="Female">
                            <span class="custom-control-label">Female</span>
                          </label>
                        </div>
                        <div class="form-group col-xl-7 col-lg-7 col-md-7 col-sm-12 col-12">
                          <label>Profile Picture</label><br>
                          <input type="file" name="vImgName" class="form-control">
                        </div>
                        <!--============Country============-->
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
                          
                        <hr>
                        <div class="form-group col-xl-7 col-lg-7 col-md-7 col-sm-12 col-12">
                          <button type="submit" class="btn btn-sm btn-dark">Submit</button>
                          <button type="reset" class="btn btn-sm btn-primary">Reset</button>
                          <a href="<?=$methodUrl?>" type="submit" class="btn btn-sm btn-light">Cancel</a>
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