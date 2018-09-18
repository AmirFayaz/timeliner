
function correctPhone(str)
{
    return toEnglishNum(str,true).replace(/^0+/, '');
}

function input_checkPhone(element)
{
    var phone = element.val();
    var phoneResult = 1;
    var _ERROR = false;

    phone = correctPhone(phone);

    intRegex = /^\d+$/;
    if // validate input;
    ( (phone.length !== 10) || (!intRegex.test(phone)))
    {
        element.removeClass("valid-field").addClass("invalid-field");
        $('div[id=input_userSignUpMobile_invalid]').addClass("show-element");
        $('div[id=input_userSignUpMobile_valid]').removeClass("show-element");
        $('div[id=input_userSignUpMobile_dbError]').removeClass("show-element");
        _ERROR = true;
    }
    else
    {
        _ERROR = false;
    }

    var params = {phone: phone};
    var action = 'form_checkNewMobile';
    var result = ajax_handler(action , params);
    var resultData = jQuery.parseJSON(result['data']);

    if(resultData.error)
    {
        element.removeClass("valid-field").addClass("invalid-field");
        $('div[id=input_userSignUpMobile_invalid]').removeClass("show-element");
        $('div[id=input_userSignUpMobile_valid]').removeClass("show-element");
        $('div[id=input_userSignUpMobile_dbError]').addClass("show-element");
        _ERROR = true;
    }

    if(!_ERROR)
    {
        element.removeClass("invalid-field").addClass("valid-field");
        $('div[id=input_userSignUpMobile_invalid]').removeClass("show-element");
        $('div[id=input_userSignUpMobile_valid]').addClass("show-element");
        $('div[id=input_userSignUpMobile_dbError]').removeClass("show-element");
    }

    return _ERROR;
}

function input_checkAdminPhone(element)
{
    var phone = element.val();
    var phoneResult = null;
    var _ERROR = false;

    phone = correctPhone(phone);

    intRegex = /^\d+$/;
    if // validate input;
    ( (phone.length !== 10) || (!intRegex.test(phone)))
    {
        element.removeClass("valid-field").addClass("invalid-field");
        $('div[id=input_searchFunds_adminMobile_invalid]').addClass("show-element");
        _ERROR = true;
    }else{
        _ERROR = false;
        var params = {phone: phone};
        var action = 'form_checkAdminPhone';
        var result = ajax_handler(action , params);
        var resultData = jQuery.parseJSON(result['data']);
    
        if(resultData.error)
        {
            element.removeClass("valid-field").addClass("invalid-field");
            $('div[id=input_searchFunds_adminMobile_dbError]').addClass("show-element");
            _ERROR = true;
        }
    
        if(!_ERROR)
        {
            phoneResult =  jQuery.parseJSON(resultData.data);
            element.removeClass("invalid-field").addClass("valid-field");
            $('div[id=input_searchFunds_adminMobile_invalid]').removeClass("show-element");
            $('div[id=input_searchFunds_adminMobile_dbError]').removeClass("show-element");
        }
    }

    return phoneResult;
}

function checkJustPersianAlphabet(element)
{
    var persianRegex = /^[\u0600-\u06F0\s]+$/;
    var str = $(element).val();
    var _ERROR =false;
    if (!persianRegex.test(str)) {
        _ERROR = true;
    }
    return _ERROR;
}

function input_checkName(element)
{
    var _ERROR =  checkJustPersianAlphabet(element);
    if(_ERROR)
    {
        element.removeClass("valid-field").addClass("invalid-field");
        $('div[id=input_userSignUpName_invalid]').addClass("show-element");
        $('div[id=input_userSignUpName_valid]').removeClass("show-element");
    }else{
        element.removeClass("invalid-field").addClass("valid-field");
        $('div[id=input_userSignUpName_invalid]').removeClass("show-element");
        $('div[id=input_userSignUpName_valid]').addClass("show-element");

    }
    return _ERROR;
}

