<?php

class ControllerBase extends \Phalcon\Mvc\Controller
{

    public $t, $domain;
    //public $config;

    protected function getTranslation()
    {
        $language = $this->request->getBestLanguage();
        if(strlen($language) > 2)
        {
            $language = substr($language, 0, strpos($language, "-"));
        }
        if (file_exists("../app/languages/".$language.".php"))
        {
            $messages = require "../app/languages/".$language.".php";
        }
        else
        {
            $messages = require "../app/languages/en.php";
        }
        return new \Phalcon\Translate\Adapter\NativeArray(array(
            "content" => $messages
        ));
    }

    protected function delete_expired()
    {
        if (mt_rand(1, 100) === 1)
        {
            $this->db->query("delete from tokens where expire < ".time());
        }
    }

    protected function check_bans()
    {
        $ip = ip2long($this->request->getClientAddress());
        $ban = false;
        $ipban = Bans::findFirst(array("ip='".$ip."'", "cache" => array("key" => "ban-".$ip)));
        if($ipban)
        {
            $ban = "ip";
            $this->view->setVar("ip", $this->request->getClientAddress());
            $this->view->setVar("reason", $ipban->reason);
        }
        elseif($this->user->ban == 1)
        {
            $ban = "user";
        }
        if($ban)
        {
            $this->view->pick("index/ban");
            $this->view->setVar("type", $ban);
        }
    }

    protected function echo_json($array)
    {
        $this->view->disable();
        echo json_encode($array);
    }

    protected function goBack()
    {
        $this->response->redirect($this->request->getHTTPReferer());
    }

    protected function error404()
    {
        $this->dispatcher->forward(array(
            'controller' => 'index',
            'action' => 'error404'
        ));
    }

    public function initialize()
    {

        $this->domain = $this->request->getHttpHost();

        //$this->config = Config::find(array("cache" => array("key" => "config")));

        $this->delete_expired();

        $this->check_bans();

        $this->t = $this->getTranslation();

        $this->view->setVar("domain", $this->domain);
        $this->view->setVar("user", $this->user);
        $this->view->setVar("t", $this->t);

    }

}