<?php

class LoginController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {
        if($this->request->isPost())
        {
            $name = $this->request->getPost("name");
            $password = $this->request->getPost("password");
            $user = Users::findFirst(array(
                "name" => $name
            ));
            if($user->readAttribute("password") == md5($password)){
                $this->session->set("user_id", $user->readAttribute("id"));
                $this->session->set("user_name", $name);
            }
        }
        $this->response->redirect("../");
    }

    public function logoutAction()
    {
        $this->session->destroy();
        $this->response->redirect("../");
    }

    public function registrationAction()
    {
        if($this->request->isPost()){
            $this->view->disable();

            $name = $this->request->getPost("name");
            $email = $this->request->getPost("email");
            $password = $this->request->getPost("password");

            $user = new Users();
            $user->name = $name;
            $user->email = $email;
            $user->password = md5($password);
            $user->reg_time = time();
            $user->save();

            $this->session->set("user_id", $user->readAttribute("id"));
            $this->session->set("user_name", $name);

            $this->response->redirect("../");
        }
    }

}