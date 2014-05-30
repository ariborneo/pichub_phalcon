<?php

class Auth extends Users
{

    private $cookies, $request;

    public function initialize()
    {

        $this->setNulls();

        $this->cookies = $this->getDi()->getShared('cookies');
        $this->request = $this->getDi()->getShared('request');

        $user_id = $this->cookies->get("user_id")->getValue();
        $user_hash = $this->cookies->get("user_hash")->getValue();
        if($user_id > 0 && strlen($user_hash) == 64)
        {
            $user = Users::findFirst(array("id = ".$user_id, "cache" => array("key" => "user_".$user_id)));
            if($user)
            {
                $token = Tokens::findFirst(array(
                    "user = ?0 and hash = ?1",
                    "bind" => array($user_id, Helpers::sha256($user->id . $user->password . $this->request->getUserAgent()))
                ));
                if($token && $token->expire > time())
                {
                    if (mt_rand(1, 20) === 1)
                    {
                        $expire_time = time() + 30 * 86400;
                        $this->cookies->set("user_id", $user_id, $expire_time);
                        $this->cookies->set("user_hash", $user_hash, $expire_time);
                        $token->expire = $expire_time;
                        $token->update();
                    }
                    $this->assign($user->toArray());
                }
                else
                {
                    $this->cookies->delete("user_id");
                    $this->cookies->delete("user_hash");
                }
            }
        }

    }

    public function setNulls()
    {
        $this->assign(array(
            "id" => 0,
            "name" => "",
            "ban" => 0
        ));
    }

    public function login($name, $password)
    {
        $user = Users::findFirst(array(
            "name = ?0 and password = ?1",
            "bind" => array($name, Helpers::sha256($password))
        ));
        if($user)
        {
            $this->login_complete($user);
            return true;
        }
        else
        {
            return false;
        }
    }

    public function login_complete($user)
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

    public function logout()
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
    }

}