<!--============================================================== -->
<!-- left sidebar -->
<!-- ============================================================== -->
<div class="nav-left-sidebar sidebar-dark">
    <div class="menu-list">
        <nav class="navbar navbar-expand-lg navbar-light">
            <a class="d-xl-none d-lg-none" href="#">Dashboard</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav flex-column">
                    <li class="nav-divider">Menu</li>
                    <li class="nav-item ">
                        <a class="nav-link" href="<?=admin_url()?>">
                          <i class="fa fa-fw fa-warehouse"></i>Dashboard 
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" href="<?=admin_url('users/list')?>">
                          <i class="fas fa-fw fa-user"></i>Registered Users 
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" href="<?=admin_url('master/category')?>">
                          <i class="fas fa-align-justify"></i>Manage Product Category 
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" href="<?=admin_url('master/product')?>">
                          <i class="fas fa-box"></i>Manage Product 
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" href="<?=admin_url('management/order-report')?>">
                          <i class="far fa-file-alt"></i>Order Report
                        </a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-8" aria-controls="submenu-8"><i class="far fa-file-alt"></i>Manage Reports</a>
                        <div id="submenu-8" class="collapse submenu" style="">
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="nav-link" href="<?=admin_url('')?>">
                                        <i class="fas fa-arrow-right"></i> Payment Report
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li> -->
                </ul>
            </div>
        </nav>
    </div>
</div>
<!-- ============================================================== -->
<!-- end left sidebar -->
<!-- ============================================================== -->
