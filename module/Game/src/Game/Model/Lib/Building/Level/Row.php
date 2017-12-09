<?php

namespace Game\Model\Lib\Building\Level;

use Ap\Model\Row as Prototype;

class Row extends Prototype
{
    use \Game\Model\Lib\Building\Provider;
    protected $_attribute_rowset, $_need_rowset, $_depend_rowset, $_production_rowset;
    
    public function getInputFilter()
    {
        if($this->_inputFilter === null){
            $inputFilter = new \Ap\InputFilter\InputFilter;
            $inputFilter->add(array(
                'name' => 'level',
                'required' => true,
                'validators'    =>  array(
                    array(
                        'name'  =>  'Int'
                    )
                )
            ));
            $inputFilter->add(array(
                'name' => 'time_build',
                'required' => true,
                'validators'    =>  array(
                    array(
                        'name'  =>  'Int'
                    )
                )
            ));
            $inputFilter->add(array(
                'name' => 'exp',
                'required' => true,
                'validators'    =>  array(
                    array(
                        'name'  =>  'Int'
                    )
                )
            ));
            $inputFilter->add(array(
                'name' => 'user_level_need',
                'required' => false,
                'filters'   =>  array(
                    array(
                        'name'=>'ToNull'
                    )
                ),
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
    
    public function getDependRowset()
    {
        if($this->_depend_rowset === null){
            $this->_depend_rowset = $this->getSm()->get('Lib\Building\Level\Depend\Table')
                ->fetchAllBy('level_id', $this->id);
        }
        return $this->_depend_rowset;
    }
    
    public function getNeedRowset()
    {
        if($this->_need_rowset === null){
            $this->_need_rowset = $this->getSm()->get('Lib\Building\Level\Need\Table')
                ->fetchAllBy('level_id', $this->id);
        }
        return $this->_need_rowset;
    }
    
    public function getAttributeRowset()
    {
        if($this->_attribute_rowset === null){
            $this->_attribute_rowset = $this->getSm()->get('Lib\Building\Level\Attribute\Table')
                ->fetchAllBy('level_id', $this->id);
        }
        return $this->_attribute_rowset;
    }
    
    public function getProductionRowset()
    {
        if($this->_production_rowset === null){
            $this->_production_rowset = $this->getSm()->get('Lib\Building\Level\Production\Table')
                ->fetchAllBy('level_id', $this->id);
        }
        return $this->_production_rowset;
    }
}