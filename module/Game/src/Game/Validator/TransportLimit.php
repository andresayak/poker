<?php

namespace Game\Validator;

class TransportLimit extends AbstractValidator 
{
    protected $options = array(
        'type'    => null, 
    );
    
    protected $types = array(
        'transport', 'attack', 'spy'
    );
    
    public function setType($type)
    {
        if(!in_array($type, $this->types)){
            throw new \Exception('Type invalid ['.$type.']');
        }
        $this->options['type'] = $type;
        return $this;
    }
    
    public function isValid($value)
    {
        
        $cityRow = $this->getFilter()->getCityRow();
        $limit = $cityRow->getAttrValue($this->options['type'].'_limit');
        $count = count($cityRow->getTransportRowset()->getRowsetBy('type', $this->options['type']));
        
        $result = ($count < $limit);
        
        if(!$result){
            $this->error(self::TRANSPORT_LIMIT);
        }
        return $result;
    }
}