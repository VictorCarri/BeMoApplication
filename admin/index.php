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
								<button data-url=\"". $row["url"] . "\" data-for=\"title\">Update</button>
							</td>
							<td>
								<input type=\"text\" value=\"" . $row["description"] . "\" data-url=\"" . $row["url"] . "\" />
								<br />
								<button data-url=\"". $row["url"] . "\" data-for=\"description\">Update</button>
							</td>";
					}
				?>
			</table>
		</section>
	</body>
</html>
