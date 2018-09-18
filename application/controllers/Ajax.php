<?php

class Ajax extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // $this->load->model('users_model');
    }

    public function index()
    {
        if( $this->input->post() !== NULL )
        {

            extract($this->input->post());
            if(isset($action) && isset($params))
            {

                $arguments = [];
                foreach($params as $key => $val)
                {
                    $arguments[$key] = $val;
                }
                $function_name = $action;


                $this->$function_name($arguments);
            }
        }
    }

    private function search($args)
    {
        $words =  [
            'amir','meghdad','hosein','reza','fateme','jalal','ghasem','dariush','sohrab'
        ];

        extract($args);
        
        $index = [];
        if(strlen($search_text)>0){
            foreach($words as $i => $word)
            {
                $is_in_word = strpos($word , $search_text);
                if($is_in_word!==FALSE){
                    $index[] = $i;
                }
            }
            
            echo "suggestion: ".$search_text."<br/>";
            echo '<ul>';
            foreach($index as $i=>$d)
            {
                echo "<li>".$words[$d]."</li>";
            }
            if(sizeof($index)==0)
            {
                echo "<li> Not Found! </li>";
            }
            echo '</ul>';
        
        
        }
    }

    private function open_message($args)
    {

        $messages = [];
        $messages['danger']['title'] = 'New Danger message! ';
        $messages['danger']['message'] = 'There is a new DANGER !!';

        $messages['warning']['title'] = 'New warning message! ';
        $messages['warning']['message'] = 'There is a new warning !!';

        $messages['info']['title'] = 'New info message! ';
        $messages['info']['message'] = 'There is a new info !!';

        $messages['success']['title'] = 'New success message! ';
        $messages['success']['message'] = 'There is a new success !!';

        extract($args);

        $title = $messages[$type]['title'];
        $message = $messages[$type]['message'];

        // print_r($args);
        
        $show_message = 
        '<div class="message '
        .$type
        .'"><b class="close-btn" onclick="close_message(this)">&times</b><strong>'
        .$title
        .'</strong><em>'
        .$message
        .'</em></div>';

        echo $show_message;
    }

    private function form_userSignUp($args)
    {
        extract($args);  
        extract($userData);  

        $result=[];

        $result['error'] = $this->users_model->add_user($userData);
        $result['data'] = [];

        echo json_encode($result);

    }

    private function form_checkNewMobile($args)
    {
        extract($args);  

        $result=[];
        
        $isNew = $this->users_model->checkNewMobile($phone);

        $result['error'] = $isNew ? FALSE : TRUE;
        $result['data'] = $phone;

        echo json_encode($result);
    }

    private function form_checkUserLogin($args)
    {
        extract($args);  
        extract($userData);  

        $result=[];
        
        $userResult = $this->users_model->checkUserLogin($mobile,$password);
        
        $result['error'] = $userResult===FALSE ? TRUE : FALSE;
        $result['data'] = json_encode($userResult);
        
        echo json_encode($result);
        $this->session->sess_regenerate(TRUE);
        $this->session->set_userdata('user_login',$userResult);
    }

    private function form_createFund($args)
    {
        extract($args);  
        extract($fundData);  

        $result=[];
        
        $fundResult = $this->funds_model->add_fund($fundData);
        
        $result['error'] = $fundResult===FALSE ? TRUE : FALSE;
        $result['data'] = json_encode($fundResult);
        
        echo json_encode($result);
        // $this->session->sess_regenerate(TRUE);
        // $this->session->set_userdata('user_login',$userResult);
    }

    private function getFundsCreatedByID($args)
    {
        extract($args);  

        
        $result=[];
        
        $fundsResult = $this->funds_model->get_funds_created_by_id($id);
        
        $result['error'] = sizeof($fundsResult)==0 ? TRUE : FALSE;
        $result['data'] = sizeof($fundsResult)==0 ? NULL : json_encode($fundsResult);

        echo json_encode($result);
    }

    private function form_checkAdminPhone($args)
    {
        extract($args);  
        $result=[];

        $userResult = $this->users_model->get_user_by_mobile($phone);
        if($userResult === NULL)
        {
            $result['error'] = TRUE;
            $result['data'] = 'nothing found!';
        }
        else
        {
            $res['user_id'] = $userResult['user_id'];
            $result['error'] = FALSE;
            $result['data'] = json_encode($res);
        }
        
        echo json_encode($result);

    }
    
    private function buy_stock($fund_id,$user_id,$count,$is_verified = '0')
    {
        // get current stocks of user in this fund
        $stock = $this->stocks_model->stock;
        $crnt_stocks = sizeof($this->stocks_model->get_stocks_of_user_of_fund($user_id,$fund_id));
        if($count == $crnt_stocks && $is_verified == '0'){
            return;
        }
        elseif($crnt_stocks !== 0)
        {
            // delete current stocks of user in this fund
            $this->stocks_model->delete_stocks_of_user_of_fund($user_id,$fund_id);
        }
        // add requested stocks of user in this fund
        if($count != 0)
        {
            $stocks = [];
            $verifier_id = ($this->funds_model->get_fund_by_id($fund_id))['created_by_id'];

            for($i=0 ; $i<$count ; $i++)
            {
                $stock['user_id'] = $user_id;
                $stock['fund_id'] = $fund_id;
                $stock['win_lot_id'] = 0;
                $stock['stock_name'] = $i+1;
                $stock['verifier_id'] = $verifier_id;
                switch($is_verified)
                {
                    // case NULL:
                    case '0':
                        $stock['is_verified'] = ($verifier_id == $user_id) ? '1' : '0';
                        break;
                    case '-1':
                    case '1':
                        $stock['is_verified'] = $is_verified;
                        break;
                }

                // $stock['is_verified'] = '1';
                $stocks[$i] = $stock;
            }

            $this->stocks_model->insert_stocks_batch($stocks);
        }
        return;
    }

    private function form_verifyStocks($args)
    {
        if(is_array($args)) 
        {
            extract($args);
        }
        if(is_array($data)) 
        {
            extract($data);
        }

        // print_r($args);
        // return;
        $is_verified = [];
        $is_verified ['verify'] = '1';
        $is_verified ['reject'] = '-1';
        $is_verified ['pending'] = '0';

        $this->buy_stock($fund_id,$user_id,$stocksCount,$is_verified[$act]);
        // echo 'hi';
        // return;

        $user_stocks = $this->stocks_model->get_stocks_of_user_of_fund($user_id,$fund_id);
        $user = $this->users_model->get_user_by_id($user_id);
        $fund_data = $this->funds_model->get_fund_by_id($fund_id);

        $stock_status = [];
        $stock_status['total'] = sizeof($user_stocks);
        $stock_status['is_verified'] = $user_stocks[0]['is_verified'];
        $stock_status['user_id'] = $user_id;

        show_stock_requests_list_items($stock_status,$fund_data,$user);
    }

    private function get_verifiedStocksOfFund($args)
    {
        if(is_array($args)) 
        {
            extract($args);
        }

        $verified_stocks = sizeof($this->stocks_model->get_verified_stocks_of_fund_id($fund_id));

        echo $verified_stocks;
    }
    
    private function form_startFund($args)
    {
        if(is_array($args)) 
        {
            extract($args);
        }

        $start = $this->funds_model->start_fund($fund_id,$stocks_number);

        if($start)
        {
            $done = TRUE;
        }
        else
        {
            $done = FALSE;
        }
        echo json_encode($done);
    }
    
    private function form_requestStocksFund($args)
    {
        if(is_array($args)) 
        {
            extract($args);
        }
        if(is_array($data)) 
        {
            extract($data);
        }
        

        $this->buy_stock($fund_id,$user_id,$stocksCount);

        $user_stocks = $this->stocks_model->get_stocks_of_user_of_fund($user_id,$fund_id);
        $fund_data = $this->funds_model->get_fund_by_id($fund_id);

        show_stocks_information_of_user($fund_data , $user_stocks);
    }
    
    private function show_fund_search_items($args)
    {

        if( is_array($args) ) extract($args);

        $result=[];

        $result['error'] = FALSE;
        $result['data'] = show_search_funds_list_items($fundData);

        echo json_encode($result);
    }

    private function get_days_difference_to_today($args)
    {
        if( is_array($args) ) extract($args);
        if( is_array($date) ) extract($date);

        $difference = days_to_today( persian2timestamp($yyyy,$mm,$dd) );
        echo json_encode($difference);
        die();
    }
    
    private function form_setLotteryDate($args)
    {
        if( is_array($args) ) extract($args);
        if( is_array($date) ) extract($date);

        $lot_model = new $this->lottery_model;
        $acc_model = new $this->accounting_model;

        $lottery = $lot_model->lottery;
        $debt = $acc_model->installment;
        $timestamp = persian2timestamp($yyyy,$mm,$dd);
        
        $lottery['fund_id'] = $fund_id;
        $lottery['loan_number'] = $loan_number;
        $lottery['date'] = $timestamp;
        
        $prev_lot_set = $lot_model->get_lotteries_of_fund($fund_id , $loan_number);
        if($prev_lot_set == NULL)
        {
            $lot_model->add_lottery($lottery);
            $lot_id = ($lot_model->get_lotteries_of_fund($fund_id , $loan_number))['lot_id'];

            $stocks = $this->stocks_model->get_distict_stocks_of_fund($fund_id);
            $fund = $this->funds_model->get_fund_by_id($fund_id);
            // $loan_amount = $fund['installment']*$fund['stocks_number'];
            foreach($stocks as $stock)
            {
                $debt['lot_id'] = $lot_id;
                $debt['user_id'] = $stock['user_id'];
                $debt['amount'] = - $fund['installment']*$stock['total'];
                $debt['insert_at'] = time()+Date('Z');
                
                $debt['verify_by_id'] = $fund['created_by_id'];
                $debt['verify_status'] = 1;


                $this->accounting_model->add_lottery_installments($debt);
            }
        }
        else
        {
            $lot_model->modify_lottery_date($prev_lot_set['lot_id'],$timestamp);
        }

    }

    private function form_payDebt($args)
    {
        if( is_array($args) ) extract($args); // pay_action , debt_action
        // if( is_array($pay) ) extract($pay);

        $installment['lot_id'] = $debt_action['lot_id'];
        $installment['user_id'] = $debt_action['user_id'];
        $pay_date = $pay_action['pay_date'];
        $installment['pay_date'] = persian2timestamp($pay_date['yyyy'],$pay_date['mm'],$pay_date['dd']);
        $installment['tracking_code'] = $pay_action['trackingCode'];
        $installment['comment'] = $pay_action['comment'];
        $installment['amount'] = $pay_action['amount'];
        $fund_id = ($this->lottery_model->get_lottery_by_id( $debt_action['lot_id'] ))['fund_id'];
        $installment['verify_by_id'] = $this->funds_model->get_fund_creator($fund_id);

        echo $this->accounting_model->pay_installment($installment);
    }

    private function form_runLot($args)
    {
        if( is_array($args) ) extract($args); // lot_id , winner

        $lot = $this->lottery_model->get_lottery_by_id($lot_id);
        if(is_admin_of_fund($lot['fund_id']))
        {
            $stock_id = $winner['stock_id'];
            // update lottery table
            $this->lottery_model->set_lottery_winner($lot_id,$stock_id);
            
            // check lottery table
            $lot = $this->lottery_model->get_lottery_by_id($lot_id);
            if( $lot['winner_stock_id'] == $stock_id )
            {
                // update stocks table
                $this->stocks_model->set_stock_win_lot($stock_id,$lot_id);

                // check stocks table
                $stock = $this->stocks_model->get_stock_by_id($stock_id);
                if($stock['win_lot_id'] == $lot_id)
                {
                    $user_id = $stock['user_id'];
                    $acc_model = new $this->accounting_model;
                    $loan_amount = $this->funds_model->get_fund_loan_amount($lot['fund_id']);
                    $loan = $acc_model->loan;
                    $loan['lot_id'] = $lot_id;
                    $loan['user_id'] = $user_id;
                    $loan['amount'] = $loan_amount;
                    $loan['insert_at'] = time()+Date('Z');
                    $loan['verify_by_id'] = $user_id;
                    $loan['verify_status'] = 1;
                    
                    // update accounting table
                    $this->accounting_model->add_lottery_loan($loan);

                    // check accounting table
                    $action = $this->accounting_model->get_lottery_loan($lot_id);
                    if($action['amount'] == $loan_amount && 
                        $action['type'] == $acc_model::loan_type && 
                        $action['user_id'] == $user_id)
                    {
                        // update funds table
                        $this->funds_model->set_fund_status_for_next_lot($lot['fund_id']);
                        if( ($lot['loan_number']+1) == ($this->funds_model->get_fund_by_id($lot['fund_id']))['status'] )
                        {
                            $result['error'] = false;
                            echo json_encode($result);
                            return;
                        }
                    }
                    // delete stock lot_id winner
                    $this->accounting_model->delete_action($action['action_id']);
                }
                // delete stock lot_id winner
                $this->stocks_model->set_stock_win_lot($stock_id,NULL);
            }
            // delete lot winner
            $this->lottery_model->set_lottery_winner($lot_id,NULL);
        }
        $result['error'] = true;
        echo json_encode($result);
        return;
    }

    private function form_verifyPayments($args)
    {
        if( is_array($args) ) extract($args); // payment , verify_status , collapse_data

        
        $lot = $this->lottery_model->get_lottery_by_id($payment['lot_id']);

        if(is_admin_of_fund($lot['fund_id']))
        {
            $action_id = $payment['action_id'];
            $details = array(
                'comment' => $comment,
                'date' => time()+date('Z'),
            );
            $this->accounting_model->verify_payments($action_id , $verify_status , $details);
            $action = $this->accounting_model->get_action_by_id($action_id);
            
            
            show_collapse_payments_details($action,$collapse_id,TRUE);
            return;
        }
        echo NULL;
        return;
    }

    private function persianDate($args)
    {
        if( is_array($args) ) extract($args); // payment , verify_status , collapse_data
        
        persian_date($timestamp,false,true);
    }
    

    // TimeLiner


    private function form_createProject($args)
    {
        if( is_array($args) ) extract($args); // payment , verify_status , collapse_data

        $created_by = get_loggedin_user('user_id');
        $proj_id = $this->projects_model->add_project($title,$created_by);
        echo $proj_id;

    }

    private function form_createParameter($args)
    {
        if( is_array($args) ) extract($args); 

        $param_id = $this->parameters_model->add_param($caption,$unit);
        echo $param_id;

    }

    private function form_addParamToProj($args)
    {
        if( is_array($args) ) extract($args); 

        $data = new $this->data_model;
        $data_id = $data->add_relation($param_id,$proj_id);
        if($data_id!= NULL && $data_id>0)
        {
            show_param_items_of_project($data->get($data_id));
        }
        elseif($data_id == -1)
        {
            echo $data_id;
        }
    }

    private function delete_object($args)
    {
        if( is_array($args) ) extract($args); 

        $result = $this->global_model->delete($obj,$id);
        echo $result;
    }
    
}