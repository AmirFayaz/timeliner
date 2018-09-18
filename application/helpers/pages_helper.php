<?php

if(!function_exists('show_footer_of_dashboard'))
{
    function show_footer_of_dashboard()
    {
        ?>
        <div class="row footer-fund">
                    
            <div class="col-xl-4 col-lg-4">
                <a href="#">
                    <i class="fa fa-balance-scale"></i>
                    شرایط و ضوابط
                </a>
            </div>

            <div class="col-xl-4 col-lg-4">
                <a href="#">
                    <i class="icon-directions"></i>
                    راهنما استفاده از اپلیکیشن
                </a>
            </div>

            <div class="col-xl-4 col-lg-4">
                <a href="#">
                    <i class="icon-book-open"></i>
                    اخبار و رویدادها
                </a>
            </div>

        </div>
        <?php
    }
}


if(!function_exists('page_make'))
{
    function page_make($address , $data=NULL)
    {
        $page['address'] = $address;
        $page['data'] = $data;
        return $page;
    }
}

if(!function_exists('load_view'))
{
    function load_view($pages=NULL , $lib_name = NULL)
    {
        $CI =& get_instance();
        
        $header =  'templates/header';
        $footer =  'templates/footer';

        $CI->load->view($header);

        $CI->load->view('templates/nav');

        $data['page'] = $lib_name;

        $error = TRUE;
        if($pages!== NULL  && is_array($pages))
        {
            foreach($pages as $page)
            {
                $data = isset($page['data']) ? $page['data'] : NULL;
                if( isset($page['address']) )
                {
                    $CI->load->view($page['address'],$data);
                    $error = FALSE;
                }
            }
        }
        if($error)
        {
            $CI->load->view('templates/error');
        }

        $CI->load->view($footer);
    }
}


function pr($param)
{
    echo '<pre>';
    print_r($param);
    echo '<pre>';
}