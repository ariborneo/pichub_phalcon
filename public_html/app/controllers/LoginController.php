<?php

class LoginController extends ControllerBase
{

    public function loginAction()
    {
        if($this->request->isPost())
        {
            $name = $this->request->getPost("name");
            $password = $this->request->getPost("password");
            $isLogged = $this->user->login($name, $password);
            if(!$this->request->isAjax())
            {
                $this->goBack();
            }
            if($isLogged)
            {
                $this->echo_json(array(
                    "status" => "success",
                    "action" => $this->dispatcher->getActionName()
                ));
            }
            else
            {
                $this->echo_json(array(
                    "status" => "error",
                    "action" => $this->dispatcher->getActionName()
                ));
            }
        }
        if($this->request->isAjax())
        {
            $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);
        }
    }

    public function logoutAction()
    {
        $this->user->logout();
        $this->goBack();
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
                $this->echo_json(array(
                    "status" => "error",
                    "action" => $this->dispatcher->getActionName(),
                    "messages" => $messages
                ));
            }
            else
            {
                $name = $this->request->getPost("name");
                $email = $this->request->getPost("email");
                $password = $this->request->getPost("password");
                $user = $this->user->create_user(array(
                    "name" => $name,
                    "email" => $email,
                    "password" => Helpers::sha256($password)
                ));
                $this->user->login_complete($user);
                $this->goBack();
            }
        }
        elseif($this->user->id > 0)
        {
            $this->goBack();
        }
        if($this->request->isAjax())
        {
            $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);
        }
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
                    $this->user->create_user(array(
                        "name" => $name,
                        "vk_id" => $uid
                    ));
                    $this->goBack();
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
                $this->user->login_complete($user);
                $this->goBack();
            }
        }
        else
        {
            $error = "No code";
        }

        if($error)
        {
            $this->echo_json(array(
                "status" => "error",
                "message" => $error
            ));
        }
    }

    public function forgotAction()
    {
        $key = $this->dispatcher->getParam("key");
        if($key)
        {
            $forgotKey = ForgotKeys::findFirst("key='".$key."'");
            if($forgotKey)
            {
                $user = Users::findFirst("id='".$forgotKey->user."'");
                $password = Helpers::generateString(10);
                $user->password = Helpers::sha256($password);
                $user->update();
                $forgotKey->delete();
                $this->modelsCache->delete("user_".$user->id);
                $this->mail->send(
                    array($user->email => $user->name),
                    'Пароль успешно изменен на PicHub.ru',
                    'forgot_changed',
                    array(
                        "user" => $user->name,
                        "password" => $password
                    )
                );
                $this->view->pick("login/forgot_changed");
                $this->view->setVar("user", $user->name);
                $this->view->setVar("password", $password);
            }
            else
            {
                $this->response->redirect();
            }
        }
        elseif($this->request->isPost())
        {
            $email = $this->request->getPost("email");
            $user = Users::findFirst("email='".$email."'");
            if($user)
            {
                $forgotKey = ForgotKeys::findFirst("user=".$user->id);
                $key = Helpers::generateString(50);
                if($forgotKey)
                {
                    $forgotKey->key = $key;
                    $forgotKey->update();
                }
                else
                {
                    $forgotKey = new ForgotKeys();
                    $forgotKey->assign(array(
                        "user" => $user->id,
                        "key" => $key
                    ));
                    $forgotKey->create();
                }
                $this->mail->send(
                    array($user->email => $user->name),
                    'Восстановление пароля на PicHub.ru',
                    'forgot',
                    array(
                        "key" => $key,
                        "domain" => $this->domain
                    )
                );
            }
        }
    }

}