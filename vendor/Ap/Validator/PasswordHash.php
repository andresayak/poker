<?php

namespace Ap\Validator;
use Ap\Validator\AbstractValidator;

class PasswordHash extends AbstractValidator
{
    protected $messageTemplates = array(
        'invalid'    => "The input is not valid",
    );

    public function isValid($value, $context = null)
    {
        $this->setValue($value);
        $hash = $this->getFilter()->getSm()->get('Auth\Service')->passwordHash($value);
        //var_dump($value, $this->getFilter()->getUserRow()->password, $hash);exit;
        if($this->getFilter()->getUserRow()->password == $hash){
            return true;
        }
        $this->error('invalid');
        return false;
    }
}
