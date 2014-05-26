<?php

class UploadController extends ControllerBase
{

    public $type, $file;

    public function indexAction()
    {

        $error = false;
        $pic_root = "../public/";
        $this->type = $this->get_type();

        if($this->type)
        {

            $type_num = $this->type_num();

            if($this->type == "main")
            {
                $this->file = current($this->request->getUploadedFiles());
                $error = $this->check_file(array(
                    "mime" => $this->file->getType(),
                    "size" => $this->file->getSize()
                ));
            }
            elseif($this->type == "url")
            {
                $info = $this->remote_file_info($this->request->getPost("url"));
                $error = $this->check_file($info);
                if(!$error)
                {
                    $data = @$this->file_get_contents_curl($this->request->getPost("url"));
                    if(!$data)
                    {
                        $error = "Not valid file";
                    }
                }
            }
            elseif($this->type == "wc")
            {
                $data = $this->base64_to_data($this->request->getPost("base64"));
            }

            if(!$error){

                $folders = Helpers::create_folders();
                $imgcode = $this->create_imgcode();
                $ext = $this->getext();
                $filename = $imgcode . "." . $ext;

                if($this->type == "wc" || $this->type == "url")
                {
                    file_put_contents($pic_root . $folders["b"] . $filename, $data);
                    $image = new Imgproc($pic_root . $folders["b"] . $filename);
                }
                else
                {
                    $image = new Imgproc($this->file->getTempName());
                }
                $image->save($pic_root . $folders["b"] . $filename);
                $image->resize(200);
                $image->save($pic_root . $folders["s"] . $filename);
                $image->alter_crop(100);
                $image->save($pic_root . $folders["c"] . $filename);

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
                    "private" => 0,
                    "up_type" => $type_num,
                    "editcode" => Helpers::generatekey()
                ));

                if($img->album > 0)
                {
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
            $this->echo_response(array(
                "status" => "error",
                "message" => $error
            ));
        }
        else
        {
            $this->echo_response(array(
                "status" => "success",
                "code" => $imgcode,
                "c_path" => "/" . $folders["c"] . $filename,
                "filesize" => filesize($pic_root . $folders["b"] . $filename),
                "type" => array($type_num, $this->type)
            ));
        }

    }

    protected function type_num()
    {
        $nums = array(
            "main" => "1",
            "url" => "2",
            "wc" => "3"
        );
        return $nums[$this->type];
    }

    protected function getext()
    {
        if($this->type == "main")
        {
            $ext = Helpers::getext($this->file->getName());
        }
        elseif($this->type == "url")
        {
            $ext = Helpers::getext($this->request->getPost("url"));
        }
        elseif($this->type == "wc")
        {
            $ext = "png";
        }
        return $ext;
    }

    protected function get_type()
    {
        if($this->request->isPost() && count($this->request->getUploadedFiles()) > 0)
        {
            $type = "main";
        }
        elseif($this->request->hasPost("url"))
        {
            $type = "url";
        }
        elseif($this->request->hasPost("base64"))
        {
            $type = "wc";
        }
        else
        {
            $type = false;
        }
        return $type;
    }

    protected function check_file($info)
    {
        if($info["size"] / 1000000 > 10)
        {
            $error = "Wrong size";
        }
        elseif(!Helpers::check_file_type($info["mime"]))
        {
            $error = "Wrong type";
        }
        else
        {
            $error = false;
        }
        return $error;
    }

    protected function create_imgcode()
    {
        $imgcode = Helpers::generatekey();
        while(Images::count("code='".$imgcode."'") > 0){
            $imgcode = Helpers::generatekey();
        };
        return $imgcode;
    }

    protected function base64_to_data($base64)
    {
        $base64 = str_replace('data:image/png;base64,', '', $base64);
        $base64 = str_replace(' ', '+', $base64);
        return base64_decode($base64);
    }

    protected function remote_file_info($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        return array(
            "mime" => curl_getinfo($ch, CURLINFO_CONTENT_TYPE),
            "size" => curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD)
        );
    }

    protected function file_get_contents_curl($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

}