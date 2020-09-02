<!doctype html>
<!--[if IE 8 ]><html lang="en" class="ie8"><![endif]-->
<!--[if IE 9 ]><html lang="en" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html lang="en"><!--<![endif]-->
<?php
	require_once("./db.php"); // DB setup
	require_once("./common.php"); // Common functions
	$getIsIndexableSQL = "SELECT indexable FROM pages WHERE url=:url";
	$isIndexableStmt = $dbh->prepare($getIsIndexableSQL);
	$isIndexableStmt->execute(array(":url" => basename($_SERVER["SCRIPT_FILENAME"])));

	foreach ($isIndexableStmt->fetchAll() as $row)
	{
		$isIndexable = boolval($row["indexable"]);
	}

	$pageInfoSQL = "SELECT description,title FROM pages WHERE url=:url";
	$pageInfoStmt = $dbh->prepare($pageInfoSQL);
	$pageInfoStmt->execute(
		array(
			":url" => basename($_SERVER["SCRIPT_FILENAME"])
		)
	);

	foreach ($pageInfoStmt->fetchAll() as $row)
	{
		$desc = $row["description"];
		$title = $row["title"];
	}
?>
	
	<head>
	<meta name="viewport" content="initial-scale=1 maximum-scale=1"/>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="<?php echo $desc; ?>" />
		<!-- <title>Contact Us</title> -->
		<title><?php echo $title; ?></title>
		<link rel="stylesheet" type="text/css" media="screen" href="./css/styles.css"  />
		<link rel="stylesheet" type="text/css" media="screen" href="./css/colourtag-page3.css"  />
		<link rel="stylesheet" type="text/css" media="screen" href="./css/flexslider.css"  />
		<link rel="stylesheet" type="text/css" media="screen" href="./css/contentcenter.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="./css/ec9on.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="./css/rimage.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="./css/ssoff.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="./css/sslide.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="./css/sidenone.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="./css/olight90.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="./css/fontarial.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="./css/title26.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="./css/fontarialspan.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="./css/bts46.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="./css/btoff.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="./css/fontarialnav.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="./css/nav17.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="./css/fontarialside.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="./css/fontarialheader.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="./css/fontarialcontent.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="./css/font13.css" />
		
		<style type="text/css" media="all">#feature {background-image: url(resources/contact-us.png);}</style>
		<!--[if lt IE 9]>
		<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<script type="text/javascript" src="rw_common/themes/Endeavor/scripts/html5shiv.js"></script>
		<![endif]-->	
			
		<script type="text/javascript" src="./js/javascript.js"></script>
		<script type="text/javascript" src="./js/jquery-1.7.1.min.js"></script>
		
		
		
		<script type="text/javascript" src="./js/function.js"></script>
		<script type="text/javascript" src="./js/jquery.fitvids.js"></script>
		<script type="text/javascript" src="./js/jquery.flexslider.js"></script>

			<?php
				if (!$isIndexable) // Allows admin to control it
				{
					echo "<meta name=\"robots\" content=\"noindex\" />";
				}
			?>
<!-- Start Google Analytics -->
<?php
	$gaStmt = $dbh->query("SELECT code FROM analytics WHERE service='Google Analytics'");

	foreach ($gaStmt->fetchAll() as $row)
	{
		echo $row["code"];
	}
?>
<!-- End Google Analytics -->


<!-- Start Facebook Pixel -->
<?php
	$gaStmt = $dbh->query("SELECT code FROM analytics WHERE service='Facebook Pixel'");

	foreach ($gaStmt->fetchAll() as $row)
	{
		echo $row["code"];
	}
?>
<!-- End Facebook Pixel -->
</head>
		
<body>

<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?id=235586069975455&amp;ev=NoScript" /></noscript>

	<div id="wrapper">
	
		
<div id="hwrap">
			<header class="navbar navbar-default navbar-inverse navbar-fixed-top" role="navigation">
			<div id="headwrap">
					<div id="titlelogo">
						<a href="./index.php">
						<div id="logo">
							<?php
								echo makeImageHTML("Site logo"); // ID 1 is our logo
								file_put_contents("imageHTML", makeImageHTML("Site logo"));
							?>
						</div>	
							<h1></h1></a>
							<h2></h2>
					</div>
						
						
						<div id="mwrap">
							<div id="lt"></div>
							<div id="lm"></div>
							<div id="lb"></div>
						</div>
						
						
						<div id="nwrap">
							<div id="menuBtn"></div>
							<nav>
								<ul class="navigation">
									<li><a href="index.php" rel="self">Main</a></li>
									<li id="current"><a href="contact-us.php" rel="self" id="current">Contact Us</a></li>
								</ul>
							</nav>	
						</div>
				</div>
			</header>
		
			
				
				<div class="banner video_banner">
					<div id="feature">
					<div id="extraContainer11">
						<div class="videoWrapper">
								    
						</div>
					</div>
					
					
					
						
						<div id="extraContainer1">
						</div>
					
						
						<div class="banner-text">
						
						</div>
							<div id="extraContainer9"></div>
					</div>
					
					
				</div>	
					
					
								
			</div>												
				
		
					
	
			<div class="clear"></div>
			
		
			<div id="container">
					<div id="extraContainer7"></div>
					<div id="extraContainer8"></div>
							
							<section>
								
								<div id="padding">
