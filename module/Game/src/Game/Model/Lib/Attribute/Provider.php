<?php

namespace Game\Model\Lib\Attribute;

trait Provider {

    use \Game\Provider\ProvidesServiceManager;
    
    protected $_attribute_row;

    public function getAttributeRow() 
    {
        if($this->_attribute_row === null){
            $this->_attribute_row = $this->getSm()->get('Lib\Attribute\Table')->fetchBy('code', $this->attribute_code);
        }
        return $this->_attribute_row;
    }

    public function setAttributeRow(Row $attributeRow) 
    {
        $this->_attribute_row = $attributeRow;
        $this->attribute_code = $attributeRow->code;
        return $this;
    }
}
