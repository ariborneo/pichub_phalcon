<?php

class Helpers extends CustomModel
{

    static function check_file_type($type){
        return in_array($type, array(
            "image/jpeg",
            "image/png",
            "image/gif"
        ));
    }

    static function create_folder($type){
        $public_root = "../public/";
        $y = date("y");
        $m = date("m");
        $d = date("d");
        if(!is_dir($public_root."pic_".$type."/".$y)){
            mkdir($public_root."pic_".$type."/".$y, 0770);
        }
        if(!is_dir($public_root."pic_".$type."/".$y."/".$m)){
            mkdir($public_root."pic_".$type."/".$y."/".$m, 0770);
        }
        if(!is_dir($public_root."pic_".$type."/".$y."/".$m."/".$d)){
            mkdir($public_root."pic_".$type."/".$y."/".$m."/".$d, 0770);
        }
        return "pic_".$type."/".$y."/".$m."/".$d."/";
    }

    static function create_folders()
    {
        return array(
            "b" => Helpers::create_folder("b"),
            "c" => Helpers::create_folder("c"),
            "s" => Helpers::create_folder("s")
        );
    }

    static function generatekey(){
        return md5(uniqid(rand(),true));
    }

    static function getext($filename){
        return substr(strrchr($filename, '.'), 1);
    }

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

    static function sha256($text)
    {
        $salt = "";
        return hash("sha256", $salt . $text);
    }

}