function input_checkPassword(password1,password2)
{
    var pass1 = password1.val();
    var pass2 = password2.val();
    var _ERROR = false;

    var persianRegex = /^[\u0600-\u06FF\s]+$/;
    var hasPersian = false;
    for(var i=0; i<pass1.length ; i++)
    {
        if (persianRegex.test(pass1[i])) {
            hasPersian = true;
        }
    }

    if(pass1.length<8 || hasPersian)
    {
        //console.log("PASS Short!");
        password1.removeClass("valid-field").addClass("invalid-field");
        $('div[id=input_userSignUpPassword1_invalid]').addClass("show-element");
        $('div[id=input_userSignUpPassword1_valid]').removeClass("show-element");
        _ERROR = true;
    }

    if(pass1!==pass2)
    {
        //console.log("PASS Conflict!");
        password1.removeClass("valid-field").addClass("invalid-field");
        password2.removeClass("valid-field").addClass("invalid-field");
        $('div[id=input_userSignUpPassword2_invalid]').addClass("show-element");
        _ERROR = true;
    }

    if(!_ERROR)
    {
        password1.removeClass("invalid-field").addClass("valid-field");
        password2.removeClass("invalid-field").addClass("valid-field");
        $('div[id=input_userSignUpPassword1_valid]').addClass("show-element");
        $('div[id=input_userSignUpPassword2_valid]').addClass("show-element");
        $('div[id=input_userSignUpPassword1_invalid]').removeClass("show-element");
        $('div[id=input_userSignUpPassword2_invalid]').removeClass("show-element");
    }

    return _ERROR;
}

function input_checkFundName(element)
{
    var _ERROR = false;
    if(element.val().length <3) _ERROR =true;

    if(_ERROR)
    {
        element.removeClass("valid-field").addClass("invalid-field");
        $('div[id=input_createFund_fundName_invalid]').addClass("show-element");
        $('div[id=input_createFund_fundName_valid]').removeClass("show-element");
    }else{
        element.removeClass("invalid-field").addClass("valid-field");
        $('div[id=input_createFund_fundName_invalid]').removeClass("show-element");
        $('div[id=input_createFund_fundName_valid]').addClass("show-element");
    }
    return _ERROR;
}

function input_checkStocksNumber(element)
{
    var _ERROR = false;
    if(!isInt(toEnglishNum(element.val()))) _ERROR =true;

    if(_ERROR)
    {
        element.removeClass("valid-field").addClass("invalid-field");
        $('div[id=input_createFund_stocksNumber_invalid]').addClass("show-element");
        $('div[id=input_createFund_stocksNumber_valid]').removeClass("show-element");
    }else{
        element.removeClass("invalid-field").addClass("valid-field");
        $('div[id=input_createFund_stocksNumber_invalid]').removeClass("show-element");
        $('div[id=input_createFund_stocksNumber_valid]').addClass("show-element");
    }
    return _ERROR;
}

function input_checkInstallment(element)
{
    var _ERROR = false;
    console.log(element.val());
    if(!isInt(toEnglishNum(element.val()))) _ERROR =true;

    if(_ERROR)
    {
        element.removeClass("valid-field").addClass("invalid-field");
        $('div[id=input_createFund_installment_invalid]').addClass("show-element");
        $('div[id=input_createFund_installment_valid]').removeClass("show-element");
    }else{
        element.removeClass("invalid-field").addClass("valid-field");
        $('div[id=input_createFund_installment_invalid]').removeClass("show-element");
        $('div[id=input_createFund_installment_valid]').addClass("show-element");
    }
    return _ERROR;
}

function form_resetErrors()
{
    $('.valid-field').each(function(i, obj) {
        $(obj).removeClass("valid-field").removeClass("show-element");
    });
    $('.invalid-field').each(function(i, obj) {
        $(obj).removeClass("invalid-field").removeClass("show-element");
    });
    $('.validation').each(function(i, obj) {
        $(obj).removeClass("invalid-field").removeClass("show-element");
    });
}

function form_userSignUp()
{
    var _ERROR = false;

    form_resetErrors();

    var inputName = $('input[id=input_userSignUpName]');
    var inputPhone = $('input[id=input_userSignUpMobile]');
    var inputPassword1 = $('input[id=input_userSignUpPassword1]');
    var inputPassword2 = $('input[id=input_userSignUpPassword2]');

    var formData = {
        'name'  : inputName.val(),
        'mobile'  : correctPhone(inputPhone.val()),
        'password'  : inputPassword1.val(),
        'password2'  : inputPassword2.val(),
    };
    
    var passwordError = input_checkPassword(inputPassword1,inputPassword2);
    var phoneError = input_checkPhone(inputPhone);
    var nameError = input_checkName(inputName);

    if(nameError || passwordError || phoneError)
    {
        _ERROR = true;
    }


    if(!_ERROR)
    {
        var userData = {
            'name'  : formData['name'],
            'mobile'  : formData['mobile'],
            'password'  : formData['password']
        };

        var params = {userData: userData};
        var action = 'form_userSignUp';
    
        result = ajax_handler(action , params);
    }
}

