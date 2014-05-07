<?php

class CustomModel extends \Phalcon\Mvc\Model
{

    public function increase($param)
    {
        ++$this->$param;
        $this->update();
    }

    public function decrease($param)
    {
        --$this->$param;
        $this->update();
    }

}