<?php

use Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\StringLength;

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

            $user = Users::findFirst(array("id = ".$img->user, "cache" => array("key" => "user_".$img->user)));

            $this->view->setVar("image", array(
                "code" => $code,
                "user" => $img->user,
                "username" => $user->name,
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
                "order" => "id DESC",
                "cache" => array("key" => "comments_".$img->id)
            )));

            $this->view->setVar("title", "Изображение");
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
            $comment->save(array(
                "image" => $image->id,
                "user" => $uid,
                "text" => $this->request->getPost("text"),
                "time" => time()
            ));
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
            if($image && $image->user != $this->user->id)
            {
                $validation = new Phalcon\Validation();
                $validation
                    ->add('text', new PresenceOf(array(
                        'message' => 'The text is required'
                    )))
                    ->add('text', new StringLength(array(
                        'minimumMessage' => 'The text is too short',
                        'min' => 20
                    )));
                $messages = $validation->validate($_POST);
                if (count($messages)) {
                    $array = array();
                    foreach ($messages as $message) {
                        $array[] = $message->getMessage();
                    }
                    echo json_encode($array);exit;
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
                    $this->response->redirect();
                }
            }
        }
    }

}