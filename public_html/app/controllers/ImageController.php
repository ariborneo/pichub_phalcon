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

            $this->view->setVar("image", array(
                "code" => $code,
                "user" => $img->user,
                "username" => $user ? $user->name : 0,
                "path" => Helpers::getdirbydate($img->time).$code.".".$img->ext,
                "opis" => $img->opis,
                "time" => Helpers::showdatetime($img->time),
                "views" => $img->views,
                "album" => $album,
                "likes" => $img->likes,
                "me_like" => Likes::findFirst(array(
                        "image = ?0 and user = ?1",
                        "bind" => array($img->id, $this->user->id)
                )),
                "comments" => $img->comments,
                "editcode" => $img->editcode,
                "is_edit" => $is_edit
            ));

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

}