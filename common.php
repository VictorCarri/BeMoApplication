<?php
	require_once(__DIR__ . "/db.php"); // Generate an absolute path
	$root = "/srv/www/htdocs";

	/**
	* @param $purpose The purpose of the image (eg. "Site logo"). Also used as the image's alt text
	* @return Image HTML as a string.
	**/
	function makeImageHTML($purpose, $prefix = "./")
	{
		global $dbh;
		$getImageDataSQL = "SELECT * FROM images WHERE purpose=:purpose";
		$gidStmt = $dbh->prepare($getImageDataSQL);
		$gidStmt->execute(
			array(
				":purpose" => $purpose
			)
		);

		$html = "";

		foreach ($gidStmt->fetchAll() as $row)
		{
			$imgAttrs = json_decode($row["attrs"]); // Fetch the image's attributes
			$html .= "<img src=\"". $prefix ."img/" . $row["path"] . "\" alt=\"" . $purpose . "\" width=\"" . $imgAttrs->width . "\" height=\"" . $imgAttrs->height . "\" />";
		}
		
		file_put_contents("makeImageHTML", $html);
		return $html;
	}

	/**
	* @param $purpose The purpose of the image (eg. "Site logo"). Also used as the image's alt text.
	* @param prefix Prepended to the path to allow files in subdirectories to use paths that are relative to the document root.
	* @return Image path as a string.
	**/
	function makeImageURL($purpose, $prefix = "./")
	{
		global $dbh;
		$getImageDataSQL = "SELECT * FROM images WHERE purpose=:purpose";
		$gidStmt = $dbh->prepare($getImageDataSQL);
		$gidStmt->execute(
			array(
				":purpose" => $purpose
			)
		);

		$url = "";

		foreach ($gidStmt->fetchAll() as $row)
		{
			$imgAttrs = json_decode($row["attrs"]); // Fetch the image's attributes
			$url .= $prefix . "img/" . $row["path"];
		}
		
		file_put_contents("makeImageURL", $url);
		return $url;
	}
?>
