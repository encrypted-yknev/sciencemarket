<?php
session_start();
require_once("connectDb.php");

function sanitizeData($text)	{
	$text=trim($text);
	$text=stripslashes($text);
	$text=htmlspecialchars($text);
	return $text;
}

if($_SERVER["REQUEST_METHOD"]=="POST"){
	if(isset($_POST['requestTyp']))	{
		$requestTyp = $_POST['requestTyp'];
	}
	if(isset($_POST['title']))	{
		$title = sanitizeData($_POST['title']);
	}
	if(isset($_POST['summary']))	{
		$summary = sanitizeData($_POST['summary']);
	}
	if(isset($_POST['users']))	{
		$users = sanitizeData($_POST['users']);
	}
	if(isset($_POST['univ']))	{
		$univ = sanitizeData($_POST['univ']);
	}
	if(isset($_POST['loc']))	{
		$loc = sanitizeData($_POST['loc']);
	}
	if(isset($_POST['strtDt']))	{
		$strtDt = sanitizeData($_POST['strtDt']);
	}
	if(isset($_POST['endDt']))	{
		$endDt = sanitizeData($_POST['endDt']);
	}
	if(isset($_POST['skillsReq']))	{
		$skillsReq = sanitizeData($_POST['skillsReq']);
	}
	if($requestTyp == 1)	{		
		if(isset($_POST['skills']))	{
			$skills = sanitizeData($_POST['skills']);
		}
		$authid = null;
	}
	else if($requestTyp == 2)	{
		if(isset($_POST['stage']))	{
			$stage = sanitizeData($_POST['stage']);
		}
		if(isset($_POST['desc']))	{
			$desc = sanitizeData($_POST['desc']);
		}
		if(isset($_POST['authid']))	{
			$authid = sanitizeData($_POST['authid']);
		}
	}
	
	
	if($requestTyp == 1)	{
		$post_typ = 'C';
		$post_desc4 = $skills;
		$post_desc5 = null;
	}
	else if($requestTyp == 2)	{
		$post_typ = 'A';
		$post_desc4 = $desc;
		$post_desc5 = $stage;
	}
	try		{
		
		$sql_post_collaboration = "insert into collaborations
										(post_typ
										,post_title
										,post_desc1
										,post_desc2
										,post_desc3
										,post_desc4
										,post_desc5
										,authorship_id
										,estd_strt_dt
										,estd_end_dt
										,src_university
										,src_location
										,posted_by
										,last_updt_by
										)
									values(
										'".$post_typ."'
									   ,'".$title."'
									   ,'".$summary."'
									   ,'".$users."'
									   ,'".$skillsReq."'
									   ,'".$post_desc4."'
									   ,'".$post_desc5."'
									   ,'".$authid."'
									   ,'".$strtDt."'
									   ,'".$endDt."'
									   ,'".$univ."'
									   ,'".$loc."'
									   ,'".$_SESSION['user']."'
									   ,'".$_SESSION['user']."'									   									   
									)
		";
		
		$stmt_post_collaboration = $conn->prepare($sql_post_collaboration);
		$stmt_post_collaboration->execute();
		if($stmt_post_collaboration->rowCount() > 0)	{
			echo "1";
		}
		else	{
			echo "2";
		}
		
	}
	catch(PDOException $e)	{
		echo $e->getMessage();
	}
	
}
else	{
	echo "no";
}

 ?>
