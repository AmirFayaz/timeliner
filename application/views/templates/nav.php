<nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-static-top navbar-dark bg-dark" data-nav="brand-center">
    <a class="text-large text-bold text-light" href="<?php echo base_url('');?>">
        تایم لایــنر
    </a>
    <a class="text-normal text-bold text-info mr-3" href="<?php echo base_url('dashboard');?>">
        داشبورد
    </a>
    
    <div class="navbar-conainer content mr-auto ml-0 text-light">


        <?php 
        $crnt_user = get_loggedin_user();
        if ($crnt_user) 
        { 
            ?>

                <?php echo $crnt_user['name']; ?>
                <a class="" href="<?php echo base_url('users/signout');?>"><i class="icon-power"></i> خروج</a>

        <?php } ?>

    </div>
</nav>

    <div class="container">
