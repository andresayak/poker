<?php

namespace Game\Model\Lib\Saga;

trait Provider {

    use \Game\Provider\ProvidesServiceManager;
    
    protected $_saga_row;

    public function getSagaRow() 
    {
        if($this->_saga_row === null){
            $this->_saga_row = $this->getSm()->get('Lib\Saga\Table')->fetchBy('code', $this->saga_code);
        }
        return $this->_saga_row;
    }

    public function setSagaRow(Row $sagaRow) 
    {
        $this->_saga_row = $sagaRow;
        return $this;
    }
}
