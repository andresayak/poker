<?php

namespace Control\Controller;

use Ap\Controller\AbstractController;

class SystemController extends AbstractController
{
	public function logRorateAction()
	{
		$handler = $this->getServiceLocator()->get('Application\Service\ErrorHandling');
		$errors = $handler->rotate($this->getServiceLocator()->get('Mail\Log'));
		return 'Done! ('.count($errors).' errors)'."\n";
	}
	
	public function syncAction()
	{
		$names = $this->getServiceLocator()->getCanonicalNames();
		foreach($names AS $key=>$name){
			if(preg_match('/\\\\Table$/ui', $key)){
				$table = $this->getServiceLocator()->get($name);
				if(count($table->getCacheCols())){
					$select = $table->getTableGateway()->getSql()->select();
					$select->where(array('id'=>10217));
					$select->columns(array_merge(array($table->getKey()),$table->getCacheCols()));
					$rowset = $table->getTableGateway()->selectWith($select);
					foreach($rowset->getItems() AS $row){
						$data = $row->toArrayForSync();
						if(count($data)){
							echo $row->id."\n";
							foreach($data AS $col=>$value){
								//if($value!==null){
								echo ' - '.$col.' = '.$value."\n";
							   // }
							}
							$table->syncRow($row);
						}
					}
				}
			}
		}
		exit;
		/*
		foreach(){
			
		}*/
	}
	
	public function cacheResetAction()
	{
		$type = $this->params()->fromRoute('name');
		$service = $this->getServiceLocator()->get('CacheManager');
		$service->flushByType($type);
		return 'Done!'."\n";
	}
	
	public function chatResetAction()
	{
		$service = $this->getServiceLocator()->get('PushCommet\Service');
		$service->reset();
		return 'Done!'."\n";
	}
	
	public function removeLogAction()
	{
		$logTable = $this->getTable('User\Log');
		$logTable->deleteOld();
		return "Done!\n";
	}
	
	public function copyUserToGlobalAction()
	{
		$userTable = $this->getServiceLocator()->get('User\Table');
		$globalUser = $this->getServiceLocator()->get('GlobalAccount\TableGateway');
		$userRowset = $userTable->fetchAll();
		$count = $insert = 0;
		foreach($userRowset AS $userRow){
			if($userRow->email){
				$count++;
				$insert++;
				try {
					$globalUser->insert(array(
						'email'     =>  $userRow->email,
						'username'  =>  $userRow->username,
						'server_id' =>  SERVER_ID
						));
				} catch ( \Zend\Db\Adapter\Exception\InvalidQueryException $exc) {
					$insert--;
				}
			}
		}
		return 'Done! ('.$insert.'/'.$count.' users)'."\n";
	}

