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
