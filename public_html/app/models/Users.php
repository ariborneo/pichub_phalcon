<?php

class Users extends CustomModel
{

    public function afterCreate()
    {
        $this->getDI()->getMail()->send(
            array($this->email => $this->name),
            'Успешная регистарция на PicHub.ru',
            'reg_success',
            $this->toArray()
        );
    }

}