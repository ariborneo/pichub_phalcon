<?php

class UserController extends ControllerBase
{

    public function indexAction()
    {
        $name = $this->dispatcher->getParam("name");
        $user = Users::findFirst(array("name='".$name."'", "cache" => array("key" => "user_".$name)));

        if($user)
        {
            $uid = $user->readAttribute("id");
            $img = Images::find(array(
                "user=".$uid,
                "order" => "id DESC"
            ));
            $view_images = array();
            foreach($img as $i)
            {
                $view_images[] = array(
                    "code" => $i->code,
                    "path" => Helpers::getdirbydate($i->time).$i->code.".".$i->ext
                );
            }
            $this->view->setVar("images", $view_images);

            $this->view->setVar("user", array(
                "login" => $name,
                "time" => Helpers::showdatetime($user->readAttribute("time")),
                "vk_id" => $user->vk_id
            ));

            $albums = Albums::find("user=".$uid);
            $this->view->setVar("albums", $albums->toArray());
            $this->view->setVar("title", $name);
        }
        else
        {
            $this->error404();
        }
    }

    public function create_albumAction()
    {
        if($this->request->isPost() && $this->user->id > 0)
        {
            $validation = new CustomValidation();
            $validation->rule("name", "not_empty");
            $messages = $validation->_validate($_POST);
            if (count($messages))
            {
                $this->echo_json($messages);
            }
            else
            {
                $album = new Albums();
                $album->save(array(
                    "name" => $this->request->getPost("name"),
                    "user" => $this->user->id,
                    "time" => time(),
                    "count" => 0,
                    "private" => 0
                ));
                $this->response->redirect("user/".$this->user->name);
            }
        }
    }

    public function albumAction()
    {
        $id = $this->dispatcher->getParam("id");
        $album = Albums::findFirst($id);
        $this->view->setVar("album", $album->toArray());

        $img = Images::find(array(
            "album=".$id,
            "order" => "id DESC"
        ));
        $view_images = array();
        foreach($img as $i)
        {
            $view_images[] = array(
                "code" => $i->code,
                "path" => "pic_c/".Helpers::getdirbydate($i->time).$i->code.".".$i->ext
            );
        }
        $this->view->setVar("images", $view_images);
        $this->view->setVar("title", "Альбом " . $album->name);
    }

}