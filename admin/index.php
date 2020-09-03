<?php
	require_once("../db.php");

	session_start();	
	file_put_contents("index.log", "\$_SESSION is " . var_export($_SESSION, true), FILE_APPEND);

	if (isset($_SESSION) && isset($_SESSION["user"])) // The user is logged in
	{
		if ($_SESSION["perm"] === '0') // The user is an administrator
		{
?>
		<!DOCTYPE html>
		<html>
			<head>
				<title>Administrator Panel</title>
				<script type="application/javascript" src="../js/jquery-1.7.1.min.js"></script>
				<script type="application/javascript" src="../js/admPanel.js"></script>
				<link rel="stylesheet" type="text/css" href="../css/admPanel.css" />
			</head>
			<body>
				<section>
					<form action="./logout.php">
						<button>
							Logout
						</button>
					</form>
				</section>
				<hr />
				<section id="contact-form-email">
					<form>
						Contact Form Email Address: <input type="email" value="<?php echo $admEmail; ?>" id="admEmInp" />
						<input type="submit" id="setEmail" value="Change Email" />
					</form>
				</section>
				<hr />
				<section id="indexable-pages">
					<h1>Set Page Indexability</h1>
					<table>
						<tr>
							<th>
								URL
							</th>
							<th>
								Is this page indexable by search engines?
							</th>
						</tr>
					<?php
						$getPagesSQL = "SELECT url,indexable FROM pages"; // We just want to loop through them
						$getPagesStmt = $dbh->prepare($getPagesSQL);
						$getPagesStmt->execute();
		
						foreach ($getPagesStmt->fetchAll() as $row)
						{
							echo "<tr><td>" . $row["url"] . "</td><td><input type=\"checkbox\" id=\"" . $row["url"] . "\"";
		
							if (boolval($row["indexable"]))
							{
								echo " checked";
							}
		
							echo " /></td></tr>";
						}
					?>
					</table>
				</section>
				<hr />
				<section>
					<h1>Set Page Titles and Descriptions</h1>
					<table>
						<tr>
							<th>
								Page
							</th>
							<th>
								Title
							</th>
							<th>
								Description
							</th>
						</tr>
						<?php
							$getPageInfoSQL = "SELECT url,description,title FROM pages";
							$getPageInfoStmt = $dbh->query($getPageInfoSQL);
		
							foreach ($getPageInfoStmt->fetchAll() as $row)
							{
								echo "<tr>
									<td>" . $row["url"] . "</td>
									<td>
										<input type=\"text\" value=\"" . $row["title"] . "\" data-url=\"". $row["url"] . "\" />
										<br />
										<button data-url=\"". $row["url"] . "\" data-for=\"title\" class=\"pageInfo\">Update</button>
									</td>
									<td>
										<input type=\"text\" value=\"" . $row["description"] . "\" data-url=\"" . $row["url"] . "\" />
										<br />
										<button data-url=\"". $row["url"] . "\" data-for=\"description\" class=\"pageInfo\">Update</button>
									</td>";
							}
						?>
					</table>
				</section>
				<hr />
				<section>
					<h1>
						Google Analytics Tag
					</h1>
					<label for="ga-analytics-code">
						Enter your Google Analytics code here to have it display across all pages&#58;
					</label>
					<textarea data-for="ga">
					<?php
						$getGACodeSQL = "SELECT code FROM analytics WHERE service='Google Analytics'";
						$getGACodeStmt = $dbh->query($getGACodeSQL);
		
						foreach ($getGACodeStmt->fetchAll() as $row)
						{
							echo $row["code"];
						}
					?>
					</textarea>
					<button class="updateAnalytics" id="ga">
						Update
					</button>
				</section>
				<hr />
				<section>
					<h1>
						Facebook Pixel Code
					</h1>
					<label for="fb-pixel-code">
						Enter your Facebook Pixel code here to have it display across all pages&#58;
					</label>
					<textarea data-for="fb">
					<?php
						$getGACodeSQL = "SELECT code FROM analytics WHERE service='Facebook Pixel'";
						$getGACodeStmt = $dbh->query($getGACodeSQL);
		
						foreach ($getGACodeStmt->fetchAll() as $row)
						{
							echo $row["code"];
						}
					?>
					</textarea>
					<button class="updateAnalytics" id="fb">
						Update
					</button>
				</section>
				<hr />
				<section>
					<h1>
						Change Images
					</h1>
					<table>
						<tr>
							<th>
								Purpose
							</th>
							<th>
								Image
							</th>
						</tr>
						<?php
							$getImgsSQL = "SELECT purpose FROM images";
							$getImgsStmt = $dbh->query($getImgsSQL);
							
							foreach ($getImgsStmt->fetchAll() as $row)
							{
								echo "<tr><td>". $row["purpose"] ."</td><td><img src=\"". makeImageURL($row["purpose"], "../")  ."\" height=\"100\" width=\"200\" data-purpose=\"". str_replace(" ", "-", $row["purpose"]) . "\" />
								<br />
								<form data-purpose=\"". str_replace(" ", "-", $row["purpose"]) . "\" enctype=\"multipart/form-data\">
									<input type=\"file\" id=\"" . str_replace(" ", "-", $row["purpose"]) . "\" accept=\".png,.jpg,.gif\" />
									<button data-purpose=\"". str_replace(" ", "-", $row["purpose"]) . "\" class=\"imgButton\">
								</form>
								Replace image.
								</button>
								</td></tr>";
							}
						?>
					</table>
				</section>
			</body>
		</html>
<?php
		} // if (permissions are correct)

		else // Invalid permissions
		{
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Insufficient permissions</title>
	</head>
	<body>
		You are logged in, but aren&apos;t an administrator.
		<br />
		Please <a href="../index.php">click here</a> to return to the home page.
	</body>
</html>
<?php
		} //
	} // isset($_SESSION)

	else // Not logged in
	{
		header("Location: ./login.php");
	}
?>
