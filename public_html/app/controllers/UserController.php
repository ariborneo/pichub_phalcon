<?php

class UserController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {
        $name = $this->dispatcher->getParam("name");
        $user = Users::findFirst("name='".$name."'");
        $uid = $user->readAttribute("id");
        $img = Images::find("user='".$uid."'");
        $view_images = array();
        foreach($img as $i)
        {
            $view_images[] = array(
                "code" => $i->code,
                "path" => "pic_c/".Show::getdirbydate($i->time).$i->code.".".$i->ext
            );
        }
        $this->view->setVar("images", $view_images);
        $this->view->setVar("user", array(
            "login" => $name,
            "reg_time" => Show::showdatetime($user->readAttribute("reg_time"))
        ));
    }

}