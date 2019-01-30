<?php
	//Database connection parameters
	$servername = "localhost";
	$username = "username";
	$password = "password";
	$databaseName = "database";

	// Functions
	
	function log2File($logText, $logFile = "knbts.sms.log"){
		$now = new DateTime("now", new DateTimeZone('Africa/Nairobi'));
		error_log("{$now->format('Y-m-d G:i:s')} $logText\n", 3, $logFile);
	}

	function dbLog($query){

		global $servername, $username, $password, $databaseName;

		// Create connection
		$conn = new mysqli($servername, $username, $password, $databaseName);
		// Check connection
		if ($conn->connect_error) {
			log2File("Connection failed: " . $conn->connect_error);
			return false;
		} 

		if ($conn->query($query) === TRUE) {
			return true;
		} else {
			log2File("Error: $query \n" . $conn->error);
		}

		$conn->close();
	}
	
	function dbFetch($query){

		global $servername, $username, $password, $databaseName;

		$result = null;

		// Create connection
		$conn = new mysqli($servername, $username, $password, $databaseName);
		// Check connection
		if ($conn->connect_error) {
			log2File("Connection failed: " . $conn->connect_error);
			return $result;
		} 

		try {
			$result = $conn->query($query);
		} catch (Exception $e) {
			log2File("Error: $query \n" . $conn->error);
		}

		$conn->close();

		return $result;
	}
	
	function getSMSURI() 
	{    

		$smsUsername = $smsAPIKey = $smsURI = $smsSenderID = "";

		$query = "SELECT title, content FROM configurations WHERE title IN ('sms_username', 'sms_key', 'sms_sender_id', 'sms_sender_uri')";
		$smsCredentials = dbFetch($query);

		while ($row = $smsCredentials->fetch_assoc()) {
			switch ($row['title']) {
				case 'sms_username':
					$smsUsername = $row['content'];
					break;
				case 'sms_key':
					$smsAPIKey = $row['content'];
					break;
				case 'sms_sender_id':
					$smsSenderID = $row['content'];
					break;
				case 'sms_sender_uri':
					$smsURI = $row['content'];
					break;
			}
		}
		$smsURI = $smsURI."username=$smsUsername&Apikey=$smsAPIKey&from=$smsSenderID&to=DESTADDR&message=MESG";

		return $smsURI;
	}
	
	function sendSMS($smsURI, $destination, $message) 
	{    
		//Strip front zero(0) and append +254
		if(strcmp(substr($destination, 0, 1), '0') == 0){
			$destination = "+254".substr($destination,1);
		}

		//Append +254 if the leading number is seven 7
		if(strcmp(substr($destination, 0, 1), '7') == 0){
			$destination = "+254$destination";
		}

		if(strlen($destination) != 13){
			log2File("Couldn't send to $destination");
			return false;
		}
		
		$smsURI = str_replace("DESTADDR", urlencode($destination), $smsURI);
		$smsURI = str_replace("MESG", urlencode($message), $smsURI);
		
		$defaults = array( 
			CURLOPT_URL => $smsURI, 
			CURLOPT_HEADER => 0, 
			CURLOPT_RETURNTRANSFER => TRUE, 
			CURLOPT_TIMEOUT => 4 
		); 
		
		$ch = curl_init(); 
		curl_setopt_array($ch, $defaults); 
		if( ! $result = curl_exec($ch)) 
		{ 
			trigger_error(curl_error($ch)); 
		} 
		curl_close($ch); 
		
		log2File("Message sent to $destination");

		return $result; 
	}
?>
