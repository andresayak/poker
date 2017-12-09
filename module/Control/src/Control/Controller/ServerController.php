<?php

namespace Control\Controller;

use Ap\Controller\AbstractController;

class ServerController extends AbstractController
{
    public function showServersAction()
    {
        $request = $this->getRequest();
        $serverTable = $this->getTable('Server');
        $mode = $request->getParam('mode', 'all');

        $serverRowset = array();
        switch ($mode) {
            case 'disabled':
                $serverRowset = $serverTable->fetchAllByStatus('disabled');
                break;
            case 'enabled':
                $serverRowset = $serverTable->fetchAllByStatus('enabled');
                break;
            case 'all':
            default:
                $serverRowset = $serverTable->fetchAll();
                break;
        }
        if (count($serverRowset) == 0) {
            return "There are no users in the database\n";
        }

        $result = '';

        foreach ($serverRowset as $serverRow) {
            $result .= $serverRow->id . ' ' .$serverRow->name . ' ' . $serverRow->host . ' '.$serverRow->status."\n";
        }
        return $result;
    }
    
    public function tumblerAction()
    {
        $request = $this->getRequest();
        $mode = $request->getParam('mode', 'enabled');
        if(!$name = $request->getParam('name', false)){
            return "ServerName not set\n";
        }

        if(!$serverRow = $this->getTable('Server')->fetchByName($name)){
            return "Server not found\n";
        }
        
        $serverRow->status = $mode;
        $serverRow->save();

        return "Done\n"; 
    }
}
