<?php

class ChartsController extends ControllerBase
{

    public function indexAction()
    {
        $name = $this->dispatcher->getParam("name");
        if($name == "top")
        {
            $sort = "views";
            $this->view->pick("charts/top");
        }
        else
        {
            $sort = "time";
            $this->view->pick("charts/last");
        }

        $images = Images::query()->where("private = 0")->orderBy($sort . " DESC")->limit(100)->execute()->toArray();
        for($i = 0; $i < count($images); ++$i)
        {
            $images[$i]["c_path"] = '/pic_c/'.Helpers::getdirbydate($images[$i]["time"]).$images[$i]["code"].'.'.$images[$i]["ext"];
            $images[$i]["time"] = Helpers::showdatetime($images[$i]["time"]);
        }
        $this->view->setVar("images", $images);

    }

}