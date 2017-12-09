<?php

namespace Game\Validator;

class CheckChatStoplist extends AbstractValidator 
{
    protected $options = array(
    );
    protected $_stoplist;
    
    public function getStoplist()
    {
        if(null === $this->_stoplist){
            $configFile = realpath(dirname(INDEX_PATH) . '/../config') . '/stopwords.php';
            $this->_stoplist = include $configFile;
        }
        return $this->_stoplist;
    }


    public function isValid($value)
    {
        $result = true;
        foreach($this->getStoplist() AS $world){
            if(preg_match('/\b'.preg_quote($world, '/').'\b/iu', $value)){
                $result = false;
                $this->error(self::BAN_STATUS);
                break;
            }
        }
        if(!$result){
            $userRow = $this->getFilter()->getUserRow();
            $userRow->ban_chat_status = 'on';
            $userRow->ban_chat_timeend = time() + 3600;
            $userRow->save();
        }
        return $result;
    }
}