function form_userLogin()
{
    var inputPhone = $('input[id=input_userLoginMobile]');
    var inputPassword = $('input[id=input_userLoginPassword]');

    var formData = {
        'mobile'  : correctPhone(inputPhone.val()),
        'password'  : toEnglishNum(inputPassword.val(),true)
    };

    console.log(formData);
    
    var params = {userData: formData};
    var action = 'form_checkUserLogin';
    var result = ajax_handler(action , params);
    console.log(result);

    if(!result['error'])
    {
        var resultData = jQuery.parseJSON(result['data']);
    
        if(resultData.error)
        {
            // $('div[id=form_userLogin_valid]').removeClass("show-element");
            // $('div[id=form_userLogin_invalid]').addClass("show-element");
        }
        else
        {
            // $('div[id=form_userLogin_valid]').addClass("show-element");
            // $('div[id=form_userLogin_invalid]').removeClass("show-element");
            pageRedirect('dashboard');
        }
    }
}

function form_userNewPassword()
{
    //console.log("NewPassword");
}

function form_createFund()
{
    var _ERROR = false;



    form_resetErrors();

    var inputFundName = $('input[id=input_createFund_fundName]');
    var inputInstallment = $('input[id=input_createFund_installment]');
    var inputStocksNumber = $('input[id=input_createFund_stocksNumber]');
    var inputCreatedById = $('input[id=input_createFund_createdById]');
    var inputCurrency = $('select[id=input_createFund_currency]');
    
    var formData = {
        'name'  : inputFundName.val(),
        'created_by_id'  : parseInt(inputCreatedById.val()),
        'created_at'  : + new Date(),
        'installment'  : parseInt(toEnglishNum(inputInstallment.val())),
        'stocks_number'  : parseInt(inputStocksNumber.val()),
        'status'  : 0,
        'currency'  : inputCurrency.val(),
    };

    console.log(formData);
    
    var nameError = input_checkFundName(inputFundName);
    var stocksError = input_checkStocksNumber(inputStocksNumber);
    var installmentError = input_checkInstallment(inputInstallment);
    
    if(nameError || stocksError || installmentError)
    {
        _ERROR = true;
        console.log(nameError);
        console.log(stocksError);
        console.log(installmentError);
    }

    if(!_ERROR)
    {
        var params = {fundData: formData};
        var action = 'form_createFund';
    
        result = ajax_handler(action , params);
        console.log(result);
        
        if(!result['error'])
        {
            var resultData = jQuery.parseJSON(result['data']);
        
            if(resultData.error)
            {
                $('div[id=form_createFund_valid]').removeClass("show-element");
                $('div[id=form_createFund_invalid]').addClass("show-element");
            }
            else
            {
                $('div[id=form_createFund_valid]').addClass("show-element");
                $('div[id=form_createFund_invalid]').removeClass("show-element");
                setTimeout(pageRedirect('dashboard'),3000);
            }
        }
    }
}

function getFundsCreatedByID(userID)
{
    var params = {id: userID};
    var action = 'getFundsCreatedByID';

    var funds=  [];

    result = ajax_handler(action , params);

    if(!result['error'])
    {
        var resultData = jQuery.parseJSON(result['data']);
        if(!resultData.error)
        {
            funds = jQuery.parseJSON(resultData.data);
            return funds;
        }
    }

    return null;

}

function form_searchFunds()
{
    form_resetErrors();

    var inputAdminMobile = $('input[id=input_searchFunds_adminMobile]');
    
    var user = input_checkAdminPhone(inputAdminMobile);
    if(user !== null)
    {
        var funds = getFundsCreatedByID(user['user_id']);
        if(funds !== null)
        {
            $('div[id=input_searchFunds_resultList]').addClass('show-element');
            var ul = $('div[id=input_searchFunds_resultList] ul');
            ul.html('');

            $.each(funds,function()
            {
                var params = {fundData: this}; // this
                var action = 'show_fund_search_items';
                result = ajax_handler(action , params);
                console.log(result);
                if(!result['error'])
                {
                    var resultData = jQuery.parseJSON(result['data']);
                    if(!resultData.error)
                    {
                        funds = resultData.data;
                        ul.prepend('<li>'+funds+'</li>');
                    }
                }
            });
            $('div[id=input_searchFunds_resultNotFound]').removeClass("show-element");
        }
        else
        {
            $('div#input_searchFunds_resultList').removeClass('show-element');
            $('div[id=input_searchFunds_resultNotFound]').addClass("show-element");
        }
    }
}

