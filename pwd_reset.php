<?php
session_start();
if(isset($_SESSION["logged_in"]))	{
	if($_SESSION["logged_in"])	{
		header("location:dashboard.php");
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Science Market - Password reset</title>
<meta name="description" content="Science Market is an online market place to connect with peers, people, groups or expert. Discuss topics in question answer forum, connect with experts under expert connect, collaborate with people and provide favours. This is a password reset page in case you forget your password" >
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="styles/header.css">
<link rel="stylesheet" type="text/css" href="/styles/login.css" >
<link rel="stylesheet" type="text/css" href="styles/footer.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="js/login.js"></script>
<link rel="stylesheet" href="styles/bootstrap.min.css">
<script src="js/bootstrap.min.js"></script>
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">

</head>
<body>

<?php
include "connectDb.php";
$message="<div class='msg-default'>Password reset</div>";
$pwddb="";
$userid="";
$nameError="*";
$nameSuc="";
$pwdError="*";
$checked=$success=true;

if ($_SERVER["REQUEST_METHOD"] == "POST")	{
	#check for empty field
	if(empty($_POST["emailid"]))	{
		$message="<div class='alert alert-danger login-message'>Enter mandatory fields</div>";
		$checked=false;
	}
	else	{
		
		$email=processData($_POST["emailid"]);
		if(!validate_email($email))	{
			$message="<div class='alert alert-danger login-message'>Invalid Email - Try again</div>";
			$checked=false;
			$emailid="";
		}
		else	{
			$sql_fetch_user="select count(1) as cnt_user from users where email_addr = '".$email."'";
			$stmt=$conn->prepare($sql_fetch_user);
			$stmt->execute();
			$result=$stmt->fetch();
			$count=$result['cnt_user'];
			
			if($count == 0)
				$message="<div class='alert alert-danger login-message'>Uh oh! No such user exists</div>";
			else if($count > 1) 
				$message="<div class='alert alert-danger login-message'>Multiple user with same e-mail</div>";
			else if($count == 1)	{	
				$pwd_id = md5(uniqid());
				try	{
					$sql_updt_pwd_id = "update users set frgt_pwd_id = '".$pwd_id."' where email_addr = '".$email."'";
					$stmt_updt_pwd_id = $conn->prepare($sql_updt_pwd_id);
					$stmt_updt_pwd_id->execute();
					if($stmt_updt_pwd_id->rowCount() > 0)	{
						$mail_to = "venkycse93@gmail.com";
						$mail_from = "admin@sciencemarket.org";
						include "send_mail.php";
					}
					else	{
						$message="<div class='alert alert-danger login-message'>Some error occurred</div>";
					}
				}
				catch(PDOException $e)	{
					$message="<div class='alert alert-danger login-message'>Server error. Please try again later</div>";
				}
				
			}
		}
	}
}
#Function to trim extra spaces/backslashes and avoiding cross-scripting
function processData($text)	{
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
?>
<div id="bg-window"></div>
	<!--<div class="container"> -->
		<div id="main-container" style="min-height:300px;">
			</br>
			<div id="login-logo">
				<img id="" src="img/logo4.svg" width=15% height=15%/>
				<img id="" src="img/logo.svg" width=50% height=50%/>
			</div></br>	
			<?php echo $message; ?></br>
			<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
				<div class="form-group form-section">
					<label for="mail-area" class="form-labels">Registered E-mail</label>
					<input class="form-control input-class" id="mail-area" type="text" name="emailid" placeholder="Enter Email ID" value="<?php echo $mailid;?>" />
				</div>
				
			<!--	<div class="g-signin2" data-onsuccess="onSignIn"></div></br> -->
				
				</br>
				<div class="form-row-3">
					<input class="btn btn-primary inp-button" type="submit" value="Logon" />
					<a href="register.php" class="btn btn-primary inp-button">New user? Sign up</a>
				</div>
			</form>
		</div>
	<!--</div>-->
</body>
</html>
