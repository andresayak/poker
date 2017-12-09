<?php

namespace Game\Validator;

 class AbstractValidator extends \Zend\Validator\AbstractValidator
{
    const INVALID_CALLBACK = 'callbackInvalid';
    const FACEBOOK_INVALID_ID = 'facebook_invalid_id';
    const FACEBOOK_INVALID_PRICE = 'facebook_invalid_price';
    const INVALID_VALUE = 'callbackValue';
    const ACCESS_DENIED = 'noAccess';
    const ERROR_NO_RECORD_FOUND = 'noRecordFound';
    const ERROR_RECORD_FOUND    = 'recordFound';
    const TRANSPORT_LIMIT       = 'transport_limit';
    const ATTACK_VILLAGE_LIMIT       = 'attack_village_limit';
    const ATTACK_GUNPOWDER_LIMIT       = 'attack_gunpowder_limit';
    const TRANSPORT_NOT_EATING  = 'transport_not_eating';
    const TRANSPORT_RESOURCE_COUNT_LIMIT       = 'transport_resource_count_limit';
    const TRANSPORT_GEO_INVALID = 'transport_geo_invalid';
    const GEO_POINT_NOT_EMPTY = 'geopoint_not_empty';
    const WALL_MAX_LEVEL_LIMIT = 'wall_max_level_limit';
    const WALL_MAX_LEVEL = 'wall_max_level';
    const WALL_SLOT = 'wall_slot';
    const CITYNAME_INVALID = 'cityname_invalid';
    const CITYLIMIT = 'city_limit';
    const TRANSPORT_NO_YOUR_CITY = 'transport_no_your_city';
    const TRANSPORT_NO_YOUR_VILLAGE = 'transport_no_your_village';
    const ATTACK_YOUR_VILLAGE = 'attack_your_village';
    const ATTACK_YOUR_CITY = 'attack_your_city';
    const ATTACK_CITY_IN_CAP = 'attack_city_in_cap';
    const SPY_YOUR_CITY = 'spy_your_city';
    const SERVER_DISABLED = 'server_disabled';
    
