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
              <?php 
              ?>
              <div class="username"><?php echo $person_nm ?><span class="semi-bold"></span></div>
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
            <li <?php echo $active;?>>
              <a href="#">
                <i class="icon-custom-home"></i>
                <span class="title">Dashboard</span>
              </a>
              <ul class="sub-menu">
                <li <?php echo $active_1;?>><a href="<?php echo base_url('dashboard/index')?>">Attendance</a></li>
                <li <?php echo $active_2;?>><a href="<?php echo base_url('dashboard/index_slide')?>">Slide & no Slide</a></li>
                <li <?php echo $active_3;?>><a href="<?php echo base_url('dashboard/overtime')?>">Overtime</a></li>
              </ul>
            </li>
            <?php
            if($person_id == 1) {?>
            <!--<li <?php echo $active1;?>>
              <a href="<?php echo base_url('employee')?>">
                <i class="fa fa-user"></i>
                <span class="title">Employee</span>
              </a>
            </li>-->
            <li <?php echo $active2;?>>
              <a href="#">
                <i class="fa fa-sign-in"></i>
                <span class="title">Attendance</span>
                <span class="arrow"></span>
              </a>
              <ul class="sub-menu">
                <li <?php echo $active2_1;?>><a href="<?php echo base_url('attendance')?>"><i class="fa fa-list-ul"></i> List of Attendance</a></li>
                <li <?php echo $active2_2;?>><a href="<?php echo base_url('report/overtime')?>"><i class="fa fa-clock-o"></i> Overtime</a></li>
                <li <?php echo $active3;?>>
                  <a href="#">
                    <i class="fa fa-puzzle-piece"></i>
                    <span class="title">Config</span>
                    <span class="arrow"></span>
                  </a>
                  <ul class="sub-menu">
                    <li <?php echo $active3_1;?>><a href="<?php echo base_url('config')?>">Parameter</a></li>
                    <li <?php echo $active3_2;?>><a href="<?php echo base_url('config/holiday')?>">Holiday</a></li>
                  </ul>
                </li>
              </ul>
            </li>
            <li <?php echo $active3 ?>>
              <a href="javascript:;">
                <i class="fa fa-usd"></i>
                <span class="title">Payroll</span>
                <span class="arrow"></span>
              </a>
              <ul class="sub-menu">
                <li><a href="<?php echo base_url('payroll/payroll_master')?>"><i class="fa fa-database"></i> Master</a></li>
                <li><a href="<?php echo base_url('payroll/monthly_income')?>"><i class="fa fa-calendar"></i> Monthly Income</a></li>

                <li <?php echo $active3 ?>>
                  <a href="javascript:;">
                    <i class="fa fa-gear"></i>
                    <span class="title">Setup</span>
                    <span class="arrow"></span>
                  </a>
                  <ul class="sub-menu">
                    <li><a href="<?php echo base_url('payroll/payroll_component')?>">Component</a></li>
                    <li><a href="<?php echo base_url('payroll/payroll_config')?>">Config Payroll</a></li>
                    <li><a href="<?php echo base_url('payroll/payroll_config_tax')?>">Config Tax</a></li>
                    <li><a href="<?php echo base_url('payroll/payroll_setup')?>">Generate</a></li>
                    <li><a href="<?php echo base_url('payroll/payroll_group')?>">Group</a></li>
                  </ul>
                </li>
                <!--
                <li <?php echo $active3 ?>>
                  <a href="javascript:;">
                    <i class="fa fa-usd"></i>
                    <span class="title">Reference</span>
                    <span class="arrow"></span>
                  </a>
                  <ul class="sub-menu">
                    <li><a href="<?php echo base_url('payroll/payroll_tax_component')?>">Tax Component</a></li>
                    <li><a href="<?php echo base_url('payroll/payroll_ptkp')?>">Tax PTKP</a></li>
                    <li><a href="<?php echo base_url('payroll/payroll_tax_progressive')?>">Tax Progressive</a></li>
                    <li><a href="<?php echo base_url('payroll/payroll_tax_method')?>">Tax Method</a></li>
                    <li><a href="<?php echo base_url('payroll/job_value')?>">Job Value</a></li>
                    <li><a href="<?php echo base_url('payroll/payroll_umk')?>">UMK</a></li>
                  </ul>
                </li>
                -->
                <li <?php echo $active2?>>
                  <a href="javascript:;">
                    <i class="fa fa-file-text-o"></i>
                    <span class="title">Report</span>
                    <span class="arrow"></span>
                  </a>
                  <ul class="sub-menu">
                    <li><a href="<?php echo base_url('report/payroll_monthly_report')?>">Payroll Invoice</a></li>
                  </ul>
                </li>
                <!-- END SELECTED LINK -->
              </ul>
            </li>
            <?php } else {?>
            <li <?php echo $active2;?>>
              <a href="#">
                <i class="fa fa-sign-in"></i>
                <span class="title">Attendance</span>
                <!--<span class="arrow"></span>-->
              </a>
              <ul class="sub-menu">
                <li <?php echo $active2_2;?>><a href="<?php echo base_url('attendance_form/overtime')?>">Overtime</a></li>
              </ul>
            </li>
            <?php }?>
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