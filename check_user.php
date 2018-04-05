<?php

include "connectDb.php";
if($_POST["type"]=="username"){
	$user=htmlspecialchars(stripslashes(trim($_REQUEST['user'])));

	if(strlen($user) > 0)	{
		try		{
			$sql_check_unique_user="select count(*) as count_user from users where user_id='$user'";
			$stmt=$conn->prepare($sql_check_unique_user);
			$stmt->execute();
			$result=$stmt->fetch();
			$count=$result['count_user'];

			if($count > 0)
				echo "1";
			else
				echo "0";
		}

		catch(PDOException $e)	{
			echo "Internal server error";
		}
	}
	else
		echo "Enter Username";
	}


else if($_POST["type"]=="email"){
	$email=htmlspecialchars(stripslashes(trim($_REQUEST['mail'])));

	if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
		echo "2";
		return 1;
	}

	if(strlen($email) > 0)	{
		try		{
			$sql_check_unique_mail="select count(1) as count_email from users where email_addr='$email'";
			$stmt=$conn->prepare($sql_check_unique_mail);
			$stmt->execute();
			$result=$stmt->fetch();
			$count=$result['count_email'];

			if($count > 0)
				echo "1";
			else
				echo "0";
		}

		catch(PDOException $e)	{
			echo "Internal server error";
		}
	}
	else
		echo "Enter EmailID";
}

?>
