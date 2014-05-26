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
            $validation = new CustomValidation();
            $validation
                ->rule("name", "not_empty")
                ->rule("email", "not_empty")
                ->rule("email", "email")
                ->rule("subject", "not_empty")
                ->rule("text", "not_empty")
                ->rule("text", "min_length", array(20))
                ->rule("captcha", "identical", array($this->session->get("captcha")));
            $messages = $validation->_validate($_POST);
            if (count($messages))
            {
                if(!$this->request->isAjax())
                {
                    $this->response->redirect();
                }
                $this->echo_response(array(
                    "status" => "error",
                    "action" => $this->dispatcher->getActionName(),
                    "messages" => $messages
                ));
            }
            else
            {
                $feedback = new Feedback();
                $feedback->save(array(
                    "name" => $this->request->getPost("name"),
                    "email" => $this->request->getPost("email"),
                    "subject" => $this->request->getPost("subject"),
                    "text" => $this->request->getPost("text"),
                    "time" => time(),
                    "user" => $this->user->id,
                    "ip" => ip2long($this->request->getClientAddress())
                ));
                if(!$this->request->isAjax())
                {
                    $this->response->redirect();
                }
                $this->echo_response(array(
                    "status" => "success",
                    "action" => $this->dispatcher->getActionName()
                ));
            }
        }
        //$this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);
    }

    public function captchaAction()
    {
        $this->view->disable();
        $this->response->setHeader("Content-Type", "image/png");
        $captcha = imagecreate(70, 20);
        imagecolorallocate($captcha, 245, 245, 245);
        $captcha_number = Helpers::generateString(6);
        $this->session->set("captcha", $captcha_number);
        imagestring($captcha, 5, 8, 2, $captcha_number, imagecolorallocate($captcha, 73, 126, 194));
        imagepng($captcha);
        imagedestroy($captcha);
    }

    public function error404Action(){ }

}