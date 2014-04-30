<?php

class UploadController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {

        $up = new Upload();

        foreach ($this->request->getUploadedFiles() as $file) {

            if($up->check_type($file->getType())){

                $b_folder = $up->create_folders("b");
                $c_folder = $up->create_folders("c");
                $s_folder = $up->create_folders("s");

                $imgcode = $up->generatekey();
                while(Images::findFirst("code='".$imgcode."'")){
                    $imgcode = $up->generatekey();
                };
                $ext = $up->getext($file->getName());
                $filename = $imgcode . "." .  $ext;

                $file->moveTo('../public/'. $b_folder . $filename);

                $image = new Phalcon\Image\Adapter\GD('../public/'. $b_folder . $filename);
                $image->resize(200);
                $image->save('../public/'. $s_folder . $filename);

                $image = new Phalcon\Image\Adapter\GD('../public/'. $b_folder . $filename);
                $w = $image->getWidth();
                $h = $image->getHeight();
                if($h > $w){
                    $o_x = 0;
                    $o_y = round(($h-$w)/2);
                    $image->crop($w, $w, $o_x, $o_y);
                }else{
                    $o_x = round(($w-$h)/2);
                    $o_y = 0;
                    $image->crop($h, $h, $o_x, $o_y);
                }
                $image->resize(100);
                $image->save('../public/'. $c_folder . $filename);

                $img = new Images();
                $img->code = $imgcode;
                $img->ext = $ext;
                $img->opis = "";
                $img->user = $this->session->get("user_id");
                if($img->user == "") $img->user = 0;
                $img->ip = $this->request->getClientAddress();
                $img->time = time();
                $img->views = 0;
                $img->save();

            }

        }

        $this->response->redirect("../");

    }

}