<?php

namespace Game\InputFilter\User;

use Game\InputFilter\InputFilter;
use Game\Model\User\Uid\Row AS UidRow;
use Zend\Session\Container;

class PlayFacebook extends InputFilter
{
    protected $_uid_row;
    protected $_facebook_name;
    protected $_facebook_lang;
    protected $_facebook_response;
    
    public function __construct($sm)
    {
        $this->_sm = $sm;
        $config = $this->getSm()->get('config');
        if(isset($config['testAuth'])){
            $this->add(array(
                'name' => 'accessToken',
                'required' => false,
            ));
        }else{
            $this->add(array(
                'name' => 'accessToken',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    // array(
                    //     'name' => 'serverEnabled',
                    //     'break_chain_on_failure' => true
                    // ),
                    array(
                        'name'  =>  'checkBan',
                        'break_chain_on_failure'=>true
                    ),
                )
            ));
        }
        $validators = array();
        //if(!isset($config['testAuth'])){
            $validators[] = array(
                'name' => 'facebookCheckAccess',
                'break_chain_on_failure' => true
            );
        //}
        $validators[] = array(
            'name' => 'uidNotUsed',
            'options' => array(
                'type' => 'fb',
            ),
            'break_chain_on_failure' => true
        );
        $this->add(array(
            'name' => 'userID',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => $validators
        ));
        
        $this->add(array(
            'name' => 'lang',
            'required'              =>  true,
            'allow_empty'           =>  true,
            'continue_if_empty'     =>  true,
            'filters'   =>  array(
                array(
                    'name'  =>  'Lang',
                    'options'   =>  array(
                        'type'  =>  'fb',
                    ),
                )
            )
        ));
        $this->add(array(
            'name'      =>  'referrer_user_id',
            'required'  =>  false,
        ));
    }
    
    public function setFacebookResponse($data)
    {
        $this->_facebook_response = $data;
        return $this;
    }
    public function setFacebookName($name)
    {
        $this->_facebook_name = $name;
        return $this;
    }
    
    public function getFacebookName()
    {
        return $this->_facebook_name;
    }
    
    public function setFacebookLang($lang)
    {
        $this->_facebook_lang = $lang;
        return $this;
    }
    
    public function getFacebookLang()
    {
        return $this->_facebook_lang;
    }
    
    public function setUidRow(UidRow $row)
    {
        $this->_uid_row = $row;
        $this->_user_row = $row->getUserRow();
        return $this;
    }
    
    public function getUidRow()
    {
        return $this->_uid_row;
    }
    
    public function finish()
    {
        $lang = $this->getValue('lang');
        
        if($this->_uid_row === null){
            $table = $this->getSm()->get('User\Table');
            $facebookData = $this->_facebook_response;
            $userRow = $table->createRow(array(
                'email'           =>    (isset($facebookData['email'])?$facebookData['email']:''),
                'social_link'     =>    $facebookData['link'],
                'social_id'  =>    $this->getValue('userID'),
                'social_name'     =>    $this->getFacebookName(),
                'social_type'     =>    AUTH_TYPE  
            ));
            $table->signup($userRow);
            $userRow->save();
            
            $referrer_user_id = (int)$this->getValue('referrer_user_id');
            if($referrer_user_id and $referrerRow = $table->fetchBy('facebook_user_id', $referrer_user_id)){
                $userRow->addReferrerRow($referrerRow);
                $userRow->save();
            }
            
            $uidTable = $this->getSm()->get('User\Uid\Table');
            $uidRow = $uidTable->createRow(array(
                'signkey'   =>  md5(uniqid()),
                'user_id'   =>  $userRow->id,
                'token'     =>  session_id(),
                'lang'      =>  $lang,
                'auth_type' =>  'fb',
                'uid'       =>  $this->getValue('userID'),
                'facebook_access_token' =>  $this->getValue('accessToken')
            ));
            $uidRow->save();
            $this->setUidRow($uidRow);
            $this->setUserRow($userRow);
        }else{
            $this->_uid_row->setFromArray(array(
                'lang'  =>  $lang,
                'facebook_access_token' =>  $this->getValue('accessToken'),
                'token' =>  session_id()
            ))->save();
            $this->setUserRow($this->_uid_row->getUserRow());
        }

        // обновляємо час останнього конекту і інше
        $this->getUidRow()->getUserRow()->updateDayly();
        
        $container = new Container('options');
        $container->lang = $lang;
        
        $authService = $this->getSm()->get('Auth\Service');
        $authService->authenticate($this->getUidRow()->user_id);
    }
}