function form_requestStocks()
{
    var _ERROR = false;

    form_resetErrors();

    var inputStocksCount = $('input[id=input_requestStocks_stocksCount]');
    var inputAvailableStocks = $('input[id=input_requestStocks_availableStocksNumber]');
    var inputUserId = $('input[id=input_requestStocks_crntUserId]');
    var inputFundId = $('input[id=input_requestStocks_fundId]');
    
    var formData = {
        'stocksCount'  : toEnglishNum(inputStocksCount.val()),
        'availableStocks'  : toEnglishNum(inputAvailableStocks.val()),
        'user_id'  : toEnglishNum(inputUserId.val()),
        'fund_id'  : toEnglishNum(inputFundId.val()),
    };

    if( ! isInt(formData['stocksCount']) )
    {
        $('div[id=input_requestStocks_invalidNumber]').addClass("show-element");
        $('div[id=input_requestStocks_illegalNumber]').removeClass("show-element");
        _ERROR = true;
    }
    else if( formData['stocksCount'] > formData['availableStocks'] )
    {
        $('div[id=input_requestStocks_invalidNumber]').removeClass("show-element");
        $('div[id=input_requestStocks_illegalNumber]').addClass("show-element");
        _ERROR = true;
    }

    if(!_ERROR)
    {
        var params = {data: formData }; // formData
        var action = 'form_requestStocksFund';
        result = ajax_handler(action , params);
        if(!result['error'])
        {
            $('span[id=text-crnt-user-stocks-info]').html(result['data']);
            $('div[id=input_requestStocks_validResponse]').addClass("show-element");
        }

    }
    return;
}

function get_verifiedStocksOfFund(fund_id)
{
    params = {fund_id: fund_id};
    action = 'get_verifiedStocksOfFund';
    result = ajax_handler(action , params);
    if(result['error'])
    {
        swal_error();
        return null;
    }
    return result['data'];
}

function form_verifyStocks(act,user_id)
{

    var _ERROR = false;

    form_resetErrors();
 
    var inputCrntUserId = $('input[id=input_verifyStocks_crntUserId][data-userid='+user_id+']');
    var inputCrntStocks = $('input[id=input_verifyStocks_currentStocksRequest][data-userid='+user_id+']');
    var inputFundId = $('input[id=input_verifyStocks_fundId][data-userid='+user_id+']');
    var inputIsVerified = $('input[id=input_verifyStocks_isVerified][data-userid='+user_id+']');
    var inputStockCount = $('input[id=input_verifyStocks_stockCountEdit][data-userid='+user_id+']');
    var inputInstallment = $('input[id=input_verifyStocks_inputInstallment]');

    isVerified = toEnglishNum(inputIsVerified.val());
    var verifiedStocksOfFund = toEnglishNum(get_verifiedStocksOfFund(toEnglishNum(inputFundId.val())));
    
    var formData = {
        'crnt_user_id'  : toEnglishNum(inputCrntUserId.val()),
        'fund_id'  : toEnglishNum(inputFundId.val()),
        'user_id'  : toEnglishNum(user_id),
        'installment'  : toEnglishNum(inputInstallment.val()),
        'stocksCount'  : inputStockCount.hasClass('is_selected') && act!='reject' ? 
                            toEnglishNum(inputStockCount.val()) :
                            toEnglishNum(inputCrntStocks.val()),
    };


    if(formData['fund_id']  && formData['user_id'] && formData['crnt_user_id'] )
    {
        if( ! isInt(formData['stocksCount']) )
        {
            $('div[id=input_verifyStocks_invalidNumber][data-userid='+formData['user_id']+']')
                .addClass("show-element");
            $('div[id=input_verifyStocks_illegalNumber][data-userid='+formData['user_id']+']')
                .removeClass("show-element");
            _ERROR = true;
        }
    }
    else
    {
        _ERROR = true;
    }

    if(!_ERROR)
    {
        if(act=='reject')
        {
            formData.stocksCount = toEnglishNum(inputCrntStocks.val());
        }
        
        var params = {data: formData , act: act};
        var action = 'form_verifyStocks';
        result = ajax_handler(action , params);
        console.log(result);
        if(result['error'])
        {
            swal_error();
            return ;
        }

        $('ul[id="ul_fund_member_list_item"][data-userid='+formData['user_id']+']').
            html(result['data']);

        verifiedStocksOfFund = get_verifiedStocksOfFund(formData.fund_id);
        $('#span_verifyStocks_showVerifiedStocksOfFund').fadeOut().
            html(number_format(verifiedStocksOfFund)).fadeIn();
        $('#span_verifyStocks_calculatedLoanforVerifiedStocksOfFund').fadeOut().
            html(number_format(verifiedStocksOfFund*formData.installment)).fadeIn();
    }
}


