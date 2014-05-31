<?php

class ImageController extends ControllerBase
{

    public function indexAction()
    {
        $code = $this->dispatcher->getParam("code");
        $editcode = $this->dispatcher->getParam("editcode");
        $img = Images::findFirst("code='".$code."'");
        if($img)
        {
            $is_edit = $editcode == $img->editcode ? true : false;

            $img->increase("views");
            $album = Albums::findFirst($img->album);
            if($album) $album = $album->toArray();

            $user = Users::findFirst(array("id = ".$img->user, "cache" => array("key" => "user_".$img->user)));

            $this->view->setVar("image", array_merge($img->toArray(), array(
                "username" => $user ? $user->name : 0,
                "path" => Helpers::getdirbydate($img->time).$code.".".$img->ext,
                "time" => Helpers::showdatetime($img->time),
                "album" => $album,
                "me_like" => Likes::findFirst(array(
                        "image = ?0 and user = ?1",
                        "bind" => array($img->id, $this->user->id)
                    )),
                "is_edit" => $is_edit
            )));

            $comments = Comments::find(array(
                "image=".$img->id,
                "order" => "id DESC",
                "cache" => array("key" => "comments_".$img->id)
            ))->toArray();
            foreach($comments as $key => $comment)
            {
                $comments[$key]["time"] = Helpers::showdatetime($comment["time"]);
                $comments[$key]["user"] = Users::findFirst(array("id=".$comment["user"], "cache" => array("key" => "user_".$comment["user"])));
            }
            $comments = json_decode(json_encode($comments), FALSE);

            $this->view->setVar("comments", $comments);
            $this->view->setVar("title", "Изображение");
        }
        else
        {
            $this->error404();
        }
    }

    public function likeAction()
    {
        $uid = $this->user->id;
        if($uid > 0)
        {
            $code = $this->dispatcher->getParam("code");
            $image = Images::findFirst("code='".$code."'");
            $like = Likes::findFirst(array(
                "image = ?0 and user = ?1",
                "bind" => array($image->id, $uid)
            ));
            if(!$like)
            {
                Likes::add($image->id, $uid);
                $image->increase("likes");
                $action = "like";
            }
            else
            {
                $like->delete();
                $image->decrease("likes");
                $action = "dislike";
            }
            if(!$this->request->isAjax())
            {
                $this->goBack();
            }
            $this->echo_json(array(
                "status" => "success",
                "action" => $action,
                "likes" => $image->likes
            ));
        }
        else
        {
            if(!$this->request->isAjax())
            {
                $this->goBack();
            }
            $this->echo_json(array(
                "status" => "error",
                "message" => "Not auth"
            ));
        }
    }

    public function comment_addAction()
    {
        $uid = $this->user->id;
        $code = $this->dispatcher->getParam("code");
        $image = Images::findFirst("code='".$code."'");
        if($uid > 0 && $image)
        {
            $comment = new Comments();
            $comment->save(array(
                "image" => $image->id,
                "user" => $uid,
                "text" => $this->request->getPost("text"),
                "time" => time()
            ));
            $image->increase("comments");
            $this->modelsCache->delete("comments_" . $image->id);
            if(!$this->request->isAjax())
            {
                $this->goBack();
            }
            $this->echo_json(array(
                "status" => "success",
                "action" => $this->dispatcher->getActionName(),
                "info" => array(
                    "comments" => $image->comments,
                    "comment" => array(
                        "id" => $comment->id,
                        "time" => Helpers::showdatetime($comment->time)
                    ),
                    "user" => array(
                        "id" => $uid,
                        "name" => $this->user->name
                    )
                )
            ));
        }
        else
        {
            if(!$this->request->isAjax())
            {
                $this->goBack();
            }
            $this->echo_json(array(
                "status" => "error",
                "message" => "something wrong"
            ));
        }
    }

    public function comment_delAction()
    {
        $uid = $this->user->id;
        $id = $this->dispatcher->getParam("id");
        $comment = Comments::findFirst($id);
        if($comment && $uid == $comment->user)
        {
            $image = Images::findFirst($comment->image);
            $image->decrease("comments");
            $comment->delete();
            $this->modelsCache->delete("comments_" . $image->id);
            if(!$this->request->isAjax())
            {
                $this->goBack();
            }
            $this->echo_json(array(
                "status" => "success",
                "action" => $this->dispatcher->getActionName(),
                "info" => array(
                    "comments" => $image->comments
                )
            ));
        }
        else
        {
            if(!$this->request->isAjax())
            {
                $this->goBack();
            }
            $this->echo_json(array(
                "status" => "error",
                "message" => "something wrong"
            ));
        }
    }

