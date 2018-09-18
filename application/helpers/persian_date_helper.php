<?php

/*

پنجشنبه ۱۱ دی ۱۳۴۸
Thursday 1 January 1970

 (دورهٔ ۳۳ساله): ۱۲۸۰ - ۱۲۸۴ - ۱۲۸۸ - ۱۲۹۲ - ۱۲۹۶ - ۱۳۰۰ - ۱۳۰۴ - ۱۳۰۸
(دورهٔ ۳۳ساله): ۱۳۱۳ - ۱۳۱۷ - ۱۳۲۱ - ۱۳۲۵ - ۱۳۲۹ - ۱۳۳۳ - ۱۳۳۷ - ۱۳۴۱
(دورهٔ ۲۹ساله): ۱۳۴۶ - ۱۳۵۰ - ۱۳۵۴ - ۱۳۵۸ - ۱۳۶۲ - ۱۳۶۶ - ۱۳۷۰
(دورهٔ ۳۳ساله): ۱۳۷۵ - ۱۳۷۹ - ۱۳۸۳ - ۱۳۸۷ - ۱۳۹۱ - ۱۳۹۵ - ۱۳۹۹ - ۱۴۰۳. 

*/

const debug = FALSE;

function get_leaps($first_leap , $duration)
{
    $leap_years = [];
    for($y = 0 ; $y<$duration-1 ; $y++)
    {
        if( $y % 4 == 0)
        {
            $leap_years[] =  $y+$first_leap;
        }
    }
    return $leap_years;
}

function get_leap_years($type = 'fa')
{
    $first_leap = 1276;
    $leap_years = [];
    $periods = [33,33,33,33,33];
    for ($i = 1 ; $i<=sizeof($periods) ; $i++)
    {
        $get_leaps = get_leaps($first_leap, $periods[$i-1] );
        $leap_years = array_merge ($leap_years , $get_leaps );
        $first_leap =  $leap_years[sizeof($leap_years)-1]+5;            
    }
    
    if($type == 'ts')
    {
        foreach($leap_years as $i => $y)
        {
            $timestamp['ts'][$i] = persian2timestamp($y,12,30);
            $timestamp['fa'][$i] = $y;
        }
        unset($leap_years);
        $leap_years = $timestamp;
    }
    // print_r($leap_years);
    return $leap_years;
}

function get_month_days($is_leap_year = FALSE)
{
    $month_days = [0];
    for($i = 1 ; $i<=12 ; $i++)
    {
        if($i <=6)
        {
            $days = 31;
        }elseif( $i != 12)
        {
            $days = 30;
        }else
        {
            $days = 29;
        }
        $month_days[] = $days;
    }

    $month_days_sum = [0];
    for($i = 1 ; $i<=12 ; $i++)
    {
        $month_days_sum[] += $month_days_sum[$i-1] + $month_days[$i];
    }


    $month_days_sum_toend = $month_days_sum;
    $month_days_sum_toend[0] = $is_leap_year ? 366 : 365;
    for($i = 1 ; $i<=12 ; $i++)
    {
        $month_days_sum_toend[$i] = $month_days_sum_toend[0] - $month_days_sum[$i-1];
    }
    $month_days_sum_toend[] = 0;


    $result = [];
    $result['month_days'] = $month_days;
    $result['month_days_sum'] = $month_days_sum;
    $result['month_days_sum_toend'] = $month_days_sum_toend;

    return $result;
}

function persian_months($num = NULL)
{
    $months  = array(
        1 => 'فروردین',
        2 => 'اردیبهشت',
        3 => 'خرداد',
        4 => 'تیر',
        5 => 'مرداد',
        6 => 'شهریور',
        7 => 'مهر',
        8 => 'آبان',
        9 => 'آذر',
        10 => 'دی',
        11 => 'بهمن',
        12 => 'اسفند',
    );
    if($num)
    {
        return $months[$num];
    }
    return $months;
}

function get_unix_zero_time()
{
    $days_in_year = 365;

    $unix0time['year'] = 1348;
    $unix0time['month'] = 10;
    $unix0time['day']= 11;
    $month_days = get_month_days();
    $unix0time['days_to_start'] = $month_days['month_days_sum'][9] + 10 ; 
    $unix0time['days_to_end'] = $days_in_year-($unix0time['days_to_start']+1); 
    return $unix0time;
}

