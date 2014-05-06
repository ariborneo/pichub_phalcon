<?php

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        $this->view->setVar("albums", Albums::find("user=".$this->user->id)->toArray());
    }

    public function  feedbackAction()
    {
        if($this->request->isPost())
        {
            $feedback = new Feedback();
            $feedback->assign(array(
                "name" => $this->request->getPost("name"),
                "email" => $this->request->getPost("email"),
                "subject" => $this->request->getPost("subject"),
                "text" => $this->request->getPost("text"),
                "time" => time()
            ))->save();
            $this->response->redirect();
        }
    }

    public function error404Action(){ }

}