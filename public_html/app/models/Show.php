<?php

class Show extends \Phalcon\Mvc\Model
{

    static function getdirbydate($date){
        $date = date('Y-m-d H:i:s', $date);
        list($date) = preg_split("/ /", substr($date, 2), 5);
        $date = str_replace("-","/", $date);
        return $date.'/';
    }

    static function showdatetime($datetime){
        $datetime = date('Y-m-d H:i:s',$datetime);
        $month = array("января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря");
        list($date,$time) = explode(" ",$datetime);
        list($y,$m,$d) = explode("-",$date);
        $cur_y = date("Y");
        $cur_m = date("m");
        $cur_d = date("d");
        if($cur_y==$y && $cur_m==$m && $cur_d==$d){
            $date = 'Сегодня';
        }elseif(($cur_y==$y && $cur_m==$m && $cur_d-1==$d) || ( $cur_y==$y && $cur_m-1==$m && ($d==30 || $d==31) && $cur_d==1 ) || ( $cur_y-1==$y && $cur_m==12 && ($d==30 || $d==31) && $cur_d==1 ) ){
            $date = 'Вчера';
        }else{
            if(substr($d,0,1)=='0'){$d = substr($d,1);}
            $date = $d.' '.$month[$m-1];
            if($y<$cur_y){ $date .= ' '.$y; }
        }
        if(substr($time,0,1)=='0'){$time = substr($time,1);}
        return $date.' в '.substr($time,0,-3);
    }

}
