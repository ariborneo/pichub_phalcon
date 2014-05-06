<?php

class UserController extends ControllerBase
{

    public function indexAction()
    {
        $name = $this->dispatcher->getParam("name");
        $user = Users::findFirst("name='".$name."'");

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
                    "path" => "/pic_c/".Helpers::getdirbydate($i->time).$i->code.".".$i->ext
                );
            }
            $this->view->setVar("images", $view_images);

            $this->view->setVar("user", array(
                "login" => $name,
                "time" => Helpers::showdatetime($user->readAttribute("time"))
            ));

            $albums = Albums::find("user=".$uid);
            $this->view->setVar("albums", $albums->toArray());
        }
        else
        {
            $this->view->disable();
            echo "Пользователя не существует";
        }
    }

    public function create_albumAction()
    {
        if($this->request->isPost())
        {
            $album = new Albums();
            $album->assign(array(
                "name" => $this->request->getPost("name"),
                "user" => $this->user->id,
                "time" => time(),
                "count" => 0
            ));
            $album->save();
            $this->response->redirect("user/".$this->user->name);
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
    }

}