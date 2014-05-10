<?php

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        if($this->user->id > 0)
        {
            $this->view->setVar("albums", Albums::find("user=".$this->user->id)->toArray());
        }
    }

    public function feedbackAction()
    {
        if($this->request->isPost())
        {
            $feedback = new Feedback();
            $feedback->save(array(
                "name" => $this->request->getPost("name"),
                "email" => $this->request->getPost("email"),
                "subject" => $this->request->getPost("subject"),
                "text" => $this->request->getPost("text"),
                "time" => time(),
                "user" => $this->user->id,
                "ip" =>ip2long($this->request->getClientAddress())
            ));
            $this->response->redirect();
        }
    }

    public function error404Action(){ }

}