    public function del_requestAction()
    {
        if($this->request->isPost())
        {
            $code = $this->dispatcher->getParam("code");
            $image = Images::findFirst("code='".$code."'");
            if($image && $image->user != $this->user->id)
            {
                $validation = new CustomValidation();
                $validation
                    ->rule("text", "not_empty")
                    ->rule("text", "min_length", array(20));
                $messages = $validation->_validate($_POST);
                if(count($messages) > 0)
                {
                    $this->echo_json(array(
                        "status" => "error",
                        "action" => $this->dispatcher->getActionName(),
                        "messages" => $messages
                    ));
                }
                else
                {
                    $del_request = new DelRequests();
                    $del_request->save(array(
                        "image" => $image->id,
                        "text" => $this->request->getPost("text"),
                        "user" => $this->user->id,
                        "ip" => ip2long($this->request->getClientAddress()),
                        "time" => time()
                    ));
                    $this->echo_json(array(
                        "status" => "success",
                        "action" => $this->dispatcher->getActionName()
                    ));
                }
            }
        }
    }

    public function editAction()
    {
        if($this->request->isPost())
        {
            $code = $this->dispatcher->getParam("code");
            $img = Images::findFirst("code='".$code."'");
            if($img && $img->user == $this->user->id)
            {
                $filepath = Helpers::getdirbydate($img->time).$code.".".$img->ext;
                $image = new Imgproc("../public/pic_b/".$filepath);

                if($this->request->getPost("rotate") > 0)
                {
                    $image->rotate($this->request->getPost("rotate"));
                    $this->edit_log("rotate", $img->id);
                }

                $resize = $this->request->getPost("resize");
                if(in_array($resize, array(1, 2, 3)))
                {
                    if($resize == 1)
                    {
                        $value = $this->request->getPost("resize_w");
                    }
                    elseif($resize == 2)
                    {
                        $value = $this->request->getPost("resize_h");
                    }
                    else
                    {
                        $value = $this->request->getPost("resize_p");
                    }
                    $image->alter_resize($resize, $value);
                    $this->edit_log("resize", $img->id);
                }

                $title = iconv('windows-1251', 'utf-8', $this->request->getPost("title"));
                $title_color = $this->request->getPost("title_color");
                $title_size = $this->request->getPost("title_size");
                if(strlen($title) > 0 && ctype_xdigit($title_color) && $title_size >= 8 && $title_size <= 72)
                {
                    $image->text($title, 10, $image->getHeight() * 0.97, null, $title_color, $title_size);
                    $this->edit_log("text", $img->id);
                }

                $reflect = $this->request->getPost("reflect");
                if($reflect == 1 || $reflect == 2)
                {
                    if($reflect == 1)
                    {
                        $direction = Phalcon\Image::HORIZONTAL;
                    }
                    else
                    {
                        $direction = Phalcon\Image::VERTICAL;
                    }
                    $image->flip($direction);
                    $this->edit_log("flip", $img->id);
                }

                $filter = $this->request->getPost("filter");
                if(in_array($filter, array("grey", "red", "green", "blue", "negate", "sepia")))
                {
                    $image->filter($filter);
                    $this->edit_log("filter", $img->id);
                }

                $light_perc = $this->request->getPost("light");
                if($light_perc > 0 && $light_perc <= 100)
                {
                    $image->filter("light", $light_perc);
                    $this->edit_log("light", $img->id);
                }

                $contrast_perc = $this->request->getPost("contrast");
                if($contrast_perc > 0 && $contrast_perc <= 100)
                {
                    $image->filter("contrast", $contrast_perc);
                    $this->edit_log("contrast", $img->id);
                }

                $smooth_perc = $this->request->getPost("smooth");
                if($smooth_perc > 0 && $smooth_perc <= 100)
                {
                    $image->filter("smooth", $smooth_perc);
                    $this->edit_log("smooth", $img->id);
                }

                $corner_radius = $this->request->getPost("corner_radius");
                if($corner_radius >= 3 && $corner_radius <= 100)
                {
                    $image->corner_radius($corner_radius);
                    $this->edit_log("corner_radius", $img->id);
                }

                if($this->request->getPost("reflection_effect") == 1)
                {
                    $image->reflection(100);
                    $this->edit_log("reflection", $img->id);
                }

                $blur = $this->request->getPost("blur");
                if($blur > 0 && $blur <= 100)
                {
                    $image->blur($blur);
                    $this->edit_log("blur", $img->id);
                }

                $image->save("../public/pic_b/".$filepath);
                $image->resize(200);
                $image->save("../public/pic_s/".$filepath);
                $image->alter_crop(100);
                $image->save("../public/pic_c/".$filepath);
            }
            $this->response->redirect("show/".$code);
        }
        else
        {
            $this->response->redirect();
        }
    }

    protected function edit_log($action, $image)
    {
        $l = new ImagesEditLog();
        $l->create(array(
            "action" => $action,
            "image" => $image,
            "time" => time()
        ));
    }

    public function change_privateAction()
    {
        $code = $this->dispatcher->getParam("code");
        $image = Images::findFirst("code='".$code."'");
        if($image && $image->user == $this->user->id)
        {
            $image->private = $image->private == 1 ? 0 : 1;
            $image->update();
            if(!$this->request->isAjax())
            {
                $this->goBack();
            }
            $this->echo_json(array(
                "status" => "success",
                "action" => $this->dispatcher->getActionName(),
                "private" => (int) $image->private
            ));
        }
        else
        {
            $this->goBack();
        }
    }

}