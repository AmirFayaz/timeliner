<?php

if(isset($page) && $page=='dashboard')
{
  ?><div class="herobg"></div><?php
}
?>

<!-- Horizontal navigation-->
<div class="header-navbar navbar-expand-sm navbar 
            navbar-horizontal navbar-fixed navbar-light 
            navbar-without-dd-arrow navbar-shadow menu-border"
      role="navigation" data-menu="menu-wrapper">
  <!-- Horizontal menu content-->
  <div class="part-right">
    <div class="navbar-container main-menu-content" data-menu="menu-container">
      <!-- include includes/mixins-->
      <ul class="nav navbar-nav" id="main-menu-navigation" data-menu="menu-navigation">
    
        <li class="nav-item">
          <a class="nav-link" href="<?php echo base_url();?>">
              <img src="<?php echo base_url();?>app-assets/images/icons/home.svg" 
                    alt="" style=" width: 40px; ">
            <span>صفحه اصلی</span>
          </a>
        </li>

        <li class="dropdown nav-item" data-menu="dropdown">
          <a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown">
              <img src="<?php echo base_url();?>app-assets/images/icons/fund-box.svg" 
                    alt="" style=" width: 40px; ">
            <span>صندوق ها</span>
          </a>
          <ul class="dropdown-menu">
            <li data-menu="">
              <a class="dropdown-item" href="#" data-toggle="dropdown">
                صندوق های بسته شده
                <submenu class="name"></submenu>
              </a>
            </li>
            <li data-menu="">
              <a class="dropdown-item" href="#" data-toggle="dropdown">
                صندوق های فعال
                <submenu class="name"></submenu>
              </a>
            </li>
            <li data-menu="">
              <a class="dropdown-item" href="#" data-toggle="dropdown">
                ایجاد صندوق جدید
                <submenu class="name"></submenu>
              </a>
            </li>
            <li data-menu="">
              <a class="dropdown-item" href="#" data-toggle="dropdown">
                جستجوی صندوق ها
                <submenu class="name"></submenu>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="#">
              <img src="<?php echo base_url();?>app-assets/images/icons/message.svg" 
                    alt="" style=" width: 40px; ">
            <span>پیام ها</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="#">
              <img src="<?php echo base_url();?>app-assets/images/icons/accounting.svg" 
                    alt="" style=" width: 40px; ">
            <span>حسابداری</span>
          </a>
        </li>

      </ul>

    </div>
  </div>

  <div class="part-left">
    <a href="2.dashboard.html">                
      <img class="brand-logo" alt="sandogh admin logo" 
          src="<?php echo base_url();?>app-assets/images/logo/sandogh-logo-lights.png">               
    </a>
  </div>
  <!-- /horizontal menu content-->
</div>
<!-- Horizontal navigation-->
