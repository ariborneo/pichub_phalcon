<?php

use Phalcon\Validation\Validator,
    Phalcon\Validation\ValidatorInterface,
    Phalcon\Validation\Message;

class UniqueUsername extends Validator implements ValidatorInterface
{

    public function validate($validator, $attribute)
    {
        $value = $validator->getValue($attribute);
        if(Users::count("name = '".$value."'") > 0)
        {
            $message = $this->getOption('message');
            if (!$message) {
                $message = 'User exist';
            }
            $validator->appendMessage(new Message($message, $attribute));
            return false;
        }
        else
        {
            return true;
        }
    }

}
