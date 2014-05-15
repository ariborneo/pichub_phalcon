<?php

use Phalcon\Validation\Validator,
    Phalcon\Validation\ValidatorInterface,
    Phalcon\Validation\Message;

class UniqueEmail extends Validator implements ValidatorInterface
{

    public function validate($validator, $attribute)
    {
        $value = $validator->getValue($attribute);
        if(Users::count("email = '".$value."'") > 0)
        {
            $message = $this->getOption('message');
            if (!$message) {
                $message = 'Email exist';
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
