<?php

class ChartsController extends \Phalcon\Mvc\Controller
{

    public function indexAction() { }

    public function topAction()
    {
        $images = Images::query()->orderBy("views DESC")->execute();
        $this->view->setVar("images", $images->toArray());
    }

    public function lastAction()
    {
        $images = Images::query()->orderBy("time DESC")->execute();
        $this->view->setVar("images", $images->toArray());
    }

}