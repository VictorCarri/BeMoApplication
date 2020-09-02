<?php
	try
	{
		require_once("../db.php");
		require_once("../common.php");
		$getAdmEmailSQL = "SELECT email FROM users WHERE username='admin'";
		$getAdmEmStmt = $dbh->prepare($getAdmEmailSQL);
		$getAdmEmStmt->execute();

		foreach ($getAdmEmStmt->fetchAll() as $row)
		{
			$admEmail = $row["email"];
		}

		if (isset($_POST)) // There is POST data
		{
			file_put_contents("post", var_export($_POST, true));

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
			} // else if (isset($_POST["service"]))

			else if (isset($_POST["replaceImg"])) // Replace a site image
			{
				$targDir = "../img"; // Common images dir.
				file_put_contents("files", var_export($_FILES, true));
				$relTarget = $targDir . "/" . htmlentities(strip_tags(trim(basename($_FILES["file"]["name"]))));
				file_put_contents("relTarget", var_export($relTarget, true));
				$imgType = $_FILES["file"]["type"];
				file_put_contents("imgType", var_export($imgType, true));
				$acceptedTypes = array("image/png", "image/jpeg", "image/gif");

				if (mime_content_type($_FILES["file"]["tmp_name"]) === $imgType) // The type the browser sent is the actual type (security check)
				{
					if (in_array($imgType, $acceptedTypes)) // Acceptable file
					{
						if (!file_exists($absTarget)) // File wasn't already uploaded
						{
							$maxSizeInGB = 1;
							$maxSizeInBytes = $maxSizeInGB * pow(1024, 3); // Convert GB to bytes for comparison
							file_put_contents("maxSizeInBytes", var_export($maxSizeInBytes, true));

							if ($_FILES["file"]["size"] <= $maxSizeInBytes) // The image is small enough
							{
								if ($_FILES["file"]["error"] === UPLOAD_ERR_OK) // No other error occurred
								{
									if (move_uploaded_file($_FILES["file"]["tmp_name"], $relTarget)) // Successfully transferred the file to the images directory
									{
										$updateSQL = "UPDATE images SET path=:path WHERE purpose=:purpose"; // SQL to use to change the image's path
										$stmt = $dbh->prepare($updateSQL);
										$pathToStore = basename($relTarget);
										$purposeToStore = str_replace("-", " ", htmlentities(strip_tags(trim($_POST["purpose"]))));
										$res = $stmt->execute(
											array(
												"path" => $pathToStore, // We only need the file's name
												"purpose" => $purposeToStore
											)
										);
										file_put_contents("query", "UPDATE images SET path='" . $pathToStore ."' WHERE purpose='" . $purposeToStore ."'");

										if ($res) // Successfully updated DB
										{
											die(
												json_encode(
													array(
														"success" => $relTarget
													)
												)
											);
										}

										else // Failed to update DB
										{
											die(
												json_encode(
													array(
														"failure" => "dbUpdateFailed"
													)
												)
											);
										}
									}
		
									else // Couldn't move the file
									{
										die(
											json_encode(
												array(
													"failure" => "moveFailed"
												)
											)
										);
									}
								}
	
								else // Some other error occurred
								{
									die(
										json_encode(
											array(
												"failure" => "unknown"
											)
										)
									);
								}
							}
	
							else // Too big
							{
								die(
									json_encode(
										array(
											"failure" => "tooBig"
										)
									)
								);
							}
						}
	
						else // File was already uploaded
						{
							die(
								json_encode(
									array(
										"failure" => "alreadyExists"
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
									"failure" => "invalidImgType"
								)
							)
						);
					}
				} // if (mime_content_type(...
	
				else // Mismatched types - malicious request
				{
					die(
						json_encode(
							array(
								"failure" => "MIMEmismatch"
							)
						)
					);
				}
			}

			else
			{
				file_put_contents("admPost", var_export($_POST, true));
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
