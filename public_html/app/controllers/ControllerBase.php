<?php

class ControllerBase extends \Phalcon\Mvc\Controller
{

    public $user;

    public $db;

    public $t;

    protected function _getTranslation()
    {
        $language = $this->request->getBestLanguage();
        if(strlen($language) > 2)
        {
            $language = substr($language, 0, strpos($language, "-"));
        }
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
        $ip = ip2long($this->request->getClientAddress());
        if(Bans::findFirst(array("ip='".$ip."'", "cache" => array("key" => "ban-".$ip))))
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
            $user = Users::findFirst(array("id = ".$user_id, "cache" => array("key" => "user_".$user_id, "lifetime" => 300)));
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
                else
                {
                    $this->cookies->delete("user_id");
                    $this->cookies->delete("user_hash");
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

        $this->t = $this->_getTranslation();

        $this->view->setVar("user", $this->user);
        $this->view->setVar("t", $this->t);

    }

}