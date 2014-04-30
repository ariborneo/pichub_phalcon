<?php

class IndexController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {
        $this->view->setVar("user", array(
            "name" => $this->session->get("user_name"),
            "id" => $this->session->get("user_id")
        ));

        $this->view->setVar("albums", Albums::find("user='".$this->session->get("user_id")."'")->toArray());
    }

    public function error404Action(){ }

}