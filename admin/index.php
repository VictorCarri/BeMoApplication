<?php
	try
	{
		require_once("../db.php");
		$getAdmEmailSQL = "SELECT email FROM users WHERE username='admin'";
		$getAdmEmStmt = $dbh->prepare($getAdmEmailSQL);
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
					$setAdmEmSQL = "UPDATE users SET email=:email WHERE username='admin'";
					$setAdmEmStmt = $dbh->prepare($setAdmEmSQL);
					$setAdmEmStmt->execute(array(':email' => $email)); // Update the array
					$toReturn = array("success" => "setEmail");
					die(
						json_encode(
							array("setEmRes" => "success")
						)
					);
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

			else if (isset($_POST["indexable"])) // Request to change a page's indexability
			{
				$isIndexable = $_POST["indexable"] == "true" ? true : false;
				$url = htmlentities(strip_tags(trim($_POST["pageURL"])));

				if ($url != "")
				{
					$setIndexableSQL = "UPDATE pages SET indexable=:indexable WHERE url=:url";
					$setIndexableStmt = $dbh->prepare($setIndexableSQL);
					$numRows = $setIndexableStmt->execute(
						array(
							"indexable" => intval($isIndexable),
							"url" => $url
						)
					);
				
					if ($numRows > 0)
					{
						die(
							json_encode(
								array(
									"success" => "success",
									"indexable" => intval($isIndexable),
									"rawIndexable" => $_POST["indexable"]
								)
							)
						);
					}

					else
					{
						die(
							json_encode(
								array(
									"failure" => "Didn't change any rows"
								)
							)
						);
					}
				}

				else
				{
					die(
						json_encode(
							array(
								"failure" => "Invalid URL"
							)
						)
					);
				}
			}

			else if (isset($_POST["url"])) // Request to change a page
			{
				if (isset($_POST["type"]) && isset($_POST["content"]))
				{
					if ($_POST["type"] === "description") // Updating a page's description
					{
						$usql = "UPDATE pages SET description=:content WHERE url=:url"; // Statement to update page info
						$uStmt = $dbh->prepare($usql);
						$execRes = $uStmt->execute(
							array(
								"content" => htmlentities(strip_tags(trim($_POST["content"]))),
								"url" => strip_tags(trim($_POST["url"]))
							)
						);
					
						if ($execRes)
						{
							die(
								json_encode(
									array(
										"success" => "Updated description"
									)
								)
							);
						}

						else
						{
							die(
								json_encode(
									array(
										"failure" => $uStmt->errorInfo()[0]
									)
								)
							);
						}
					}

					else // Updating a page's title
					{
						$usql = "UPDATE pages SET title=:content WHERE url=:url"; // Statement to update page info
						$uStmt = $dbh->prepare($usql);
						$execRes = $uStmt->execute(
							array(
								"content" => htmlentities(strip_tags(trim($_POST["content"]))),
								"url" => strip_tags(trim($_POST["url"]))
							)
						);
					
						if ($execRes)
						{
							die(
								json_encode(
									array(
										"success" => "Updated title"
									)
								)
							);
						}

						else
						{
							die(
								json_encode(
									array(
										"failure" => $uStmt->errorInfo()[0]
									)
								)
							);
						}
					}
				}

				else
				{
					die(
						json_encode(
							array(
								"failure" => "Missing data"
							)
						)
					);
				}
			} // else if isset($_POST["url"])

			else if (isset($_POST["service"])) // Need to update an analytics tag
			{
				if (isset($_POST["code"])) // Need new script code
				{
					$code = trim($_POST["code"]);
					$updateSQL = "UPDATE analytics SET code=:code WHERE service=:service";
					$stmt = $dbh->prepare($updateSQL);
					$res = $stmt->execute(
						array(
							"code" => $code,
							"service" => $_POST["service"]
						)
					);

					if ($res) // Success
					{
						die(
							json_encode(
								array(
									"success" => "updated code for service " . $_POST["service"]
								)
							)
						);
					}

					else // Failure
					{
						die(
							json_encode(
								array(
									"failure" => $uStmt->errorInfo()[0]
								)
							)
						);
					}
				}

				else // Error
				{
					die(
						json_encode(
							array(
								"failure" => "noCode"
							)
						)
					);
				}
			}
		} // if (isset($_POST))
	} // try

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
		<script type="application/javascript" src="../js/admPanel.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/admPanel.css" />
	</head>
	<body>
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
				$getPagesSQL = "SELECT * FROM pages"; // We just want to loop through them
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
	</body>
</html>