function form_startFund(element)
{
    var _ERROR = false;

    console.log( $(element).attr("data-fundid") );
    fundId = parseInt($(element).attr("data-fundid"));
    var params = {fund_id: fundId};
    var action = 'get_verifiedStocksOfFund';
    result = ajax_handler(action , params);
    if(!result['error'] && isInt(result['data']))
    {
        var stocksNumber = result['data'];
        // var text_message = "<p class='text-small text-medium'>این صندوق تا کنون "+
        //                         stocksNumber+
        //                         " سهم دارد. آیا می خواهید با همین تعداد سهم شروع به کار کنید؟</p>"+
        //                         "<p class='text-xsmall text-medium border border-danger rounded m-1 p-1'>"+
        //                         "<i class='fas fa-exclamation-triangle text-large text-danger'></i>"+
        //                         "پس از شروع به کار عضوگیری صندوق غیرفعال خواهد شد"
        //                         "</p>"
        // var res = swal({
        //             title: "شروع به کار صندوق",
        //             text: 'آیا می خواهید با همین تعداد سهم شروع به کار کنید؟',

        //             type: 'question',

        //             focusConfirm: false,
                    
        //             showCancelButton: true,
        //             confirmButtonColor: '#3085d6',
        //             cancelButtonColor: '#888',
        //             confirmButtonText: 'بله',
        //             cancelButtonText: 'نه پشیمون شدم !'
        //             })
        //             .then((result) => {
        //                 if (result.value) {

                            var params = {fund_id: fundId , stocks_number: stocksNumber};
                            var action = 'form_startFund';
                            result2 = ajax_handler(action , params);

                            console.log(result2);

                            if(result2['error'] || !result2['data'])
                            {
                                swal_error();
                                return;
                            }
                            else
                            {
                                swal({

                                    title: "<h5 class='text-normal text-bold'>فعالیت صندوق آغاز شد</h5>",
                                    html:  "می توانید اولین قرعه کشی را انجام دهید !",
                
                                    type: 'success',
                
                                    focusConfirm: false,
                                    
                                    showCancelButton: false,
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: "خـب!",
                
                                }).then(() =>{location.reload();});
                            }
                        // } 
                    // });
    
        console.log(res);
    }
    else{
        swal_error();
        return;
    }
    
    // console.log(_ERROR);

    // if(_ERROR)
    // {
    // }
}

function form_setLotteryDate(target)
{
    var uniqueId = (target['dataset']['uniqueId']);

    var date = dateSelector( $('#pds_container_'+uniqueId) );
    console.log(date);
    if(date !== false)
    {
        var fund_id = target['dataset']['fundId'];
        var loan_number = target['dataset']['loanNumber'];
        var params = {
            date: date , 
            fund_id : fund_id , 
            loan_number : loan_number 
        };
        var action = 'form_setLotteryDate';
        result = ajax_handler(action , params);
        console.log(result);
        if(!result['error'])
        {
            swal_success('reload');
        }
        else
        {
            swal_error();
        }

    }
}

function get_persianDateSelector(element)
{
    var uniqueId = $(element).attr('data-unique-id');
    date = {
        'dd' : toEnglishNum( $('#pds_day_'+uniqueId).val() ),
        'mm' : toEnglishNum( $('#pds_month_'+uniqueId).val() ),
        'yyyy' : toEnglishNum( $('#pds_year_'+uniqueId).val() ),
    };
    console.log(date);
    
    if( date.dd && date.mm && date.yyyy )
        return date;
    else
        return false;
    
}

