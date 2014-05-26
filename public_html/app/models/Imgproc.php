<?php

class Imgproc extends Phalcon\Image\Adapter\GD
{

    public function alter_crop($size)
    {
        $w = $this->_width;
        $h = $this->_height;
        if($h > $w){
            $o_x = 0;
            $o_y = round(($h-$w)/2);
            $this->crop($w, $w, $o_x, $o_y);
        }else{
            $o_x = round(($w-$h)/2);
            $o_y = 0;
            $this->crop($h, $h, $o_x, $o_y);
        }
        $this->resize($size);
    }

    public function from_string($data)
    {
        $this->image = imagecreatefromstring($data);
    }

}