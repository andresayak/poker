<?php


namespace Control\Controller;

use Ap\Controller\AbstractController;

class UserController extends AbstractController
{
    public function indexAction()
    {
        throw new \Exception('test');
    }
    
    public function getNotificationsAction()
    {
        $user_id = $this->params()->fromRoute('user_id');
        
        $userRow = $this->getServiceLocator()->get('User\Table')->fetchBy('id', $user_id);
        
        $translator = $this->getServiceLocator()->get('MvcTranslator');
        
        if($userRow){
            foreach($userRow->getUidRowset()->getItems() AS $uidRow){
                $notificationRowset = $this->getServiceLocator()->get('Lib\Notification\Rowset');
                foreach($notificationRowset->getItems() AS $notificationRow){
                    if((int)$notificationRow->push_status){
                        $message = $translator->translate('notification.'.$notificationRow->code, 'default', $uidRow->lang);
                        echo $notificationRow->code."\n".$message."\n\n";
                    }
                }
            }
        }
    }
    
    public function duelListAction()
    {
        $duelService = $this->getServiceLocator()->get('Duel\Service');
        
        $list = $duelService->showList();
        foreach($list AS $item){
            if($duelService->isPlayUser($item->user_id)){
                echo 'wait user '.$item->user_id."\n";
            }else{
                echo 'bad user '.$item->user_id."\n";
            }
        }
        return "Done!\n";
    }
    
    public function tavernAction()
    {
        $userTable = $this->getServiceLocator()->get('User/Table');
        
        $tavernService = $this->getServiceLocator()->get('Tavern\Service');
        $duelService = $this->getServiceLocator()->get('Duel\Service');
        $commetService = $this->getServiceLocator()->get('PushCommet\Service');
        $transaction = $this->getServiceLocator()->get('Transaction');
        
        $list = $tavernService->showList();
        print_r($list);
        foreach($list AS $item){
            if($item->time_add + 5 < time()){
                if($tavernService->isPlayUser($item->user_id)){
                    $userRow = $userTable->fetchBy('id', $item->user_id);
                    if($userRow){
                        $tavernService->cancelForUserGame($userRow->id);
                        $tavernService->setUserRow($userRow);
                        $transaction->run(function() use($commetService, $userRow, $item, $userTable){
                            $playerRate = (int)$item->rate;
                            $randWin = rand(1, TAVERN_RATE);
                            if($randWin == 1){
                                $status = 'win';
                                $playerWinGems = rand(20, $playerRate) + $playerRate;
                                $playerLossGems = 0;
                            }else{
                                $status = 'loss';
                                $playerWinGems = 0;
                                $playerLossGems = $playerRate;
                            }
                            $bot_id = rand(1, 1000000);
                            $result = array(
                                'type'          =>  'tavern',
                                'result'       =>  array(
                                    'status'    =>  $status,
                                    'win_gems'  =>  $playerWinGems,
                                    'loss_gems' =>  $playerLossGems,
                                    'player_user_id'    =>  $bot_id,
                                    'player_username'    =>  'Player'.$bot_id,
                                ),
                                'exp'   =>  $userRow->exp,
                                'gems'  =>  $userRow->getGemCount(),
                            );
                            $commetService->sendToUser(
                                $result, $userRow);
                            $userObjectRow = $userRow->getObjectByCode('gem');
                            $userObjectRow->blockForUpdate();
                            $userRow->updateObjectRow('gem', $playerWinGems - $playerLossGems);
                        });
                        echo 'end tavern for user '.$userRow->id."\n";
                    }else{
                        echo 'user not found '.$userRow->id."\n";
                    }
                }
            }else{
                echo 'old tavern for user '.$item->user_id."\n";
            }
        }
        
        $list = $duelService->showList();
        foreach($list AS $item){
            if($item->time_add + 5 < time()){
                if($duelService->isPlayUser($item->user_id)){
                    $userRow = $userTable->fetchBy('id', $item->user_id);
                    if($userRow){
                        $duelService->cancelForUserGame($userRow->id);
                        $duelService->setUserRow($userRow);
                        $transaction->run(function() use($commetService, $userRow, $item, $userTable){
                            $randWin = rand(1, DUEL_RATE);
                            if($randWin == 1){
                                $status = 'win';
                                $playerWinGems = 500;
                            }else{
                                $status = 'loss';
                                $playerWinGems = 0;
                            }
                            $bot_id = rand(1, 1000000);
                            $result = array(
                                'type'          =>  'duel',
                                'result'       =>  array(
                                    'status'    =>  $status,
                                    'win_gems'  =>  $playerWinGems,
                                    'player_user_id'    =>  $bot_id,
                                    'player_username'    =>  'Player'.$bot_id,
                                ),
                                'exp'   =>  $userRow->exp,
                                'gems'  =>  $userRow->getGemCount(),
                            );
                            $commetService->sendToUser(
                                $result, $userRow);
                            $userObjectRow = $userRow->getObjectByCode('gem');
                            $userObjectRow->blockForUpdate();
                            $userRow->updateObjectRow('gem', $playerWinGems);
                        });
                        echo 'end duel for user '.$userRow->id." list in push (".count($commetService->getList()).")\n";
                    }
                }
            }
        }
        
        return "Done!\n";
    }
    
