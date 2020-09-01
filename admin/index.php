<?php
	try
	{
		$user = "bemoappuser";
		$pass = "bemoapplogin";
		$dbh = new PDO("mysql:host=localhost;dbname=bemoapp", $user, $pass);
		$getAdmEmailSQL = "SELECT email FROM users WHERE username='admin'";
		$getAdmEmStmt = $dbh->prepare($getAdmEmailSQL);
		$setAdmEmSQL = "UPDATE users SET email=:email WHERE username='admin'";
		$setAdmEmStmt = $dbh->prepare($setAdmEmSQL);
		$getAdmEmStmt->execute();

		foreach ($getAdmEmStmt->fetchAll() as $row)
		{
			$admEmail = $row["email"];
		}

		if (isset($_POST)) // There is POST data
		{
			if (isset($_POST["email"])) // Request to change admin email
			{
				$email = htmlentities(strip_tags(trim($_POST["email"])));
	
				if ($email != "")
				{
					$setAdmEmStmt->execute(array(':email' => $email)); // Update the array
					$toReturn = array("success" => "setEmail");
					die(json_encode(array("setEmRes" => "success")));
				}

				else
				{
					die(
						json_encode(
							array(
								"setEmRes" => "invalidEmail"
							)
						)
					);
				}
			}
		}
	}

	catch (PDOException $e)
	{
		die("Error: " . $e->getMessage() . "<br />");
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Administrator Panel</title>
		<script type="application/javascript" src="../js/jquery-1.7.1.min.js"></script>
		<script type="application/javascript" src="./admPanel.js"></script>
	</head>
	<body>
		<section id="contact-form-email">
			<form>
				Contact Form Email Address: <input type="email" value="<?php echo $admEmail; ?>" id="admEmInp" />
				<input type="submit" id="setEmail" />
			</form>
		</section>
		<hr />
	</body>
</html>
