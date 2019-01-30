<?php

	include 'functions.php';

	$smsURI = getSMSURI();

	// File to read from (relative path)
	$fileName = "tmp_sms/test_messages.csv";
	
	$smsFile = fopen($fileName, "r");

	if($smsFile !== false){
		// Output one line until end-of-file
		while(!feof($smsFile)) {
			$line = fgets($smsFile);
			$words = explode("|", $line);
			$number = $message = "";
			
			foreach($words as $key => $word){
				switch($key){
				 case 0: 
				 case 1: 
				 case 2: 
					if(is_numeric($word)){
						$number = $word; 
					}
				 break;
				 case 3: $message = $word; break;
				}
			}
			
			if(is_numeric($number)){
				$status = sendSMS($smsURI, $number, $message);

				$success = 0;
				if(strpos($status, "<status>Success</status>") !== false) $success = 1;
				$sendingAttempts = 1;

				$now = new DateTime("now", new DateTimeZone('Africa/Nairobi'));
				$timeSent = $now->format('Y-m-d G:i');

				dbLog("INSERT INTO sms (phone, message, status, sent_at, sending_attempts) VALUES ('$number', '$message', $success, '$timeSent', $sendingAttempts)");

				log2File($status);
			}
		}

		fclose($smsFile);
	
		unlink($fileName);
	}
?>