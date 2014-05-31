<?php

class Imgproc extends Phalcon\Image\Adapter\GD
{

    public function alter_crop($size)
    {
        $w = $this->_width;
        $h = $this->_height;
        if($h > $w)
        {
            $o_x = 0;
            $o_y = round(($h-$w)/2);
            $this->crop($w, $w, $o_x, $o_y);
        }
        else
        {
            $o_x = round(($w-$h)/2);
            $o_y = 0;
            $this->crop($h, $h, $o_x, $o_y);
        }
        $this->resize($size);
        return $this->_image;
    }

    public function from_string($data)
    {
        $this->image = imagecreatefromstring($data);
        return $this->_image;
    }

    public function corner_radius($radius)
    {
        $rate = 5;
        $w = $this->_width;
        $h = $this->_height;
        imagealphablending($this->_image, false);
        imagesavealpha($this->_image, true);
        $rs_radius = $radius * $rate;
        $rs_size = $rs_radius * 2;
        $corner = imagecreatetruecolor($rs_size, $rs_size);
        imagefill($corner, 0, 0, 0xFFFFFF);
        $trans = imagecolorallocate($corner, 255, 255, 255);
        imagecolortransparent($corner, $trans);
        $positions = array(
            array(0, 0, 0, 0),
            array($rs_radius, 0, $w-$radius, 0),
            array($rs_radius, $rs_radius, $w-$radius, $h-$radius),
            array(0, $rs_radius, 0, $h-$radius),
        );
        foreach ($positions as $pos)
        {
            imagecopyresampled($corner, $this->_image, $pos[0], $pos[1], $pos[2], $pos[3], $rs_radius, $rs_radius, $radius, $radius);
        }
        $i =- $rs_radius;
        $y2 =- $i;
        $r_2 = $rs_radius * $rs_radius;
        for (; $i <= $y2; $i++)
        {
            $y = $i;
            $x = sqrt($r_2 - $y * $y);
            $y += $rs_radius;
            $x += $rs_radius;
            imageline($corner, $x, $y, $rs_size, $y, $trans);
            imageline($corner, 0, $y, $rs_size - $x, $y, $trans);
        }
        foreach ($positions as $pos){
            imagecopyresampled($this->_image, $corner, $pos[2], $pos[3], $pos[0], $pos[1], $radius, $radius, $rs_radius, $rs_radius);
        }
        imagedestroy($corner);
        return $this->_image;
    }

    function filter($filter, $value = null)
    {
        if($filter=="grey"){
            imagefilter($this->_image, IMG_FILTER_GRAYSCALE);
        }elseif($filter=="red"){
            imagefilter($this->_image, IMG_FILTER_COLORIZE, 255, 0, 0);
        }elseif($filter=="green"){
            imagefilter($this->_image, IMG_FILTER_COLORIZE, 0, 255, 0);
        }elseif($filter=="blue"){
            imagefilter($this->_image, IMG_FILTER_COLORIZE, 0, 0, 255);
        }elseif($filter=="negate"){
            imagefilter($this->_image, IMG_FILTER_NEGATE);
        }elseif($filter=="light"){
            imagefilter($this->_image, IMG_FILTER_BRIGHTNESS, $value);
        }elseif($filter=="blur"){
            imagefilter($this->_image, IMG_FILTER_GAUSSIAN_BLUR);
        }elseif($filter=="contrast"){
            imagefilter($this->_image, IMG_FILTER_CONTRAST, $value);
        }elseif($filter=="smooth"){
            imagefilter($this->_image, IMG_FILTER_SMOOTH, $value);
        }elseif($filter=="sepia"){
            imagefilter($this->_image,IMG_FILTER_GRAYSCALE);
            imagefilter($this->_image,IMG_FILTER_BRIGHTNESS, -30);
            imagefilter($this->_image,IMG_FILTER_COLORIZE, 90, 55, 30);
        }
        imagesavealpha($this->_image, true);
        return $this->_image;
    }

    public function alter_resize($type, $value)
    {
        $w = $this->_width;
        $h = $this->_height;
        if($type == 1)
        {
            $width = $value;
            if($width < $w)
            {
                $height = round($width / $w * $h);
            }
        }
        elseif($type == 2)
        {
            $height = $value;
            if($height < $h)
            {
                $width = round($height / $h * $w);
            }
        }
        elseif($type == 3)
        {
            $percents = $value;
            if($percents < 100)
            {
                $height = round($h / 100 * $percents);
                $width = round($w / 100 * $percents);
            }
        }
        if(isset($width) && isset($height))
        {
            $im = imagecreatetruecolor($width, $height);
            imagefill($im, 0, 0, 0xFFFFFF);
            $white = imagecolorallocate($im, 255, 255, 255);
            imagecolortransparent($im, $white);
            imagecopyresampled($im, $this->_image, 0, 0, 0, 0, $width, $height, $w, $h);
            $this->_image = $im;
        }
        return $this->_image;
    }

}