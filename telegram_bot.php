<?php
require_once 'crud/mysql.php';
    /**
     * Description  : check used storage for alerting message on telegram
     * @Author      : Seng Sopheak
     * @Date        : 01-April-2021
     
     */
    function alertMessageOnTelegram($message)
    {     	
        $url = "https://api.telegram.org/bot5483593804:AAGiA_QmBCxGYwOmJrRdkuLMkPLG7GOYYKE/sendMessage";
        $payload = json_encode(array("chat_id" => "-1001665842323", "text" => $message)); 
        // $url = "https://api.telegram.org/bot1854850722:AAG37aHzspDo5u2t_qh6ttcbtGMm8-OO3WM/sendMessage";
        // $payload = json_encode(array("chat_id" => "@cbs_alert20_demo", "text" => $message));
        $curl = curl_init();
		
		curl_setopt($curl, CURLOPT_URL,				$url);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 	"POST");
		curl_setopt($curl, CURLOPT_POSTFIELDS, 		$payload);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 	1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, 		array('Accept: application/json', 'Content-type: application/json; charset=utf-8'));
		
		$response = curl_exec($curl);
		$result = json_decode($response);
		if($result->ok != 1)
        {
            echo 'Curl error: ' . $response;
        }
		$curl_close($curl);

    }
    function getAlertData($row=array(),$reportDate,$dateTime='07:30:00',$serverRemark,$emjNormal,$emjDanger){
        // $msgSyncData = $serverRemark;
        $showEmj = $row[0]["number_of_active_account"] > 0 ? $emjNormal : $emjDanger;
        $msgSyncData = $showEmj."Sync Date ON : ".date('Y-m-d').' '.$dateTime;
        $msgSyncData .= "\n".$showEmj."Reporting Date : ".$reportDate."";
        $msgSyncData .= "\n\t\t\t\t\t Acc :  ".$row[0]["number_of_active_account"];
        $msgSyncData .= "\n\t\t\t\t\t Balance  :  $".number_format($row[0]["current_balance"]);
        $msgSyncData .= "\n\t\t\t\t\t Daily Disb Acc :  ".$row[0]["daily_disb_acc"]; 
        $msgSyncData .= "\n\t\t\t\t\t Daily Disb Amt :  $" .number_format($row[0]["daily_disb_amt"]);
        // $msgSyncData .= "\n \t Report Date: ".$row[0]["report_date"];
        alertMessageOnTelegram($msgSyncData."\n--");
    }


try {
    $db = new Database();
    $db->connect();
    // $db->sql("SELECT CURRENT_TIME() as time");
    // $row1 = $db->getResult();
    $serverRemark = "\xF0\x9F\x93\xA2 IP: 192.168.10.6 [Sync Data] \n--\n  ";
    $msgSyncData = " Sync Date on";
    $emjNormal = "\xE2\x9C\x85";
    $emjDanger = "\xE2\x9D\x8C";
    // $disb_acc =[0]["daily_disb_acc"];
    // $disb_amt =[0]["daily_disb_amt"];
    // $currentTime = strtotime($row1[0]["time"]);
    $currentTime = strtotime(date('H:i:s'));
    $reportDate = date('Y-m-d');

    $scheduleTimeSyncPIC = array("1630","1648","1658","1708","1718","1728","1738","1748","1758","1808","1818","1828","1838","1848","1858","1908","1918","1928","1938","1948","1958","2008","2018","2028","2038","2048","2058","2108");


    if ($currentTime >= strtotime('00:01:00') && $currentTime < strtotime('16:00:00'))
    {
        $reportDate = date("Y-m-d", strtotime("-1 days"));
    }


    $db->sql("SELECT 
    COUNT(*) AS number_of_active_account, 
    SUM(exchange_amount(ccy, current_balance, report_date, 0)) AS current_balance, 
    SUM(IF(DATE_FORMAT(disburse_date,'%Y-%m-%d')=DATE_FORMAT(report_date,'%Y-%m-%d'),1,0)) as daily_disb_acc,
    sum(exchange_amount(ccy,IF(DATE_FORMAT(disburse_date,'%Y-%m-%d')=DATE_FORMAT(report_date,'%Y-%m-%d'),total_disbursed,0),report_date,0)) as daily_disb_amt,
    report_date,
    trigger_date
FROM cbs_report.pic_daily_loan_list 
    WHERE report_date = '".$reportDate."'");
    $row = $db->getResult();


   
    if($row[0]["number_of_active_account"] > 0) {
        echo "Number of Records: ".$row[0]['number_of_active_account'];

   
        if ($currentTime >= strtotime('00:01:00') && $currentTime < strtotime('12:00:00'))
        {
        	
            $dataTime = '07:30:00';
            getAlertData($row,$reportDate,$dataTime,$serverRemark,$emjNormal,$emjDanger);
           
        } else
        {
          // if(in_array(date("Hi",$currentTime),$scheduleTimeSyncPIC)){
                $dataTime = date("H:i:s",$currentTime);
                getAlertData($row,$reportDate,$dataTime,$serverRemark,$emjNormal,$emjDanger);
                // $msgSyncData = $msgSyncData." success at ".date('Y-m-d').' '.date("H:i:s",$currentTime);
                // alertMessageOnTelegram($msgSyncData."\n--");
         //   }
            
        }
        
    } else {
        // echo "Number of Records: ".$row[0]['number_of_active_account']; 
        // $msgSyncData = $serverRemark."0\n Total Amount (Million): $0.00 \n Report Date: ".$reportDate."\n".$emjDanger.$msgSyncData." fail at ".date('Y-m-d H:i:s');
        // alertMessageOnTelegram($msgSyncData."\n--");
    }

    $db->disconnect();

} catch (Exception $e) {
    echo "Error: (".$e->getMessage().")";
    exit();
}

