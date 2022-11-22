<?php

require_once 'telegram/telegram_notification.php';
require_once 'crud/mysql.php';

try {

    $serverRemark = "\xF0\x9F\x93\xA2 IP: 192.168.10.6 [Sync Data] \n--\n Total Record: ";
    $msgSyncData = " list_of_loan sync";
    $emjNormal = "\xE2\x9C\x85";
    $emjDanger = "\xE2\x9D\x8C"; 
    $currentTime = strtotime(date('H:i:s'));
    $reportDate = date('Y-m-d');

    $db = new Database();
    $db->connect();
    if ($currentTime >= strtotime('00:01:00') && $currentTime < strtotime('16:00:00'))
    {
        $reportDate = date("Y-m-d", strtotime("-1 days"));
    }

    $db->sql("SELECT COUNT(*) AS number_of_active_account, SUM(exchange_amount(ccy, current_balance, report_date, 0)) AS current_balance, report_date FROM daily_loan_list WHERE report_date = '".$reportDate."'");
    $row = $db->getResult();

    if($row[0]["number_of_active_account"] > 0) {
        echo "Number of Records: $row[0]['number_of_active_account']";
        $msgSyncData = $serverRemark.$row[0]["number_of_active_account"]."\n Total Amount (Million): $".number_format($row[0]["current_balance"] / 1000000, 2)."\n Report Date: ".$row[0]["report_date"]."\n ".$emjNormal.$msgSyncData;
        if ($currentTime >= strtotime('00:01:00') && $currentTime < strtotime('12:00:00'))
        {
            $msgSyncData = $msgSyncData." success at ".date('Y-m-d').' '.'07:30:00';
        } else
        {
            $msgSyncData = $msgSyncData." success at ".date('Y-m-d').' '.'16:30:00';
        }
    } else {
        echo "Number of Records: $row[0]['number_of_active_account']"; 
        $msgSyncData = $serverRemark."0\n Total Amount (Million): $0.00 \n Report Date: ".$reportDate."\n".$emjDanger.$msgSyncData." fail at ".date('Y-m-d H:i:s');
    }

    alertMessageOnTelegram($msgSyncData."\n--");

    $db->disconnect();

} catch (Exception $e) {
    echo "Error: (".$e->getMessage().")";
    exit();
}

?>