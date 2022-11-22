<?php

    /**
     * Description  : check used storage for alerting message on slack
     * @Author      : Seng Sopheak
     * @Date        : 01-April-2021
     */
    function alertMessageOnSlack($message, $room=null, $icon=null, $url=null)
    {

        $room = ($room) ? $room : "ruc_conpass"; // Group for being forwarded messages
        $icon = ($icon) ? $icon : ":server:";
        $data = "payload=" . json_encode(array(
                "channel"       =>  "#{$room}",
                "text"          =>  $message,
                "icon_emoji"    =>  $icon
              ));
        $url = ($url) ? $url : "https://hooks.slack.com/services/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"; // Webhook endpoint (URL)

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);

        if($result === false)
        {
            echo 'Curl error: ' . curl_error($ch);
        }

        curl_close($ch);

    }

?>