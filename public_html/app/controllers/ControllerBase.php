<?php

class ControllerBase extends \Phalcon\Mvc\Controller
{

    public $user;

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
        $this->modelsManager->createQuery("DELETE FROM tokens WHERE expire < ".time())->execute();
    }

    public function initialize()
    {

        $this->user = new Users();
        $this->user->id = 0;
        $this->user->name = "";
        $this->user->ban = 0;

        $user_id = $this->cookies->get("user_id")->getValue();
        $user_hash = $this->cookies->get("user_hash")->getValue();
        if($user_id > 0 && strlen($user_hash) == 64)
        {
            $token = Tokens::findFirst("user=".$user_id." and hash='".Helpers::sha256($this->request->getUserAgent())."'");
            if($token && $token->expire > time())
            {
                $user = Users::findFirst($token->user);
                if($user)
                {
                    /*
                    $this->cookies->set("user_id", $user_id, time() + 30 * 86400);
                    $this->cookies->set("user_hash", $user_hash, time() + 30 * 86400);
                    $token->expire = time() + 30 * 86400;
                    $token->update();
                    */
                    $this->user = $user;
                }
            }
        }

        if (mt_rand(1, 100) === 1)
        {
            $this->delete_expired();
        }

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

        $this->view->setVar("user", $this->user);
        $this->view->setVar("t", $this->_getTranslation());

    }

}