function persian2timestamp($yyyy,$mm,$dd , $hour=12,$min=0,$sec=0,$zone='Asia/Tehran')
{
    // leap yesrs
    $leap_years = get_leap_years();
    
    $is_leap_year = in_array($yyyy,$leap_years) ? TRUE : FALSE ;
    
    // month data
    $get_month = get_month_days($is_leap_year);
    
    $month_days = $get_month['month_days'];
    $month_days_sum = $get_month['month_days_sum'];
    $month_days_sum_toend = $get_month['month_days_sum_toend'];
    
    // set constraints
    if      ($yyyy>max($leap_years)) $yyyy = max($leap_years);
    elseif  ($yyyy<min($leap_years)) $yyyy = min($leap_years);
    
    if      ($mm>12) $mm = 12;
    elseif  ($mm<1) $mm = 1;


    $esfand30th = FALSE;
    if      ($is_leap_year && $mm==12 && $dd>29) 
    {
        $dd=30;
        $esfand30th = TRUE;
        echo !debug ? '' :  "\n leap year ";
    }
    elseif  ($dd>$month_days[$mm]) $dd = $month_days[$mm]; 
    elseif  ($dd<1) $dd = 1;
    
    // unix zero timestamp in persian calendar
    $unix0time = get_unix_zero_time();

    if($yyyy > $unix0time['year'])
    {
        $diff_y = $yyyy - ($unix0time['year']+1);
        $diff_m = $mm-1;
        $diff_d = $dd;
        
        $days_diff = ($diff_y*365)+($month_days_sum[$diff_m]+$diff_d)+$unix0time['days_to_end'];

        for($_y = $unix0time['year'] ; $_y <$yyyy ; $_y++)
        {
            if(in_array($_y, $leap_years)){
                $days_diff++;
            }
        }
    }

    elseif($yyyy < $unix0time['year'])
    {
        $diff_y = ($unix0time['year']-1) - $yyyy;
        $diff_m = $mm;
        $diff_d = $dd;
        $diff_d += $esfand30th ? 1 : 0;

        $days_diff = -( ($diff_y*365)+($month_days_sum_toend[$diff_m]-$diff_d ) + $unix0time['days_to_start'] );

        for($_y = $yyyy ; $_y <$unix0time['year'] ; $_y++)
        {
            if(in_array($_y, $leap_years)){
                $days_diff--;
            }
        }
    }
    else
    {
        $days_diff = ($month_days_sum[$mm-1] + $dd -1) - $unix0time['days_to_start'];
    }

    $timestamp = ($days_diff)*24*60*60;
    
    // $localzonetime = $mm > 6 ? 3.5 : 4.5;
    // $timestamp += (12*60*60);
    
    return $timestamp;
}

function timestamp2persian($timestamp,$zone='Asia/Tehran')
{
    
    $timestamp -= $timestamp%(60*60*24);
    // $timestamp += (12*60*60);
    
    $leap_years = get_leap_years('ts');

    foreach($leap_years['ts'] as $i => $ts)
    {
        if($timestamp < $ts)
        {
            $last_leap['ts'] = $leap_years['ts'][$i-1];
            $last_leap['fa'] = $leap_years['fa'][$i-1];
            $last_leap['ts'] -= $last_leap['ts']%(60*60*24);

            echo !debug ? '' : "\n".$last_leap['ts'];
            echo !debug ? '' : "\n".$last_leap['fa'];
            
            $diff['ss'] = $timestamp-$last_leap['ts'];
            echo !debug ? '' : "\n".$diff['ss'];
            
            echo !debug ? '' : floor($timestamp/(60*60*24));

            if(floor($timestamp/(60*60*24)) == $last_leap['ts']/(60*60*24) )
            {
                echo !debug ? '' : 'yes';
                $date['year'] = $last_leap['fa'];
                $date['month'] = 12;
                $date['day'] = 30;
                return $date;
            }
            else
            {

                $diff['dd'] = floor(($diff['ss'])/(60*60*24));
                
                $diff['yy'] = ceil($diff['dd'] / 365 );

                $days_in_year = $diff['dd'] % 365;

                echo !debug ? '' : "\n".$diff['dd'];
                
                echo !debug ? '' : "\n".$diff['yy'];

                echo !debug ? '' : "\n".$days_in_year;

                if($days_in_year ==0 )
                {
                    $days_in_year = 365;
                    $diff['yy']--;
                }

                $get_month = get_month_days();
                $month_days_sum = $get_month['month_days_sum'];

                $date = [];
                foreach($month_days_sum as $i=>$m)
                {
                    if($days_in_year <= $m)
                    {
                        $date['year'] = $last_leap['fa'] + $diff['yy'];
                        $date['month'] = $i;
                        $date['day'] = $days_in_year - $month_days_sum[$i-1];
                                
                        return $date;
                    }
                }
            }
        }
    }
    return NULL;
}