<div class="message-text"><span style="font-size:17px; font-weight:bold; ">BeMo Academic Consulting Inc. </span><br /><span><span style="font-size:13px; font-weight:bold; "><u>Toll Free</u></span><span style="font-size:13px; ">: </span><span style="font-size:14px; ">1-855-900-BeMo (2366)</span><span style="font-size:13px; "><br /></span><span style="font-size:13px; font-weight:bold; "><u>Email</u></span><span style="font-size:13px; ">: </span><span style="font-size:14px; "><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="9cf5f2faf3dcfef9f1f3fdfffdf8f9f1f5fffff3f2efe9f0e8f5f2fbb2fff3f1">[email&#160;protected]</a></span></div><br />

<form action="./mailer.php" method="post" enctype="multipart/form-data">
	 <div>
		<label>Name:</label> *<br />
		<input class="form-input-field" type="text" value="" name="senderName" size="40"/><br /><br />

		<label>Email Address:</label> *<br />
		<input class="form-input-field" type="text" value="" name="senderAddr" size="40"/><br /><br />

		<label>How can we help you?</label> *<br />
		<textarea class="form-input-field" name="message" rows="8" cols="38"></textarea><br /><br />

		<div style="display: none;">
			<label>Spam Protection: Please don't fill this in:</label>
			<textarea name="comment" rows="1" cols="1"></textarea>
		</div>
		<input type="hidden" name="form_token" value="16720582625f4eb91fbaec9" />
		<input class="form-input-button" type="reset" name="resetButton" value="Reset" />
		<input class="form-input-button" type="submit" name="submitButton" value="Submit" />
	</div>
</form>

<br />
<div class="form-footer"><span style="font-size:15px; font-weight:bold; "><u>Note</u></span><span style="font-size:15px; ">: If you are having difficulties with our contact us form above, send us an email to <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="573e3931381735323a3836343633323a3e3434383924223b233e39307934383a">[email&#160;protected]</a> (copy &amp; paste the email address)</span><span style="font-size:13px; "><br /></span></div><br />

</div>
								
							</section>
						<div id="asidewrap">
							<aside>
								<div id="sidecontent">
									<div id="sideTitle"></div>	
									<a class= "social" href= "https://www.facebook.com/bemoacademicconsulting">F</a>
<a class= "social" href= "https://twitter.com/BeMo_AC">L</a>	
									
								</div>	
							</aside>
						</div>	
						<div class="clear"></div>
				
								<div id="ecwrap"></div>
								<div id="ec2wrap">	<div id="extraContainer2"></div></div>
								<div id="ec3wrap">	<div id="extraContainer3"></div></div>
								<div id="ec4wrap">	<div id="extraContainer4"></div></div>
								<div id="ec5wrap">	<div id="extraContainer5"></div></div>
								<div id="ec6wrap">	<div id="extraContainer6"></div></div>

								<div id="extraContainer10"></div></div>		
						<footer>
						
							<div id="footer">&copy;2013-2016 BeMo Academic Consulting Inc. All rights reserved. <a href="http://www.cdainterview.com/disclaimer-privacy-policy.html"target="_blank"><span style="text-decoration:underline;">Disclaimer & Privacy Policy</span></a> <a href="#" id="rw_email_contact"><span style="text-decoration:underline;">Contact Us</span></a><script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script type="text/javascript">var _rwObsfuscatedHref0 = "mai";var _rwObsfuscatedHref1 = "lto";var _rwObsfuscatedHref2 = ":in";var _rwObsfuscatedHref3 = "fo@";var _rwObsfuscatedHref4 = "bem";var _rwObsfuscatedHref5 = "oac";var _rwObsfuscatedHref6 = "ade";var _rwObsfuscatedHref7 = "mic";var _rwObsfuscatedHref8 = "con";var _rwObsfuscatedHref9 = "sul";var _rwObsfuscatedHref10 = "tin";var _rwObsfuscatedHref11 = "g.c";var _rwObsfuscatedHref12 = "om";var _rwObsfuscatedHref = _rwObsfuscatedHref0+_rwObsfuscatedHref1+_rwObsfuscatedHref2+_rwObsfuscatedHref3+_rwObsfuscatedHref4+_rwObsfuscatedHref5+_rwObsfuscatedHref6+_rwObsfuscatedHref7+_rwObsfuscatedHref8+_rwObsfuscatedHref9+_rwObsfuscatedHref10+_rwObsfuscatedHref11+_rwObsfuscatedHref12; document.getElementById('rw_email_contact').href = _rwObsfuscatedHref;</script></div>
								
								<div id="socialicons">
								<div id="socialicons1"></div>
								</div>
							
						</footer>
							
							
			</div>
			 			
					<a href="#" class="scrollup">Scroll</a>	
	
				
			
	</div>			
</body>
</html>