function dateSelector(element)
{
    date = get_persianDateSelector(element);
    
    if(date!=false)
    {
        var uniqueId = $(element).attr('data-unique-id');
    
        var params = {date: date};
        var action = 'get_days_difference_to_today';
        result = ajax_handler(action , params);
        console.log(result);
        if(!result['error'])
        {
            var resultData = jQuery.parseJSON(result['data']);
            $('#pds_difference_'+uniqueId).html(resultData['text']).fadeIn(1000);
        }
        date.diff = resultData.diff;
        return date;
    }
    else
    {
        $('#pds_difference_'+uniqueId).html('').fadeOut(1000);
    }
    return false;
}

function form_payDebt(element)
{
    var debt = jQuery.parseJSON ( $(element).attr('data-debt') ) ;
    var form_id =  ( $(element).attr('data-form-id') ) ;
    var action_id = debt.action_id;

    var input_trackingCode = $('#form_payDebt_trackingCode_actionid'+action_id+'[data-form-id='+form_id+']');
    var input_comment = $('#form_payDebt_comment_actionid'+action_id+'[data-form-id='+form_id+']');
    var input_date = $('#pds_container_form_payDebt_date_actionid'+action_id+'_'+form_id);
    // var input_persianDate = $( $(element).closest("li").siblings("[roll='show_pds']") ).find("div[name='date_selector']");

    
    var pay = {
        'trackingCode' : $.trim( input_trackingCode.val() ),
        'comment' : input_comment.val(),
        'amount' : Math.abs(debt.amount),
        'lot_id' : debt.lot_id,
        'user_id' : debt.user_id,
        'pay_date' : dateSelector(input_date),
    };
    console.log(pay);

    var pds_invalid_feedback = $('#pds_invalid_form_payDebt_date_actionid'+action_id+'_'+form_id);

    if(!pay.pay_date || pay.pay_date.diff>0) pds_invalid_feedback.removeClass('fade').fadeIn();
    else pds_invalid_feedback.fadeOut();

    if(!pay.trackingCode) input_trackingCode.addClass('is-invalid');
    else input_trackingCode.removeClass('is-invalid');
    
    if(pay.trackingCode && pay.pay_date && pay.pay_date.diff<=0)
    {
        var params = {pay_action: pay , debt_action : debt};
        var action = 'form_payDebt';
        result = ajax_handler(action , params);
        
        console.log(result);
        
        if(!result['error'] && result['data']=='1')
        {
            swal_success('reload');
        }
        else
        {
            swal_error();
        }
    }
}

function form_runLot(element)
{
    var lot_id = $(element).attr('data-lotid');
    var stocks = jQuery.parseJSON($(element).attr('data-quallifiedstocks'));
    // console.log(lot_id)
    // console.log(stocks)

    var inputs = {
        'stocksQualified_all' : $('#form_runLot_stocksQualified_all[data-lotid='+lot_id+']').prop("checked"),
        'stocksQualified_payed' : $('#form_runLot_stocksQualified_payed[data-lotid='+lot_id+']').prop("checked"),
        'lotType_real' : $('#form_runLot_lotType_real[data-lotid='+lot_id+']').prop("checked"),
        'lotType_test' : $('#form_runLot_lotType_test[data-lotid='+lot_id+']').prop("checked"),
    }
    // console.log(inputs);

    var lotType = inputs.lotType_test ? 'test' : 'real';
    var quallifiersType = inputs.stocksQualified_all ? 'all' : 'payed';
    stocksQualified = stocks[quallifiersType]
    var stocks_number = stocksQualified.length;
    var rand_index = Math.floor(Math.random() * stocks_number);
    // console.log(winner_index);
    // console.log(stocksQualified[winner_index]);
    var winner = stocksQualified[rand_index];

    switch(lotType)
    {
        case 'test':
            swal({
                title: "قرعه کشی آزمایشی",
                text: "سهم شماره " + winner.stock_id +" متعلق به "+ winner.name + " برنده شد ! ",
                icon: "success",
                buttons: "خـب!",
                dangerMode: false,
            });
        break;
        case 'real':

        swal({
		    title: "آیا مطمئن هستید؟",
		    text: "با اجرای قرعه کشی نام برنده در سیستم ثبت می شود و اطلاعات قرعه کشی قابل تغییر نخواهد بود",
		    icon: "warning",
		    buttons: {
                cancel: {
                    text: "لفو عملیات",
                    value: null,
                    visible: true,
                    className: "",
                    closeModal: false,
                },
                confirm: {
                    text: "اجرای قرعه کشی",
                    value: true,
                    visible: true,
                    className: "",
                    closeModal: false
                }
		    }
		})
		.then((isConfirm) => {
		    if (isConfirm) {  
                var params = {winner: winner , lot_id : lot_id};
                var action = 'form_runLot';
                result = ajax_handler(action , params);
                console.log(result);
                var has_error = result['error'] || (jQuery.parseJSON(result['data'])).error;
                if(!has_error)
                {
                    swal("قرعه کشی انجام شد!",
                     "سهم شماره " + winner.stock_id +" متعلق به "+ winner.name + " برنده شد ! ", 
                     "success").then(result =>
                                    {
                                        location.reload();
                                    });
                }
                else
                {
                    swal_error();
                }
                        
		    } else {
		        swal("لغو گردید", "قرعه کشی انجام نشد", "error");
		    }
		});

        break;
    }

}

