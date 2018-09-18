<?php
defined('BASEPATH') or exit('no direct script access allowed');

/*  ----------------------------  view  ----------------------------  */


/*  ----------------------------  model  ----------------------------  */


// model -> get stocks [pending]
if(!function_exists('get_pending_stocks_of_fund'))
{
    function get_pending_stocks_of_fund($fund_id)
    {
        $ci =& get_instance();
        $pending_stocks = $ci->stocks_model->get_pending_stocks_of_fund_id($fund_id);

        return $pending_stocks;
    }
}

// model -> get debts of user in fund
if(!function_exists('get_debts_of_user_in_fund'))
{
    function get_debts_of_user_in_fund($fund,$user_id,$lot_id = NULL)
    {
        $ci =& get_instance();
        if($lot_id) $lotteries[] = $ci->lottery_model->get_lottery_by_id($lot_id);
        else $lotteries = $fund['lotteries'];

        $report = [];
        $report['sum'] = 0;
        $report['debt_cnt'] = 0;
        $report['actions'] = [];

        foreach($lotteries as $i => $_lottery)
        {
            $lot_id = $_lottery['lot_id'];
            $debts[$lot_id]['action'] = $ci->accounting_model->get_user_dabts_in_lot($user_id,$lot_id,false);
            
            $sum = 0;
            foreach($debts[$lot_id]['action'] as $actions)
            {
                $sum += $actions['verify_status']=='-1' ? 0 : $actions['amount'];
            }
            $debts[$lot_id]['data'] = array(
                'action_id' =>  $debts[$lot_id]['action'][0]['action_id'],
                'user_id' =>  $debts[$lot_id]['action'][0]['user_id'],
                'lot_id' =>  $debts[$lot_id]['action'][0]['lot_id'],
                'fund_id' =>  $_lottery['fund_id'],
                'amount' =>  $debts[$lot_id]['action'][0]['amount'],
                'currency' =>  $fund['currency'],
                'loan_number' =>  $_lottery['loan_number'],
                'date' =>  $_lottery['date'],
                'sum' =>  $sum, 
            );

            $debts[$lot_id]['sum'] = $debts[$lot_id]['data']['sum'];
            if($debts[$lot_id]['sum'] < 0 )
            {
                $report['debt_cnt']++;
            }
            
            $report['sum'] += $debts[$lot_id]['sum'];
            
        }

        $report['actions'] = $lotteries ? $debts : NULL;

        return $report;

    }
}

/*  ----------------------------  forms  ----------------------------  */

// forms -> start fund
if( !function_exists('show_form_admin_start_fund'))
{
    function show_form_admin_start_fund($fund)
    {
        $status = $fund['status'];
        if(get_loggedin_user('user_id')===$fund['created_by_id'] && $status == '0')
        {
            ?>
            <button class="btn btn-sm btn-warning mt-2 mb-1 text-small text-medium"
                onclick="form_startFund(this)" data-fundid="<?php echo $fund_id;?>" >
                شروع بکار صندوق با سهام تایید شده تا الان
            </button>
            <?php
        }
    }
}

// forms -> change stocks [user] 
if(!function_exists('show_form_user_request_stocks'))
{
    function show_form_user_request_stocks($fundData,$stock_cnt)
    {
        $availablestocksNumber = $fundData['stocks_number'];
        $request_stock_label = $stock_cnt ? 'تغییر تعداد سهام' : 'درخواست سهام';
        $crnt_user_id = get_loggedin_user('user_id');
        ?>
        <form class="form form-horizontal" action="" id="form_requeststocks">
            <div class="form-body">

                <div class="form-group row m-0">                     
                    <div class="col-sm-6 col-lg-6 pl-0">
                        <div class="col-sm-6 col-lg-6 pl-0">
                            <div class="form-group">
                                <label for="input_requeststocks_stockscount" 
                                        class="">
                                    <?php echo $request_stock_label.' : ';?>
                                </label>
                                <input type="text" class="touchspin-vertical" value="" data-bts-min="0"
                                        id="input_requeststocks_stockscount" placeholder="تعداد سهام"/>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- <div class="col-sm-12 col-lg-3 pl-0"> -->
                <div class="form-group m-0">
                    <button type="submit" value="ثبت" id="form_submit_requeststocks"
                            class="btn btn-primary btn-sm m-0" ><i class="fa fa-check"></i>ثبت</button>
                </div>
            </div>

            <!-- hidden inputs -->
            <input  type="hidden" id="input_requeststocks_availablestocksNumber" 
                    value="<?php echo $availablestocksNumber ;?>">
            <input  type="hidden" id="input_requeststocks_crntuserid" 
                    value="<?php echo $crnt_user_id ;?>">
            <input  type="hidden" id="input_requeststocks_fundid" 
                    value="<?php echo $fundData['fund_id'] ;?>">

            <div class="form-row validation invalid-feedback" id="input_requeststocks_invalidnumber">
                <i class="fas fa-times"></i>
                تعداد سهام باید یک عدد صحیح مثبت باشد!
            </div>
            <div class="form-row validation invalid-feedback" id="input_requeststocks_illegalnumber">
                <i class="fas fa-times"></i>
                تعداد سهام از حد مجاز بیشتر است
                <br/>
                سهام مجاز = <?php echo $availablestocksNumber ;?>
            </div>
            <div class="form-row validation valid-feedback" id="input_requeststocks_validresponse">
                <i class="fas fa-check"></i>
                درخواست شما برای مدیر صندوق ارسال شد
            </div>

        </form>

    <?php
    }
}

// forms -> verify/deny/edit stocks [admin] 
if(!function_exists('show_form_admin_verify_stocks'))
{
    function show_form_admin_verify_stocks($stock,$fundData,$user)
    {

        $req_stock_cnt = $stock['total'];
        $is_verified = $stock['is_verified'];
        $crnt_user_id = get_loggedin_user('user_id');
        ?>
        <div class="block m-0">
            <btn type="form_submit_btn" id="form_verifyStocks_reject" value="-1"
                data-userid="<?php echo $user["user_id"]; ?>"
                class="btn btn-outline-danger pr-1 pl-1 pt-0 pb-0 mt-1 ml-1"
                onclick="form_submit_btn(this)"
                >
                رد
            </btn>
            <btn type="form_submit_btn" id="form_verifyStocks_verify" value="1"
                data-userid="<?php echo $user["user_id"]; ?>"
                class="btn btn-outline-success pr-1 pl-1 pt-0 pb-0 mt-1 ml-1"
                onclick="form_submit_btn(this)"
                >
                تایید
            </btn>
            <btn type="form_submit_btn" id="form_verifyStocks_edit" value="0"
                data-userid="<?php echo $user["user_id"]; ?>"
                class="btn btn-outline-secondary pr-1 pl-1 pt-0 pb-0 mt-1 ml-1"
                onclick="form_submit_btn(this)"
                >
                ویرایش
            </btn>


            <div class="hidden" id="div_verifyStocks_stockCountEdit" 
                data-userid="<?php echo $user["user_id"]; ?>">
                <label id="form_verifyStocks_edit_label" 
                        for="input_verifyStocks_stockCount" >تعداد سهام :‌</label>
                <input  type="number" id="input_verifyStocks_stockCountEdit" 
                        class="form-control col col-auto" data-userid="<?php echo $user["user_id"];?>"
                        value="<?php echo $req_stock_cnt ;?>">
            </div>

            <!-- hidden inputs -->
            <input  type="hidden" id="input_verifyStocks_currentStocksRequest" 
                    data-userid="<?php echo $user["user_id"];?>"
                    value="<?php echo $req_stock_cnt ;?>">
            <input  type="hidden" id="input_verifyStocks_isVerified" 
                    data-userid="<?php echo $user["user_id"];?>"
                    value="<?php echo $is_verified ;?>">
            <input  type="hidden" id="input_verifyStocks_crntUserId" 
                    data-userid="<?php echo $user["user_id"];?>"
                    value="<?php echo $crnt_user_id ;?>">
            <input  type="hidden" id="input_verifyStocks_fundId" 
                    data-userid="<?php echo $user["user_id"];?>"
                    value="<?php echo $fundData['fund_id'] ;?>">
            <input  type="hidden" id="input_verifyStocks_userId" 
                    data-userid="<?php echo $user["user_id"];?>"
                    value="<?php echo $user["user_id"];?>">


            <div class="form-row validation invalid-feedback" id="input_verifyStocks_invalidnumber"
                    data-userid="<?php echo $user["user_id"];?>">
                <i class="fas fa-times"></i>
                تعداد سهام باید یک عدد صحیح مثبت باشد!
            </div>
        </div>
        <?php
    }
}

//  forms -> pay debt [user]
if(!function_exists('show_form_paydebt'))
{
    function show_form_paydebt($member_accounting ,$fund , $collapse_id,$is_admin)
    {
        $installment = $member_accounting['installment'];
        $payment = $member_accounting['payment'];
        $member = $member_accounting['user_info'];
        ?>
        <ul class="card collapse-icon accordion-icon-rotate p-0 m-0">
            <div id="<?php echo $collapse_id; ?>"
                role="tabpanel" aria-labelledby="headingcollapse1" class="m-n collapse" style="">
                <div class="card-content">
                    <div class="card-body p-0">
                        <div class="min-width m-0 collapse-info">
                            <ul>
                                <li>
                                    <fieldset class="position-relative">
                                        <input  class="form-control" 
                                                type="text" 
                                                disabled
                                                value="<?php echo 'مبلغ : '. -$installment['amount'].' '.$fund['currency']; ?>"
                                                id="form_payDebt_amount_actionid<?php echo $installment['action_id'];?>" 
                                                data-form-id="<?php echo $collapse_id; ?>"
                                                />
                                        <div class="form-control-position">
                                        </div>
                                    </fieldset>
                                </li>

                                <li>
                                    <fieldset class="position-relative">
                                        <input type="text" class="form-control" 
                                                id="form_payDebt_trackingCode_actionid<?php echo $installment['action_id'];?>" 
                                                data-form-id="<?php echo $collapse_id; ?>"
                                                placeholder="شماره پیگیری"
                                                >
                                        <div class="invalid-feedback">
                                            وارد کردن شماره پیگیری الزامی است
                                        </div>
                                        <div class="form-control-position">
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                            <ul>
                                <li>
                                    <?php 
                                    show_pds( "form_payDebt_date_actionid".$installment['action_id'].'_'.$collapse_id , 'تاریخ پرداخت :‌ ' , NULL , FALSE);
                                    ?>
                                </li>
                            </ul>
                            <ul>
                                <li>
                                    <input  class="form-control" 
                                            type="text" 
                                            id="form_payDebt_comment_actionid<?php echo $installment['action_id'];?>" 
                                            data-form-id="<?php echo $collapse_id; ?>"
                                            placeholder="یادداشت واریز کننده" />
                                </li>
                            </ul>
                            <!-- <ul>
                                <li>
                                    <form action="#" class="dropzone drop-field dropzone-area" 
                                        id="form_payDebt_image_actionid<?php echo $installment['action_id'];?>" 
                                        data-form-id="<?php echo $collapse_id; ?>"
                                        >
                                        <div class="dz-message">بارگذاری تصویر فیش</div>
                                    </form>
                                </li>
                            </ul> -->
                            <ul>
                                <input class="btn btn-sm btn-outline-info"
                                        type="button"
                                        id="form_payDebt_submit_actionid<?php echo $installment['action_id'];?>" 
                                        data-form-id="<?php echo $collapse_id; ?>"
                                        data-debt='<?php echo json_encode($installment);?>'
                                        onclick="form_payDebt(this)"
                                        value="ثبت اطلاعات پرداخت"/>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </ul>
        <?php
    }
}

/*  ----------------------------  dashboard  ----------------------------  */

