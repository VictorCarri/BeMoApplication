<?php
	session_start();
	unset($_SESSION); // Delete session variables
	session_destroy(); // End the session
	header('Location: ../index.php'); // Redirect them to the homepage
?>
