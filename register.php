<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Science Market : Get started. Register here.</title>
<link rel="stylesheet" type="text/css" href="styles/register.css">
<link rel="stylesheet" type="text/css" href="styles/footer.css">
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<link rel="stylesheet" href="styles/bootstrap.min.css">
<!-- Latest compiled JavaScript -->
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/register.js"></script>
<!--<script src='https://www.google.com/recaptcha/api.js'></script>-->
</head>
<body>
<?php #include "header.php";
include "connectDb.php";
$name=$userid=$pwd=$repwd=$mail="";					#Variables to store input values.
$message=$subgroup="";
function inbt_email_check($mail)	{
	$index_of_char=strpos($mail,'@');
	$domain_name=substr($mail,$index_of_char+1);
	if(!strcmp($domain_name,'jh.edu'))
		return 1;
	return 0;
}
function get_group_id($subgroup)	{
	if($subgroup == 'A')
		return 2;
	else if($subgroup == 'F')
		return 3;
	else if($subgroup == 'G')
		return 4;
	else if($subgroup == 'U')
		return 5;
	else if($subgroup == 'P')
		return 6;
	else return 0;
}
if($_SERVER["REQUEST_METHOD"]=="POST")	{

	#Check for proper name - empty/invalid characters
	$name=htmlspecialchars(stripslashes(trim($_POST['name'])));
	$userid=htmlspecialchars(stripslashes(trim($_POST['user'])));
	$pwd=htmlspecialchars(stripslashes(trim($_POST['pwd'])));
	$hashed_pwd=md5($pwd);
	$mail=htmlspecialchars(stripslashes(trim($_POST['mail'])));

	if(!empty($name) && !empty($userid) && !empty($hashed_pwd) && !empty($mail))	{
		$secret = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe'; //Replace with our own, these keys are localhost keys. Replace both this key and the one in HTML. 
		$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
        $responseData = json_decode($verifyResponse);
		if($responseData->success){
			if(filter_var($mail, FILTER_VALIDATE_EMAIL)){
				try	{
      
					$sql="insert into users
					(user_id,disp_name,encrypt_pwd,email_addr,status)
					values
					('".$userid."','".$name."','".$hashed_pwd."','".$mail."','A')";

					$conn->exec($sql);
					$message = "Registration successful";
					/* Logic starts for INBT email check */
					if(inbt_email_check($mail) == 1)	{
						/* Check which subgroup user belongs to  */
						$sql_check_subgroup="select code_typ from codevalues where code_val='EMAILID' and code_desc='".trim($mail)."'";
                        $message = $sql_check_subgroup;
						$stmt_check_subgroup=$conn->prepare($sql_check_subgroup);
						$stmt_check_subgroup->execute();
						if($stmt_check_subgroup->rowCount()>0)	{
							$result_code=$stmt_check_subgroup->fetch();
							$subgroup=$result_code['code_typ'];
						}
						else	{
							$subgroup="";
						}
						$subgroup_id=get_group_id($subgroup);
						if($subgroup_id <> 0)	{
							$sql_add_mbr_subgroup="insert into group_mbr (user_id,group_id,created_by,last_updt_by) 
												   values ('".$userid."',".$subgroup_id.",'admin','admin')";
							$conn->exec($sql_add_mbr_subgroup);
							$sql_add_mbr_inbt="insert into group_mbr (user_id,group_id,created_by,last_updt_by) 
											   values ('".$userid."',1,'admin','admin')";
							$conn->exec($sql_add_mbr_inbt);

                       					
                        }
						
					}
					session_start();
					include "session.php";	#Starting user session

					header("location:user_preference.php");		#On successful registration, redirect to dashboard
				}
				catch(PDOException $e)	{
					// Debug only $message = $e->getMessage();
					$message = "Uh oh something went wrong, please try again";
				}
			}
			else{
				$message="Invalid email";
			}
		}
	else{
		$message="Captcha Invalid, Try again";
	}
}
else
	$message = "Uh oh!! All fields are mandatory";
}



 ?>
