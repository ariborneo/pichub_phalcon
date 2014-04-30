<?php

class ShowController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {
        $code = $this->dispatcher->getParam("code");
        $img = Images::findFirst("code='".$code."'");
        ++$img->views;
        $img->update();
        $this->view->setVar("image", array(
            "code" => $code,
            "path" => "/pic_b/".Show::getdirbydate($img->time).$code.".".$img->ext,
            "opis" => $img->opis,
            "time" => Show::showdatetime($img->time),
            "views" => $img->views
        ));
    }

}