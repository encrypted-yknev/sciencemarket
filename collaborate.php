<?php
session_start();
if(!$_SESSION["logged_in"])	{
	header("location:index.php");
}
include "connectDb.php";
include "forum/functions/get_time.php";
include "forum/functions/get_time_offset.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Science Market - Collaboration</title>
<meta name="description" content="Science market. Collaboration and co-authorship. Propose new ideas and share authorship with the skillsets you pursue." >
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="styles/header.css">
<link rel="stylesheet" type="text/css" href="styles/dashboard.css">
<link rel="stylesheet" type="text/css" href="styles/collaborate.css">
<link rel="stylesheet" type="text/css" href="styles/footer.css">
<link href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700,800" rel="stylesheet" type="text/css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="styles/bootstrap.min.css">
<!-- Latest compiled JavaScript -->
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/header.js"></script></head>
<script type="text/javascript" src="js/collaborate.js"></script></head>
<body onload="refreshNotify()">
<div id="block"></div>
<?php include "header.php"; ?>
	
	<div class="container" style="min-height:500px;">
		<div id="side-nav">
			<table border="0">
				<tr>
					<td>
						<div id="nav-id">
							<div class="side-bar"></div>
							<div class="side-bar"></div>
							<div class="side-bar"></div>
						</div>
					</td>
					<td>
						<div id="media-image"><img src="img/logo.jpg" width="200" height="50"/></div>
					</td>
				</tr>
			</table></br>
			<div id="page-title"><span>Collaborate</span></div></br>
		</div>
		<div id="options-menu">
			<div class="row">
				<div class="col-sm-12" id="pic-row">
					<img src="<?php echo $_SESSION["pro_img"]; ?>" id="side-menu-img" alt="profile image" width="100" height="120"> 
				</div>
			</div></br>

			<div>upvotes   : <span class="badge"><?php echo $_SESSION["up_votes"]; ?></span></div>
			<div>downvotes : <span class="badge"><?php echo $_SESSION["down_votes"]; ?></span></div>
					
			</br>
			<ul class="nav nav-pills nav-stacked">
				<li><a href="profile.php" ><span class="glyphicon glyphicon-user" ></span>&nbsp;My Profile</a></li>
				<li><a href="dashboard.php"><span class="glyphicon glyphicon-home" ></span>&nbsp;Dashboard</a></li>
				<li><a href="forum" ><span class="glyphicon glyphicon-question-sign"></span>&nbsp;Q/A Forum</a></li>
				<li><a href="expert_connect.php" ><span class="glyphicon glyphicon-transfer" ></span>&nbsp;Expert Connect</a></li>
				<li><a href="#" ><span class="glyphicon glyphicon-refresh" ></span>&nbsp;Collaborate</a></li>
				<li><a href="" ><span class="glyphicon glyphicon-gift" ></span>&nbsp;Favours</a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-off"></span>&nbsp;Logout</a></li>
			</ul>
		</div>
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-10">
				<div id="head-bottom"></div>
				
			</div>
		</div></br>
		<div class="head-section">
			<h2 class="main-title">Collaborate</h2>
		</div></br>
		<div id="main-body">
			<table class="table" >
				<tbody>
					<tr>
						<td>
							<div class="col-section div-section">
								<span class="div-text text1"><strong>Collaborations</strong></span>								
							</div>
						</td>
						<td>
							<div class="auth-section div-section">
								<span class="div-text text2"><strong>Authorship</strong></span>								
							</div>
						</td>
					</tr>
                    <tr>
                        <td>
                            <div class="sub-text">
								<span>Are you coming up with new idea to experiment things? Here's your chance to request for assistance</span>
							</div>
                        </td>
                        <td>
                            <div class="sub-text">
								<span>Want to share authorship in research papers? Check out authorship proposals</span>
							</div>
                        </td>
                    </tr>
				</tbody>
			</table>
		</div>
		<div id="main-cont" class="row" style="display:none;">
			<div class="col-sm-2" style="background-color:#ddd;" >
				<div class="col-section-side" style="margin-top:15px;">
					<span class="text1"><strong>Collaborations</strong></span>
				</div></br>
				<div class="auth-section-side">
					<span class="text2"><strong>Authorship</strong></span>
				</div></br>
			</div>
			<div class="col-sm-10" style="background-color:#f9f9f9; height:auto; padding:0px !important" >
				<div class="right-head">
					<span class="head-nav1"></span>
					<span class="head-nav2"></span>
				</div></br>
				<div id="col-cards">
					<h2>This section will contain list of collaboration job cards</h2>
				</div>
				<div id="auth-cards">
					<h2>This section will contain list of authorship job cards</h2>
				</div>
				<div id="col-form">
					<div class="form-group" style="width:80%; margin-left:20px;">
						<h3>Post a new idea</h3>
						<hr>
						<div class="form-group">
							<label for="title" class="form-labels">One line description (Title)</label>
							<input class="form-control" id="title" type="text" name="title" placeholder="Title" value="" />
						</div>
						<div class="form-group">
							<label for="summary" class="form-labels">Summary</label>
							<textarea class="form-control" id="summary" type="text" name="summary" placeholder="Enter brief idea of project" value="" ></textarea>
						</div>
						<div class="form-group">
							<label for="skills" class="form-labels">What skillsets you bring to the table?</label>
							<input class="form-control" id="skills" type="text" name="skills" placeholder="Enter skillsets" value="" />
						</div>
						<div class="form-group">
							<label for="users" class="form-labels">Other collaborators</label>
							<input class="form-control" id="users" type="text" name="users" placeholder="Enter other collaboraters" value="" />
						</div>
						<div class="form-group">
							<label for="univ" class="form-labels">University</label>
							<input class="form-control" id="univ" type="text" name="univ" placeholder="Enter University name" value="" />
						</div>
						<div class="form-group">
							<label for="loc" class="form-labels">Location</label>
							<input class="form-control" id="loc" type="text" name="loc" placeholder="Enter Location" value="" />
						</div>
						<div class="form-group">
							<label for="dt1" class="form-labels">Estimated start date</label>
							<input class="form-control" id="dt1" type="date" name="dt1" placeholder="Enter estimated start date" value="" />
						</div>
						<div class="form-group">
							<label for="dt2" class="form-labels">Estimated end date</label>
							<input class="form-control" id="dt2" type="date" name="dt2" placeholder="Enter estimated end date" value="" />
						</div>
						<div class="form-group">
							<label for="skills2" class="form-labels">Required skillsets</label>
							<input class="form-control" id="skills2" type="text" name="skills2" placeholder="Enter required skillsets" value="" />
						</div>
						</br>
						<div id="message-col"></div>
						<button class="btn btn-primary" onclick="postCollaboration(1)">Post</button>
					</div>
				</div>
				<div id="auth-form">
					<div class="form-group" style="width:80%; margin-left:20px;">
						<h3>Propose authorship</h3>
						<hr>
						<div class="form-group">
							<label for="title1" class="form-labels">One line description of project</label>
							<input class="form-control" id="title1" type="text" name="title1" placeholder="Title" value="" />
						</div>
						<div class="form-group">
							<label for="summary1" class="form-labels">Summary</label>
							<textarea class="form-control" id="summary1" type="text" name="summary1" placeholder="Enter brief idea of project" value="" ></textarea>
						</div>
						<div class="form-group">
							<label for="stage" class="form-labels">Stage of completion</label>
							<input class="form-control" id="stage" type="text" name="stage" placeholder="Enter stage of completion" value="" />
						</div>
						<div class="form-group">
							<label for="users1" class="form-labels">Current collaborators</label>
							<input class="form-control" id="users1" type="text" name="users1" placeholder="Enter current collaboraters" value="" />
						</div>
						<div class="form-group">
							<label for="desc" class="form-labels">Brief description of the job</label>
							<input class="form-control" id="desc" type="text" name="desc" placeholder="Enter brief description" value="" />
						</div>
						<div class="form-group">
							<label for="authid" class="form-labels">Authorship ID</label>
							<input class="form-control" id="authid" type="text" name="authid" placeholder="Enter Authorship ID" value="" />
						</div>
						<div class="form-group">
							<label for="univ1" class="form-labels">University</label>
							<input class="form-control" id="univ1" type="text" name="univ1" placeholder="Enter University name" value="" />
						</div>
						<div class="form-group">
							<label for="loc1" class="form-labels">Location</label>
							<input class="form-control" id="loc1" type="text" name="loc1" placeholder="Enter Location" value="" />
						</div>
						<div class="form-group">
							<label for="strtdt1" class="form-labels">Estimated start date</label>
							<input class="form-control" id="strtdt1" type="date" name="strtdt1" placeholder="Enter estimated start date" value="" />
						</div>
						<div class="form-group">
							<label for="enddt2" class="form-labels">Estimated end date</label>
							<input class="form-control" id="enddt2" type="date" name="enddt2" placeholder="Enter estimated end date" value="" />
						</div>
						<div class="form-group">
							<label for="skills21" class="form-labels">Required skillsets</label>
							<input class="form-control" id="skills21" type="text" name="skills2" placeholder="Enter required skillsets" value="" />
						</div>
						</br>
						<div id="message-auth"></div>
						<button class="btn btn-primary" onclick="postCollaboration(2)">Post</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php include "footer.php"; ?>
</body>
</html>
