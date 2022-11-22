<?php

	require 'class.phpmailer.php';
	
	function Send_Mail($to, $subject, $body)
	{
		$from 			  = "suppor@conpass.biz"; // set default sending email here
		$mail 			  = new PHPMailer();
		$mail->IsSMTP(true); // SMTP
		$mail->SMTPAuth   = true; // SMTP authentication
		$mail->Mailer 	  = "smtp";
		$mail->Host       = "tls://email-smtp.us-east.amazonaws.com"; // Amazon SES server, note "tls://" protocol
		$mail->Port       = 465; // set the SMTP port
		$mail->Username   = "AKIAJ674QCJNXSJUWUQ"; // SES SMTP  username
		$mail->Password   = "ApiDaeHpjnVo7fwGL/+WacHZLYbXL7ZKmBP8E5wOL+0"; // SES SMTP password
		$mail->SetFrom($from, 'Server Alert');
		$mail->Subject    = $subject;
		$mail->MsgHTML($body);
		$address 		  = $to;
		$mail->AddAddress($address, $to);
		if(!$mail->Send())
			return false;
		else
			return true;
	}
	
?>