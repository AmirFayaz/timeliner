<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('show_entrance_tab_signin'))
{
    function show_entrance_tab_signin()
    {
        ?>


        <form id="form_userLogin">

            <div class="form-row">

                <div class="invalid-feedback" id="form_userLogin_invalid">
                    <i class="fas fa-times"></i>
                    کاربری با این مشخصات پیدا نشد !
                    <br/>
                    ممکن است CAPSLOCK روشن باشد
                    <br/>
                    ممکن است کلمه عبور را به زبان فارسی وارد کردید
                </div>

                <div class="valid-feedback" id="form_userLogin_valid">
                    <i class="fas fa-check"></i>
                    اطلاعات صحیح است !
                    به داشبورد کاربر منتقل می شود
                </div>
                
            </div>

            <div class="form-row">

                <div class="col-md-4 mb-3">
                    <label for="input_userLoginMobile">شماره موبایل</label>
                    <input type="phone" class="form-control user-entrance" id="input_userLoginMobile" 
                    placeholder="۰۹۱۲۳۴۵۶۷۸۹" value="" required>
                </div>
            </div>

            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="input_userLoginPassword">رمز عبور</label>
                    <input type="password" class="form-control user-entrance" id="input_userLoginPassword" 
                    placeholder="" value="" required>
                    <div class="" id="input_userLoginPassword_validation">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <button href="dashboard" class="btn btn-info align-center col-md-4" type="submit">ورود</button>
            </div>

        </form>
        <?php
    }
}

if(!function_exists('get_loggedin_user'))
{
    function get_loggedin_user($key = null)
    {
        $CI =& get_instance();
        if($CI->session->has_userData('user_login'))
        {
            $user_login = $CI->session->userData('user_login');
            if($key!== null && isset($user_login[$key]))
            {
                return $user_login[$key];
            }
            return $user_login;
        }
        else{
            return NULL;
        }
    }
}