<?php
if(isset($_COOKIE['user_tz']))
	date_default_timezone_set($_COOKIE['user_tz']);

include "connectDb.php";

function convert_utc_to_local($utc_timestamp)	{
	
	try	{
		$date_utc=new DateTime($utc_timestamp,new DateTimeZone('UTC'));
		if(isset($_COOKIE['user_tz']))
			$date_utc->setTimeZone(new DateTimeZone($_COOKIE['user_tz']));	
		else
			$date_utc->setTimeZone(new DateTimeZone('UTC'));	
		$date_final = $date_utc->format('Y-m-d H:i:s');
		return $date_final;
	}
	catch(Exception $e)	{
		echo 'Some error occurred';
	}
}
$url_path = $_SERVER['PHP_SELF'];
$count_slash = substr_count($url_path,"/");
if($count_slash==1)
	$slashes = "";
else if($count_slash==2)
	$slashes = "../";
else if($count_slash==3)
	$slashes = "../../";
else if($count_slash==4)
	$slashes = "../../../";

?>

<header id="masthead" role="banner" class="">
	<div id="sc-logo-section">
		<div id="head-logo">
			<img style="margin-top:10px" src="<?php echo $slashes; ?>img/logo4.svg" height=70% />
			<a href="<?php echo $slashes; ?>dashboard.php">
				<img src="<?php echo $slashes; ?>img/logo.svg" height=45% />
			</a>
		</div>		
	</div>
	<div id="navigation-section">
		<nav id="menu1">
			<ul id="head-menu1">
				<a class="nav-link" href="<?php echo $slashes; ?>home.php" title="Home"><li class="nav-list">HOME</li></a>
				<a class="nav-link" href="<?php echo $slashes; ?>about.php" title="About"><li class="nav-list">ABOUT</li></a>
				<a class="nav-link" href="<?php echo $slashes; ?>index.php" title="Login portal"><li class="nav-list">LOG IN</li></a>
				<a class="nav-link" href="<?php echo $slashes; ?>register.php" title="Registration portal"><li class="nav-list">REGISTER</li></a>
			</ul>
		</nav>
	</div>
	<div id="navigation-section-media">
		<nav id="menu2">
			<ul id="head-menu2">
				<a class="nav-link" href="<?php echo $slashes; ?>home.php" title="Home"><li class="nav-list">HOME</li></a> 
				<a class="nav-link" href="<?php echo $slashes; ?>about.php" title="About"><li class="nav-list">ABOUT</li></a>
				<a class="nav-link" href="<?php echo $slashes; ?>index.php" title="Login portal"><li class="nav-list">LOG IN</li></a>
				<a class="nav-link" href="<?php echo $slashes; ?>register.php" title="Registration portal"><li class="nav-list">REGISTER</li></a>
			</ul>
		</nav>
	</div>
		
</header>
<button id = "go-top-btn" class="btn btn-primary" onclick="window.scrollTo(0,0)">&#9650;</button>
<div id="block-bg"></div>
<input id="slash" type="hidden" value="<?php echo $slashes; ?>" />