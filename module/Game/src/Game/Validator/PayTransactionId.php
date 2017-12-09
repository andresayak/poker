<?php

namespace Game\Validator;

class PayTransactionId extends AbstractValidator 
{
    public function isValid($value)
    {
        $payLogTable = $this->getFilter()->getSm()->get('System\Paylog\Table');
        $payLogRow = $payLogTable->fetchByMerchantAndTransaction(AUTH_TYPE, $value);
        if($payLogRow){
            $this->error(self::INVALID_TRANSACTION);
            return false;
        }
        return true;
    }
}