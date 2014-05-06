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
            $feedback->name = $this->request->getPost("name");
            $feedback->email = $this->request->getPost("email");
            $feedback->subject = $this->request->getPost("subject");
            $feedback->text = $this->request->getPost("text");
            $feedback->time = time();
            $feedback->save();
            $this->response->redirect();
        }
    }

    public function error404Action(){ }

}