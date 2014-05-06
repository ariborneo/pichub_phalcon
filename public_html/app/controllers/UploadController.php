<?php

class UploadController extends ControllerBase
{

    public function indexAction()
    {

        foreach ($this->request->getUploadedFiles() as $file) {

            if(Helpers::check_file_type($file->getType())){

                $folders = Helpers::create_folders();

                $imgcode = Helpers::generatekey();
                while(Images::findFirst("code='".$imgcode."'")){
                    $imgcode = Helpers::generatekey();
                };
                $ext = Helpers::getext($file->getName());
                $filename = $imgcode . "." .  $ext;

                $file->moveTo('../'. $folders["b"] . $filename);

                $image = new Imgproc("../". $folders["b"] . $filename);
                $image->resize(200);
                $image->save('../'. $folders["s"] . $filename);

                $image = new Imgproc("../". $folders["b"] . $filename);
                $image->alter_crop(100);
                $image->save('../'. $folders["c"] . $filename);

                $album = $this->request->getPost("album");
                if(!($album > 0 && Albums::findFirst("user=".$this->user->id)))
                {
                    $album = 0;
                }

                $img = new Images();
                $img->code = $imgcode;
                $img->ext = $ext;
                $img->opis = $this->request->getPost("opis");
                $img->user = $this->user->id;
                if($img->user == "") $img->user = 0;
                $img->ip = $this->request->getClientAddress();
                $img->time = time();
                $img->views = 0;
                $img->album = $album;
                $img->likes = 0;
                $img->comments = 0;
                $img->save();

                if($img->album > 0){
                    $album = Albums::findFirst($album);
                    ++$album->count;
                    $album->update();
                }

            }

        }

        $this->response->redirect("show/".$imgcode);

    }

}