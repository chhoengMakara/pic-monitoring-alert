<?php

	/**
	 * Description  : check used storage and db backup for alerting message via telegram
	 * @Author      : Seng Sopheak
	 * @Date        : 01-April-2021
	 */
	 
    require_once 'storage/class.diskstatus.php';
    require_once 'telegram/telegram_notification.php';

    try {

		$directory = 'D:'; // change directory for processing check and alert
		$diskStatus = new DiskStatus($directory);
		$usedSpace  = $diskStatus->usedSpace();
		$usedSpacePercentage = $diskStatus->usedSpacePercentage();
		
		$emjNormal = "\xE2\x9C\x85";
		$emjWarning = "\xE2\x9A\xA0";
		$emjDanger = "\xE2\x9D\x8C";
		$totalSpace = "Total Space: ".$diskStatus->totalSpace()."<br />";
        $freeSpace = "Free Space: ".$diskStatus->freeSpace()."<br />";
        $usedSpace = "Used Space: ".$usedSpace."<br />";
		$checkSpace = $totalSpace.$freeSpace.$usedSpace;

		echo $checkSpace;
		$msgStorage = " server's storage usage reaches ".$usedSpacePercentage."%!\n";
		if($usedSpacePercentage >= 90){
			$msgStorage = $emjDanger.$msgStorage;
		}elseif($usedSpacePercentage >= 70){
			$msgStorage = $emjWarning.$msgStorage;
		}else{
			$msgStorage = $emjNormal.$msgStorage;
		}	
		
		$serverRemark = "\xF0\x9F\x93\xA2 IP: 192.168.10.6 [Backup Data] \n";
		$telMSGAlert = $serverRemark."--\n".str_replace("<br />", "\n", $checkSpace).$msgStorage;
		$fileList = glob($directory."/BEAN_2000_DB_backup/0-AUTO/*"); // change sub-directory for checking db files
		$isBackup =  false;
		$msgSBackup = $telMSGAlert.$emjNormal.' backup db done successfully at ';
		$msgFBackup = $telMSGAlert.$emjDanger.' not found backup db file at ';
		foreach ($fileList AS $fileName) {
			$fName = pathinfo($fileName, PATHINFO_FILENAME);
			if(is_file($fileName) && filesize($fileName) >= 1024 && SUBSTR($fName, -4) == 'BEAN') {
				$currentTime = strtotime(date('H:i:s'));
				$customizedCurrentTime = strtotime('-1 hours', $currentTime);
				$currentDate = strtotime(date('Y-m-d'));
				$customizedCurrentDate = strtotime('-1 days', $currentDate);
				$fDateTime = TRIM(SUBSTR($fName, 0, strpos($fName, " "))).str_replace("-", ":", SUBSTR($fName, strpos($fName, " "), 9));
				$backupDateTime = strtotime($fDateTime);
				if (
					$customizedCurrentTime >= strtotime('-1 days', strtotime('23:00:00')) && 
					$customizedCurrentTime <= strtotime('-1 days', strtotime('23:59:59')) &&
					$backupDateTime >= strtotime(date('Y-m-d', $customizedCurrentDate).' '.'23:00:00') && 
					$backupDateTime <= strtotime(date('Y-m-d', $customizedCurrentDate).' '.'23:59:59')
					)
				{
					$isBackup = true;
					$msgSBackup = $msgSBackup . $fDateTime;
					break;
				} elseif (
					$currentTime >= strtotime('00:01:00') && 
					$currentTime < strtotime('12:00:00') &&
					$backupDateTime >= strtotime(date('Y-m-d').' '.'04:00:00') && 
					$backupDateTime < strtotime(date('Y-m-d').' '.'12:00:00'))
				{
					$isBackup = true;
					$msgSBackup = $msgSBackup . $fDateTime;
					break;
				} elseif (
					$currentTime >= strtotime('12:00:00') && 
					$currentTime < strtotime('23:00:00') &&
					$backupDateTime >= strtotime(date('Y-m-d').' '.'12:00:00') && 
					$backupDateTime < strtotime(date('Y-m-d').' '.'23:00:00'))
				{
					$isBackup = true;
					$msgSBackup = $msgSBackup . $fDateTime;
					break;
				} else 
				{
					$isBackup = false;
				}
			} 
		}
		
		if (!$isBackup) {
			if (
					$customizedCurrentTime >= strtotime('-1 days', strtotime('23:00:00')) && 
					$customizedCurrentTime <= strtotime('-1 days', strtotime('23:59:59'))
				)
			{
				$msgFBackup = $msgFBackup.' '.date('Y-m-d H:i:s', strtotime('now'.'-1 hours'));
			} else {
				$msgFBackup = $msgFBackup.' '.date('Y-m-d H:i:s');
			}
			alertMessageOnTelegram($msgFBackup."\n--");
		}else {
			alertMessageOnTelegram($msgSBackup."\n--");
		}

	} catch (Exception $e) {
		echo 'Error ('.$e->getMessage().')';
		exit();
	}

?>