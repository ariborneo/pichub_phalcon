<?php

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
            $this->response->redirect();
        }
        $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);
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
            $validation = new CustomValidation();
            $validation
                ->rule("name", "not_empty")
                ->rule("name", "min_length", array(3))
                ->rule("name", "unique_username")
                ->rule("email", "not_empty")
                ->rule("email", "email")
                ->rule("email", "unique_email")
                ->rule("password", "not_empty")
                ->rule("password", "min_length", array(6))
                ->rule("captcha", "identical", array($this->session->get("captcha")));
            $messages = $validation->_validate($_POST);
            if (count($messages))
            {
                $this->echo_response($messages);
            }
            else
            {
                $name = $this->request->getPost("name");
                $email = $this->request->getPost("email");
                $password = $this->request->getPost("password");
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
        elseif($this->user->id > 0)
        {
            $this->response->redirect();
        }
        $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);
    }

    public function login_vkAction()
    {
        $error = false;
        if($this->request->has("code"))
        {

            $conf = array(
                "vk_app_id" => "4357987",
                "vk_app_secret" => "viCuTUhk7lJ1ujmfXY2p",
                "redirect" => "http://pichub.local/login_vk"
            );

            $code = $this->request->get("code");
            $json = json_decode(file_get_contents("https://oauth.vk.com/access_token?client_id=".$conf['vk_app_id']."&client_secret=".$conf['vk_app_secret']."&code=".$code."&redirect_uri=".$conf["redirect"]));

            $uid = $json->user_id;
            $token = $json->access_token;

            $user = Users::findFirst("vk_id=".$uid);

            if(isset($token) && !$user)
            {
                if($this->user->id == 0)
                {
                    $name = "id" . $uid;
                    $user = new Users();
                    $user->save(array(
                        "name" => $name,
                        "time" => time(),
                        "ban" => 0,
                        "role" => 0,
                        "active" => 1,
                        "vk_id" => $uid
                    ));
                    $this->response->redirect("user/".$name);
                }
                else
                {
                    $this->user->vk_id = $uid;
                    $this->user->update();
                    $this->modelsCache->delete("user_" . $this->user->id);
                    $this->modelsCache->delete("user_" . $this->user->name);
                    $this->response->redirect("user/".$this->user->name);
                }
            }
            else
            {
                $this->login_complete($user);
                $this->response->redirect("user/".$user->name);
            }
        }
        else
        {
            $error = "No code";
        }

        if($error)
        {
            $this->echo_response(array(
                "status" => "error",
                "message" => $error
            ));
        }
    }

}