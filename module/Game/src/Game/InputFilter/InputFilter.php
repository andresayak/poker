<?php

namespace Game\InputFilter;

use Game\Validator\ValidatorChain;
use Game\Filter\FilterChain;

use Game\Model\User\Row AS UserRow;
use Game\Model\City\Row AS CityRow;
use Game\Model\Alliance\Row AS AllianceRow;
use Game\Model\Alliance\Member\Row AS MemberRow;
use Game\Model\Lib\Object\Row AS ObjectRow;
use Game\Model\Region\Row AS RegionRow;

class InputFilter extends \Zend\InputFilter\InputFilter
{
    protected $_logs = array();
    protected $_region_row;
    protected $_city_row, $_list, $_user_row, $_object_row, $_alliance_row, $_member_row;
    protected $_sm, $_messages;
    
    public function getFactory()
    {
        if (null === $this->factory) {
            parent::getFactory();
            $validatorChain = new ValidatorChain;
            $validatorChain->setFilter($this);
            
            $filterChain = new FilterChain;
            $filterChain->setFilter($this);
            
            $this->getFactory()->setDefaultValidatorChain($validatorChain);
            $this->getFactory()->setDefaultFilterChain($filterChain);
        }
        return $this->factory;
    }
    
    public function setSm($sm)
    {
        $this->_sm = $sm;
        return $this;
    }
    
    public function getSm()
    {
        return $this->_sm;
    }
    
    public function log($message)
    {
        $this->_logs[] = $message;
        return $this;
    }
    
    public function isLogs()
    {
        return count($this->_logs);
    }
    
    public function getLogs($sep = "\n")
    {
        return implode($sep, $this->_logs);
    }
    
    public function setObjectRow(ObjectRow $row)
    {
        $this->_object_row = $row;
        return $this;
    }
    
    public function getObjectRow()
    {
        if($this->_object_row === null){
            throw new \Exception('Object not set');
        }
        return $this->_object_row;
    }
    
    public function setUserRow(UserRow $row)
    {
        $this->_user_row = $row;
        return $this;
    }
    
    public function getUserRow()
    {
        if($this->_user_row === null){
            $userRow = $this->getSm()->get('Auth\Service')->getUserRow();
            if($userRow and $userRow instanceof UserRow){
                $this->setUserRow($userRow);
            }else{
                $this->_user_row = false;
            }
        }
        return $this->_user_row;
    }
    
    public function getCityRow()
    {
        if($this->_city_row === null){
            throw new \Exception('City not set');
        }
        return $this->_city_row;
    }

    public function setCityRow(CityRow $row)
    {
        $this->_city_row = $row;
        return $this;
    }
    
    public function setList(array $list)
    {
        return $this->_list = $list;
    }
    
    public function getList()
    {
        if($this->_list === null){
            throw new \Exception('List not set');
        }
        return $this->_list;
    }
    
    public function setAllianceRow(AllianceRow $row)
    {
        $this->_alliance_row = $row;
        return $this;
    }
    
    public function getAllianceRow()
    {
        if($this->_alliance_row === null){
            throw new \Exception('Alliance not set');
        }
        return $this->_alliance_row;
    }
    
    public function setMemberRow(MemberRow $row)
    {
        $this->_member_row = $row;
        return $this;
    }
    public function getMemberRow()
    {
        if($this->_member_row === null){
            throw new \Exception('Member not set');
        }
        return $this->_member_row;
    }
    
    public function getRegionRow()
    {
        if($this->_region_row === null){
            throw new \Exception('Region not set');
        }
        return $this->_region_row;
    }
    
    public function setRegionRow(RegionRow $row)
    {
        $this->_region_row = $row;
        return $this;
    }
    
    public function getMessages()
    {
        if($this->_messages === null){
            return parent::getMessages();
        }
        return $this->_messages;
    }
    
    public function getMessage($element, $validator, $messageKey) 
    {
        $value = $this->get($element)->getValue();
        return $this->get($element)->getValidatorChain()->plugin($validator)->createMessage($messageKey, $value);
    }
}