// dashboard -> [box] create fund
if(!function_exists('show_create_fund_box'))
{
    function show_create_fund_box()
    {
        ?>
        
        <div class="row create-fund fl-row">

            <div class="col-xl-6 col-lg-12 fl-l"> 
                <img class="img-responsive center-block visible-lg-block" src="<?php echo base_url();?>app-assets/images/svg/create-fund.svg" alt="tribe cropped">
                <div class="description-area dark-text">
                    <p>
                    با بررسی صندوق ها و سپس انتخاب صندوق مورد نظر می توانید اقدام به عضویت در صندوق مربوطه را بنمایید. امکان انصراف از عضویت در هر زمان پیش از تاریخ قرعه کشی فراهم می باشد. همچنین می توانید سهام خود را کاهش و یا افزایش دهید.
                    </p>
                    </div>
            </div>

        <div class="col-xl-6 col-lg-12 fl-r"> 

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">ایجاد صندوق جدید
                    <small class="block">یک صندوق جدید بسازید و عضوگیری کنید</small>
                    </h4>
                    </div>
                <div class="card-content collapse show">
                    <div class="card-body">

                        <form class="form form-horizontal" id="form_createFund" name="form_createFund">
                        <div class="form-row col col-md-12 text-bold">

                            <div class="invalid-feedback text-small" id="form_createFund_invalid">
                                <i class="fas fa-times"></i>
                                مشکلی به وجود آمده !
                                <br/>
                                لطفا ورودی های خود را کنترل کنید و دوباره تلاش کنید
                            </div>

                            <div class="valid-feedback text-small" id="form_createFund_valid">
                                <i class="fas fa-check"></i>
                                تبریک ! صندوق شما ساخته شد.
                            </div>

                        </div>
                            <div class="form-body">
                            <div class="form-group row m-0">                     
                                <div class="col-sm-12 col-lg-4 pl-0">
                                    <div class="form-group">
                                    <input type="text" class="form-control" 
                                            id="input_createFund_fundName" placeholder="نام صندوق">
                                    </div>
                                </div>

                                    <div class="col-sm-12 col-lg-3 pl-0">
                                        <div class="form-group">
                                            <input type="text" class="form-control" 
                                                    id="input_createFund_installment" placeholder="مبلغ قسط هر دوره"  />
                                        </div>
                                    </div>

                                    <div class="col-sm-12 col-lg-2 pl-0">
                                        <div class="form-group">
                                            <select class="select2-rtl form-control" id="input_createFund_currency">
                                                <optgroup label="واحد پول">
                                                    <option value="ریال">ریال</option>
                                                    <option value="تومان">تومان</option>
                                                    <option value="دلار">دلار</option>
                                                    <option value="پوند">پوند</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-sm-12 col-lg-3 pl-0">
                                        <div class="form-group">
                                            <input type="text" class="touchspin-vertical" value="" data-bts-min="1" data-bts-max="999" 
                                                    id="input_createFund_stocksNumber" placeholder="تعداد سهام = تعداد دوره قرعه کشی"/>
                                        </div>
                                    </div>

                                    <div class="col-sm-12 col-lg-3 pl-0">
                                        <div class="form-group m-0">
                                        <button type="submit" value="ثبت صندوق" id="form_submit_createFund"
                                                    class="btn btn-primary btn-sm m-0" ><i class="fa fa-check"></i> ایجاد صندوق</button>
                                        </div>
                                    </div>

                                    <input  type="hidden" id="input_createFund_createdById" value="<?php echo get_loggedin_user('user_id'); ?>">

                                </div>                            
                            </div>
                            </form>
                    </div>
                </div>
                </div>

            </div>

        </div>

        <!--                **********************************                   -->
        <?php return; ?>
        <div class="shadow-box col col-md-12">

            <span class="text-success col col-md-12 text-bold text-small">
                یک صندوق جدید بسازید و عضو گیری کنید
            </span>
            <form id="form_createFund" name="form_createFund" class="">

                <div class="form-row col col-md-12 text-bold">

                    <div class="invalid-feedback text-small" id="form_createFund_invalid">
                        <i class="fas fa-times"></i>
                        مشکلی به وجود آمده !
                        <br/>
                        لطفا ورودی های خود را کنترل کنید و دوباره تلاش کنید
                    </div>

                    <div class="valid-feedback text-small" id="form_createFund_valid">
                        <i class="fas fa-check"></i>
                        تبریک ! صندوق شما ساخته شد.
                    </div>

                </div>

                <div class="form-row col col-md-12">
                    <input  type="text" id="input_createFund_fundName" placeholder="نام صندوق" 
                            class="form-control validate-data create-fund col col-md-11 mb-1 mr-1  text-medium text-small" >
                    <div class="validation invalid-feedback" id="input_createFund_fundName_invalid">
                        <i class="fas fa-times"></i>
                        نام حداقل باید سه حرفی باشد
                    </div>
                    <div class="validation valid-feedback" id="input_createFund_fundName_valid">
                        <i class="fas fa-check"></i>
                        نام مناسب است!
                    </div>
                </div>

                <div class="form-row col col-md-12 text-small text-medium">

                    <div class="input-group mb-1 ltr">
                        <select class="custom-select text-small text-medium" id="input_createFund_currency">
                            <option value="ریال" selected>ریال</option>
                            <option value="تومان">تومان</option>
                            <option value="دلار">دلار</option>
                        </select>
                        <div class="input-group-append">
                            <label id="inputlabel_createFund_currency" class="input-group-text text-small text-medium pt-0 pb-0"
                                    for="inputgroupselect02">واحد پولی</label>
                        </div>
                    </div>

                </div>

                <div class="form-row col col-md-12">

                    <input  type="number" id="input_createFund_stocksNumber" placeholder="تعداد سهام = تعداد دوره قرعه کشی" 
                            class="form-control validate-data create-fund col col-md-6 mb-1 mr-1  text-medium text-small" >
                    <div class="validation invalid-feedback" id="input_createFund_stocksNumber_invalid">
                        <i class="fas fa-times"></i>
                        تعداد سهام باید یک عدد صحیح باشد!
                    </div>
                    <div class="validation valid-feedback" id="input_createFund_stocksNumber_valid">
                        <i class="fas fa-check"></i>
                        تعداد سهام مناسب است!
                    </div>

                    <input  type="number" id="input_createFund_installment" placeholder="مبلغ قسط هر دوره" 
                            class="form-control validate-data create-fund ol col-md-5 mb-1 mr-1  text-medium text-small" >
                    <div class="validation invalid-feedback" id="input_createFund_installment_invalid">
                        <i class="fas fa-times"></i>
                        مبلغ قسط باید یک عدد صحیح باشد!
                    </div>
                    <div class="validation valid-feedback" id="input_createFund_installment_valid">
                        <i class="fas fa-check"></i>
                        مبلغ قسط مناسب است!
                    </div>

                </div>

                <div id="calculateLoanAmount" 
                    class="create-fund form-row col col-md-12 text-success mb-1 mr-1">

                    <div class="text-small text-medium">
                        مبلغ وام :‌
                    </div>

                    <div class="text-small text-medium">
                        
                    </div>

                </div>

                <input  type="hidden" id="input_createFund_createdById" value="<?php echo get_loggedin_user('user_id'); ?>">

                <div class="form-row col col-md-12">

                    <input  type="submit" value="ثبت صندوق" id="form_submit_createFund"
                            class="btn btn-sm btn-success align-center pl-3 pr-3 col col-auto 
                            text-bold text-small margin-auto" >

                </div>
            </form>
        </div>
        <?php
    }
}

// dashboard -> [box] join a fund
if(!function_exists('show_join_fund_box'))
{
    function show_join_fund_box()
    {
        // is-valid         is-invalid
        // valid-feedback   invalid-feedback

        ?>
        
        <div class="row search-fund">

            <div class="col-xl-6 col-lg-12">
                <img class="img-responsive center-block visible-lg-block" 
                        src="<?php echo base_url();?>app-assets/images/svg/search-fund.svg" alt="tribe cropped">
                <div class="description-area dark-text">
                    <p>
                      با بررسی صندوق ها و سپس انتخاب صندوق مورد نظر می توانید اقدام به عضویت در صندوق مربوطه را بنمایید. امکان انصراف از عضویت در هر زمان پیش از تاریخ قرعه کشی فراهم می باشد. همچنین می توانید سهام خود را کاهش و یا افزایش دهید.
                    </p>
                    </div>
              </div>

              <div class="col-xl-6 col-lg-12 d-flex align-items-center">
                  <div class="card w-100">
                    <div class="card-header">
                      <h4 class="card-title">جستجو 
                        <small class="block">جستجو برای صندوق در حال عضوگیری</small>
                      </h4>
                      
                    </div>
                    <div class="card-content collapse show">
                      <div class="card-body">

                      <form id="form_searchFunds" name="form_searchFunds" class="">
                          <fieldset>
                              <div class="input-group">
                                  <input type="text" class="form-control" id="input_searchFunds_adminMobile" 
                                        placeholder="شماره موبایل مدیر صندوق" 
                                        aria-describedby="button-addon2" required oninvalid="this.setCustomValidity('لطفا شماره موبایل خود را وارد کنید')" oninput="setCustomValidity('')">
                                  
                                <div class="input-group-append" id="button-addon2">
                                  <button class="btn btn-primary" type="button"><i class="ft-search"></i></button>
                                </div>
                              </div>
                            </fieldset>
                        </form>

                        <div class="validation invalid-feedback" id="input_searchFunds_resultNotFound">
                            <i class="fas fa-times"></i>
                            برای این شماره هیچ صندوقی ثبت نشده است !
                        </div>
                        <div class="form-row col col-md-12 mb-1 fund-search-result" id="input_searchFunds_resultList">
                            نتایج جستجو :
                            <ul class="list-search-result m-1 p-1">
                            </ul>
                        </div>

                      </div>
                    </div>
                  </div>
                </div>

          </div>

        <!--                **********************************                   -->

        <?php return; ?>
        <!-- 
        <div class="shadow-box col col-md-12">
            <span class="text-danger col col-md-12 text-bold text-small">
                جست و جو برای صندوق در حال عضوگیری
            </span>
            <div>
                <form id="form_searchFunds" name="form_searchFunds" class="">
                    <div class="form-row col col-md-12 mb-1">
                        <input  type="text" id="input_searchFunds_adminmobile" placeholder="شماره موبایل مدیر صندوق" 
                                class="form-control join-fund  text-medium text-small" id="">
                        <div class="validation invalid-feedback" id="input_searchFunds_adminmobile_invalid">
                            <i class="fas fa-times"></i>
                            شماره تلفن به درستی وارد نشده است !
                        </div>
                        <div class="validation invalid-feedback" id="input_searchFunds_adminmobile_dberror">
                            <i class="fas fa-times"></i>
                            این شماره در سیستم ثبت نشده است !
                        </div>
                    </div>

                    <div class="form-row col col-md-12 mb-1">

                        <input  type="submit" value="بگــرد !"
                                class="btn btn-sm btn-danger align-center pl-3 pr-3 col col-auto  text-bold text-small margin-auto" >
                    </div>
                </form>
                <div class="validation invalid-feedback" id="input_searchFunds_resultnotfound">
                    <i class="fas fa-times"></i>
                      برای این شماره هیچ صندوقی ثبت نشده است !
                </div>
                <div class="form-row col col-md-12 mb-1 fund-search-result" id="input_searchFunds_resultlist">
                    نتایج جستجو :
                    <ul class="list-search-result m-1 p-1">
                    </ul>
                </div>
            </div>
        </div> -->
        <?php
    }
}

// dashboard -> [box] join a fund -> [list item] funds
if(!function_exists('show_search_funds_list_items'))
{
    function show_search_funds_list_items($fund)
    {
        extract($fund);
        $a_class = $status == 0 ? 'bg-warning text-dark' : 'bg-success text-light';
        return 
        "<div>
            <a class='block card-title $a_class' href=".base_url()."funds/".$fund_id.">
                <span class='inline-block text-small text-medium' >
                    صندوق : $name 
                </span>
                <span class='inline-block text-xsmall' >[کد $fund_id ]</span>
                
            </a>
            <span class='block text-xsmall text-medium' >تاریخ تاسیس :". persian_date($created_at/1000) ."</span>
            <span class='block text-xsmall text-medium' >مقدار قسط : ". number_format($installment).' '.$currency."</span>
            <span class='block text-xsmall text-medium' >تعداد کل سهام : ". number_format($stocks_number) ."</span>
            <span class='block text-xsmall text-medium' >وضعیت : $status </span>
        </div>";
    }
}

