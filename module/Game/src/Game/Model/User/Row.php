<?php

namespace Game\Model\User;

use Ap\Model\Row as Prototype;
use Game\Model\Lib\Attr\ValueList;

class Row extends Prototype
{
    protected $_password;
    protected $_quest_manager, $_city_rowset, $_attr_rowset, $_notification_rowset, $_message_rowset, $_level_row, $_friend_rowset;
    protected $_uid_rowset, $_member_row, $_object_rowset;
    protected $_inputFilter;
    
    public function getInputFilter()
    {
        if($this->_inputFilter === null){
            $inputFilter = new \Ap\InputFilter\InputFilter;
            $inputFilter->add(array(
                'name' => 'email',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    new \Zend\Validator\Db\NoRecordExists(
                        array(
                            'adapter'   =>  $this->getTable()->getTableGateway()->getAdapter(),
                            'table'     =>  $this->getTable()->getName(),
                            'field'     =>  'email',
                            'exclude'   =>  (($this->id)?array(
                                'field' => 'id',
                                'value' => $this->id
                            ):null)
                        )
                    )
                )
            ));

            $inputFilter->add(array(
                'name' => 'role',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name' => 'ban_status',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name' => 'social_id',
                'required' => false,
            ));

            $inputFilter->add(array(
                'name' => 'social_name',
                'required' => false,
            ));

            $inputFilter->add(array(
                'name' => 'social_link',
                'required' => false,
            ));

            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
    
    public function getQuestManager()
    {
        if($this->_quest_manager === null){
            $this->_quest_manager = new \Game\Service\QuestManager($this->getSm());
            $this->_quest_manager->setUserRow($this);
        }
        return $this->_quest_manager;
    }
    
    public function getMoneyCount(){
        $objectRow = $this->getObjectByCode('chip');
        return ($objectRow)?$objectRow->count:0;
    }
    
    public function getObjectByCode($code)
    {
        return $this->getObjectRowset()->getBy('object_code', $code);
    }
    
    public function updateObjectRow($code, $count = null)
    {
        $userObjectTable = $this->getSm()->get('User\Object\Table');
        if ($userObjectRow = $this->getObjectRowset()->getBy('object_code', $code)) {
            $userObjectRow->blockForUpdate();
            $userObjectRow->count+= $count;
            $userObjectRow->save();
        } else {
            if($count!==null and $count<0){
                throw new \Exception('ObjectRow not found');
            }
            $userObjectRow = $userObjectTable->createRow(array(
                'user_id'       => $this->id,
                'object_code'   => $code,
                'count'         => $count,
            ));
            $userObjectRow->save();
            $this->getObjectRowset()->add($userObjectRow);
        }
        return $this;
    }
    
    public function getObjectRowset()
    {
        if(null === $this->_object_rowset){
            $this->_object_rowset = $this->getSm()->get('User\Object\Table')->fetchAllBy('user_id', $this->id);
            foreach($this->_object_rowset->getItems() AS $objectRow){
                $objectRow->setUserRow($this);
            }
        }
        return $this->_object_rowset;
    }
    
    public function getLevelRow()
    {
        if(null === $this->_level_row){
            $this->_level_row = $this->getSm()->get('Lib\Level\User\Rowset')->getBy('level', $this->level);
        }
        return $this->_level_row;
    }
    
    public function getPassword()
    {
        return $this->_password;
    }
    
    public function setPassword($password)
    {
        $this->_password = $password;
        $this->password = $this->getSm()->get('Auth\Service')->passwordHash($password);
        return $this;
    }
    
    public function isFriend($user_id)
    {
        $friendRow = $this->getFriendRowset()->getBy('user_id_to', $user_id);
        return ($friendRow and $friendRow->status == 'approved');
    }
    
    public function getNextLevelRow()
    {
        return $this->getSm()->get('Lib\Level\User\Rowset')->getBy('level', $this->level + 1);
    }
    
    public function getFriendRowset()
    {
        if(null === $this->_friend_rowset){
            $this->_friend_rowset = $this->getSm()->get('User\Friend\Table')->fetchAllByUserFrom($this->id);
        }
        return $this->_friend_rowset;
    }
    
    public function getUidRowset()
    {
        if(null === $this->_uid_rowset){
            $this->_uid_rowset = $this->getSm()->get('User\Uid\Table')->fetchAllActive($this->id);
        }
        return $this->_uid_rowset;
    }
    
    public function getNotificationRowset()
    {
        if(null === $this->_notification_rowset){
            $this->_notification_rowset = $this->getSm()->get('User\Notification\Table')->fetchAllNoreadByUserId($this->id);
        }
        return $this->_notification_rowset;
    }
    
    public function getMessageRowset()
    {
        if(null === $this->_message_rowset){
            $this->_message_rowset = $this->getSm()->get('User\Message\Table')->fetchAllNoreadByUserId($this->id);
        }
        return $this->_message_rowset;
    }
    
    public function getCityRowset()
    {
        if(null === $this->_city_rowset){
            $this->_city_rowset = $this->getSm()->get('City\Table')->fetchAllByUserId($this->id);
        }
        return $this->_city_rowset;
    }
    
    public function getAttrRowset()
    {
        if(null === $this->_attr_rowset){
            $this->_attr_rowset = $this->getSm()->get('User\Attr\Table')->fetchAllByUserId($this->id);
        }
        return $this->_attr_rowset;
    }
    
    public function toArrayForApiBase()
    {
        $data = array();
        foreach(array('id', 'slot_attempt', 'exp', 'time_add', 'time_last_connect', 
            'level', 'notification_count', 'message_count',
            'social_id', 'social_type', 'social_name', 'social_link') AS $key)
            $data[$key] = $this->{$key};
        $redisChat = $this->getSm()->get('Chat\Cache\Storage');
        $data['room_id'] = $redisChat->getConnection()->get('poker_user' . $this->id);
        $data['objectList'] = $this->getObjectRowset()->toArrayForApi();
        return $data;
    }
    
    public function toArrayForApi()
    {
        $data = $this->toArrayForApiBase();
            /*if($this->getLevelRow()){
                $data['level_row'] = $this->getLevelRow()->toArray();
            }
            if($this->getNextLevelRow()){
                $data['next_level_row'] = $this->getNextLevelRow()->toArray();
            }
            $data['city_rowset'] = $this->getCityRowset()->toArrayForApi(false);
            $data['attr_rowset'] = $this->getAttrRowset()->toArrayForApi();*/
        return $data;
    }
    public function blockForUpdate()
    {
        throw new \Exception('Block user row');
    }
    
    protected function _preUpdate()
    {
        $data = $this->toArrayForSave();
        if(isset($data['exp'])){
            foreach($this->getSm()->get('Lib\Level\User\Rowset')->getItems() AS $levelRow){
                if($this->exp >= $levelRow->exp){
                    $this->level = $levelRow->level;
                }
            }
        }
    }
    
    public function updateAttributes()
    {
        $attrlist = new ValueList($this->getSm()->get('lib\Attr\Rowset'), 'user');
        $attrlist->addCitys($this->getCityRowset());
        $data = array();
        foreach($attrlist->getItems() AS $attr_code=>$item){
            $data[$attr_code] = $item->getMaxValue();
        }
        $attrTable = $this->getSm()->get('User\Attr\Table');
        $log = '';
        foreach($data AS $attr_code=>$value){
             try{
                $attrRow = $this->getAttrRowset()->getBy('attr_code', $attr_code);
            }  catch (Attr\NotSetException $e){
                $attrRow = false;
            }
            if ($attrRow) {
                if ($attrRow->getAttrRow()->typechange == 'auto') {
                    if($value === null){
                        $log.= ' - delete(U) ' . $attrRow->attr_code . "\n";
                        $attrRow->delete();
                    }elseif ($attrRow->value != $value) {
                        $log.= ' - update(U) ' . $attrRow->attr_code . ' = ' . $value. "\n";
                        $attrRow->value = $value;
                        $attrRow->save();
                    }
                }
            }else {
                if($value !== null){
                    $attrRow = $attrTable->createRow(array(
                        'value' => $value,
                        'attr_code' => $attr_code
                    ));
                    $attrRow->setUserRow($this);
                    $attrRow->save();
                    $this->getAttrRowset()->add($attrRow);
                    $log.= ' - add(U) ' . $attrRow->attr_code . ' = ' . $value . "\n";
                }
            }
        }
        return $log;
    }
    
    public function isMemberRow()
    {
        return ($this->alliance_id and $this->getMemberRow());
    }
    
    public function getMemberRow()
    {
        if($this->_member_row === null){
            $this->_member_row = $this->getSm()->get('Alliance\Member\Table')->fetchByUserId($this->id);
        }
        return $this->_member_row;
    }
    
    public function getRegionRow()
    {
        return $this->getSm()->get('Region\Rowset')->getBy('id', $this->region_id);
    }
    
    public function getAttrValue($attr_code)
    {
        try{
            $attrRow = $this->getAttrRowset()->getBy('attr_code', $attr_code);
        }  catch (\Game\Model\City\Attr\NotSetException $e){
            $attrRow = false;
        }
        return ($attrRow)?$attrRow->getValue():null;
    }
    
    public function getBanChatStatus()
    {
        if($this->ban_chat_timeend !== null 
            and $this->ban_chat_timeend < time()
        ){
            
            $this->ban_chat_status = 'off';
            $this->save();
        }
        return $this->ban_chat_status;
    }
    
    public function addInfo(&$data, $prefix = false)
    {
        $prefix = ($prefix===false)?'':$prefix.'_';
        $data[$prefix . 'user_id'] = $this->id;
        $data[$prefix . 'social_id'] = $this->social_id;
        $data[$prefix . 'social_type'] = $this->social_type;
        $data[$prefix . 'social_link'] = $this->social_link;
        $data[$prefix . 'social_name'] = $this->social_name;
    }
    
    public function updateDayly()
    {
        if($this->time_last_connect + 300 < time()){
            $this->updateSlotAttempt();
            $this->time_last_connect = time();
            $this->save();
        }
        return $this;
    }

    public function updateSlotAttempt() 
    {
        if($this->slot_time_update < strtotime('today')) {
            if($this->slot_rate and $this->slot_time_update >= strtotime('yesterday')) {
                $this->slot_attempt += ($this->slot_rate - $this->slot_attempt);
            }else{
                $this->slot_rate = 5;
                $this->slot_attempt = $this->slot_rate;
            }
            $this->slot_time_update = time();
        }
        return $this;
    }

    public function updateMoney($prize) 
    {
        $this->slot_attempt -= 1;
        if ($this->slot_attempt < 0) {
            $this->slot_attempt = 0;
        }

        $this->updateObjectRow('chip', $prize);

        $this->save();
        return $this;
    }
}