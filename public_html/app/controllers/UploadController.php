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

                $image = new Imgproc($file->getTempName());
                $image->save('../'. $folders["b"] . $filename);
                $image->resize(200);
                $image->save('../'. $folders["s"] . $filename);
                $image->alter_crop(100);
                $image->save('../'. $folders["c"] . $filename);

                $album = $this->request->getPost("album");
                if(!($album > 0 && Albums::findFirst(array("id=?0 and user=?1", "bind" => array($album, $this->user->id)))))
                {
                    $album = 0;
                }

                $img = new Images();
                $img->save(array(
                    "code" => $imgcode,
                    "ext" => $ext,
                    "opis" => $this->request->getPost("opis"),
                    "user" => $this->user->id,
                    "ip" => ip2long($this->request->getClientAddress()),
                    "time" => time(),
                    "views" => 0,
                    "album" => $album,
                    "likes" => 0,
                    "comments" => 0,
                ));

                if($img->album > 0){
                    $album = Albums::findFirst($album);
                    $album->increase("count");
                }

            }

        }

        $this->response->redirect("show/".$imgcode);

    }

}