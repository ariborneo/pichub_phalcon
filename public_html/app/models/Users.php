<?php

class Users extends CustomModel
{

    public function setNulls()
    {
        $this->assign(array(
            "id" => 0,
            "name" => "",
            "ban" => 0
        ));
    }

}