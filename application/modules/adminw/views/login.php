<!DOCTYPE html>
<html lang="en">
 
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login | <?=APP_TITLE?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?=base_url('assets/admin/')?>vendor/bootstrap/css/bootstrap.min.css">
    <link href="<?=base_url('assets/admin/')?>vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="<?=base_url('assets/admin/')?>libs/css/style.css">
    <link rel="stylesheet" href="<?=base_url('assets/admin/')?>libs/css/style_web.css">
    <link rel="stylesheet" href="<?=base_url('assets/admin/')?>vendor/fonts/fontawesome/css/fontawesome-all.css">
    <style>
    html,
    body {
        height: 100%;
    }
    body {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-align: center;
        align-items: center;
        padding-top: 40px;
        padding-bottom: 40px;
    }
    </style>
</head>

<body>
    <div class="splash-container">
        <?php if(!empty($msg = $this->session->flashdata('feedback'))): ?>
            <div class="alert <?=$this->session->flashdata('feedback_class')?>">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <strong><?=$msg?></strong>
            </div>
        <?php endif; ?>
        <div class="card ">
            <div class="card-header text-center">
                <img src="<?=assets('logo.jpg')?>" height="110" width="130" alt="logo">
                <hr style="border: 0">
                <p>Please Login to continue</p>
            </div>
            <div class="card-body">
                <?=form_open(admin_url('login/auth'));?>
                    <div class="form-group">
                        <input required="" class="form-control form-control-lg" id="username" type="email" placeholder="Enter Username" name="username" autocomplete="off" value="<?=set_value('username',@$_REQUEST['access'])?>">
                        <span class="error"><?=form_error('username')?></span>
                    </div>
                    <div class="form-group">
                        <input required="" minlength="5" class="form-control form-control-lg" id="password" type="password" placeholder="Enter Password" name="password">
                        <span class="error"><?=form_error('password')?></span>
                    </div>
                    <button style="background-color: #598F15" type="submit" class="btn btn-primary btn-lg btn-block">Sign in</button>
                <?=form_close()?>
            </div>
        </div>
    </div>
  
    <!-- ============================================================== -->
    <!-- end login page  -->
    <!-- ============================================================== -->
    <!-- Optional JavaScript -->
    <script src="<?=base_url('assets/admin/')?>vendor/jquery/jquery-3.3.1.min.js"></script>
    <script src="<?=base_url('assets/admin/')?>vendor/bootstrap/js/bootstrap.bundle.js"></script>
</body>
 
</html>