    public function addItemsAction()
    {
        $userObjectTable = $this->getServiceLocator()->get('User\Object\Table');
        $user_id = $this->params()->fromRoute('user_id');
        $count = intval($this->params()->fromRoute('count'));
        $userRow = $this->getServiceLocator()->get('User\Table')->fetchBy('id', $user_id);
        if(!$userRow){
            return 'user not found [user_id = '.$user_id.']' . "\n";
        }
        $transaction = $this->getServiceLocator()->get('Transaction');
        $itemRowset = $this->getServiceLocator()->get('Lib\Object\Rowset')->getItemRowset();
        $n = 0;
        foreach($itemRowset->getItems() AS $objectRow){
                echo ++$n.') +'.$count.' '.$objectRow->code."\n";
                $transaction->run(function() use($userRow, $objectRow, $userObjectTable, $count){
                    $userObjectRow = $userObjectTable->fetchByArray(array(
                        'user_id'       =>  $userRow->id,
                        'object_code'   =>  $objectRow->code,
                    ))->current();
                    
                    if(!$userObjectRow){
                        $userObjectRow = $userObjectTable->createRow(array(
                            'user_id'       =>  $userRow->id,
                            'object_code'   =>  $objectRow->code,
                            'count'         =>  0
                        ));
                    }
                    $userObjectRow->count+= $count;
                    if($userObjectRow->count){
                        $userObjectRow->save();
                    }
                });
        }
        return 'Done!'."\n";
    }
    
    public function copyItemsAction()
    {
        $cityObjectTable = $this->getServiceLocator()->get('City\Object\Table');
        $userObjectTable = $this->getServiceLocator()->get('User\Object\Table');
        $transaction = $this->getServiceLocator()->get('Transaction');
        $cityObjectRowset = $cityObjectTable->fetchAll();
        $n = 0;
        foreach($cityObjectRowset->getItems() AS $cityObjectRow){
            if($cityObjectRow->getObjectRow()->type == 'item'){
                echo ++$n.')'.$cityObjectRow->count.' '.$cityObjectRow->object_code."\n";
                $transaction->run(function() use($cityObjectRow, $userObjectTable, $cityObjectTable){
                    $userObjectRow = $userObjectTable->fetchByArray(array(
                        'user_id'       =>  $cityObjectRow->getCityRow()->user_id,
                        'object_code'   =>  $cityObjectRow->object_code,
                    ))->current();
                    if(!$userObjectRow){
                        $userObjectRow = $userObjectTable->createRow(array(
                            'user_id'       =>  $cityObjectRow->getCityRow()->user_id,
                            'object_code'   =>  $cityObjectRow->object_code,
                            'count'         =>  0
                        ));
                    }
                    $userObjectRow->count+= $cityObjectRow->count;
                    if($userObjectRow->count){
                        $userObjectRow->save();
                    }
                    $cityObjectRow->delete();
                });
            }
        }
        return 'Done!'."\n";
    }

    public function testpushAction()
    {
        if(!$user_id = $this->params()->fromRoute('user_id', false) 
            or !$userRow = $this->getTable('User')->fetchBy('id', $user_id)
        ){
            return 'user not found'."\n";
        }
        
        $service = $this->getServiceLocator()->get('PushCommet\Service');
        return $service->sendToUser(array('alert'=>'Hello '.$userRow->username.' player!'), $userRow, true);
    }
    
    public function clearUidsAction()
    {
        $table = $this->getTable('User\Uid');
        $table->getTableGateway()->initialize();
        $table->getTableGateway()->delete(array(
            'time_connect < ?'  => strtotime('-1 day')
        ));
        return 'Done!'."\n";
    }
    
    
    public function testNotificationAction()
    {
        if(!$user_id = $this->params()->fromRoute('user_id', false) 
            or !$userRow = $this->getTable('User')->fetchBy('id', $user_id)
        ){
            return 'user not found'."\n";
        }
        
        $service = $this->getServiceLocator()->get('PushCommet\Service');
        $periods = array(
            array(
                'message'   =>  'city_event_finish_unit_create',
            ),
        );
        foreach($periods AS $period){
            echo $service->sendToUser(array(
                'type'      =>  'notification',
                'message'   =>  'notification.'.$period['message']
            ), $userRow, true)."\n";
        }
        return 'Done!'."\n";
    }
    
