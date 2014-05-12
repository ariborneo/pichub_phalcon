<?php

$router->add("/",
    array(
        "controller" => "index",
        "action"     => "index",
    )
);
$router->add("/login",
    array(
        "controller" => "login",
        "action"     => "index",
    )
);
$router->add("/logout",
    array(
        "controller" => "login",
        "action"     => "logout",
    )
);
$router->add("/registration",
    array(
        "controller" => "login",
        "action"     => "registration",
    )
);
$router->add("/show/([a-z0-9]+)([/]?)",
    array(
        "controller" => "image",
        "action"     => "index",
        "code" => 1
    )
);
$router->add("/like/([a-z0-9]+)([/]?)",
    array(
        "controller" => "image",
        "action"     => "like",
        "code" => 1
    )
);
$router->add("/dislike/([a-z0-9]+)([/]?)",
    array(
        "controller" => "image",
        "action"     => "dislike",
        "code" => 1
    )
);
$router->add("/comment_add/([a-z0-9]+)([/]?)",
    array(
        "controller" => "image",
        "action"     => "comment_add",
        "code" => 1
    )
);
$router->add("/comment_del/([a-z0-9]+)/([0-9]+)([/]?)",
    array(
        "controller" => "image",
        "action"     => "comment_del",
        "code" => 1,
        "id" => 2
    )
);
$router->add("/del_request/([a-z0-9]+)([/]?)",
    array(
        "controller" => "image",
        "action"     => "del_request",
        "code" => 1
    )
);
$router->add("/user/(.*)",
    array(
        "controller" => "user",
        "action"     => "index",
        "name" => 1
    )
);
$router->add("/top",
    array(
        "controller" => "charts",
        "action"     => "index",
        "name"       => "top"
    )
);
$router->add("/last",
    array(
        "controller" => "charts",
        "action"     => "index",
        "name"       => "last"
    )
);
$router->add("/create_album",
    array(
        "controller" => "user",
        "action"     => "create_album",
    )
);
$router->add("/album/([0-9]+)([/]?)",
    array(
        "controller" => "user",
        "action"     => "album",
        "id" => 1
    )
);
$router->add("/upload",
    array(
        "controller" => "upload",
        "action"     => "index"
    )
);
$router->add("/feedback",
    array(
        "controller" => "index",
        "action"     => "feedback"
    )
);
$router->notFound(array(
    "controller" => "index",
    "action" => "error404"
));