function form_verifyPayments(element,func)
{
    // console.log($(element).attr('id') );
    var payment = jQuery.parseJSON( $(element).attr('data-action') );
    var collapseId = $(element).attr('data-collapse-id');
    console.log(func);
    // console.log(payment);
    var comment = "پیام آزمایشی مدیر هنگام تایید";
    if(func != payment.verify_status)
    {
        // AJAX
        var params = {
                    payment: payment , 
                    verify_status : func , 
                    comment : comment , 
                    collapse_id : collapseId
                    };
        var action = 'form_verifyPayments';
        result = ajax_handler(action , params);
        // console.log(result);
        var hasError = result['error'] || (result['data']==null) ;
        console.log(hasError);
        if(!hasError)
        {
            console.log($('#'+collapseId).parent());
            $('#'+collapseId).parent().html($(result['data']).html() );
            
            $('#heading_'+collapseId+' i').fadeOut().removeClass();

            if(func == 1) $('#heading_'+collapseId+' i').addClass('ft-check-circle').fadeIn();
            else if(func == -1) $('#heading_'+collapseId+' i').addClass('ft-minus-circle').fadeIn();
        }
    }

}


function form_createProject()
{
    var title = $('#input_projectTitle').val();
    console.log(title);

            // AJAX
    var params = {
        title: title , 
        };
    var action = 'form_createProject';
    result = ajax_handler(action , params);
    // console.log(result);
    if(!result['error'] && result['data']!= null )
    {
        var proj_id = result['data'];
        pageRedirect('project/edit/'+proj_id);
    }

}

function form_createParameter()
{
    var caption = $('#input_parameterCaption').val();
    var unit = $('#input_parameterUnit').val();

    // AJAX
    var params = {
        caption: caption , 
        unit: unit , 
        };
    var action = 'form_createParameter';
    result = ajax_handler(action , params);
    // console.log(result);
    if(!result['error'] && result['data']!= null )
    {
        // var proj_id = result['data'];
        location.reload();
    }

}

function form_addParamToProj(element)
{
    var param_id = $('#select-params').val();
    var proj_id = $(element).data('proj-id');
    console.log(param_id);
    console.log(proj_id);
    
    var params = {
        param_id: param_id , 
        proj_id: proj_id , 
    };
    var action = 'form_addParamToProj';
    result = ajax_handler(action , params);
    console.log(result);

    if(!result['error'])
    {
        if(result['data'] == "-1")
        {
            swal({
                title: "بروز خطا",
                text: "این پارامتر قبلا اضافه شده !",
                icon: "error",
                buttons: "خـب!",
                dangerMode: true,
            });    
        }
        else if(result['data']!=null )
        {
            $('#param-list').append( $(result['data']) );
        }
    }

}

function delete_object(element)
{
    var obj = $(element).data('delete-obj');
    var id = $(element).data('delete-id');

    var params = {
        obj: obj , 
        id: id , 
    };
    console.log(params);
    var action = 'delete_object';
    result = ajax_handler(action , params);
    console.log(result);
}