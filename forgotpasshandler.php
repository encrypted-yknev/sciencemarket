<?php
require_once("connectDb.php");
include 'app/ses_mail.php';


function sanitizeData($text)	{
	$text=trim($text);
	$text=stripslashes($text);
	$text=htmlspecialchars($text);
	return $text;
}

function validate_email($email)	{
	if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		return false;
	return true;
}

if($_SERVER["REQUEST_METHOD"]=="POST"){
	if(isset($_POST["email"])) {
		$email = sanitizeData($_POST["email"]);
		if(empty($email)){
			echo "Email address is mandatory";
			return 1;
		}
		if(!validate_email($email)){
			echo "Invalid Email-ID. Please enter a valid Email";
			return 1;
		}
		try	{
			$sql_fetch_user="select * from users where email_addr = '".$email."'";
			$stmt=$conn->prepare($sql_fetch_user);
			$stmt->execute();
			$result=$stmt->fetchAll();
			$count= sizeof($result);
			if($count == 0){
				echo "Uh oh! No such user exists";
				return 1;
			}
			else if($count > 1){
				echo "Multiple users with same email detected";
				return 1;
			}

			else if($count == 1)	{
				$result = $result[0]; //Get the result object.
				$id= $result["user_id"];
				$fpstatus = "1";
				$salt = "sci-market-pass-reset";
				$fpkey = hash("sha256",$id+$salt+time());
				$user_fp_update="UPDATE users SET fpstatus= '1', fpkey = '".$fpkey."' WHERE user_id = '".$id."'";
				$stmt=$conn->prepare($user_fp_update);
				$stmt->execute();
				$link= "?email=".$email."&fpkey=".$fpkey;
				$base = $_SERVER["HTTP_HOST"];
				$html_body = "<div style='border: 1px solid #efefef; text-align:center;'> <img src=\"http://www.sciencemarket.org/img/logo.svg/\" width=17% height=17% /><h1>Sciencemarket</h1>
					<p>You have requested for password reset. Please click on the link below to change your password</br>
					<a href=\"$base/resetpasswd.php$link\">Reset password</a></div>
					</p>";
				$mail = new ses_mail();
				$mail->sendEmail($email,$result["disp_name"],"Password Reset", $html_body);
				echo 1;
				return 1;
				//$stmt=$conn->prepare($);

			}
		}
		catch(PDOException $e)	{
			echo "Some error occurred";
			return 1;
		}

	}
	else {
		echo "Invalid Request";
		return 1;
	}
}
else{
	$id= 4;
	$fpstatus = "Y";
	$salt = "sci-market-pass-reset";
	$fpkey = sha1($id+$salt+time());
	$user_fp_update="UPDATE users SET fpstatus= 'Y', fpkey = '".$fpkey."' WHERE id = '".$id."'";
	echo $user_fp_update;
}
 ?>
