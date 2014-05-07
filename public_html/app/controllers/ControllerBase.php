<?php

class ControllerBase extends \Phalcon\Mvc\Controller
{

    public $user;

    public $db;

    protected function _getTranslation()
    {
        $language = $this->request->getBestLanguage();
        $language = substr($language, 0, strpos($language, "-"));
        if (file_exists("../app/languages/".$language.".php")) {
            require "../app/languages/".$language.".php";
        } else {
            require "../app/languages/en.php";
        }
        return new \Phalcon\Translate\Adapter\NativeArray(array(
            "content" => $messages
        ));
    }

    protected function delete_expired()
    {
        $this->db->query("delete from tokens where expire < ".time());
    }

    protected function check_bans()
    {
        if(Bans::findFirst("ip='".$this->request->getClientAddress()."'"))
        {
            $this->view->disable();
            echo "Baned IP";
        }
        elseif($this->user->ban == 1)
        {
            $this->view->disable();
            echo "Baned USER";
        }
    }

    protected function auth()
    {
        $user_id = $this->cookies->get("user_id")->getValue();
        $user_hash = $this->cookies->get("user_hash")->getValue();
        if($user_id > 0 && strlen($user_hash) == 64)
        {
            $user = Users::findFirst($user_id);
            if($user)
            {
                $token = Tokens::findFirst(array(
                    "user = ?0 and hash = ?1",
                    "bind" => array($user_id, Helpers::sha256($user->id . $user->password . $this->request->getUserAgent()))
                ));
                if($token && $token->expire > time())
                {
                    if (mt_rand(1, 10) === 1)
                    {
                        $expire_time = time() + 30 * 86400;
                        $this->cookies->set("user_id", $user_id, $expire_time);
                        $this->cookies->set("user_hash", $user_hash, $expire_time);
                        $token->expire = $expire_time;
                        $token->update();
                    }
                    $this->user = $user;
                }
            }
        }
    }

    public function initialize()
    {

        $this->db = $this->getDi()->getShared('db');

        $this->user = new Users();
        $this->user->setNulls();

        $this->auth();

        if (mt_rand(1, 100) === 1)
        {
            $this->delete_expired();
        }

        $this->check_bans();

        $this->view->setVar("user", $this->user);
        $this->view->setVar("t", $this->_getTranslation());

    }

}