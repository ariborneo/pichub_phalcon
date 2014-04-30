<?php

class IndexController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {
        $this->view->setVar("user", array(
            "name" => $this->session->get("user_name"),
            "id" => $this->session->get("user_id")
        ));
    }

    public function error404Action(){ }

}