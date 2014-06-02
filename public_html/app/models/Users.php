<?php

class Users extends CustomModel
{

    public function afterCreate()
    {
        if(strlen($this->email) > 0)
        {
            $this->getDI()->getMail()->send(
                array($this->email => $this->name),
                'Успешная регистарция на PicHub.ru',
                'reg_success',
                $this->toArray()
            );
        }
    }

}