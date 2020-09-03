<?php
	if (isset($_POST) && !empty($_POST))
	{
		unlink("loginLog");
		echo "Post variable is set";
		file_put_contents("loginLog", "\nPost variable is set\n", FILE_APPEND);
		var_dump($_POST);
		file_put_contents("loginLog", var_export($_POST, true) . "\n", FILE_APPEND);
		require_once("../db.php");
		$username = htmlentities(strip_tags(trim($_POST["username"])));
		$password = htmlentities(strip_tags(trim($_POST["password"])));
		echo "Username: $username";
		file_put_contents("loginLog", "Username: $username\n", FILE_APPEND);
		echo "Password: $password";
		file_put_contents("loginLog", "Password: $password\n", FILE_APPEND);
		$getLoginInfoQuery = "SELECT * FROM users WHERE username=:username";
		$getLoginInfoStmt = $dbh->prepare($getLoginInfoQuery);
		$getLoginInfoStmt->execute(
			array(
				"username" => $username
			)
		);

		foreach ($getLoginInfoStmt->fetchAll() as $row)
		{
			$hashedPW = $row["password"];
			echo "Hashed password: $hashedPW";
			file_put_contents("loginLog", "Hashed password: $hashedPW\n", FILE_APPEND);
			$email = $row["email"];
			echo "Email: $email";
			file_put_contents("loginLog", "Email: $email\n", FILE_APPEND);
			$perm = $row["permlevel"];
			echo "Perm level: $perm";
			file_put_contents("loginLog", "Perm level: $perm\n", FILE_APPEND);
		}

		if (password_verify($password, $hashedPW)) // Match, correct login
		{
			file_put_contents("loginLog", "Passwords matched\n", FILE_APPEND);
			session_start(); // Log them in
			$_SESSION["user"] = $username;
			$_SESSION["email"] = $email;
			$_SESSION["perm"] = $perm;
			file_put_contents("loginLog", "SESSION variable:\n" . var_export($_SESSION, true), FILE_APPEND);
			header('Location: index.php'); // Redirect them to the admin page, now that they're logged in
		}

		else // No match, incorrect login
		{
			file_put_contents("loginLog", "Passwords didn't match\n", FILE_APPEND);
			unset($_POST); // Ensure that this page will display correctly
			header("Location: login.php?failed");
		}
	}

	else
	{
?>
<!DOCTYPE html>
<html>
	<head>
		<title>
			Login as Admin
		</title>
	</head>
	<body>
		<form action="login.php" method="POST">
			<label for="username">
				Username&#58;
			</label>
			<input type="text" name="username" />
			<br />
			<label for="password">
				Password&#58;
			</label>
			<input type="password" name="password" />
			<br />
			<input type="submit" value="Login" />
		</form>
		<?php
			if (isset($_GET))
			{
				if (isset($_GET["failed"]))
				{
					echo "Your previous login failed";
				}
			}
		?>
	</body>
</html>
<?php
	}
?>
