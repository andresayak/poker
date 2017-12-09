<?php

namespace Game\Model\Shop;

use Ap\Model\Rowset AS Prototype;

class Rowset extends Prototype
{
    
    public function getRowsetByAuth($auth_type)
    {
        
        return $this->getRowsetBy(function($row) use($auth_type){
            if($row->auth_type == $auth_type || !$row->auth_type){
                return true;
            }
            return false;
        });
    }
    
    public function getCountPromo($shopRow)
    {
        $count = 0;
        foreach ($this->getItems() AS $row) {
            if ($row->isPromo() && $row->price == $shopRow->price 
                && $row->promotype==$shopRow->promotype
                && ($row->auth_type == AUTH_TYPE or !$row->auth_type)
            ) {
                $count++;
            }
        }
        return $count;
    }
    
    public function getOffsetPromo($shopRow)
    {
        $offset = 0;
        foreach ($this->getItems() AS $row) {
            if ($row->isPromo() && $row->price == $shopRow->price 
                && $row->promotype==$shopRow->promotype
                && ($row->auth_type == AUTH_TYPE or !$row->auth_type)
            ) {
                $offset++; 
                if ($shopRow->id == $row->id) {
                    break;
                }
            }
        }
        return $offset;
    }
    
    public function isPromoActive($shopRow)
    {
        if($shopRow->isPromo()){
            $count = $this->getCountPromo($shopRow);
            $offset = $this->getOffsetPromo($shopRow);
            $shopRow->promoTime($count, $offset);
            return $shopRow->isPromoActive();
        }
        return false;
    }
    
    public function promoTime($shopRow)
    {
        if($shopRow->isPromo()){
            $count = $this->getCountPromo($shopRow);
            $offset = $this->getOffsetPromo($shopRow);
            $shopRow->promoTime($count, $offset);
            return $count.'/'.$offset;
        }
    }
    
    public function toArrayForApi($recursive = true, $cacheData = false)
    {
        $data = array();
        foreach($this->getItems() AS $row){
            $status = false;
            $log = '';
            if($row->isPromo() && $row->promotype == 'daily'){
                $log=$this->promoTime($row);
                if($this->isPromoActive($row)){
                    $status = true;
                }
            }else{
                $status = true;
            }
            if($status){
                $data[] = $row->toArrayForApi($recursive, $cacheData);
            }
        }
        return $data;
    }
}