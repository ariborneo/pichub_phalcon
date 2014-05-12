<?php

use Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Email,
    Phalcon\Validation\Validator\StringLength;

class LoginController extends ControllerBase
{

    public function indexAction()
    {
        if($this->request->isPost())
        {
            $name = $this->request->getPost("name");
            $password = $this->request->getPost("password");
            $user = Users::findFirst(array(
                "name = ?0 and password = ?1",
                "bind" => array($name, Helpers::sha256($password))
            ));
            if($user)
            {
                $this->login_complete($user);
            }
        }
        $this->response->redirect();
    }

    protected function login_complete($user)
    {
        $token = new Tokens();
        $token->save(array(
            "user" => $user->id,
            "time" => time(),
            "ip" => ip2long($this->request->getClientAddress()),
            "expire" => time() + 30 * 86400,
            "hash" => Helpers::sha256($user->id . $user->password . $this->request->getUserAgent())
        ));
        $this->cookies->set("user_id", $user->id, $token->expire);
        $this->cookies->set("user_hash", $token->hash, $token->expire);
    }

    public function logoutAction()
    {
        $user_id = $this->cookies->get("user_id")->getValue();
        $user_hash = $this->cookies->get("user_hash")->getValue();
        if($user_id > 0 && strlen($user_hash) > 0)
        {
            $token = Tokens::findFirst(array(
                "user = ?0 and hash = ?1",
                "bind" => array($user_id, $user_hash)
            ));
            if($token)
            {
                $token->delete();
            }
        }
        $this->cookies->delete("user_id");
        $this->cookies->delete("user_hash");
        $this->response->redirect($this->request->getHTTPReferer());
    }

    public function registrationAction()
    {
        if($this->request->isPost())
        {

            $validation = new Phalcon\Validation();
            $validation
                ->add('name', new PresenceOf(array(
                    'message' => 'The name is required'
                )))
                ->add('name', new StringLength(array(
                    'minimumMessage' => 'The name is too short',
                    'min' => 6
                )))
                ->add('email', new PresenceOf(array(
                    'message' => 'The e-mail is required'
                )))
                ->add('email', new Email(array(
                    'message' => 'The e-mail is not valid'
                )))
                ->add('password', new PresenceOf(array(
                    'message' => 'The password is required'
                )))
                ->add('password', new StringLength(array(
                    'minimumMessage' => 'The password is too short',
                    'min' => 6
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

                $name = $this->request->getPost("name");
                $email = $this->request->getPost("email");
                $password = $this->request->getPost("password");

                $messages = array();
                if(Users::count("name = '".$name."'") > 0){
                    $messages[] = "This name is used";
                }
                elseif(Users::count("email = '".$email."'") > 0)
                {
                    $messages[] = "This email is used";
                }
                if(count($messages)){ echo json_encode($messages);exit; }

                $user = new Users();
                $user->save(array(
                    "name" => $name,
                    "email" => $email,
                    "password" => Helpers::sha256($password),
                    "time" => time(),
                    "ban" => 0,
                    "role" => 0,
                    "active" => 1
                ));

                $this->login_complete($user);

                $this->response->redirect();
            }
        }
    }

}