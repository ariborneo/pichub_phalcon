<?php

use Phalcon\Validation,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\StringLength,
    Phalcon\Validation\Validator\Email,
    Phalcon\Validation\Validator\Confirmation,
    Phalcon\Validation\Validator\Between,
    Phalcon\Validation\Validator\Identical,
    Phalcon\Validation\Validator\Regex,
    Phalcon\Validation\Validator\InclusionIn,
    Phalcon\Validation\Validator\ExclusionIn;

class CustomValidation extends Validation
{

    public function rule($field, $rule, $params = null)
    {
        if($rule == "not_empty")
        {
            $this->add($field, new PresenceOf(array(
                'message' => 'The ' . $field . ' is required'
            )));
        }
        elseif($rule == "regex")
        {
            $this->validate($field, new Regex(array(
                'pattern' => $params[0],
                'message' => "Wrong " . $field
            )));
        }
        elseif($rule == "email")
        {
            $this->add($field, new Email(array(
                'message' => 'The ' . $field . ' is not valid email'
            )));
        }
        elseif($rule == "min_length")
        {
            $this->add($field, new StringLength(array(
                'messageMinimum' => 'The ' . $field . ' is too short',
                'min' => $params[0]
            )));
        }
        elseif($rule == "max_length")
        {
            $this->add($field, new StringLength(array(
                'messageMaximum' => 'The ' . $field . ' is too long',
                'max' => $params[0]
            )));
        }
        elseif($rule == "between")
        {
            $this->add($field, new Between(array(
                'minimum' => $params[0],
                'maximum' => $params[1],
                'message' => 'The ' . $field . ' must be between ' . $params[0] . ' and ' . $params[1]
            )));
        }
        elseif($rule == "matches")
        {
            $this->add($field, new Confirmation(array(
             'message' => $field . ' not equal ' . $params[0],
             'with' => $params[0]
            )));
        }
        elseif($rule == "identical")
        {
            $this->add($field, new Identical(array(
                'value' => $params[0],
                'message' => "Wrong " . $field
            )));
        }
        elseif($rule == "in_array")
        {
            $this->add($field, new InclusionIn(array(
                'message' => 'Wrong ' . $field,
                'domain' => $params[0]
            )));
        }
        elseif($rule == "not_in_array")
        {
            $this->add($field, new ExclusionIn(array(
                'message' => 'Wrong ' . $field,
                'domain' => $params[0]
            )));
        }
        elseif($rule == "unique_username")
        {
            $this->add($field, new UniqueUsername(array(
                'message' => 'User exist'
            )));
        }
        elseif($rule == "unique_email")
        {
            $this->add($field, new UniqueEmail(array(
                'message' => 'Email exist'
            )));
        }
        return $this;
    }

    public function _validate($values)
    {
        $messages = $this->validate($values);
        $array = array();
        foreach ($messages as $message) {
            $array[] = $message->getMessage();
        }
        return $array;
    }

}