<?php

namespace Game\Validator;

class TransportResourceCountLimit extends AbstractValidator 
{
    public function isValid($value)
    {
        $cityRow = $this->getFilter()->getCityRow();
        
        $count = 0;
        foreach($this->getFilter()->getTransportManager()->getObjectRowset()->getItems() AS $objectRow){
            if($objectRow->getObjectRow()->type == 'resource'){
                $count+= $objectRow->count;
            }
        }
        $limit = $cityRow->getAttrValue('transport_pieces_limit') * (1+$cityRow->getAttrValue('rate_transport_pieces'));
        $result = ($count <= $limit);
        if(!$result){
            $this->error(self::TRANSPORT_RESOURCE_COUNT_LIMIT, $limit);
        }
        return $result;
    }
}