	public function monitoringAction()
	{

		try {
			$row = $this->getServiceLocator()->get('System\Server\Table')->fetchBy('ip_network_address', SERVER_IP);
			if (!$row) throw new Exception("error 1");

			$getSystemLoadAverage = function() {
				$load = sys_getloadavg();
				$result = array('loadAverageSystem' => $load[0]);

				return $result;
			};

			$getInfoMemory = function() {
				exec('free -mo', $memoryInfo);
				preg_match_all('/\s+([0-9]+)/', $memoryInfo[1], $matches);
				list($total, $used, $free, $shared, $buffers, $cached) = $matches[1];

				$result = array(
					'free'    => $free,
					'used'    => $used,
					'total'   => $total,
					'buffers' => $buffers,
					'cached'  => $cached,
					'shared'  => $shared
					);

				return $result;
			};

			$getOccupiedSpaceOnTheHardDrive = function() {
				exec('df -h', $occopiedSpace);

				preg_match_all('/([0-9])*+%/', $occopiedSpace[1], $pro);
				preg_match_all('/([0-9])*+G/', $occopiedSpace[1], $g);
				list($use, ) = $pro[0];
				list($size, $used, $avail) = $g[0];
				preg_match_all('/([0-9])*/', $use, $u);

				$result = array(
					'size'  => $size = substr($size, 0, (strlen($size)-1)),
					'used'  => $used = substr($used, 0, (strlen($used)-1)),
					'avail' => $avail = substr($avail, 0, (strlen($avail)-1)),
					'use'   => $u[0][0]
					);

				return $result;
			};

			$getStatusMySQL = function() {
				exec('service mysql status', $mysqlStatus);
				$s = $mysqlStatus[2];
				$s = trim($s);
				$result = preg_replace('~^(.*?)\s~', '', $s);
				$result = array('status' => $result);

				return $result;
			};

			$getInfoMySQL = function() use ($row){
				$errors = '';
				$masterLogFile = '';
				$readMasterLogPos = '';
				try {
					$servers = array(
						'127.0.0.1',
						);

					$mySqlAdapter = $row->getTable()->getTableGateway()->get('slave')->getAdapter();
					foreach($servers as $server) {
						$statement = $mySqlAdapter->query("SHOW SLAVE STATUS");
						$results = $statement->execute();
						$row = $results->current();
						if($row) {
							if($row['Slave_IO_Running'] == 'No') {
								$errors .= "Slave IO not running on $server\n";
							}
							if ($row['Master_Log_File']) {
								$masterLogFile = $row['Master_Log_File'];
							}
							if ($row['Read_Master_Log_Pos']) {
								$readMasterLogPos = $row['Read_Master_Log_Pos'];
							}
						} else {
							$errors .= "Not replication";
						}
					}

					if($errors) return $result = array(
						'status' => $errors,
						'Master_Log_File' => $masterLogFile,
						'Read_Master_Log_Pos' => $readMasterLogPos,
					);
					else return $result = array(
						'status' => "OK",
						'Master_Log_File' => $masterLogFile,
						'Read_Master_Log_Pos' => $readMasterLogPos,
					);
				} catch (Exception $e) {
					$errors .= "Not connect to mysql".$e;
					throw new Exception("Not connect to mysql");
				}

			};

			$getStatusApache = function() {
				exec('service apache2 status', $apache2);
				$s = trim($apache2[2]);
				$result = preg_replace('~^(.*?)\s~', '', $s);
				$result = array('status' => $result);
				return $result;
			};

			$getCountRowInFile = function($pathToFile) {
				exec('wc '.$pathToFile, $info);
				$s = trim($info[0]);
				list($countRow, ,$countWords, $countByte, $pathToFile) = explode(" ", $s);
				$result = array(
					'countRow'   => $countRow,
					'countWords' => $countWords,
					'countByte'  => $countByte,
					'pathToFile' => $pathToFile
					);

				return $result;
			};

			$getOccupiedSpaceOnTheMemcache = function() {
				$config = $this->getServiceLocator()->get('config');
				$ip = $config['redisPoker']['host'];
				exec("redis-cli -h $ip info", $occopiedSpace);
				preg_match_all('/([0-9])*/', $occopiedSpace[26], $used_memory);
				preg_match_all('/([0-9])*/', $occopiedSpace[28], $used_memory_rss);
				preg_match_all('/([0-9])*/', $occopiedSpace[29], $used_memory_peak);
				preg_match_all('/([0-9])*/', $occopiedSpace[31], $used_memory_lua);

				$result = array(
					'used_memory' => $used_memory[0][12],
					'used_memory_rss' => $used_memory_rss[0][16],
					'used_memory_peak' => $used_memory_peak[0][17],
					'used_memory_lua' => $used_memory_lua[0][16],
					);

				// print_r($result);
				return $result;
			};

			$getSocketStat = function() {
				if (file_exists('/tmp/socket_stat')) $fp = file('/tmp/socket_stat');
				if (isset($fp)) {
					$arr = (array) json_decode($fp[0]);
					$result = array(
						'users' => $arr['users'],
						'memory (use)' => $arr['memory']->use,
						'memory (all)' => $arr['memory']->all,
						);

					return $result;
				}
				return $result = array('Error' => 'No file /tmp/socket_stat');
			};

			$getProcessList = function() {
				exec('ps aucx --sort -rss | head -100', $processList);
				$arr = array();

				array_shift($processList);
				foreach ($processList as $string) {
					$newString = preg_replace('/\s+/', ' ',  $string) ;
					$newString = trim($newString);
					$arr[] = explode(" ", $newString);
				}

				$group = array();
				foreach ($arr as $key => $array) {
					foreach ($array as $nameProcess => $value) {

						if ($nameProcess == 10) {
							if (empty($group[$value][0])) $group[$value][0] = 0;
							if (empty($group[$value][1])) $group[$value][1] = 0;
							$group[$value][0] += (double) $arr[$key][2];
							$group[$value][1] += (double) $arr[$key][3];
						}
					}
				}

				return $group;
			};

			$monitoringSystem['Load Average'] = $getSystemLoadAverage();
			$monitoringSystem['RAM'] = $getInfoMemory();
			$monitoringSystem['Hard Drive'] = $getOccupiedSpaceOnTheHardDrive();
			$monitoringSystem['MySQL'] = $getInfoMySQL();
			$monitoringSystem['Memcache'] = $getOccupiedSpaceOnTheMemcache();
			$monitoringSystem['Socket stat'] = $getSocketStat();
			$monitoringSystem['Process List'] = $getProcessList();

			print_r($monitoringSystem);
			$row->status_data = json_encode($monitoringSystem);
			$row->save();
		} catch (Exception $e) {
			$monitoringSystem['Error'] = $e;
		}
	}
}
