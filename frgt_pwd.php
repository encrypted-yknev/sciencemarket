<!DOCTYPE html>
<html lang="en">
<head>
<title>Science Market - Password Reset Page</title>
<meta name="description" content="Science Market is an online market place to connect with peers, people, groups or expert. Discuss topics in question answer forum, connect with experts under expert connect, collaborate with people and provide favours." >
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="styles/header.css">
<link rel="stylesheet" type="text/css" href="/styles/login.css" >
<link rel="stylesheet" type="text/css" href="/styles/pwd_reset.css" >
<link rel="stylesheet" type="text/css" href="styles/footer.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="js/login.js"></script>
<link rel="stylesheet" href="styles/bootstrap.min.css">
<script src="js/bootstrap.min.js"></script>
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<script type="text/javascript">
$(document).ready(function(){
	$("#reset-pass").click(function(e){
		e.preventDefault();
		$("#show-alert").hide();
		$("#loader").show();
		$(this).addClass("disabled");
		var email = $("#email").val();
		var dataArray = {"email" : email};
		//console.log(email);
		$.ajax({
			url:"/forgotpasshandler.php",
			method: "POST",
			data: dataArray,
			dataType:"text",
			success: function(data){
				if(data=="1"){
				$("#loader").hide();
				$("#show-alert").hide();
				$("#success").html("Password reset link sent to your email");
				$("#submit").show();
				}

				else{
					$("#loader").hide();
					$("#error").html(data);
					$("#show-alert").show();
					$("#reset-pass").removeClass("disabled");
				}

			}

		})
	});
})
</script>
</head>
<body>


<div id="bg-window"></div>
	<!--<div class="container"> -->
<div class="container main-box">
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
			
		</div>
	</div>

	<div id="hide-form" class="row">
		<div class="col-sm-12">
			<form class="form-horizontal">
				<fieldset>
					<div class="col-sm-12" style="text-align:center;">
						<legend>Forgot Password</legend>
					</div>
					<!-- Text input-->
					<div class="col-sm-12">
						<div id="show-alert" class="row" style="display:none;">
							<div id="error" class="alert alert-danger login-message"></div>
						</div>

						<div id="loader" class="row" style="display:none;">
						  <div style="margin: 15px;" class="alert alert-info login-message">Please wait...</div>
						</div>

						<div id="submit" class="row" style="display:none;">
						  <div id="success" style="margin: 15px;" class="alert alert-success login-message"></div>
						</div>
					</div>
					<div class="form-group submit-btn">
					  <label class="col-sm-4 control-label" for="email"> Email</label>
					  <div class="col-sm-7">
					  <input class="form-control" id="email" name="email" type="text" placeholder="john@gmail.com" class="form-control input-md" required="false">

					  </div>
					</div>
					<!-- Button -->
					
				</fieldset></br>
				<div class="submit-btn" style="text-align:center;">
					<button id="reset-pass" name="reset-pass" class="btn btn-primary" style="margin:0 5px;">Reset Password</button>
					<a class="btn btn-primary" href="../" style="margin:0 5px;">Back</a>
				</div>
			</form>
		</div>
	</div>
</div>
	<!--</div>-->

<?php #include "footer.php" ?>

</body>
</html>
