<?php

class ImageController extends ControllerBase
{

    public function indexAction()
    {
        $code = $this->dispatcher->getParam("code");
        $img = Images::findFirst("code='".$code."'");
        if($img)
        {
            ++$img->views;
            $img->update();
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
                "me_like" => Likes::findFirst("image=".$img->id." and user=".$this->user->id),
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
        if(!Likes::findFirst("image=".$image->id." and user=".$uid))
        {
            $like = new Likes();
            $like->assign(array(
                "image" => $image->id,
                "user" => $uid,
                "time" => time()
            ))->save();
            ++$image->likes;
            $image->update();
        }
        $this->response->redirect("show/".$code);
    }

    public function dislikeAction()
    {
        $code = $this->dispatcher->getParam("code");
        $uid = $this->user->id;
        $image = Images::findFirst("code='".$code."'");
        $like = Likes::findFirst("image=".$image->id." and user=".$uid);
        if($like)
        {
            $like->delete();
            --$image->likes;
            $image->update();
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
            ))->save();
            ++$image->comments;
            $image->update();
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
            --$image->comments;
            $image->update();
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
                $del_request->assign(array(
                    "image" => $image->id,
                    "text" => $this->request->getPost("text"),
                    "user" => $this->user->id,
                    "ip" => $this->request->getClientAddress(),
                    "time" => time()
                ))->save();
            }
            $this->response->redirect();
        }
    }

}