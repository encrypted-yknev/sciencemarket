<?php
require_once("connectDb.php");

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
	$email = $_POST["mail"];
	$fpkey = $_POST["fpkey"];
	$pass = $_POST["pass"];
	$cnf_pass = $_POST["cnfpass"];
	if($pass!=$cnf_pass) {
		echo 2; return 1; 
	}
	try	{
		$sql_fetch_user="select * from users where email_addr = '".$email."'";
		$stmt=$conn->prepare($sql_fetch_user);
		$stmt->execute();
		$result=$stmt->fetchAll();
		$count= sizeof($result);
		if($count!=1) {
			echo "An Error occured please contact support"; 
			return 1; 
		}
		$result=$result[0];
		$id = $result["user_id"];
		if($result['fpstatus']!=1 || $fpkey!=$result["fpkey"]) {
			echo "Password reset link expired. Redirecting..."; 
			return 1;
		}
		// @TODO this password needs to be stored in sha256(salt+md5(passwd) format and not just md5. md5 is insecure.
		$hashedpass = md5($pass);
		$user_fp_update="UPDATE users SET fpstatus= '0', encrypt_pwd = '".$hashedpass."' WHERE user_id = '".$id."'";
		$stmt=$conn->prepare($user_fp_update);
		$stmt->execute();
		echo 1;
	}
	catch(PDOException $e)	{
		echo "Some error occurred";
	}

}
?>
