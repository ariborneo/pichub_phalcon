<?php

class UserController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {
        $name = $this->dispatcher->getParam("name");
        $user = Users::findFirst("name='".$name."'");
        if($user)
        {
            $uid = $user->readAttribute("id");
            $img = Images::find("user='".$uid."'");
            $view_images = array();
            foreach($img as $a)
            {
                $view_images[] = array(
                    "code" => $a->code,
                    "path" => "pic_c/".Show::getdirbydate($a->time).$a->code.".".$a->ext
                );
            }
            $this->view->setVar("images", $view_images);

            $this->view->setVar("user", array(
                "login" => $name,
                "reg_time" => Show::showdatetime($user->readAttribute("reg_time"))
            ));

            $albums = Albums::find("user='".$uid."'");
            $this->view->setVar("albums", $albums->toArray());
        }
        else
        {
            $this->response->redirect("../");
        }
    }

    public function create_albumAction()
    {
        if($this->request->isPost())
        {
            $album = new Albums();
            $album->name = $this->request->getPost("name");
            $album->user = $this->session->get("user_id");
            $album->time = time();
            $album->count = 0;
            $album->save();
            $this->response->redirect("../user/".$this->session->get("user_name"));
        }
    }

    public function albumAction()
    {
        $id = $this->dispatcher->getParam("id");
        $album = Albums::findFirst("id='".$id."'");
        $this->view->setVar("album", $album->toArray());
    }

}