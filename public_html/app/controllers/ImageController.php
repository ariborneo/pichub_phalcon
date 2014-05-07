<?php

class ImageController extends ControllerBase
{

    public function indexAction()
    {
        $code = $this->dispatcher->getParam("code");
        $img = Images::findFirst("code='".$code."'");
        if($img)
        {
            $img->increase("views");
            $album = Albums::findFirst($img->album);
            if($album) $album = $album->toArray();

            $this->view->setVar("image", array(
                "code" => $code,
                "user" => $img->user,
                "path" => "/pic_b/".Helpers::getdirbydate($img->time).$code.".".$img->ext,
                "opis" => $img->opis,
                "time" => Helpers::showdatetime($img->time),
                "views" => $img->views,
                "album" => $album,
                "likes" => $img->likes,
                "me_like" => Likes::findFirst(array(
                        "image = ?0 and user = ?1",
                        "bind" => array($img->id, $this->user->id)
                )),
                "comments" => $img->comments
            ));

            $this->view->setVar("comments", Comments::find(array(
                "image=".$img->id,
                "order" => "id DESC"
            )));
        }
        else
        {
            $this->response->redirect();
        }
    }

    public function likeAction()
    {
        $code = $this->dispatcher->getParam("code");
        $uid = $this->user->id;
        $image = Images::findFirst("code='".$code."'");
        if(!Likes::check($image->id, $uid))
        {
            Likes::add($image->id, $uid);
            $image->increase("likes");
        }
        $this->response->redirect("show/".$code);
    }

    public function dislikeAction()
    {
        $code = $this->dispatcher->getParam("code");
        $uid = $this->user->id;
        $image = Images::findFirst("code='".$code."'");
        $like = Likes::findFirst(array(
            "image = ?0 and user = ?1",
            "bind" => array($image->id, $uid)
        ));
        if($like)
        {
            $like->delete();
            $image->decrease("likes");
        }
        $this->response->redirect("show/".$code);
    }

    public function comment_addAction()
    {
        $uid = $this->user->id;
        $code = $this->dispatcher->getParam("code");
        $image = Images::findFirst("code='".$code."'");
        if($uid > 0 && $image)
        {
            $comment = new Comments();
            $comment->assign(array(
                "image" => $image->id,
                "user" => $uid,
                "text" => $this->request->getPost("text"),
                "time" => time()
            ));
            $comment->save();
            $image->increase("comments");
        }
        $this->response->redirect("show/".$code);
    }

    public function comment_delAction()
    {
        $uid = $this->user->id;
        $code = $this->dispatcher->getParam("code");
        $id = $this->dispatcher->getParam("id");
        $comment = Comments::findFirst($id);
        if($uid == $comment->user)
        {
            $image = Images::findFirst($comment->image);
            $image->decrease("comments");
            $comment->delete();
        }
        $this->response->redirect("show/".$code);
    }

    public function del_requestAction()
    {
        if($this->request->isPost())
        {
            $code = $this->dispatcher->getParam("code");
            $image = Images::findFirst("code='".$code."'");
            if($image)
            {
                $del_request = new DelRequests();
                $del_request->create()->assign(array(
                    "image" => $image->id,
                    "text" => $this->request->getPost("text"),
                    "user" => $this->user->id,
                    "ip" => $this->request->getClientAddress(),
                    "time" => time()
                ));
                $del_request->save();
            }
            $this->response->redirect();
        }
    }

}