    public function testQuestAction()
    {
        if(!$user_id = $this->params()->fromRoute('user_id', false) 
            or !$userRow = $this->getTable('User')->fetchBy('id', $user_id)
        ){
            return 'User not found'."\n";
        }
        $type = $this->params()->fromRoute('type', false);
        $userRow->getQuestManager()
            ->event($type, array());
        
        return 'Done!'."\n";
    }
    
    public function testActivityNotificationAction()
    {
        if(!$user_id = $this->params()->fromRoute('user_id', false) 
            or !$userRow = $this->getTable('User')->fetchBy('id', $user_id)
        ){
            return 'user not found'."\n";
        }
        
        $notificationTable = $this->getServiceLocator()->get('User\Notification\Table');
        
        $options = array(
            5 => array(
                'gem' => 20
            ),
            15 => array(
                'gem' => 70
            ),
            30 => array(
                'gem' => 150
            )
        );
        
        foreach ($options AS $day => $gifts) {
            foreach($gifts AS $object_code=>$count){
                $userRow->updateObjectRow($object_code, $count);
            }
            $notificationRow = $notificationTable->createRow(array(
                'template'  =>  'activity',
                'user_id'   =>  $userRow->id,
            ));
            $notificationRow->setArguments(array(
                'day'   =>  $day,
                'gifts' =>  $gifts
            ));
            $notificationTable->add($notificationRow);
        }
        return 'Done!'."\n";
    }
    
    public function signupNotificationAction()
    {
        $table = $this->getTable('User');
        $table->getTableGateway()->initialize();
        $table = $this->getServiceLocator()->get('User\Table');
        $pushService = $this->getServiceLocator()->get('PushCommet\Service');
        $rowset = $table->getTableGateway()->select(array(
            'email IS NULL'
        ));
        foreach($rowset->getItems() AS $row){
            if($row->time_add > strtotime('-1 week')){
                $log =  $pushService->sendToUser(array(
                    'type'      =>  'notification',
                    'message'   =>  'notification.signup'
                ), $row, true);
                echo $row->id.' Push '."\n";
            }else{
                $table->delete($row);
                echo $row->id.' Remode'."\n";
            }
        }
        return 'Done!'."\n";
    }
    
    
    public function removeOldAction()
    {
        
        $userTable = $this->getServiceLocator()->get('User\Table');
        $result = $userTable->getTableGateway()->delete(array(
            'time_last_connect < ?' => strtotime('-1 week'),
            'level' =>  1
        ));
        $command = 'php ' . INDEX_PATH . ' region uniqMap';
        echo $command . "\n";
        $output = array();
        exec($command . ' ', $output);
        if (count($output)) {
            foreach ($output AS $line) {
                echo $line . "\n";
            }
        }
        print_r($result);
        return 'Done! remove 1'."rows\n";
    }
    
    
    public function removeMessageAction()
    {
        $table = $this->getServiceLocator()->get('User\Message\Table');
        $messageRowset = $table->fetchAllMostDelete();
        foreach($messageRowset AS $row){
            $table->remove($row);
            echo 'id message '.$row->id."\n";
        }
        
        $table = $this->getServiceLocator()->get('User\Notification\Table');
        $notificationRowset = $table->fetchAllMostDelete();
        foreach($notificationRowset AS $row){
            $table->delete($row);
            echo 'id notification '.$row->id."\n";
        }
        echo 'Done! Remove '.count($notificationRowset).' notifications, '.count($messageRowset).' messages'."\n";
        
    }
    
    public function resetLibLevelAction()
    {
        $request = $this->getRequest();
        $table = $this->getTable('Lib/LevelUser');
        $maxLevel = $request->getParam('max', 100);
        $errorHandler = function(){
            throw new \Exception();
        };
        set_error_handler($errorHandler);
        if(!$formula = $request->getParam('formula', false)){
            return "formula not set\n";
        }
        $f = function($level, $base = 0) use ($formula){
            $exp = 0;
            eval('$exp = $base + '.$formula.';');
            return $exp;
        };
        try{
            $f(1);
        }  catch (\Exception $e){
            return "invalid formula\n";
        }
        $base = 100;
        $data = array();
        for($level=1;$level<=$maxLevel;$level++){
            $base = $f($level, $base);
            if(strlen($base)>11){
                break;
            }
            echo $level."\t\t".$base."\n";
            $data[$level] = $base;
        }
        restore_error_handler();
        $table->getTableGateway()->delete(true);
        foreach ($data AS $level=>$exp){
            $table->getTableGateway()->insert(array(
                'id'=>$level,
                'exp'=>$exp
            ));
        }
    }
}