// print_r(get_leap_years('ts'));
function echodate($timestamp)
{
    $date = timestamp2persian($timestamp);
    echo $date['year'].'-'.$date['month'].'-'.$date['day'].'  -  '.$date['hour'].':'.$date['minute'].':'.$date['second'];
}

function persian_date($timestamp,$month_name = FALSE , $minimal = FALSE)
{
    $date = timestamp2persian($timestamp);
    if($minimal) $date['year'] -= 1300;

    if($month_name)
    {
        return $date['day'].' '.(persian_months())[$date['month']].' '.$date['year'];
    }
    else
    {
        return $date['year'].'/'.$date['month'].'/'.$date['day'];
    }
}

function persian_year()
{
    $date = timestamp2persian(time());
    return $date['year'];
}

// persian date selector
function show_pds($unique_id , $title = NULL ,  $input_date = NULL , $show_labels = TRUE)
{
    if(!$input_date) $date = timestamp2persian(time());
    else $date = $input_date;
    
    if($title)
    {
        ?>
        <div class="" style="display:inline">
            <?php echo $title; ?>
        </div>
        <?php
    }
    ?>

    <div id="pds_difference_<?php echo $unique_id; ?>" class="mr-2" style="display:inline"></div>

    <div id="pds_invalid_<?php echo $unique_id; ?>"  
        class="invalid-feedback fade hide" > 
        یک تاریخ صحیح انتخاب کنید
    </div>

    <div class="row" id="pds_container_<?php echo $unique_id; ?>" 
        data-unique-id="<?php echo $unique_id; ?>"
        onChange="dateSelector(this)" >
        
        <div class="col-xs-12 col-sm-12 col-lg-4 ">
            <div class="form-group">
                <?php if($show_labels){?> 
                    <label for="pds_day_<?php echo $unique_id; ?>">روز</label>
                <?php } ?>
                <select class="select2-rtl form-control" id="pds_day_<?php echo $unique_id; ?>">
                    <optgroup label="روز">
                        <?php 
                        for($i=1;$i<=31;$i++) 
                        {
                        ?>
                            <option value="<?php echo $i ?>" 
                                <?php echo $i==$date['day'] ? 'selected' : '';?> >
                                <?php echo $i ?>
                            </option>
                        <?php 
                        } 
                        ?>
                    </optgroup>
                </select>
            </div>
        </div>


        <div class="col-xs-12 col-sm-12 col-lg-4">
            <div class="form-group">
                <?php if($show_labels){?> 
                    <label for="pds_month_<?php echo $unique_id; ?>">ماه</label>
                <?php } ?>
                <select class="select2-rtl form-control" id="pds_month_<?php echo $unique_id; ?>">
                    <optgroup label="ماه">
                        <?php 
                        for($i=1;$i<=12;$i++) 
                        {
                        ?>
                            <option value="<?php echo $i ?>"
                                <?php echo $i==$date['month'] ? 'selected' : '';?> >
                                <?php echo persian_months($i);?>
                            </option>
                        <?php 
                        } 
                        ?>
                    </optgroup>
                </select>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-lg-4">
            <div class="form-group">
                <?php if($show_labels){?> 
                    <label for="pds_year_<?php echo $unique_id; ?>">سال</label>
                <?php } ?>
                <select class="select2-rtl form-control" id="pds_year_<?php echo $unique_id; ?>">
                    <optgroup label="سال">
                    <?php 
                        for($i=persian_year()-1;$i<=persian_year()+8;$i++) 
                        {
                        ?>
                            <option value="<?php echo $i; ?>"
                                <?php echo $i===$date['year'] ? 'selected' : '';?> >
                                <?php echo $i; ?>
                            </option>
                        <?php 
                        } 
                    ?>
                    </optgroup>
                </select>
            </div>
        </div>

    </div>


    <?php
}

function days_to_today($timestamp)
{
    $now = time() + Date('Z');
    $now -= $now % (24*60*60);
    $timestamp -= $timestamp % (24*60*60);

    $difference = ( $timestamp - $now ) / (24*60*60);


    $num =  abs( $difference );
    $sign = $difference!=0 ? $difference / abs( $difference ) : 0;

    if( $sign == 0 || $num == 0 )
    {
        $text = 'امروز';
    }
    elseif($sign > 0)
    {
        if($num == 1) $text = 'فردا';
        else $text = $num.' روز بعد';
    }
    else
    {
        if($num == 1) $text = 'دیروز';
        else $text = $num.' روز قبل';
    }
    
    $result['num'] = $num;
    $result['sign'] = $sign;
    $result['diff'] = $sign*$num;
    $result['text'] = $text;

    return $result;

}