<!--<h2>User Registration</h2>-->
</br>
<div class="container">
	<div id="reg-container">
			<div class="row">
				<div id="logo-section" class="col-sm-4">
					<img src="img/logo4.svg" width="55" height="55"/>
					<img src="img/logo.svg" width="140" height="60"/>
				</div>
				<div class="col-sm-8">
					<span id="page-caption"><em>An online portal to discuss stuffs among peers, connect with experts and collaborate with people. Register now to get started</em></span>
				</div>
			</div></br>
			<div id="main-err-message"><?php echo $message; ?></div></br>
			<form class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" name="user-form" method="post">

				<div class="form-group">
					<label class="control-label col-sm-4 form-label" for="name">Enter full name</label>
					<div class="col-sm-4">
						<input class="form-control" id="name" type="text" name="name" onfocus="$('#msg-1').show();" onblur="$('#msg-1').hide();" value="<?php echo $name; ?>"
							onfocusout="validateData(this.value,'name-error')" />
						<div class="msg-info" id="msg-1">Enter valid name. Should contain only spaces and alphabets</div>
					</div>
					<div class="col-sm-4 message-section">
						<span id="name-error" class="col-3-data"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4 form-label" for="user">Choose UserID</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" id="user" name="user" onfocus="$('#msg-2').show();" onblur="$('#msg-2').hide();" value="<?php echo $userid; ?>"
							 onfocusout="validateUser(this.value)" />
						<div class="msg-info" id="msg-2">Username must be unique</div>
					</div>
					<div class="col-sm-4 message-section">
						<span id="user-error" class="col-3-data"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4 form-label" for="pwd">Choose password</label>
					<div class="col-sm-4">
						<input class="form-control" type="password" id="pwd" name="pwd" onfocus="$('#msg-3').show();" onblur="$('#msg-3').hide();"
							 onfocusout="validatePassFld(this.value,'pwd-error')" />
						<div class="msg-info" id="msg-3">Password should contain minimum of 8 characters</div>
					</div>
					<div class="col-sm-4 message-section">
						<span id="pwd-error" class="col-3-data"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4 form-label" for="repwd">Re-enter password</label>
					<div class="col-sm-4">
						<input class="form-control" type="password" id="repwd" onfocus="$('#msg-4').show();" onblur="$('#msg-4').hide();"
								onfocusout="validateRePassFld(document.getElementById('pwd').value,this.value,'repass-error')" />
						<div class="msg-info" id="msg-4">Re-enter same password as above</div>
					</div>
					<div class="col-sm-4 message-section">
						<span id="repass-error" class="col-3-data"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4 form-label" for="mail">Email</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" id="mail" name="mail" onfocus="$('#msg-5').show();" onblur="$('#msg-5').hide();" value="<?php echo $mail; ?>"
						onfocusout="validateEmail(this.value)" />
						<div class="msg-info" id="msg-5">Enter valid email of format abc@xyz.pqr</div>
					</div>


					<div class="col-sm-4 message-section">
						<span id="email-error" class="col-3-data"></span>
					</div>
				</div></br>
				<div class="form-group">
					<div class="col-sm-6 col-sm-offset-3">
					<div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div>
				</div>
				</div>
				<!--
				<div id="captcha-div">
					<div class="g-recaptcha form-group" data-sitekey="6LcnWUMUAAAAAKDPfsZ-kzcbQoU0mOECoVmCRZZn"></div>
				</div></br>-->
				<div id="button-section">
					<input id="reg-button" class="btn btn-default nav-button" type="submit" value="Register" />
					<input id="res-button" class="btn btn-default nav-button" type="reset" value="Reset" />
					<a href="index.php" id="home-button" class="btn btn-primary nav-button">Home</a>
				</div>
			</form>
	</div>

	</div>
<?php #include "footer.php"; ?>
</body>
</html>
