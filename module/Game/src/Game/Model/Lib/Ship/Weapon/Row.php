<?php

namespace Game\Model\Lib\Ship\Weapon;

use Ap\Model\Row as Prototype;

use Game\Model\Lib\Ship\Provider AS ShipProvider;
use Game\Model\Lib\Resource\ProviderWeapon;

class Row extends Prototype
{
    use ShipProvider,
        ProviderWeapon{
        ProviderWeapon::getSm insteadof ShipProvider;
        ProviderWeapon::setSm insteadof ShipProvider;
    }
    
    public function getInputFilter()
    {
        if($this->_inputFilter === null){
            $inputFilter = new \Ap\InputFilter\InputFilter;
            $inputFilter->add(array(
                'name' => 'slot_id',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'resource_code',
                'required' => true,
            ));
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}