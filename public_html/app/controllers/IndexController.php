<?php

use Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Email,
    Phalcon\Validation\Validator\StringLength;

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

            $validation = new Phalcon\Validation();
            $validation
                ->add('name', new PresenceOf(array(
                    'message' => 'The name is required'
                )))
                ->add('email', new PresenceOf(array(
                    'message' => 'The e-mail is required'
                )))
                ->add('email', new Email(array(
                    'message' => 'The e-mail is not valid'
                )))
                ->add('subject', new PresenceOf(array(
                    'message' => 'The subject is required'
                )))
                ->add('text', new PresenceOf(array(
                    'message' => 'The text is required'
                )))
                ->add('text', new StringLength(array(
                    'minimumMessage' => 'The text is too short',
                    'min' => 20
                )));
            $messages = $validation->validate($_POST);
            if (count($messages)) {
                $array = array();
                foreach ($messages as $message) {
                    $array[] = $message->getMessage();
                }
                echo json_encode($array);exit;
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
                    "ip" =>ip2long($this->request->getClientAddress())
                ));
                $this->response->redirect();
            }
        }
    }

    public function error404Action(){ }

}