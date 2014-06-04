<?php

class Helpers
{

    static function check_file_type($type)
    {
        return in_array($type, array(
            "image/jpeg",
            "image/png",
            "image/gif"
        ));
    }

    static function create_folder($type)
    {
        $public_root = "../public/";
        $y = date("y");
        $m = date("m");
        $d = date("d");
        if(!is_dir($public_root."pic_".$type."/".$y))
        {
            mkdir($public_root."pic_".$type."/".$y, 0770);
        }
        if(!is_dir($public_root."pic_".$type."/".$y."/".$m))
        {
            mkdir($public_root."pic_".$type."/".$y."/".$m, 0770);
        }
        if(!is_dir($public_root."pic_".$type."/".$y."/".$m."/".$d))
        {
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

    static function generatekey()
    {
        return md5(uniqid(rand(), true));
    }

    static function generateString($length)
    {
        $charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        $str = '';
        $count = strlen($charset);
        while ($length--)
        {
            $str .= $charset[mt_rand(0, $count-1)];
        }
        return $str;
    }

    static function getext($filename)
    {
        return substr(strrchr($filename, '.'), 1);
    }

    static function getdirbydate($date)
    {
        return date("y/m/d", $date) . "/";
    }

    static function showdatetime($datetime)
    {
        $month = array("января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря");
        list($date, $time) = explode(" ", date("Y-m-d H:i:s", $datetime));
        list($y, $m, $d) = explode("-", $date);
        $reldays = (mktime(0, 0, 0, $m, $d, $y) - mktime(0, 0, 0)) / 86400;
        if($reldays == 0)
        {
            $date = "Сегодня";
        }
        elseif($reldays == -1)
        {
            $date = "Вчера";
        }
        elseif($reldays == 1)
        {
            $date = "Завтра";
        }
        else
        {
            if(substr($d, 0, 1) == "0")
            {
                $d = substr($d, 1);
            }
            $date = $d." ".$month[$m-1];
            if($y < date("Y"))
            {
                $date .= " " . $y;
            }
        }
        if(substr($time, 0, 1) == "0")
        {
            $time = substr($time, 1);
        }
        return $date . " в " . substr($time, 0, -3);
    }

    static function sha256($text)
    {
        $salt = "";
        return hash("sha256", $salt . $text);
    }

}