<?php

class Likes extends CustomModel
{

    static function check($image, $user)
    {
        return Likes::findFirst(array(
            "image = ?0 and user = ?1",
            "bind" => array($image, $user)
        ));
    }

    static function add($image, $user)
    {
        $like = new Likes();
        $like->assign(array(
            "image" => $image,
            "user" => $user,
            "time" => time()
        ));
        $like->save();
        return $like;
    }

}