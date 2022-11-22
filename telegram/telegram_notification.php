<?php

    /**
     * Description  : check used storage for alerting message on telegram
     * @Author      : Seng Sopheak
     * @Date        : 01-April-2021
     */
    function alertMessageOnTelegram($message)
    {     	
        $url = "https://api.telegram.org/bot1640883052:AAF0oZtPXmLpKEBw5iC9U7bfoh0idONvg2I/sendMessage";
        // $payload = json_encode(array("chat_id" => "@cbs_alert20", "text" => $message)); 
        $payload = json_encode(array("chat_id" => "@cbs_alert20", "text" => $message)); 
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

?>