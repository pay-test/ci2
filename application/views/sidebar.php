<div class="page-container row-fluid">
      <!-- BEGIN SIDEBAR -->
      <!-- BEGIN MENU -->
      <div class="page-sidebar" id="main-menu">
        <div class="page-sidebar-wrapper scrollbar-dynamic" id="main-menu-wrapper">
          <!-- BEGIN MINI-PROFILE -->
          <div class="user-info-wrapper">
            <div class="profile-wrapper">
              <img src="<?php echo assets_url('assets/img/profiles/avatar.jpg')?>" alt="" data-src="<?php echo assets_url('assets/img/profiles/avatar.jpg')?>" data-src-retina="<?php echo assets_url('assets/img/profiles/avatar2x.jpg')?>" width="69" height="69" />
            </div>
            <div class="user-info">
              <div class="greeting">Welcome</div>
              <div class="username">John <span class="semi-bold">Smith</span></div>
              <div class="status">Status
                <a href="#">
                  <div class="status-icon green"></div>Online</a>
              </div>
            </div>
          </div>
          <!-- END MINI-PROFILE -->
          <!-- BEGIN SIDEBAR MENU -->
          <ul>
            <!-- BEGIN SELECTED LINK -->
            <li class="start active">
              <a href="#">
                <i class="icon-custom-home"></i>
                <span class="title">Dashboard</span>
                <span class="selected"></span>
              </a>
            </li>
            <li class="">
              <a href="javascript:;">
                <i class="fa fa-sign-in"></i>
                <span class="title">Absensi</span>
                <span class="arrow"></span>
              </a>
              <ul class="sub-menu">
                <li><a href="<?php echo base_url('absensi/kehadiran')?>">Kehadiran</a></li>
              </ul>
            </li>
             <li class="">
              <a href="<?php echo base_url('payroll')?>">
                <i class="fa fa-usd"></i>
                <span class="title">Payroll</span>
              </a>
            </li>
            <!-- END SELECTED LINK -->
          </ul>
          <!-- END SIDEBAR MENU -->
          <div class="clearfix"></div>
          <!-- END SIDEBAR WIDGETS -->
        </div>
      </div>
      <!-- BEGIN SCROLL UP HOVER -->
      <a href="#" class="scrollup">Scroll</a>
      <!-- END SCROLL UP HOVER -->
      <!-- END MENU -->
      <!-- END SIDEBAR -->