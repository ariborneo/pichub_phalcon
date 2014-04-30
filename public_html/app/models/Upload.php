<?php

class Upload extends \Phalcon\Mvc\Model
{

    public function check_type($type){
        return in_array($type, array(
            "image/jpeg",
            "image/png",
            "image/gif"
        ));
    }

    public function create_folders($type){
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

    public function generatekey(){
        return md5(uniqid(rand(),true));
    }

    public function getext($filename){
        return substr(strrchr($filename, '.'), 1);
    }

}