// dashboard -> [carousel] funds pending related to user
if(!function_exists('show_fund_pending_related_to_user'))
{
    function show_fund_pending_related_to_user($user_id,$funds)
    {
        $funds_count = is_array($funds) ? sizeof($funds) : 0;
        
        if($funds_count == 0)
        {
            return false;
        }

        ?>

        <div class="row active-fund fl-row">

            <div class="col-xl-6 col-lg-12 fl-l"> 
                <img class="img-responsive center-block visible-lg-block" src="<?php echo base_url();?>app-assets/images/svg/fund-member.svg" alt="tribe cropped">
                <div class="description-area">
                    <p>
                    با بررسی صندوق ها و سپس انتخاب صندوق مورد نظر می توانید اقدام به عضویت در صندوق مربوطه را بنمایید. امکان انصراف از عضویت در هر زمان پیش از تاریخ قرعه کشی فراهم می باشد. همچنین می توانید سهام خود را کاهش و یا افزایش دهید.
                    </p>
                </div>
            </div>

            <!-- fund list item in carousel -->

            <div class="col-xl-6 col-lg-12 fl-r">
                <div class="card">

                    <div class="card-header header-icon">
                        <img src="<?php echo base_url();?>app-assets/images/icons/open-box.png" alt="avatar">
                        <h4 class="card-title">صندوق های در حال عضوگیری</h4>
                        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-content collapse show">

                        <div class="card-body fund-info">

                            <div id="carousel-pending-funds" class="carousel slide" data-ride="carousel">


                                <ol class="carousel-indicators">
                                    <?php
                                    for($i = 0 ; $i<$funds_count ; $i++)
                                    {
                                        ?>
                                        <li data-target="#carousel-pending-funds" data-slide-to="<?php echo $i; ?>" class="<?php echo $i!=0 ?:'active';?>"></li>
                                        <?php
                                    }
                                    ?>
                                </ol>

                                <div class="carousel-inner" role="listbox">
                                    <?php
                                    for($i = 0 ; $i<$funds_count ; $i++)
                                    {
                                        ?>
                                        <div class="carousel-item <?php echo $i!=0 ?:'active';?> ">
                                            <?php
                                            show_fund_pending_items($user_id,$funds[$i],$i);
                                            ?>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                
                                <?php
                                if($funds_count>1)
                                {
                                    ?>
                                    <a class="carousel-control-prev rotate-180" href="#carousel-pending-funds" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">قبلی</span>
                                    </a>

                                    <a class="carousel-control-next rotate-180" href="#carousel-pending-funds" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">بعدی</span>
                                    </a>
                                    <?php
                                }
                                ?>

                            </div>

                        </div>

                    </div>

                    <div class="card-footer text-muted">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="badge badge-success">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                        <div class="icon-button align-self-center">
                                        </div>
                                        <span>  تمام صندوق ها 
                                            (<?php echo $funds_count; ?>)
                                        </span>
                                    </a>
                                    <div class="dropdown-menu">
                                        <?php
                                        for($i = 0 ; $i<$funds_count ; $i++)
                                        {
                                        ?>
                                        
                                        <a class="dropdown-item" href="<?php echo base_url();?>funds/<?php echo $funds[$i]['fund_id'];?>">
                                            <?php echo $funds[$i]['name'];?>
                                        </a>

                                        <?php
                                        }
                                        ?>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="<?php echo base_url();?>#">مشاهده تمام صندوق ها</a>
                                    </div>

                                </div>
                            </div>

                            <div class="col-lg-8 col-md-6 col-sm-12">
                                <span class="float-right">به روز رسانی: ۱۲:۰۲:۱۰ - ۱۳۹۷/۰۴/۱۹<i class="icon-arrow-right4"></i></span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        
        </div>
        <?php

        return true;


    }
}

// dashboard -> [carousel] funds pending related to user -> [box] fund info
if(!function_exists('show_fund_pending_items'))
{
    function show_fund_pending_items($user_id,$fund,$index=null)
    {
        $user_stocks_cnt = 0;
        $fund_stocks_cnt = 0;
        ?>
        <div class="fund-box-info">
            <div class="row">
                <div class="col-lg-12 card-box-title">
                    <div class="media align-items-stretch">
                        <?php
                        if($index!==null)
                        {
                            ?>
                            <div class="icon-title align-self-center">
                                <span>
                                <?php echo $index+1; ?>
                                </span>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="media-body">
                            <h4>
                                <?php echo $fund['name'].' ['.$fund['fund_id'].']'; ?>
                            </h4>
                            <span>افتتاح: <?php echo persian_date($fund['created_at']/1000, true);?> </span>
                        </div>
                        <div class="align-self-center loan-amound">
                            <span>مبلغ وام</span>
                            <h1><?php echo number_format($fund['installment']*$fund['stocks_number']).' '.$fund['currency'];?></h1>
                        </div>
                    </div>      
                </div>
            </div>

            <div class="card-box-body">

                <div class="min-width">
                    <ul>
                        <li>
                            سهام شما: 
                            <?php echo $user_stocks_cnt.' سهم';?>
                        </li>
                        <li>
                            اقساط دوره ای شما: 
                            <?php echo number_format($user_stocks_cnt*$fund['stocks_number']).' '.$fund['currency'];?>
                        </li>
                    </ul>
                    <ul>
                        <li>
                            <?php echo $fund['stocks_number'].' سهم';?>
                        </li>
                        <li>
                            مبلغ هر سهم: 
                            <?php echo number_format($fund['installment']).' '.$fund['currency'];?>
                        </li>
                        <li>
                            سهام تایید شده: 
                            <?php echo $fund_stocks_cnt.' سهم';?>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="read-more-btn">
                <a href="<?php echo base_url().'funds/'.$fund['fund_id']; ?>" 
                    class="btn btn-social mb-1 mr-1 btn-outline-primary">
                    <span class="icon-question font-medium-4"></span> مشاهده جزئیات
                </a>
                <ul data-toggle="modal" data-target="#default" class="p-0 modal-view-btn">
                    <a href="#" class="btn btn-social mb-1 mr-1 btn-outline-primary">
                        <span class="fa fa-pencil font-medium-4"></span>ویرایش سهام
                    </a>
                </ul>
            </div>
        </div>
        <?php
    }
}

// dashboard -> [carousel] funds working related to user
if(!function_exists('show_fund_working_related_to_user'))
{
    function show_fund_working_related_to_user($user_id , $funds)
    {
        $funds_count = is_array($funds) ? sizeof($funds) : 0;
        if($funds_count == 0)
        {
            return false;
        }

        ?>

        <div class="row close-fund">

            <div class="col-xl-6 col-lg-12">

                <img class="img-responsive center-block visible-lg-block" src="<?php echo base_url();?>app-assets/images/svg/fund.svg" alt="tribe cropped">
                <div class="description-area dark-text">
                    <p>
                    با بررسی صندوق ها و سپس انتخاب صندوق مورد نظر می توانید اقدام به عضویت در صندوق مربوطه را بنمایید. امکان انصراف از عضویت در هر زمان پیش از تاریخ قرعه کشی فراهم می باشد. همچنین می توانید سهام خود را کاهش و یا افزایش دهید.
                    </p>
                </div>
            </div>




            <div class="col-xl-6 col-lg-12">
                <div class="card">
                    <div class="card-header header-icon">
                        <img src="<?php echo base_url();?>app-assets/images/icons/closed-box.png" alt="avatar">
                        <h4 class="card-title">صندوق های بسته شده  </h4>
                        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content collapse show">

                        <div class="card-body fund-info">

                            <div id="carousel-working-funds" class="carousel slide" data-ride="carousel">
                                <ol class="carousel-indicators">
                                    <?php
                                    for($i = 0 ; $i<$funds_count ; $i++)
                                    {
                                        ?>
                                        <li data-target="#carousel-pending-funds" data-slide-to="<?php echo $i; ?>" class="<?php echo $i!=0 ?:'active';?>"></li>
                                        <?php
                                    }
                                    ?>
                                </ol>

                                <div class="carousel-inner" role="listbox">
                                    <?php
                                    for($i = 0 ; $i<$funds_count ; $i++)
                                    {
                                        ?>
                                        <div class="carousel-item <?php echo $i!=0 ?:'active';?> ">
                                            <?php show_fund_working_items($user_id,$funds[$i],$i); ?>
                                        </div>
                                    <?php
                                    }
                                        ?>
                                </div>

                                <?php
                                if($funds_count>1)
                                {
                                    ?>
                                    <a class="carousel-control-prev rotate-180" href="#carousel-working-funds" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">قبلی</span>
                                    </a>
                                    <a class="carousel-control-next rotate-180" href="#carousel-working-funds" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">بعدی</span>
                                    </a>
                                <?php
                                }
                                ?>

                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-muted">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="badge badge-success">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                        <div class="icon-button align-self-center">
                                        </div>
                                        <span>  تمام صندوق ها 
                                            (<?php echo $funds_count; ?>)
                                        </span>
                                    </a>
                                    <div class="dropdown-menu">
                                        <?php
                                        for($i = 0 ; $i<$funds_count ; $i++)
                                        {
                                        ?>
                                        
                                        <a class="dropdown-item" href="<?php echo base_url();?>funds/<?php echo $funds[$i]['fund_id'];?>">
                                            <?php echo $funds[$i]['name'];?>
                                        </a>

                                        <?php
                                        }
                                        ?>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="#">مشاهده تمام صندوق ها</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8 col-md-6 col-sm-12">
                                <span class="float-right">به روز رسانی: ۱۲:۰۲:۱۰ - ۱۳۹۷/۰۴/۱۹<i class="icon-arrow-right4"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return true;
    }
}

// dashboard -> [carousel] funds pending related to user -> [box] fund info
if(!function_exists('show_fund_working_items'))
{
    function show_fund_working_items($user_id,$fund,$index=null)
    {
        $user_stocks_cnt = 0;
        $fund_stocks_cnt = 0;
        $user_debt_amount = 0;
        $lots_done_cnt = 0;
        $last_lot_winner = '';
        $next_lot_payment_cnt = 0;
        $next_lot_date = persian_date(0 , true);
        
        ?>
    <div class="fund-box-info">

        <div class="row">
            <div class="col-lg-12 card-box-title">
                <div class="media align-items-stretch">
                    <?php
                    if($index!==null)
                    {
                        ?>
                        <div class="icon-title align-self-center">
                            <span>
                            <?php echo $index+1; ?>
                            </span>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="media-body">
                        <h4>
                            <?php echo $fund['name'].' ['.$fund['fund_id'].']'; ?>
                        </h4>
                        <span>افتتاح: <?php echo persian_date($fund['created_at']/1000, true);?> </span>
                    </div>
                    <div class="align-self-center loan-amound">
                        <span>مبلغ وام</span>
                        <h1><?php echo number_format($fund['installment']*$fund['stocks_number']).' '.$fund['currency'];?></h1>
                    </div>
                </div>      
            </div>
        </div>

        <div class="card-box-body">
            <div class="min-width">
            <ul>
                <li>
                    <?php echo 'قرعه کشی بعدی '.$next_lot_date; ?>
                </li>
                <li>
                    <?php echo $next_lot_payment_cnt.' واریز';?>
                </li>
            </ul>
            
            <ul>
                <li>
                    <span class="danger">
                    بدهی شما:
                    </span>
                    <?php echo $user_debt_amount.' '.$fund['currency'];?>
                </li>
                <li>
                    سهام شما: 
                    <?php echo $user_stocks_cnt.' سهم';?>
                </li>
            </ul>
            
            <ul>
                <li>
                    <?php echo $fund['stocks_number'].' سهم';?>
                </li>
                
                <li>
                    <?php echo $lots_done_cnt ? $lots_done_cnt.' قرعه کشی انجام شده' : 'هنوز قرعه کشی انجام نشده';?>
                </li>
                <?php
                if($lots_done_cnt)
                {
                    ?>
                    <li>
                        <?php echo $last_lot_winner;?>
                    </li>
                    <?php
                }
                ?>
            </ul>
            </div>
        </div>
        <div class="read-more-btn">
            <a href="<?php echo base_url().'funds/'.$fund['fund_id']; ?>" class="btn btn-social mb-1 mr-1 btn-outline-primary">
                <span class="icon-question font-medium-4"></span> مشاهده جزئیات</a>
        </div>
    </div>

    <!-- ///////////////////// -->
    <?php return; ?>
        <div class="fund-box-info">
            <div class="row">
                <div class="col-lg-12 card-box-title">
                    <div class="media align-items-stretch">
                        <div class="media-body">
                            <h4>
                                <?php echo $fund['name'].' ['.$fund['fund_id'].']'; ?>
                            </h4>
                            <span>افتتاح: <?php echo persian_date($fund['created_at']/1000, true);?> </span>
                        </div>
                        <div class="align-self-center loan-amound">
                            <span>مبلغ وام</span>
                            <h1><?php echo number_format($fund['installment']*$fund['stocks_number']).' '.$fund['currency'];?></h1>
                        </div>
                    </div>      
                </div>
            </div>

            <div class="card-box-body">

                <div class="min-width">
                    <ul>
                        <li>
                            سهام شما: 
                            <?php echo $user_stocks_cnt.' سهم';?>
                        </li>
                        <li>
                            اقساط دوره ای شما: 
                            <?php echo number_format($user_stocks_cnt*$fund['stocks_number']).' '.$fund['currency'];?>
                        </li>
                    </ul>
                    <ul>
                        <li>
                            <?php echo $fund['stocks_number'].' سهم';?>
                        </li>
                        <li>
                            مبلغ هر سهم: 
                            <?php echo number_format($fund['installment']).' '.$fund['currency'];?>
                        </li>
                        <li>
                            سهام تایید شده: 
                            <?php echo $fund_stocks_cnt.' سهم';?>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="read-more-btn">
                <a href="#" class="btn btn-social mb-1 mr-1 btn-outline-primary">
                    <span class="icon-question font-medium-4"></span> مشاهده جزئیات
                </a>
                <ul data-toggle="modal" data-target="#default" class="p-0 modal-view-btn">
                    <a href="#" class="btn btn-social mb-1 mr-1 btn-outline-primary">
                        <span class="fa fa-pencil font-medium-4"></span>ویرایش سهام
                    </a>
                </ul>
            </div>
        </div>
        <?php
    }
}

/*  ----------------------------  fund panel  ----------------------------  */

// fund panel [tabs]
if(!function_exists('show_fund_panel'))
{
    function show_fund_panel($fundData)
    {
        if(is_array($fundData))
        {extract($fundData);}

        $CI =& get_instance();
        $crnt_user_id = get_loggedin_user('user_id');
        $lotteries = $fundData['lotteries'];
        $crnt_user_debts = get_debts_of_user_in_fund($fundData ,$crnt_user_id);
        // $accounting = $CI->accounting_model->get_accounting_of_fund($fund_id);
        $next_lot = NULL;
        foreach($lotteries as $i => $lot)
        {
            $members_accounting = $CI->accounting_model->get_accounting_in_lot($lot['lot_id']);
            $lotteries[$i]['members_accounting'] = $members_accounting;

            if($lot['loan_number'] == $fundData['status'])
            {
                $next_lot = $lotteries[$i];
            }
        }

        $fundData['is_admin'] = is_admin_of_fund($fundData['fund_id']);
        // pr($fundData);
        echo $fundData['name'];
        ?>

        <ul class="nav nav-tabs nav-linetriangle no-hover-bg">
            <li class="nav-item">
                <a class="nav-link active" id="base-tab41" data-toggle="tab" aria-controls="tab41"
                href="#tab41" aria-expanded="true"><span>اطلاعات صندوق</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" id="base-tab42" data-toggle="tab" aria-controls="tab42" href="#tab42"
                aria-expanded="false"><span> اعضا </span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" id="base-tab43" data-toggle="tab" aria-controls="tab43" href="#tab43"
                aria-expanded="false"><span>تاریخچه</span></a>
            </li>
            
            <li class="nav-item dropdown" >
                <a class="nav-link dropdown-toggle" id="nav-link-tab" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="true">
                    <span>      قرعه کشی</span>
                </a>
                
                <div class="dropdown-menu">
                    <a class="dropdown-item" id="dropdown1-tab" href="#dropdown1" data-toggle="tab" aria-controls="dropdown1"
                        aria-expanded="true">واریزی های دوره</a>
                    <a class="dropdown-item" id="dropdown2-tab" href="#dropdown2" data-toggle="tab" aria-controls="dropdown2"
                        aria-expanded="true">انجام قرعه کشی</a>
                    <a class="dropdown-item" id="dropdown3-tab" href="#dropdown3" data-toggle="tab" aria-controls="dropdown3"
                        aria-expanded="true">ثبت قرعه کشی بعدی</a>
                </div>
            </li>
        </ul>
        
        <div class="tab-content pt-1">
            <?php
            show_tab_fund_info($fundData , $crnt_user_debts);
            show_tab_fund_members($fundData);
            show_tab_fund_history($fundData,$lotteries);
            show_tab_fund_next_lottery($fundData,$next_lot);
            ?>
        </div>
        <?php
    }
}

// fund panel -> [tab] fund information
if(!function_exists('show_tab_fund_info'))
{
    function show_tab_fund_info($fundData , $crnt_user_debts)
    {
        $ci =& get_instance();

        $fund_created_at = persian_date($fundData['created_at']/1000,true);
        $loan_amount_text = number_format($fundData['installment'] * $fundData['stocks_number']) .' '. $fundData['currency'];
        $installment_amount_text = number_format($fundData['installment']) .' '. $fundData['currency'];
        
        $admin['name'] = $fundData['admin_name'];
        $admin['user_id'] = $fundData['created_by_id'];
        $admin['mobile'] = $fundData['admin_mobile'];
        
        $lotteries = $fundData['lotteries'];
        
        $crnt_user = get_loggedin_user();
        $crnt_user_id = $crnt_user['user_id'];
        $crnt_user_stocks = $ci->stocks_model->get_stocks_of_user_of_fund($crnt_user_id,$fundData['fund_id']);
        $crnt_user_stocks_cnt = sizeof($crnt_user_stocks);
        $crnt_user_stocks_is_verified = isset($crnt_user_stocks[0]) ?? $crnt_user_stocks[0]['is_verified'];
        $verified_stocks_cnt = sizeof($fundData['verified_stocks']);

    
    
        $last_lot_winner = 0;
        $last_lot_winner_text = null;

        if($fundData['status']>1)
        {
            $pending = false;
            $status_text = 'بسته شده';
            $lot_cnt_text = $fundData['status']-1 .' دوره قرعه کشی';

            $last_lot = $lotteries[$fundData['status']-2];
            // $last_lot_winner = 
            $last_lot_winner_text = 'آخرین برنده : '.$last_lot['winner_name'];
        }
        elseif($fundData['status']==1)
        {
            $pending = false;
            $status_text = 'بسته شده';
            $lot_cnt_text = 'هنوز قرعه کشی انجام نشده';
        }
        elseif($fundData['status']==0)
        {
            $pending = true;
            $status_text = 'در حال عضو گیری';
            $lot_cnt_text = 'هنوز قرعه کشی انجام نشده';
        }
        else
        {
            $pending = false;
            $status_text = 'تعظیل';
            $lot_cnt_text = $fundData['stocks_number'] .' دوره قرعه کشی';
            $last_lot = $lotteries[$fundData['stocks_number']-1];
            // $last_lot_winner = 
            $last_lot_winner_text = 'آخرین برنده : '.$last_lot['winner_stock_id'];
        }

        $next_lot = $fundData['status'] ? $lotteries[$fundData['status']-1] : NULL;
        if($next_lot)
        {
            $next_lot_date = persian_date($next_lot['date'],true);
            $next_lot_days_from_today =  days_to_today($next_lot['date']) ;
            $next_lot_text = '<b>'.$next_lot_date.'</b>'.' ('.$next_lot_days_from_today['text'].')';
        }
        else
        {
            $next_lot_text = '<b>هنوز ثبت نشده است</b>';
        }
        ?>

        <div role="tabpanel" class="tab-pane active" id="tab41" aria-expanded="true" aria-labelledby="base-tab41">
            <div class="row">
                <div class="col-sm-12 col-md-6 col-lg-6">
                    <img class="img-responsive center-block visible-lg-block" src="<?php echo base_url(); ?>app-assets/images/svg/fund-info.svg" alt="tribe cropped">
                </div>
                <div class="col-sm-12 col-md-6 col-lg-6 d-flex align-items-center">
                    <div class="description-area dark-text">
                    <p>
                        با بررسی صندوق ها و سپس انتخاب صندوق مورد نظر می توانید اقدام به عضویت در صندوق مربوطه را بنمایید. امکان انصراف از عضویت در هر زمان پیش از تاریخ قرعه کشی فراهم می باشد. همچنین می توانید سهام خود را کاهش و یا افزایش دهید.
                    </p>
                    </div>
                </div>
            </div>

            <div class="min-width colour-icon">
                <ul>
                    <li>افتتاح: <?php echo $fund_created_at;?></li>
                    <li><?php echo $status_text; ?></li>
                </ul>

                <?php
                // // pending status // //
                if($pending)
                {
                    ?>
                    <ul>
                        <li>
                            <?php
                            $pending_stocks_cnt = sizeof(get_pending_stocks_of_fund($fundData['fund_id']));
                            show_new_stock_requests($pending_stocks_cnt);
                            ?>
                        </li>   
                    </ul>
                    
                    <ul>
                        <li>
                            <?php echo 'سهام پیش بینی شده:‌ '.$fundData['stocks_number'].' سهم';?>
                        </li>   
                        <li>
                            <?php echo 'سهام تایید شده تاکنون:‌ '.$verified_stocks_cnt;?>
                        </li>   
                        <li>
                            <?php
                            $crnt_user_stocks_installment_text = '';
                            if($crnt_user_stocks_cnt == 0)
                            {
                                $crnt_user_stocks_text = ' شما هیچ سهامی در این صندوق ندارید ';
                            }
                            else
                            {
                                switch($crnt_user_stocks_is_verified)
                                {
                                    case '0':
                                        $crnt_user_stocks_text = "شما $crnt_user_stocks_cnt سهم درخواست داده اید ( در انتظار تایید مدیر )";
                                        $crnt_user_stocks_installment_text = "قسط دوره ای شما :‌ ".number_format($fundData['installment']*$crnt_user_stocks_cnt).' '.$fundData['currency'] ;
                                        break;
                                    case '1':
                                        $crnt_user_stocks_text = "شما $crnt_user_stocks_cnt سهم تایید شده دارید";
                                        $crnt_user_stocks_installment_text = "قسط دوره ای شما :‌ ".number_format($fundData['installment']*$crnt_user_stocks_cnt).' '.$fundData['currency'] ;
                                        break;
                                    case '-1':
                                        $crnt_user_stocks_text = "شما $crnt_user_stocks_cnt سهم درخواست داده اید که توسط مدیر رد شد";
                                        break;
                                    default:
                                        $crnt_user_stocks_text = '';
                                        break;
                                }
                            }

                            echo  $crnt_user_stocks_text;

                            ?>
                            <div class="modal fade text-left" id="modal-change-crnt-user-stocks" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
                                aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h4 class="modal-title" id="myModalLabel1">
                                                درخواست سهام در صندوق <?php echo $fundData['name']; ?>
                                            </h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body no-bg">
                                            <div class="card-box-body">
                                                <div class="min-width">
                                                    <ul>
                                                        <li>مبلغ وام: <?php echo $loan_amount_text; ?></li>  
                                                        <li>قسط دوره ای هر سهم: <?php echo $installment_amount_text; ?></li>  
                                                    </ul>
                                                    <ul>
                                                        <li><?php echo $crnt_user_stocks_text; ?></li>  
                                                        <li><?php echo $crnt_user_stocks_installment_text; ?></li>  
                                                    </ul>
                                                    <ul>
                                                        <li>
                                                            ویرایش تعداد سهام :‌
                                                            <input type="text" id="input_requeststocks_stockscount" placeholder="تعداد سهام" 
                                                                    class="touchspin-vertical" value="<?php echo $crnt_user_stocks_text; ?>" 
                                                                    data-bts-min="0" />
                                                        </li>
                                                    </ul>
                                                    <ul>
                                                        <li>
                                                            <?php echo 'سهام پیش بینی شده:‌ '.$fundData['stocks_number'].' سهم';?>
                                                        </li>   
                                                        <li>
                                                            <?php echo 'سهام تایید شده تاکنون:‌ '.$verified_stocks_cnt;?>
                                                        </li>   
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn mr-1 mb-1 btn-success btn-sm" 
                                                    id="type-success" data-dismiss="modal"><i class="fa fa-check"></i> ثبت تغییرات</button>
                                            <button type="button" class="btn mr-1 mb-1 btn-danger btn-sm" 
                                                    id="cancel-button" data-dismiss="modal"><i class="fa fa-times"></i> انصراف از عضویت</button>
                                            
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <span data-toggle="modal" data-target="#modal-change-crnt-user-stocks" class="p-0">
                                <a href="#" class="btn btn-sm mr-1 btn-outline-primary">
                                    ویرایش سهام
                                </a>
                            </span>

                        </li>   
                        <!-- <li>   
                            <?php
                            // show_form_user_request_stocks($fundData,$crnt_user_stocks_cnt);
                            ?>
                        </li>    -->
                    </ul>
                    <?php
                }
                // // working status // //
                else
                {
                    ?>
                    <ul >
                        <li>
                        <?php echo 'قرعه کشی این دوره: '.$next_lot_text; ?>
                        </li>   
                        <li class="bg-warning">
                            <i class="ft-alert-triangle"></i>
                            ۳ واریز تایید نشده

                        </li>  
                    </ul>

                    <ul >
                        <?php show_accounting_information_of_user($fundData , $crnt_user , $crnt_user_stocks , $crnt_user_debts); ?>
                        <li> <?php echo $crnt_user_stocks_cnt.' سهم از شماست'; ?> </li>  
                    </ul>

                    <ul class="bg-warning">
                        <li>
                            <i class="ft-trending-down"></i>
                            صندوق در ۲ قرعه کشی مبلغ   10,000,000 تومان بدهکار است
                        </li>  
                    </ul>

                    <ul >
                        <li>
                            <?php echo $fundData['stocks_number'].' سهم';?>
                        </li>   
                        <li><?php echo $lot_cnt_text;?> </li>   
                        <li><?php echo $last_lot_winner_text;?> </li>   
                    </ul>
                    <?php
                }
                ?>

                
                <ul>
                    <li>مبلغ وام: <?php echo $loan_amount_text; ?></li>  
                    <li>پرداختی هر سهم: <?php echo $installment_amount_text; ?></li>  
                </ul>

                <ul>
                    <li>
                        <?php echo 'مدیر صندوق: '.$admin['name']; ?>
                    </li>  
                    <li>
                        <a href="tel:+98<?php echo $admin['mobile']; ?>"> <i class="ft-phone"></i> 
                            <?php echo '0'.$admin['mobile']; ?>
                        </a>
                    </li>  
                </ul>
            </div>
        </div>

        <?php
    }
}

// // fund panel -> [tab] fund information ->‌ [box] current user stocks
// if( !function_exists('show_stocks_information_of_user'))
// {
//     function show_stocks_information_of_user($fundData,$user_stocks)
//     {
//         $ci =& get_instance();
//         $fundData = $ci->funds_model->get_fund_by_id($fundData['fund_id']);

//         $stock_cnt = sizeof($user_stocks);
//         if($stock_cnt == 0)
//         {
//             echo ' شما هیچ سهامی در این صندوق ندارید ';
//         }
//         else
//         {
//             switch($user_stocks[0]['is_verified'])
//             {
//                 case '0':
//                     echo "شما $stock_cnt سهم درخواست داده اید";
//                     echo ' ( در انتظار تایید مدیر ) ';
//                     break;
//                 case '1':
//                     echo "شما $stock_cnt سهم دارید";
//                     break;
//                 case '-1':
//                     echo "شما $stock_cnt سهم درخواست داده اید که توسط مدیر رد شد";
//                     break;
//             }
//         }

//         if($fundData['status'] == 0)
//         {
//             show_form_user_request_stocks($fundData,$stock_cnt);
//         }
//     }
// }

// fund panel -> [tab] fund information ->‌ [box] current user accounting
if( !function_exists('show_accounting_information_of_user'))
{
    function show_accounting_information_of_user($fundData,$user,$user_stocks,$debts)
    {
        $modal_target = "modal_member_in_fund_crnt_user"
        ?>

        <li id="ul_fund_member_crnt_user" 
                data-toggle="modal" 
                data-target="#<?php echo $modal_target;?>" 
                data-userid="<?php echo $user['user_id'];?>">
                <?php 
                if($debts['sum']!=0)
                {
                    ?>
                    <span class="">
                        <?php echo  'بدهی شما '. -$debts['sum'].' '.$fundData['currency'];?>
                    </span>
                    <span class="">
                        <?php echo ' ( '.$debts['debt_cnt'].' قسط معوقه'.' )';?>
                    </span>
                    <?php
                }
                else
                {
                    ?>
                    <span class="">
                        <?php echo 'شما هیچ قسط معوقه ندارید.';?>
                    </span>
                    <?php
                }
                ?>
        </li>
        <!-- <div class="modal fade text-left" id="<?php echo $modal_target;?>" 
            tabindex="-1" role="dialog"  
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                                salam
                    </div>
                </div>
            </div>
        </div> -->
        <?php
        show_modal_member_in_fund($user,$fundData,$user_stocks,$modal_target);
    }
}

// fund panel -> [tab] fund information -> [‌box] new members stock requests
if(!function_exists('show_new_stock_requests'))
{
    function show_new_stock_requests($pending_stocks_cnt)
    {
        ?>
        <i class="ft-user-plus"></i>
        <?php
        if($pending_stocks_cnt!=0)
        {
        ?>
            <?php echo $pending_stocks_cnt.' درخواست سهام در انتظار تایید مدیر صندوق است.';?>
            <a class="clickOnId btn btn-sm mr-1 btn-outline-primary" href="#CLICK:#base-tab42">مشاهده درخواست ها</a>
        <?php
        }
        else echo 'هیچ درخواست سهام جدیدی ثبت نشده است' ;
    }
}

// fund panel -> [tab] fund members
if(!function_exists('show_tab_fund_members'))
{
    function show_tab_fund_members($fundData)
    {

        
        // $verifier_id = $fundData['created_by_id'];
        if($fundData['status'] == 0)
        {
            $stock_list = get_stocks_list_of_fund($fundData['fund_id'],false);
        }
        else
        {
            $stock_list = get_stocks_list_of_fund($fundData['fund_id'],true);
        }
        ?>
        <div class="tab-pane" id="tab42" aria-labelledby="base-tab42">
        <?php 
        show_tab_fund_members_header(); 
        show_stocks_counter_box($fundData);
        ?>      
        <div class="min-width wide-cell-first colour-icon text-center important-data">
            <!-- Header List  -->
            <ul class="header-list">
                <li>کاربر</li>
                <?php
                if($fundData['status']==0) {?>
                    <li>تعداد سهام</li>
                    <li>وضعیت</li>
                <?php } else { ?>
                    <li>معوقه</li>
                    <li>تایید نشده</li>
                <?php } ?>
            </ul>
            <?php
            foreach ($stock_list as $index => $stock)
            {
                // $member = $CI->users_model->get_user_by_id($stock['user_id']);
                $member['name'] = $stock['member_name'];
                $member['user_id'] = $stock['user_id'];
                $member['mobile'] = $stock['member_mobile'];
                $debts = get_debts_of_user_in_fund($fundData,$stock['user_id']);
                // List Items
                $modal_target = "modal_member_in_fund_userid_".$stock['user_id'];
                ?>
                <ul id="ul_fund_member_list_item" 
                    data-toggle="modal" 
                    data-target="#<?php echo $modal_target;?>" 
                    data-userid="<?php echo $stock["user_id"];?>">
                    <?php
                    if($fundData['status']==0)
                        show_stock_requests_list_items($stock,$fundData,$member);
                    else
                        show_fund_members_list_items($stock,$fundData,$member , $debts);
                    ?>
                </ul>
                <?php
                show_modal_member_in_fund($member,$fundData,$stock,$modal_target);
            }
            ?>
        </div>
    </div>
    <?php
    }
}

// fund panel -> [tab] fund members -> [row] Header
if(!function_exists('show_tab_fund_members_header'))
{
    function show_tab_fund_members_header()
    {
        ?>
        <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-6">
                <img class="img-responsive center-block visible-lg-block" src="<?php echo base_url(); ?>app-assets/images/svg/member-list.svg" alt="tribe cropped">
            </div>
            <div class="col-sm-12 col-md-6 col-lg-6 d-flex align-items-center">
                <div class="description-area dark-text">
                    <p>
                        با بررسی صندوق ها و سپس انتخاب صندوق مورد نظر می توانید اقدام به عضویت در صندوق مربوطه را بنمایید. 
                        امکان انصراف از عضویت در هر زمان پیش از تاریخ قرعه کشی فراهم می باشد. 
                        همچنین می توانید سهام خود را کاهش و یا افزایش دهید.
                    </p>
                </div>
            </div>
        </div>

        <?php
    }
}

// fund panel -> [tab] fund members -> [modal] member info
if(!function_exists('show_modal_member_in_fund'))
{
    function show_modal_member_in_fund($member,$fundData,$stock,$modal_target)
    {
        // $member = $member_accounting['member'];
        if(isset($stock['total']))
            $stock_cnt = $stock['total'];
        else
            $stock_cnt = sizeof($stock);

        ?>
        <div class="modal fade text-left" id="<?php echo $modal_target;?>" 
            tabindex="-1" role="dialog"  
            aria-labelledby="myModalLabel_userid_<?php echo $member["user_id"];?>"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel_userid_<?php echo $member["user_id"];?>">اطلاعات عضو</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- user modal body -->
                    <div class="modal-body">
                        <div class="min-width colour-icon normal-cell-first">
                            <!-- user name -->
                            <ul>
                                <li>  
                                    <a class="phone-no" href="tel:+98<?php echo $member['mobile']; ?>" 
                                        data-toggle="tooltip" data-placement="right"
                                        title="0<?php echo $member['mobile']; ?>"> 
                                        <i class="ft-phone"></i> 
                                    </a>
                                    نام :
                                    <?php echo $member["name"];?>
                                </li>
                            </ul>
                            <!-- last login -->
                            <ul>
                                <li>آخرین ورود: ؟؟</li>
                            </ul>
                            <!-- stocks cnt , points -->
                            <ul>
                                <li><?php echo 'تعداد سهام:‌ '.$stock_cnt.' سهم'; ?></li>
                                <li>
                                    <i class="icon-star"></i>
                                    خوش حسابی: ؟؟
                                </li>
                            </ul>
                            <!-- accounting section -->
                            <!-- accounting : header -->
                            <ul class="header-list">
                                <li>لیست اقســاط</li>
                            </ul>
                            <!-- accounting : filter results -->
                            <!-- accounting : result list -->
                            <?php
                            show_transactions_of_user_of_fund($member,$fundData,$modal_target); 
                            ?>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">بستن</button>
                    </div>
                </div>
            </div>
        </div>
        <!--  effects. -->
        <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
            <!-- background of photoswipe. 
                it's a separate element as animating opacity is faster than rgba(). -->
            <div class="pswp__bg"></div>
            <!-- slides wrapper with overflow:hidden. -->
            <div class="pswp__scroll-wrap">
                <!-- container that holds slides. 
                    photoswipe keeps only 3 of them in the dom to save memory.
                    don't modify these 3 pswp__item elements, data is added later on. -->
                <div class="pswp__container">
                    <div class="pswp__item"></div>
                    <div class="pswp__item"></div>
                    <div class="pswp__item"></div>
                </div>
                <!-- default (photoswipeui_default) interface on top of sliding area. can be changed. -->
                <div class="pswp__ui pswp__ui--hidden">
                    <div class="pswp__top-bar">
                        <!--  controls are self-explanatory. order can be changed. -->
                        <div class="pswp__counter"></div>
                        <button class="pswp__button pswp__button--close" title="close (esc)"></button>
                        <button class="pswp__button pswp__button--share" title="share"></button>
                        <button class="pswp__button pswp__button--fs" title="toggle fullscreen"></button>
                        <button class="pswp__button pswp__button--zoom" title="zoom in/out"></button>
                        <!-- preloader demo http://codepen.io/dimsemenov/pen/yybwor -->
                        <!-- element will get class pswp__preloader-active when preloader is running -->
                        <div class="pswp__preloader">
                            <div class="pswp__preloader__icn">
                                <div class="pswp__preloader__cut">
                                    <div class="pswp__preloader__donut"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                        <div class="pswp__share-tooltip"></div>
                    </div>
                    <button class="pswp__button pswp__button--arrow--left" title="previous (arrow left)">
                    </button>
                    <button class="pswp__button pswp__button--arrow--right" title="next (arrow right)">
                    </button>
                    <div class="pswp__caption">
                        <div class="pswp__caption__center"></div>
                    </div>
                </div>
            </div>
        </div>
        <!--/  effects -->  
        <?php
    }
}

// fund panel -> [tab] fund members -> [box] stock counter
if(!function_exists('show_stocks_counter_box'))
{
    function show_stocks_counter_box($fundData)
    {
        // $ci =& get_instance();
        // $verified_stocks = sizeof($ci->stocks_model->get_verified_stocks_of_fund_id($fundData['fund_id']));
        $verified_stocks = sizeof($fundData['verified_stocks']);
        ?>
        <input  type="hidden" id="input_verifyStocks_inputInstallment" 
        value="<?php echo $fundData['installment'] ;?>">


            <?php
            if($fundData['status'] == 0)
            {
                ?>
                <div class="text-small text-medium">
                    <?php echo 'کل سهام تایید شده : ';?>
                    <span id="span_verifyStocks_showVerifiedStocksOfFund" class="text-medium">
                        <?php echo number_format($verified_stocks) ;?>
                    </span>
                    <?php echo ' سهم';?>
                </div>
                <div class="text-small text-medium">
                    <?php echo 'مبلغ وام با این تعداد سهم : ';?>
                    <span id="span_verifyStocks_calculatedLoanforVerifiedStocksOfFund" class="text-medium">
                        <?php echo number_format($verified_stocks*$fundData['installment']);?>
                    </span>
                    <?php echo $fundData['currency'];?>
                </div>
                    <?php 
                    if(get_loggedin_user('user_id')===$fundData['created_by_id'] && $fundData['status'] == '0')
                    {
                        ?>
                        <button class="btn btn-sm btn-primary mt-2 mb-1 text-small text-medium"
                            onclick="form_startFund(this)" data-fundid="<?php echo $fundData['fund_id'];?>" >
                            شروع بکار صندوق با سهام تایید شده تا الان
                        </button>
                        <?php
                    }
                    ?>
            <?php
            }
            else
            {
            ?>

            <?php
            }

    }
}

// fund panel -> [tab] fund members -> [list item] stock requests
if( !function_exists('show_stock_requests_list_items'))
{
    function show_stock_requests_list_items($stock,$fundData,$member)
    {
        $req_stock_cnt = $stock['total'];
        $is_verified = $stock['is_verified'];
        
        $crnt_user_id = get_loggedin_user('user_id');

        ?>
        <li>
            <a class="phone-no" 
                href="<?php echo 'tel:+98'.$member['mobile'];?>" 
                data-toggle="tooltip" 
                data-placement="right"
                title="<?php echo '+98'.$member['mobile'];?>"> 
                <i class="ft-phone"></i> 
            </a>
            <?php echo $member['name'];?>
        </li>
        <li>
            <span>
                <?php echo '  '.$req_stock_cnt.' سهم'; ?>
            </span>
        </li>
        <li>
            <?php
            switch($is_verified)
            {
                case '1':
                    ?>
                    <span class="bg-success">
                        <b>تایید شده</b>
                    </span>
                    <?php
                    break;
                case  '0':
                    ?>
                    <span class="">
                        در حال بررسی
                    </span>
                    <?php
                    break;
                case  '-1':
                    ?>
                    <span class="bg-danger">
                        تایید نـشده
                    </span>
                    <?php
                    break;
            }
            ?>
            <?php 
            if($crnt_user_id === $fundData['created_by_id'] && $fundData['status']== 0)
            {
                show_form_admin_verify_stocks($stock,$fundData,$member);
            }
            ?>
        </li>


        <?php
    }
}

// fund panel -> [tab] fund members -> [list item] members
if( !function_exists('show_fund_members_list_items'))
{
    function show_fund_members_list_items($stock,$fundData,$member ,$debts)
    {
        $stock_cnt = $stock['total'];
        $is_verified = $stock['is_verified'];
        $crnt_user_id = get_loggedin_user('user_id');
        ?>
        <li>
            <a class="phone-no" 
                href="<?php echo 'tel:+98'.$member['mobile'];?>" 
                data-toggle="tooltip" 
                data-placement="right"
                title="<?php echo '+98'.$member['mobile'];?>"> 
                <i class="ft-phone"></i> 
            </a>
            <?php echo $member['name'];?>
        </li>
        <li>
            <span>
            <?php
                if($debts['debt_cnt']!=0)
                {
                    echo $debts['debt_cnt'].' قسط'; 
                }
                else
                {
                    echo 'ندارد';
                }
            ?>
            </span>
        </li>

        <li><span> ؟؟ </span></li>
        <?php 
        
    }
}

if(!function_exists('show_transactions_of_user_of_fund'))
{
    function show_transactions_of_user_of_fund($user,$fund,$modal_target)
    {
        $ci =& get_instance(); 
        $debts = get_debts_of_user_in_fund($fund,$user['user_id']);
        $lots = $fund['lotteries'];
        // echo '<pre>'; print_r($debts); echo '</pre>';
        // $crnt_user_id = get_loggedin_user('user_id');

        // if( isset($debts['actions']) )
        // {
        //     foreach($debts['actions'] as $debt_in_lot)
        //     {
        if( $lots != NULL )
        {
            foreach($lots as $lot)
            {
                $member_accounting = $ci->accounting_model->get_accounting_in_lot($lot['lot_id'] , $user['user_id'] );
                show_installment_details($member_accounting[0],$fund,$modal_target);
            }
        }
        else
        {
            ?>
            <ul>
                هیج اطلاعاتی ثبت نشده است
            </ul>
            <?php
        }
    }
}

if(!function_exists('show_loan_details'))
{
    function show_loan_details($member_accounting,$fund,$unique_id,$type = 'lots')
    {
        // $user_id = $actions_in_lot['data']['user_id'];
        $crnt_user_id = get_loggedin_user('user_id');
        $is_admin = $fund['is_admin'];
        
        $member = $member_accounting['user_info'];
        $mem_id = $member['user_id'];
        
        $installment = $member_accounting['installment'];
        $payment = $member_accounting['payment'];
        $lot = $member_accounting['lottery'];

        if($payment !== NULL )
        {
            $status['sum'] = $installment['amount'] + $payment['amount'];
            $status['verify'] = $payment['verify_status'];
        }
        else
        {
            $status['sum'] = NULL;
            $status['verify'] = NULL;
        }
        
        ?>
        <ul>
            <li>
                <?php 
                if($type == 'lots')
                {
                    echo "<b>دوره".$lot['loan_number']."</b>- ".persian_date($lot['date'],false,true);
                    ?>
                    </li> <li>
                    <?php 
                    echo abs($installment['amount']).' '.$fund['currency'];
                }
                elseif($type == 'name')
                {
                    ?>
                    <a href="tel:+98<?php echo $member['mobile']; ?>"> 
                        <i class="ft-phone"></i> 
                    </a>
                    <?php
                    echo $member['name'];
                }
                ?>
            </li>
        <?php

        $collapse_id = "collapse_userinfo_installments_userid_".$mem_id.'_lotid_'.$lot['lot_id'].'_'.$unique_id;

        ?>
        <li>
        <div class="card collapse-icon accordion-icon-rotate m-0">
        <div id="heading_<?php echo $collapse_id;?>" class="card-header p-0">
        <?php 
        $show_form = FALSE;
        switch($status['verify'])
        {
            case '-1':
                ?> <i class="ft-minus-circle"></i>
                <?php
                break;
            case '1':
                ?> <i class="ft-check-circle"></i>
                <?php
                break;
            case '0':
                ?> <i class="ft-alert-triangle"></i>
                <?php
                break;
            case NULL:
                ?> <i class="ft-minus-circle"></i>
                <b class="danger">واریز نـشـده</b>
                <?php
                if($crnt_user_id === $mem_id || $is_admin) $show_form = TRUE;
                break;
        }

        if($status['verify'] === NULL )
        {
            if($show_form)
            {?>
                <a  data-toggle="collapse" 
                    href="#<?php echo $collapse_id;?>" 
                    aria-expanded="false" 
                    aria-controls="<?php echo $collapse_id;?>" 
                    class="collapsed">
                    ثبت اطلاعات واریز
                </a>
            <?php } ?>
            </div>
            </div>
            </li>
            </ul>
            <?php
            if($show_form) show_form_paydebt($member_accounting,$fund,$collapse_id,$is_admin);
        }
        else
        {
            ?>
            <a  data-toggle="collapse" 
                href="#<?php echo $collapse_id;?>" 
                aria-expanded="false" 
                aria-controls="<?php echo $collapse_id;?>" 
                class="collapsed">
                اطلاعات واریز
            </a>
            </div>
            </div>
            </li>
            </ul>
            <?php
            show_collapse_payments_details($payment,$collapse_id,$is_admin);
        }

    }
}

if(!function_exists('show_installment_details'))
{
    function show_installment_details($member_accounting,$fund,$unique_id,$type = 'lots')
    {
        // $user_id = $actions_in_lot['data']['user_id'];
        $crnt_user_id = get_loggedin_user('user_id');
        $is_admin = $fund['is_admin'];
        
        $member = $member_accounting['user_info'];
        $mem_id = $member['user_id'];
        
        $installment = $member_accounting['installment'];
        $payment = $member_accounting['payment'];
        $lot = $member_accounting['lottery'];

        if($payment !== NULL )
        {
            $status['sum'] = $installment['amount'] + $payment['amount'];
            $status['verify'] = $payment['verify_status'];
        }
        else
        {
            $status['sum'] = NULL;
            $status['verify'] = NULL;
        }
        
        ?>
        <ul>
            <li>
                <?php 
                if($type == 'lots')
                {
                    echo "<b>دوره".$lot['loan_number']."</b>- ".persian_date($lot['date'],false,true);
                    ?>
                    </li> <li>
                    <?php 
                    echo abs($installment['amount']).' '.$fund['currency'];
                }
                elseif($type == 'name')
                {
                    ?>
                    <a href="tel:+98<?php echo $member['mobile']; ?>"> 
                        <i class="ft-phone"></i> 
                    </a>
                    <?php
                    echo $member['name'];
                }
                ?>
            </li>
        <?php

        $collapse_id = "collapse_userinfo_installments_userid_".$mem_id.'_lotid_'.$lot['lot_id'].'_'.$unique_id;

        ?>
        <li>
        <div class="card collapse-icon accordion-icon-rotate m-0">
        <div id="heading_<?php echo $collapse_id;?>" class="card-header p-0">
        <?php 
        $show_form = FALSE;
        switch($status['verify'])
        {
            case '-1':
                ?> <i class="ft-minus-circle"></i>
                <?php
                break;
            case '1':
                ?> <i class="ft-check-circle"></i>
                <?php
                break;
            case '0':
                ?> <i class="ft-alert-triangle"></i>
                <?php
                break;
            case NULL:
                ?> <i class="ft-minus-circle"></i>
                <b class="danger">واریز نـشـده</b>
                <?php
                if($crnt_user_id === $mem_id || $is_admin) $show_form = TRUE;
                break;
        }

        if($status['verify'] === NULL )
        {
            if($show_form)
            {?>
                <a  data-toggle="collapse" 
                    href="#<?php echo $collapse_id;?>" 
                    aria-expanded="false" 
                    aria-controls="<?php echo $collapse_id;?>" 
                    class="collapsed">
                    ثبت اطلاعات واریز
                </a>
            <?php } ?>
            </div>
            </div>
            </li>
            </ul>
            <?php
            if($show_form) show_form_paydebt($member_accounting,$fund,$collapse_id,$is_admin);
        }
        else
        {
            ?>
            <a  data-toggle="collapse" 
                href="#<?php echo $collapse_id;?>" 
                aria-expanded="false" 
                aria-controls="<?php echo $collapse_id;?>" 
                class="collapsed">
                اطلاعات واریز
            </a>
            </div>
            </div>
            </li>
            </ul>
            <?php
            show_collapse_payments_details($payment,$collapse_id,$is_admin);
        }

    }
}

if(!function_exists('show_collapse_payments_details'))
{
    function show_collapse_payments_details($action,$collapse_id,$is_admin)
    {
        $details = json_decode($action['action_details'],true);
        $verify = json_decode($action['verify_details'],true);
        // pr($details);
        // pr($verify);
        // 

        ?>
        <ul class="card collapse-icon accordion-icon-rotate p-0 m-0">
            <div id="<?php echo $collapse_id; ?>"
                role="tabpanel" aria-labelledby="heading_<?php echo $collapse_id; ?>" class="m-n collapse" style="">
                <div class="card-content">
                    <div class="card-body p-0">

                        <div class="min-width m-0 collapse-info">
                            <ul>
                                <li>
                                    شماره پیگیری: 
                                    <?php echo $details['tracking_code']; ?>
                                </li>
                                <li>
                                    تاریخ پرداخت : 
                                    <?php echo $details['pay_date']>0 ?  persian_date($details['pay_date']) : ''; ?>
                                </li>
                            </ul>
                            <!-- <ul>
                                <li>توع واریزی: ??</li>
                                <li>مبلغ: 
                                    <?php echo $action['amount'].' '.$fund['currency']; ?>
                                </li>
                            </ul> -->
                            <?php
                            if($details['comment'])
                            {
                                ?>
                                <ul><li>یادداشت واریز کننده: 
                                    <?php echo $details['comment']; ?>
                                </li></ul>
                                <?php
                            }
                            ?>
                            <ul>
                                <li>
                                     تاریخ ثبت : 
                                    <?php echo $details['insert_date'] ? persian_date($details['insert_date']) : ''; ?>
                                </li>

                                <?php 
                                $show_verify_btns['0'] = $is_admin ? TRUE : FALSE;
                                
                                $show_verify_btns['-1'] = FALSE;
                                $show_verify_btns['1'] = FALSE;

                                switch($action['verify_status'])
                                {
                                    case '-1':
                                    echo '<li class="danger">تایید نشده</li>';
                                    $show_verify_btns['0'] = TRUE;
                                    $show_verify_btns['1'] = TRUE;
                                    break;
                                    case '1':
                                    echo '<li class="success">تایید شده</li>';                                    
                                    $show_verify_btns['-1'] = TRUE;
                                    break;
                                case '0':
                                    echo '<li class="">در انتظار تایید مدیر</li>';                                    
                                    $show_verify_btns['0'] = TRUE;
                                    $show_verify_btns['1'] = TRUE;
                                    $show_verify_btns['-1'] = TRUE;
                                    break;
                                }
                                ?>
                            </ul>

                            <?php
                            if($verify['comment'])
                            {
                                ?>
                                <ul><li>یادداشت مدیـر : 
                                    <?php echo $verify['comment']; ?>
                                </li></ul>
                                <?php
                            }

                            if($is_admin)
                            {
                            ?>
                                <ul>
                                    <li>
                                        <?php
                                        if($show_verify_btns['1'])
                                        {
                                            ?>
                                            <button class="btn btn-sm btn-outline-success pt-0 pb-0 pr-1 pl-1"
                                                data-action='<?php echo json_encode($action);?>'
                                                data-function = "1"
                                                data-collapse-id = "<?php echo $collapse_id; ?>"
                                                onclick="<?php echo 'form_verifyPayments(this,1)';?>"
                                                id="collapse_pay_details_form_verify_payment_actionid<?php echo $action['action_id'];?>_V"
                                                >
                                                <i class="ft-check-circle"></i>
                                                <b>
                                                تایید
                                                </b>
                                            </button>
                                            <?php
                                        }
                                        if($show_verify_btns['-1'])
                                        {
                                            ?>
                                            <button class="btn btn-sm btn-outline-danger pt-0 pb-0 pr-1 pl-1"
                                                data-action='<?php echo json_encode($action);?>'
                                                data-function = "-1"
                                                data-collapse-id = "<?php echo $collapse_id; ?>"
                                                onclick="<?php echo 'form_verifyPayments(this,-1)';?>"
                                                id="collapse_pay_details_form_verify_payment_actionid<?php echo $action['action_id'];?>_D"
                                                >
                                                <i class="ft-minus-circle"></i>
                                                <b>
                                                عدم تایید
                                                </b>
                                            </button>
                                            <?php
                                        }
                                        if($show_verify_btns['0'] && 0)
                                        {
                                            ?>
                                            <button class="btn btn-sm btn-outline-info pt-0 pb-0 pr-1 pl-1"
                                                data-action='<?php echo json_encode($action);?>'
                                                data-function = "edit"
                                                data-collapse-id = "<?php echo $collapse_id; ?>"
                                                onclick="<?php echo 'form_verifyPayments(this,0)';?>"
                                                id="collapse_pay_details_form_verify_payment_actionid<?php echo $action['action_id'];?>_E"
                                                >
                                                <i class="ft-edit"></i>
                                                <b>
                                                ویرایش
                                                </b>
                                            </button>
                                            <?php
                                        }
                                        ?>
                                    </li>
                                </ul>
                            <?php
                            }
                            ?>
                            <!-- <ul>
                                <li>ثبت کننده: محمد رجبی</li>
                                <li>تایید کننده: رضا یزدانی</li>
                            </ul> -->
                        </div>
                    </div>
                </div>
            </div>
        </ul>

        <?php
    }
}

// fund panel -> [tab] fund history
if(!function_exists('show_tab_fund_history'))
{
    function show_tab_fund_history($fundData,$lotteries)
    {
        ?>
    <div class="tab-pane" id="tab43" aria-labelledby="base-tab43">

        <div class="row">

            <div class="col-sm-12 col-md-6 col-lg-6">
                <img class="img-responsive center-block visible-lg-block" src="<?php echo base_url(); ?>app-assets/images/svg/winner.svg" alt="tribe cropped">
            </div>


            <div class="col-sm-12 col-md-6 col-lg-6 d-flex align-items-center">
                <div class="description-area dark-text">
                    <p>
                        با بررسی صندوق ها و سپس انتخاب صندوق مورد نظر می توانید اقدام به عضویت در صندوق مربوطه را بنمایید. امکان انصراف از عضویت در هر زمان پیش از تاریخ قرعه کشی فراهم می باشد. همچنین می توانید سهام خود را کاهش و یا افزایش دهید.
                    </p>
                </div>

            </div>
        </div>

        <div class="min-width text-center short-cell-first colour-icon">
            <?php 
            if(sizeof($lotteries))
            {
                ?>
                <ul class="header-list">
                    <li>دوره</li>
                    <li>تاریخ قرعه کشی</li>
                    <li>برنده</li>
                </ul>

                <?php
                foreach ($lotteries as $lot)
                {
                    $unique_id = 'modal_fund'.$lot['fund_id'].'_history_lottery'.$lot['lot_id'].'_details';
                    ?>
                    <ul data-toggle="modal" data-target="#<?php echo $unique_id; ?>">
                        <li>
                            <i class="ft-minus"></i>
                            <?php echo $lot['loan_number']; ?>
                        </li>
                        <li><span>
                            <?php echo persian_date($lot['date'],true); ?>
                        </span></li>
                        <li><span>
                            <?php echo $lot['winner_stock_id']!=NULL ? $lot['winner_name'] : 'هنوز قرعه کشی انجام نشده'; ?>
                        </span></li>
                    </ul>
                    <?php
                    show_modal_lottery_details($fundData,$lot,$unique_id);
                }
            }
            else
            {
                ?>
                هنوز هیچ قرعه کشی ثبت نشده است
                <?php
            }
            ?>
        </div>
    </div>

        <!--
        <li>
            <i class="ft-minus-circle"></i>
            <i class="ft-check-circle"></i>
            <i class="ft-alert-triangle"></i>
        </li>
        -->
        
      <?php
    }
}

if(!function_exists('show_modal_lottery_details'))
{
    function show_modal_lottery_details($fundData,$lot,$unique_id)
    {
        $members_accounting = $lot['members_accounting'];
        $title = "قرعه کشی دوره ".$lot['loan_number']." صندوق ".$fundData['name'];
        ?>
        <!-- Modal -->
        <div class="modal fade text-left" id="<?php echo $unique_id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel1"><?php echo $title; ?></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <?php show_lot_details($fundData,$lot); ?>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">بستن</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}


if(!function_exists('show_tab_fund_next_lottery'))
{
    function show_tab_fund_next_lottery($fundData,$next_lot)
    {
        $members_accounting = $next_lot['members_accounting'];
        $qualified_stocks = get_quallified_stocks($members_accounting);

        show_tab_fund_dropdown_next_lot_payments($fundData,$next_lot);
        show_tab_fund_dropdown_next_lot_run($fundData,$next_lot, $qualified_stocks);
        show_tab_fund_dropdown_next_lot_set($fundData,$next_lot);
    }
}

// fund panel -> [tab] next lottery -> set next lottery
if(!function_exists('show_tab_fund_dropdown_next_lot_set'))
{
    function show_tab_fund_dropdown_next_lot_set($fundData,$lot)
    {
        $crnt_user_id = get_loggedin_user('user_id');

        $is_admin = ($crnt_user_id === $fundData['created_by_id']) ? TRUE : FALSE;

        $date['day'] = null;
        $date['month'] = null;
        $date['year'] = null;

        if($lot)
        {
            $date = timestamp2persian($lot['date']);
            $diff_date = days_to_today($lot['date']);
            
            if($diff_date['sign'] > 0)
                $date_text = $diff_date['num'].' روز مانده به قرعه کشی';
            else
                $date_text = 'قرعه کشی برای '.$diff_date['text'].' ثبت شده ';
            
            $persian_date = persian_date($lot['date'],TRUE);
        }
        else
        {
            $date = timestamp2persian(time());            
            $date_text = 'هنوز تاریخ قرعه کشی تنظیم نشده';
            $persian_date = NULL;
        }

        $unique_id = "form_next_lot_set_date_fundid".$fundData['fund_id']."_status".$fundData['status'] ;
        ?>

        <div class="tab-pane" id="dropdown3" role="tabpanel" aria-labelledby="dropdown3-tab" aria-expanded="false">
            <pre></pre>
            <form class="form form-horizontal" id="form_setLotteryDate" 
                    data-fund-id="<?php echo $fundData['fund_id'];?>" 
                    data-loan-number="<?php echo $fundData['status'];?>"
                    data-unique-id="<?php echo $unique_id; ?>"
                    >

                <div class="form-body">
                    <div class="form-group row m-0">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <img class="img-responsive center-block visible-lg-block" 
                                    src="<?php echo base_url(); ?>app-assets/images/svg/calnedar.svg" 
                                    alt="tribe cropped">
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-6">

                            <div class="row">
                                <div class="col-sm-12 col-lg-12 lottery-time fonticon-container">
                                    <div class="fonticon-wrap">
                                        <i class="icon-calendar"></i>
                                    </div>
                                    <label class="fonticon-classname">
                                        <?php echo $date_text; ?>
                                    </label>
                                    <label class="fonticon-unit">
                                        <b>
                                        <?php echo $persian_date; ?>
                                        </b>
                                    </label>
                                </div>
                            </div>

                            <?php 
                            if($is_admin)
                            {
                                if($fundData['status']>0)
                                {
                                    
                                    show_pds( $unique_id , NULL , $date , TRUE);
                                    ?>
                                    <div class="col-xs-12 col-sm-2 col-lg-1">
                                        <div class="form-group m-0">
                                            <button type="submit" class="btn mr-1 mb-1 btn-success btn-sm m-0" 
                                                    data-fund-id="<?php echo $fundData['fund_id'];?>" 
                                                    data-loan-number="<?php echo $fundData['status'];?>"
                                                    id="submit_next_lot_set_date"><i class="fa fa-check"></i> ثبت تاریخ قرعه کشی</button>
                                        </div>
                                    </div>
                                    <?php
                                }
                                elseif($fundData['status']==0)
                                {
                                    ?>
                                    <div>
                                    <b>
                                    <?php
                                        echo 'صندوق در حال عضو گیری است';
                                    ?>
                                    </b>
                                    </div>
                                    <div>
                                    <?php
                                        echo 'با توجه به وضعیت صندوق ثبت تاریخ قرعه کشی ممکن نیست !';
                                    ?>
                                    </div>
                                    <?php
                                }
                            }
                            else
                            {
                                ?>
                                <div class="">
                                <?php
                                    echo 'ثبت تاریخ قرعه کشی توسط مدیر صندوق امکان پذیر است';
                                ?>
                                </div>
                                <?php
                            }

                            ?>
                            
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <?php
    }
}

// fund panel -> [tab] next lottery -> run next lottery
if(!function_exists('show_tab_fund_dropdown_next_lot_run'))
{
    function show_tab_fund_dropdown_next_lot_run($fundData,$next_lot,$qualified_stocks)
    {
        $is_admin = $fundData['is_admin'];

        // $next_lot = $ci->lottery_model->get_lotteries_of_fund($fundData['fund_id'] , $fundData['status']);
        $lot_done = FALSE; 
        $date_ok = FALSE;

        if($next_lot)
        {
            $next_lot_date = persian_date($next_lot['date']);
            $next_lot_days_from_today =  days_to_today($next_lot['date']) ;
            $next_lot_date_text = 'تاریخ قرعه کشی <b>'.$next_lot_date.'</b> - '.$next_lot_days_from_today['text'];
            $next_lot_loan_number = '<b>قرعه کشی دوره '.$next_lot['loan_number'].'</b>';
            $lot_done = $next_lot['winner_stock_id'] ? TRUE : FALSE;
            if($next_lot_days_from_today['diff']<=0)
            {
                $date_ok = TRUE;
            }
            $members_accounting = $next_lot['members_accounting'];
        }
        else
        {
            $next_lot_loan_number = '<b>قرعه کشی دوره'.$fundData['status'].'</b>';
            $next_lot_date_text = 'تاریخ قرعه کشی <b>هنوز ثبت نشده است</b>';
            $members_accounting =  NULL;
        }

        $test_lottery = TRUE;
        $real_lottery = FALSE;

        if($is_admin && $date_ok && !$lot_done)
        {
            $real_lottery = TRUE;
        }

        $qualified_stocks_cnt = [];
        $qualified_stocks_cnt['all'] = sizeof($qualified_stocks['all']);
        $qualified_stocks_cnt['payed'] = sizeof($qualified_stocks['payed']);

        ?>
        <div class="tab-pane" id="dropdown2" role="tabpanel" aria-labelledby="dropdown2-tab" aria-expanded="false">
        <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-6">
                <img class="img-responsive center-block visible-lg-block" src="<?php echo base_url(); ?>app-assets/images/backgrounds/start-lottery.png" alt="tribe cropped">
            </div>
            <div class="col-sm-12 col-md-6 col-lg-6">
                <div class="min-width">
                    <ul>
                        <li> <?php echo $next_lot_loan_number;?> </li>
                        <li> <?php echo $next_lot_date_text;?> </li>
                    </ul>
                    <ul class="header-list">
                        <li>
                            نوع قرعه کشی را انتخاب کنید :
                        </li>
                    </ul>
                    <ul>
                        <li>
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" 
                                        data-lotid="<?php echo $next_lot['lot_id'];?>"
                                        name="form_runLot_lotType" id="form_runLot_lotType_test" checked>
                                    <label class="custom-control-label" for="form_runLot_lotType_test">
                                        <b>قرعه کشی آزمایشی</b>
                                        <div class="text-secondary">
                                        نتیجه این قرعه کشی جایی ثبت نمی شود و فقط یک آزمایش است.
                                        </div>
                                    </label>
                                </div>
                            </fieldset>
                        </li>
                    </ul>
                    <ul>
                        <li>
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" 
                                        data-lotid="<?php echo $next_lot['lot_id'];?>"
                                        name="form_runLot_lotType" id="form_runLot_lotType_real" 
                                    <?php echo $real_lottery ? '':'disabled';?> >
                                    <label class="custom-control-label" for="form_runLot_lotType_real">
                                        <b>قرعه کشی واقعی</b>
                                        <?php 
                                        if($lot_done)
                                        { ?>
                                            <div class="primary"><b>
                                                قرعه کشی واقعی دوره <?php echo $next_lot['loan_number'];?> قبلا انجام شده و برنده آن مشخص شده است.
                                            </b></div>
                                            <?php
                                        }
                                        elseif(!$date_ok && $is_admin)
                                        { ?>
                                            <div class="warning"><b>
                                            هنوز زمان قرعه کشی واقعی فرا نرسیده است !
                                            </b></div>
                                        <?php 
                                        }
                                        ?>
                                        <div class="text-secondary">
                                        این قرعه کشی فقط توسط مدیر انجام می شود و نتیجه آن غیر قابل تغییر است.
                                        </div>
                                    </label>
                                </div>
                            </fieldset>
                        </li>
                    </ul>
                    <ul class="header-list">
                        <li>
                            قرعه کشی بین چه کسانی انجام شود ؟
                        </li>
                    </ul>
                    <ul>
                        <li>
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" 
                                        data-lotid="<?php echo $next_lot['lot_id'];?>"
                                        name="form_runLot_stocksQualified" id="form_runLot_stocksQualified_all" checked
                                        <?php echo $qualified_stocks_cnt['all']>0 ?'':'disabled';?>
                                        >
                                    <label class="custom-control-label" for="form_runLot_stocksQualified_all">تمام سهامی که تاکنون وام نگرفته اند</label>
                                </div>
                            </fieldset>
                        </li>
                        <li><?php echo $qualified_stocks_cnt['all'];?> سهم</li>
                    </ul>
                    <ul>
                        <li>
                            <fieldset>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" 
                                        data-lotid="<?php echo $next_lot['lot_id'];?>"
                                        name="form_runLot_stocksQualified" id="form_runLot_stocksQualified_payed"
                                        <?php echo $qualified_stocks_cnt['payed']>0 ?'':'disabled';?>
                                        >
                                    <label class="custom-control-label" for="form_runLot_stocksQualified_payed">فقط سهامی که تاکنون وام نگرفته اند و واریز آنها تایید شده است</label>
                                </div>
                            </fieldset>
                        </li>
                        <li><?php echo $qualified_stocks_cnt['payed'] ? $qualified_stocks_cnt['payed'].' سهم': 'هیچ سهمی نیست !';?></li>
                    </ul>
                    <ul>
                        <li>
                            <fieldset class="form-group m-0">
                                <textarea class="form-control" id="form_runLot_message" rows="3" placeholder="پیام قبل از قرعه کشی"></textarea>
                            </fieldset>
                        </li>
                    </ul>
                    <div class="col-lg-12">
                        <div class="read-more-btn btn-ver">
                            <button type="button" class="btn mr-1 mb-1 btn-success btn-sm" 
                                    id="form_runLot_submit"
                                    data-lotid="<?php echo $next_lot['lot_id'];?>"
                                    data-quallifiedstocks='<?php echo json_encode($qualified_stocks);?>'
                                    onclick="form_runLot(this)"
                                    >
                                    <i class="fa fa-check"></i>
                                     انجام قرعه کشی
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <?php
    }
}

// fund panel -> [tab] next lottery -> next lottery payments
if(!function_exists('show_tab_fund_dropdown_next_lot_payments'))
{
    function show_tab_fund_dropdown_next_lot_payments($fundData,$next_lot)
    {
        $members_accounting = $next_lot['members_accounting'];
        ?>
        <div class="tab-pane" id="dropdown1" role="tabpanel" aria-labelledby="dropdown1-tab" aria-expanded="false">
            <div class="row">
                <div class="col-sm-12 col-md-6 col-lg-6">
                    <img class="img-responsive center-block visible-lg-block" src="<?php echo base_url(); ?>app-assets/images/svg/deposits.svg" alt="tribe cropped">
                </div>
                <div class="col-sm-12 col-md-6 col-lg-6 d-flex align-items-center">
                    <div class="description-area dark-text">
                        <p>
                            با بررسی صندوق ها و سپس انتخاب صندوق مورد نظر می توانید اقدام به عضویت در صندوق مربوطه را بنمایید. امکان انصراف از عضویت در هر زمان پیش از تاریخ قرعه کشی فراهم می باشد. همچنین می توانید سهام خود را کاهش و یا افزایش دهید.
                        </p>
                    </div>
                </div>
            </div>
            
            <?php
            show_lot_details($fundData,$next_lot);
            ?>
        </div>

        <?php
    }
}

if(!function_exists('is_admin_of_fund'))
{
    function is_admin_of_fund($fund_id)
    {
        $CI =& get_instance();
        $created_by_id = $CI->funds_model->get_fund_creator($fund_id);
        $logged_in = get_loggedin_user('user_id');
        return $created_by_id === $logged_in;
    }
}

if(!function_exists('show_lot_details'))
{
    function show_lot_details($fundData,$lot)
    {
        $members_accounting = $lot['members_accounting'];

        if($lot)
        {
            $lot_date = persian_date($lot['date']);
            $lot_days_from_today =  days_to_today($lot['date']) ;
            $lot_date_text = 'تاریخ قرعه کشی <b>'.$lot_date.'</b> - '.$lot_days_from_today['text'];
            $lot_loan_number = '<b>قرعه کشی دوره '.$lot['loan_number'].'</b>';
            
            $qualified_stocks = get_quallified_stocks($lot['lot_id']);
            // $qualified_stocks_cnt = $fundData['stocks_number']-$fundData['status']+1;
            $qualified_stocks_cnt = [];
            $qualified_stocks_cnt['all'] = sizeof($qualified_stocks['all']);
            $qualified_stocks_cnt['payed'] = sizeof($qualified_stocks['payed']);

            $payment_status = [];
            $payment_status['payed']['1'] = 0;
            $payment_status['payed']['0'] = 0;
            $payment_status['payed']['-1'] = 0;
            $payment_status['not_payed'] = 0;

            foreach($members_accounting as $member_accounting)
            {
                if($member_accounting['payment']!== NULL)
                {
                    $status = $member_accounting['payment']['verify_status'];
                    $payment_status['payed'][$status]++;
                }
                else
                {
                    $payment_status['not_payed']++;
                }
            }

            ?>
            <div class="min-width colour-icon">
                <ul>
                    <li> <?php echo $lot_loan_number;?> </li>
                    <li> <?php echo $lot_date_text;?> </li>
                </ul>
                <ul>
                    <li>تعداد شرکت کننده : <?php echo $qualified_stocks_cnt['all']; ?> سهم</li>
                </ul>
                <?php
                if($lot['winner_stock_id'])
                {
                    ?>
                    <ul class="header-list">
                        <li>
                        برنده قرعه کشی
                        </li>
                    </ul>
                    <ul>
                        <li>
                            <?php echo $lot['winner_name']; ?>
                        </li>
                    </ul>
                    <?php
                }
                ?>
                <ul class="header-list">
                    <li>
                    واریزی های دوره
                    </li>
                </ul>
    
                <ul>
                    <li>
                        <i class="ft-check-circle"></i> 
                        <?php echo $payment_status['payed']['1']? : 'صفر'; ?> تایید شده
                    </li>
                    <li>
                        <i class="ft-alert-triangle"></i> 
                        <?php echo $payment_status['payed']['0']? : 'صفر'; ?> در انتظار تایید
                    </li>
                    <li>
                        <i class="ft-minus-circle"></i> 
                        <?php echo ($payment_status['payed']['-1']+$payment_status['not_payed']) ? : 'صفر'; ?> واریز نشده
                    </li>
                </ul>
                <?php
                foreach($members_accounting as $member_accounting)
                {
                    $member = $member_accounting['user_info'];
                    $unique_id = 'lot_payments_userid'.$member['user_id'].'_lotid'.$fundData['status'];
                    $debts = get_debts_of_user_in_fund($fundData,$member['user_id'],$lot['lot_id']);
                    // show_installment_details($member,$fundData,$debts['actions'][$lot['lot_id']],$unique_id,'name');
                    show_installment_details($member_accounting,$fundData,$unique_id,'name');
                }
                ?>
            </div>
            <?php
        }
    }
}