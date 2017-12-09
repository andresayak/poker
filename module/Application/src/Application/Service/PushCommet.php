<?php

namespace Application\Service;

class PushCommet
{
    protected $_name = 'PushCommet';
    protected $_options = array();
    protected $_list = array();
    protected $_sm, $_cache, $_host;
    const MAX = 50;
    
    public function __construct($sm, $options = array())
    {
        $this->_sm = $sm;
        $this->_options = $options;
        return $this;
    }
    
    public function getSm()
    {
        return $this->_sm;
    }
    
    public function getList()
    {
        return $this->_list;
    }
    
    public function getOption($name)
    {
        if(!isset($this->_options[$name])){
            throw new \Exception('$this->_options['.$name.'] not set');
        }
        return $this->_options[$name];
    }
    
    public function setOption($name, $value)
    {
        $this->_options[$name] = $value;
        return $this;
    }
    
    public function getWriteUrl($token)
    {
        return str_replace('<token>', $token, $this->getOption('write'));
    }
    
    public function getReadUrl($token)
    {
        return str_replace('<token>', $token, $this->getOption('read'));
    }
    
    public function getAllianceReadUrl($userRow)
    {
        if($userRow->getMemberRow()){
            $token = 'alliance_'.md5(PUSHCOMMET_SALT.'_alliance_'.$userRow->getMemberRow()->alliance_id);
            return $this->getReadUrl($token);
        }else{
            return false;
        }
    }
    
    public function getRegionReadUrl()
    {
        return $this->getReadUrl(md5(PUSHCOMMET_SALT.'_world'));
    }
    
    public function send(array $data, $channel = 'private', $user_id = null, $room_id = null)
    {
        $serviceName = isset($data['serviceName'])?$data['serviceName']:'Cache\Redis';
        if(!isset($this->_list[$serviceName])){
            $this->_list[$serviceName] = array();
        }
        $token = md5($channel.'/'.(is_array($user_id)?implode(',',$user_id):$user_id));
        if(!isset($this->_list[$serviceName][$token])){
            $this->_list[$serviceName][$token] = array(
                'channel'   =>  $channel,
                'user_id'   =>  $user_id,
                'room_id'   =>  $room_id,
                'messages'  =>  array()
            );
        }
        $this->_list[$serviceName][$token]['messages'][] = $data;
    }
    
    public function sendToParseCom($message, $uid, $badge = 0)
    {
        $config = $this->getOption('parse.com');
        $log = '';
        if(AUTH_TYPE == 'default' and $config['enable']){
            $log.=  ' - AUTH_TYPE = '.AUTH_TYPE."\n";
            $log.= ' - parse: ' . $uid . "\n";
            $outData = $this->_encode(array(
                'where' =>  array(
                    'deviceType'    =>  'ios',
                    'deviceToken'   =>  $uid
                ),
                'data'  =>  array(
                    'alert' =>  $message,
                    'badge' =>  $badge
                )
            ));
            
            $rest = curl_init();
            curl_setopt($rest,CURLOPT_URL, $config['url']);
            curl_setopt($rest,CURLOPT_PORT, 443);
            curl_setopt($rest,CURLOPT_POST, 1);
            curl_setopt($rest,CURLOPT_POSTFIELDS, $outData);
            curl_setopt($rest, CURLOPT_HTTPHEADER,
                    array("X-Parse-Application-Id: " . $config['application_id'],
                            "X-Parse-REST-API-Key: " . $config['key'],
                            "Content-Type: application/json"));

            $log.= curl_exec($rest);
        }
        return $log;
    }
    
    public function sendToUser(array $data, $userRow, $parseStatus = false)
    {
        if(!$this->getOption('enable'))
            return;
        $time = strtotime('-1 hour');
        $log = 'sendToUser:'."\n";
        
        if (isset($data['message'])) {
            $message = $data['message'];
        }
        if (isset($data['alert'])) {
            $message = $data['alert'];
        }

        $badge = 0;
        foreach($userRow->getNotificationRowset()->getItems() AS $notificationRow){
            if((int)$notificationRow->getTemplateRow()->push_status){
                $badge++;
            }
        }
        $translator = $this->getSm()->get('MvcTranslator');
        foreach($userRow->getUidRowset()->getItems() AS $uidRow){
            $data['uid_id'] = $uidRow->id;
            if($parseStatus){
                if(isset($data['type']) and $data['type'] == 'notification'){
                    $message = $translator->translate($data['message'], 'default', $uidRow->lang);
                }
                $log.= $this->sendToParseCom($message, $uidRow->uid, $badge);
            }
            if(AUTH_TYPE == 'vk' and $uidRow->auth_type == 'vk'){
                if (isset($data['type']) and $data['type'] == 'notification' and $parseStatus) {
                    if((int)$uidRow->user_id == 12983){
                        $message = $translator->translate($data['message'], 'default', $uidRow->lang);
                        //if($userRow->time_last_connect < $time){
                            $this->sendToUserOnlyVK($message, $userRow);
                        //}
                    }
                }
            }
        }
        $this->send($data, 'private', $userRow->id);
        
        return $log;
    }
    
    public function sendToUserOnlyPush(array $data, $userRow)
    {
        if(!$this->getOption('enable'))
            return;
        $log = '--';
        
        if (isset($data['message'])) {
            $message = $data['message'];
        }
        if (isset($data['alert'])) {
            $message = $data['alert'];
        }

        $badge = 0;
        foreach ($userRow->getNotificationRowset()->getItems() AS $notificationRow) {
            if ((int) $notificationRow->getTemplateRow()->push_status) {
                $badge++;
            }
        }
        $translator = $this->getSm()->get('MvcTranslator');
        foreach ($userRow->getUidRowset()->getitems() AS $uidRow) {
            if (isset($data['type']) and $data['type'] == 'notification') {
                $message = $translator->translate($data['message'], 'default', $uidRow->lang);
            }
            $log.= $this->sendToParseCom($message, $uidRow->uid, $badge) . "\n";
        }

        return $log;
    }
    
    
    public function sendToUserOnlyVK($message, $userRow)
    {
        $service = $this->getSm()->get('Vkontakte');
        $log = '--';
        $badge = $userRow->notification_count+$userRow->message_count;
        $log.= $service->sendNotification($userRow, $message, $badge);
        return $log;
    }
    
    public function getCache()
    {
        if($this->_cache === null){
            $this->_cache = $this->getSm()->get('Chat\Cache\Storage');
        }
        return $this->_cache;
    }
    
    public function addToMainChat($data)
    {
        $name = $this->_name.'_world';
        $connect = $this->getCache()->getConnection();
        $connect->lPush($name, $this->_encode($data));
        if ($connect->lSize($name) > self::MAX){
            $connect->lTrim($name, 0, self::MAX - 1);
        }
        $this->send($data, 'region_chat');
    }
    
    public function reset()
    {
        $name = $this->_name.'_world';
        $connect = $this->getCache()->getConnection();
        $connect->delete($name);
    }

    protected function _encode($data) 
    {
        return json_encode($data);
    }
    
    protected function _decode($data) 
    {
        return json_decode($data);
    }
    
    public function run()
    {
        foreach($this->_list AS $serviceName=>$list){
            $connect = $this->getSm()->get($serviceName)->getConnection();
            foreach($list AS $user_id=>$data){
                if(isset($data['serviceName'])){
                    unset($data['serviceName']);
                }
                $data['proccess_id'] = getmypid();
                $connect->rPush('socketServer', $this->_encode($data));
            }
        }
        $this->resetList();
        return $this;
    }
    
    public function resetList()
    {
        $this->_list = array();
        return $this;
    }
}