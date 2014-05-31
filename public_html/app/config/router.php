<?php

$router->add("/", "Index::index");
$router->add("/captcha", "Index::captcha");
$router->add("/login", "Login::login");
$router->add("/logout", "Login::logout");
$router->add("/registration", "Login::registration");
$router->add("/login_vk", "Login::login_vk");
$router->add("/forgot", "Login::forgot");
$router->add("/show/{code}", "Image::index");
$router->add("/like/{code}", "Image::like");
$router->add("/comment_add/{code}", "Image::comment_add");
$router->add("/comment_del/{id}", "Image::comment_del");
$router->add("/del_request/{code}", "Image::del_request");
$router->add("/edit/{code}", "Image::edit");
$router->add("/change_private/{code}", "Image::change_private");
$router->add("/user/{name}", "User::index");
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
$router->add("/create_album", "User::create_album");
$router->add("/album/{id}", "User::album");
$router->add("/upload", "Upload::index");
$router->add("/feedback", "Index::feedback");
$router->notFound(array(
    "controller" => "index",
    "action" => "error404"
));