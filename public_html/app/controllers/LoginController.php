<?php

class LoginController extends ControllerBase
{

    public function indexAction()
    {
        if($this->request->isPost())
        {
            $name = $this->request->getPost("name");
            $password = $this->request->getPost("password");
            $user = Users::findFirst("name='".$name."' and password='".Helpers::sha256($password)."'");
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
        $token->assign(array(
            "user" => $user->id,
            "time" => time(),
            "expire" => time() + 30 * 86400,
            "hash" => Helpers::sha256($this->request->getUserAgent())
        ))->save();
        $this->cookies->set("user_id", $user->id, $token->expire);
        $this->cookies->set("user_hash", $token->hash, $token->expire);
    }

    public function logoutAction()
    {
        $user_id = $this->cookies->get("user_id")->getValue();
        $user_hash = $this->cookies->get("user_hash")->getValue();
        if($user_id > 0 && strlen($user_hash) > 0)
        {
            $token = Tokens::findFirst("user=".$user_id." and hash='".$user_hash."'");
            if($token)
            {
                $token->delete();
            }
        }
        $this->cookies->delete("user_id");
        $this->cookies->delete("user_hash");
        $this->response->redirect();
    }

    public function registrationAction()
    {
        if($this->request->isPost())
        {

            $name = $this->request->getPost("name");
            $email = $this->request->getPost("email");
            $password = $this->request->getPost("password");

            $user = new Users();
            $user->assign(array(
                "name" => $name,
                "email" => $email,
                "password" => Helpers::sha256($password),
                "time" => time()
            ))->save();

            $this->login_complete($user);

            $this->response->redirect();
        }
    }

}