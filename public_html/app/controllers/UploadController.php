<?php

class UploadController extends ControllerBase
{

    public function indexAction()
    {

        $error = "";

        if($this->request->isPost() && count($this->request->getUploadedFiles()) > 0)
        {

            $file = current($this->request->getUploadedFiles());

            if($file->getSize() / 1000000 > 10) $error = "Wrong size";
            elseif(!Helpers::check_file_type($file->getType())) $error = "Wrong type";

            if(!$error){

                $folders = Helpers::create_folders();

                $imgcode = Helpers::generatekey();
                while(Images::findFirst("code='".$imgcode."'")){
                    $imgcode = Helpers::generatekey();
                };
                $ext = Helpers::getext($file->getName());
                $filename = $imgcode . "." .  $ext;

                $image = new Imgproc($file->getTempName());
                $image->save('../public/'. $folders["b"] . $filename);
                $image->resize(200);
                $image->save('../public/'. $folders["s"] . $filename);
                $image->alter_crop(100);
                $image->save('../public/'. $folders["c"] . $filename);

                $album = $this->request->getPost("album");
                if(!($album > 0 && Albums::findFirst(array("id=?0 and user=?1", "bind" => array($album, $this->user->id)))))
                {
                    $album = 0;
                }

                $img = new Images();
                $img->assign(array(
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
                    "private" => 0
                ));
                $img->save();

                if($img->album > 0){
                    $album = Albums::findFirst($album);
                    $album->increase("count");
                }

            }

        }
        else
        {
            $error = "No post or file";
        }

        if($error)
        {
            echo json_encode(array(
                "status" => "error",
                "message" => $error
            ));
        }
        else
        {
            echo json_encode(array(
                "status" => "success",
                "code" => $imgcode,
                "c_path" => "/" . $folders["c"] . $filename,
                "filesize" => filesize('../public/'. $folders["b"] . $filename)
            ));
        }

        exit;

    }

}