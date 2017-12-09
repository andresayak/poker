<?php

namespace Game\Model\User\Interaction;

use Ap\Model\Rowset as Prototype;

class Rowset extends Prototype
{
    public function toArrayForApi($recursive = true)
    {
        $data = array();
        $auth_user_id = $this->getSm()->get('Auth\Service')->getUserRow()->id;
        foreach($this->getItems() AS $row){
            $status = $auth_user_id == $row->user_id_from;
            $data[] = array(
                'user_id'       =>  (($status)?$row->user_id_to:$row->user_id_from), 
                'username'      =>  (($status)?$row->getUserToRow()->username:$row->getUserFromRow()->username),
                'time_update'   =>  $row->time_update, 
            );
        }
        return $data;
    }
}