    const USERNAME_INVALID = 'username_invalid';
    const FORGOT_KEY_INVALID = 'forgot_key_invalid';
    const CONFIRM_KEY_INVALID = 'confirm_key_invalid';
    const PROMO_NOT_ACTIVE = 'promot_not_active';
    const ATTACK_CAPITAL_NO_ALLIANCE = 'attack_capital_no_alliance';
    const ATTACK_YOUR_CAPITAL = 'attack_your_capital';
    const DEFEND_CAPITAL_NO_TIME = 'defend_capital_no_time';
    const DEFEND_NO_YOUR_CAPITAL = 'defend_no_your_capital';
    const OBJECT_LIST_INVALID = 'object_list_invalid';
    const OBJECT_NO_FOUND = 'object_no_found';
    const OBJECT_TYPE_INVALID = 'object_type_invalid';
    const OBJECT_NO_MOVE = 'object_no_move';
    const OBJECT_NO_UNIT = 'object_no_unit';
    const OBJECT_NO_SPY = 'object_no_spy';
    const OBJECT_NO_USABLE = 'object_not_usable';
    const OBJECT_NOT_ENOUGH_COUNT = 'not_enough_count';
    const OBJECT_NOT_ENOUGH_LEVEL = 'not_enough_level';
    const REGION_WAR_NOT_CREATE = 'region_war_not_create';
    const REGION_WAR_NOT_END = 'region_war_not_end';
    const REGION_WAR_NOT_START = 'region_war_not_start';
    const REGION_WAR_IS_START = 'region_battle_is_start';
    const REGION_WAR_MIN_PRICE= 'region_battle_min_price';
    const REGION_WAR_YOUR_BATTLE = 'region_battle_your_battle';
    const REGION_WAR_YOUR_CAPITAL = 'region_battle_your_capital';
    const EVENT_ARLEDY_CREATED = 'event_already_created';
    const IDS_LIST_INVALID = 'ids_list_invalid';
    const ORDER_HAVE_NO_USABLE_OBJECT = 'order_have_not_usable_object';
    const ORDER_HAVE_OBJECT_FROM_ITEMS = 'order_have_object_from_items';
    const ORDER_HAVE_TYPE_GEMS = 'order_have_type_gem';
    const USER_NOT_HAVE_ALLIANCE = 'user_not_have_alliance';
    const INVERTED_IS_DISABLED = 'inverted_is_disabled';
    const BAN_STATUS = 'ban_status';
    const OTHER_ERROR = 'other_error';
    const INVALID_VER = 'invalid_version';
    const INVALID_TRANSACTION = 'invalid_transaction';
    protected  $messageTemplates = array(
        self::FACEBOOK_INVALID_ID => 'Invalid facebook app ID',
        self::FACEBOOK_INVALID_PRICE => 'Invalid offert price',
        self::ERROR_NO_RECORD_FOUND => "No record matching the input was found %value%",
        self::ERROR_RECORD_FOUND    => "A record matching the input was found",
        self::INVALID_VALUE    => "The input is not valid",
        self::TRANSPORT_GEO_INVALID    => 'Invalid geo positition %value%',
        self::GEO_POINT_NOT_EMPTY => 'Geo point is not empty',
        self::WALL_MAX_LEVEL_LIMIT => 'Max level limit ',
        self::WALL_MAX_LEVEL => 'Is max level',
        self::WALL_SLOT => 'Is not empty slot',
        self::CITYNAME_INVALID=>'City name have invalid symbol',
        self::CITYLIMIT => 'City limit %value%',
        self::USERNAME_INVALID => 'username is invalid',
        self::FORGOT_KEY_INVALID => 'forgot key is invalid',
        self::CONFIRM_KEY_INVALID => 'confirm key is invalid',
        
        self::TRANSPORT_NO_YOUR_CITY => 'You can not transport not your city',
        self::TRANSPORT_NO_YOUR_VILLAGE => 'You can not transport not your village',
        self::ATTACK_YOUR_VILLAGE => 'You can not attack your village',
        self::ATTACK_YOUR_CITY => 'You can not attack your city',
        self::ATTACK_CITY_IN_CAP => 'City under a dome',
        self::SPY_YOUR_CITY => 'You can not spy your city',
        self::SERVER_DISABLED => 'You can not signup on this server',
        
        self::ACCESS_DENIED    => "Access denied",
        self::INVALID_CALLBACK => "An exception has been raised within the callback",
        self::TRANSPORT_LIMIT   =>  "Transport limit",
        self::ATTACK_VILLAGE_LIMIT  =>  'Village limit',
        self::ATTACK_GUNPOWDER_LIMIT    =>  'Gunpowder limit',
        self::TRANSPORT_NOT_EATING => 'Not enough "%value%" foods',
        self::TRANSPORT_RESOURCE_COUNT_LIMIT => 'Transport resource limit is %value%',
        self::ATTACK_CAPITAL_NO_ALLIANCE => 'For an attack on the capital you have to be in an alliance',
        self::ATTACK_YOUR_CAPITAL => 'You can not attack the capital of which is owned by your alliance',      
        self::DEFEND_CAPITAL_NO_TIME    => 'Your troops will not have time before the start of the battle',
        self::DEFEND_NO_YOUR_CAPITAL    =>  'Capital not owned by your alliance',
        self::OBJECT_LIST_INVALID   =>  'List have invalid format',
        self::OBJECT_NO_FOUND       =>  'Object "%value%" not found',
        self::OBJECT_TYPE_INVALID   =>  'Object type "%value%" is invalid',
        self::OBJECT_NO_MOVE        =>  'Object "%value%" not move',
        self::OBJECT_NO_UNIT        =>  'Object "%value%" no unit',
        self::OBJECT_NO_SPY         =>  'Object "%value%" no spy',
        self::OBJECT_NO_USABLE      => 'Object "%value%" not usabled',
        self::ORDER_HAVE_NO_USABLE_OBJECT   =>  'Order %value% have no usable object',
        self::ORDER_HAVE_TYPE_GEMS  =>  'Order %value% have type gems',
        self::ORDER_HAVE_OBJECT_FROM_ITEMS  =>  'Order %value% object from items list',
        self::OBJECT_NOT_ENOUGH_COUNT   =>  'Object "%value%" not enough count',
        self::OBJECT_NOT_ENOUGH_LEVEL   =>  'Object "%value%" not enough level',
        self::REGION_WAR_NOT_CREATE =>  'Region battle not created',
        self::REGION_WAR_NOT_START  =>  'Region battle not started',
        self::REGION_WAR_NOT_END    => 'Region battle not ended',
        self::REGION_WAR_IS_START       =>  'Region battle is started',
        self::REGION_WAR_MIN_PRICE      =>  'Low price',
        self::REGION_WAR_YOUR_BATTLE    =>  'It is your battle',
        self::REGION_WAR_YOUR_CAPITAL   =>  'It is your capital',
        self::EVENT_ARLEDY_CREATED      =>  'Event already created',
        self::IDS_LIST_INVALID          =>  'Ids list invalid',
        self::USER_NOT_HAVE_ALLIANCE    => 'You dont have alliance',
        self::INVERTED_IS_DISABLED      =>    'Inverted is disabled',
        self::BAN_STATUS    =>  'User or/and IP address is banned',  
        self::OTHER_ERROR   =>  'Other error: %value%',
        self::INVALID_VER   => 'Invalid API version',
        self::INVALID_TRANSACTION => 'Invalid transaction_id',
        self::PROMO_NOT_ACTIVE => 'promo not active'
    );
    
    protected $_sm, $_filter;
    protected $options = array(
        'callback'         => null,     // Callback in a call_user_func format, string || array
    );
    
    public function getList()
    {
        return $this->messageTemplates;
    }
    
    public function __construct($options = null)
    {
        if (is_callable($options)) {
            $options = array('callback' => $options);
        }
        parent::__construct($options);
    }
    
    public function getCallback()
    {
        return $this->options['callback'];
    }
    
    public function setCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new \Exception('Invalid callback given');
        }
        $this->options['callback'] = $callback;
        return $this;
    }
    
    public function getFilter()
    {
        return $this->_filter;
    }
    
    public function setFilter($filter)
    {
        $this->_filter = $filter;
        return $this;
    }
    
    public function setResult($value) 
    {
        if($this->getCallback()){
            call_user_func_array($this->getCallback(), array($value));
        }
    }
    
    public function isValid($value) {
    }
}