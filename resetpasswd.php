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
$err_acc=0;
if(!isset($_GET["email"]) || !isset($_GET["fpkey"])){
	$err_acc = 1;
}
$email = $_GET["email"];
$fpkey = $_GET["fpkey"];

//echo $fpkey;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Science Market - Password Reset Page</title>
<meta name="description" content="Science Market is an online market place to connect with peers, people, groups or expert. Discuss topics in question answer forum, connect with experts under expert connect, collaborate with people and provide favours." >
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
<script type="text/javascript">
$(document).ready(function(){
	var flag = <?php echo $err_acc; ?>;

	if(flag==1){
		$("#reset-pass").addClass("disabled");
		$("#error").html("Invalid page access, please use a valid reset link");
		$("#show-alert").show();
	}
	$("#reset-pass").click(function(e){
		e.preventDefault();
		if(flag!=1){
		$("#show-alert").hide();
		$("#loader").show();
		$(this).addClass("disabled");
		var email = $("#mail-area").val();
		var dataArray = $("#password-reset").serialize();
	//	console.log(dataArray);
		$.ajax({
			url:"/resetpasshandler.php",
			method: "POST",
			data: dataArray,
			dataType:"text",
			success: function(data){
				if(data=="1"){
					$("#loader").hide();
					$("#show-alert").hide();
					$("#success").html("Password changed succesfully. Redirecting...");
					$("#submit").show();
					
					setTimeout(window.location.replace("/index.php"),1500);
				}
				else if(data=="2"){
					$("#loader").hide();
					$("#error").html("Error passwords do not match");
					$("#show-alert").show();
					$("#reset-pass").removeClass("disabled");
				}
				else{
					$("#loader").hide();
					$("#error").html(data);
					$("#show-alert").show();
					$("#hide-form").hide();
					
					setTimeout(window.location.replace("/frgt_pwd.php"),5000);
				}

			}

		})
	}
	});
})
</script>
</head>
<body>

<?php #include "header.php"; ?>


<div id="bg-window"></div>
	<!--<div class="container"> -->

	<div style="padding: 15px; background: #fff; max-width: 400px; min-height: 450px;" class="container">
	<div class="row">
	<div class="col-sm-12">
		<div id="login-logo">
			<img id="" src="img/logo4.svg"  style="width: 23%; max-width:70px; max-height: 70px;"/>
			<img id="" src="img/logo.svg"  style="width:75%; max-width:250px; max-height: 70px;"/>
		</div></br>
	</div>
	</div>
	<div class="row">
	<div class="col-sm-12">
	<div id="show-alert" class="row" style="display:none;">
		<div id="error" style="margin: 15px;" class="alert alert-danger login-message"></div>
	</div>

	<div id="loader" class="row" style="display:none;">
	  <div style="margin: 15px;" class="alert alert-info login-message">Please wait ...</div>
	</div>

	<div id="submit" class="row" style="display:none;">
	  <div id="success" style="margin: 15px;" class="alert alert-success login-message"></div>
	</div>
</div>
</div>
	<div id="hide-form" class="row">
		<div class="col-sm-12">
	<form id="password-reset" class="form-horizontal">
	<fieldset>

	<div class="col-sm-12" style="text-align:center;">
	<legend>Reset Password</legend></div>

	<!-- Text input-->
	<div class="form-group submit-btn">
	  <label class="col-sm-4 control-label" for="email">Password</label>
	  <div class="col-sm-7">
	  <input id="pass" name="pass" type="password" placeholder="New Password" class="form-control input-md" required="">

	  </div>
	</div>

	<!-- Text input-->
	<div class="form-group submit-btn">
	  <label class="col-sm-4 control-label" for="email">Confirm Password</label>
	  <div class="col-sm-7">
	  <input id="cnf-pass" name="cnfpass" type="password" placeholder="Confirm New Password" class="form-control input-md" required="">
	  <input class="input-class" id="pafpkey" type="hidden" name="fpkey" value="<?php echo $fpkey?>" />
	  <input class="input-class" id="email" type="hidden" name="mail" value="<?php echo $email?>" />
	  </div> 
	</div> 

	<!-- Button -->
	<div class="form-group submit-btn" style="text-align: center;">
	  <label class="col-md-4 control-label" for="reset-pass"></label>
	  <div class="col-md-4">
	    <button id="reset-pass" name="reset-pass" class="btn btn-primary">Reset Password</button>
	  </div>
	</div>

	</fieldset>
	</form>
</div>
	</div>
	</div>



<?php #include "footer.php" ?>

</body>
</html>
