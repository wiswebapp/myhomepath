<?php
  $action = ucfirst(strtolower($data['action']));
  $pageTitle = $action." Subscription Plan";
  $methodUrl = admin_url('master/');
  $tconfig = $data['tconfig'];
  $category = $data['category'];

  if($action == 'Edit'){
    $pageData = $data['pageData'][0];
  }

  $product = $data['productData'];

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
                          <a href="<?=$methodUrl?>subscription" class="btn btn-default" style="float: right;"> Back to listing</a>
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
                      <form action="<?=$methodUrl.strtolower($action).'-subscription/'.$pageData['iProductId']?>" enctype="multipart/form-data" method="post" accept-charset="utf-8" id="regform">

                        <div class="form-group col-xl-7 col-lg-7 col-md-7 col-sm-12 col-12">
                          <label>Select Product <span class="text-danger">*</span></label>
                          <select name="iProductId" class="form-control" required="">
                            <option value="">----Select Product---</option>
                            <?php 
                              foreach ($product as $value) { 
                                $sct = "";
                                if($pageData['iProductId'] == $value['iProductId']){
                                  $sct = "selected";
                                }
                            ?>
                              <option <?=$sct?> value="<?=$value['iProductId']?>"><?=$value['vProductName']?></option>
                            <?php } ?>
                          </select>
                        </div>

                        <?= buildInputText('Plan Name','vPlanName','text',set_value('vPlanName',$pageData['vPlanName']))?>

                        <?= buildInputText('Plan Amount','iAmount','text',set_value('iAmount',$pageData['iAmount']))?>

                        <?= buildInputText('Plan Validity(in Days)','iValidity','number',set_value('iValidity',$pageData['iValidity']))?>

                        <hr>
                        <div class="form-group col-xl-7 col-lg-7 col-md-7 col-sm-12 col-12">
                          <button type="submit" class="btn btn-sm btn-dark">Submit</button>
                          <button type="reset" class="btn btn-sm btn-primary">Reset</button>
                          <a href="<?=$methodUrl?>subscription" type="submit" class="btn btn-sm btn-light">Cancel</a>
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
    
    $(document).ready(function(){

      $("#isSubscribe").change(function(){
        myVal = $(this).val();
        if(myVal == "Subscription"){
          $("#iAmount").val(0);
          $("#iProductAmount").hide();
        }else{
          $("#iProductAmount").show();
        }
      });

    });
    </script>
</body>
 
</html>