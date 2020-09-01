<?php
	try
	{
		$user = "bemoappuser";
		$pass = "bemoapplogin";
		$dbh = new PDO("mysql:host=localhost;dbname=bemoapp", $user, $pass);
		$getAdmEmailSQL = "SELECT email FROM users WHERE username='admin'";
		$getAdmEmStmt = $dbh->prepare($getAdmEmailSQL);
		$getAdmEmStmt->execute();
		$message = wordwrap(htmlentities(strip_tags(trim($_POST["message"]))), 70, "\r\n");
		$from = htmlentities(strip_tags(trim($_POST["senderName"])));
		$subject = "Mail from ".$from;
		$replyTo = htmlentities(strip_tags(trim($_POST["senderAddr"])));
		$headers = "From: webmaster@example.com" . "\r\n" .
		"Reply-To: " . $replyTo . "\r\n" .
		"X-Mailer: PHP/" . phpversion();

		foreach ($getAdmEmStmt->fetchAll() as $row)
		{
			$admEmail = $row["email"];
		}	
		
		mail($admEmail, $subject, $message, $headers);
		echo json_encode(array("success" => "sent"));
	}

	catch (PDOException $e)
	{
		die("Error: " . $e->getMessage() . "<br />");
	}
?>
