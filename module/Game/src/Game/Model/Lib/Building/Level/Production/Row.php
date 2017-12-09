<?php

namespace Game\Model\Lib\Building\Level\Production;

use Ap\Model\Row as Prototype;

use Game\Model\Lib\Building\Level\Provider AS LevelProvider;
use Game\Model\Lib\Resource\Provider AS ResourceProvider;
    
class Row extends Prototype
{
    
    use LevelProvider,
        ResourceProvider{
        ResourceProvider::getSm insteadof LevelProvider;
        ResourceProvider::setSm insteadof LevelProvider;
    }
    
    public function getInputFilter()
    {
        if($this->_inputFilter === null){
            $inputFilter = new \Ap\InputFilter\InputFilter;
            $inputFilter->add(array(
                'name' => 'resource_code',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'count',
                'required' => true,
                'validators'    =>  array(
                    array(
                        'name'  =>  'Int'
                    )
                )
            ));
            $inputFilter->add(array(
                'name' => 'period',
                'required' => true,
                'validators'    =>  array(
                    array(
                        'name'  =>  'Int'
                    )
                )
            ));
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
    
    public function getNeedRowset()
    {
        if($this->_need_rowset === null){
            $this->_need_rowset = $this->getSm()->get('Lib\Building\Level\Production\Need\Table')
                ->fetchAllBy('production_id', $this->id);
        }
        return $this->_need_rowset;
    }
}