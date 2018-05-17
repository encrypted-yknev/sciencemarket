<?php
session_start();
if(!$_SESSION["logged_in"])	{
	header("location:index.php");
}
include "connectDb.php";
include "forum/functions/get_time.php";
include "forum/functions/get_time_offset.php";
if(isset($_GET['group']))
    $group_id=$_GET['group'];
else
    $group_id=0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Science Market - Groups</title>
<meta name="description" content="Science market. Groups, manage groups. Post in subgroups interact withing members of the group" >
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="styles/header.css">
<link rel="stylesheet" type="text/css" href="styles/dashboard.css">
<link rel="stylesheet" type="text/css" href="styles/groups.css">
<link rel="stylesheet" type="text/css" href="styles/footer.css">
<link rel="stylesheet" type="text/css" href="styles/qa_forum.css">
<link href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700,800" rel="stylesheet" type="text/css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="styles/bootstrap.min.css">
<!-- Latest compiled JavaScript -->
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/header.js"></script>
<script type="text/javascript" src="js/collaborate.js"></script>
<script type="text/javascript" src="js/groups.js"></script>
<script type="text/javascript" src="js/qa_forum.js"></script>

</head>

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
        <div class="row">
			<div class="col-sm-2" id="main-side-column" style="background:#FBFBFB;">
				<div class="filter-section"></br>
					<span class="menu-title side-title">Filter by</span></br>
					<?php
                            if($_SESSION['subgroup']=='A')  {
                                $group_id_inp=5;
                                $group_list=implode(", ",$_SESSION['subgroups_a']);
                            }
                            else if($_SESSION['subgroup']=='F') {
                                $group_id_inp=5;
                                $group_list=implode(", ",$_SESSION['subgroups_f']);
                            }
                            else if($_SESSION['subgroup']=='U') {
                                $group_id_inp=5;
                                $group_list=implode(", ",$_SESSION['subgroups_u']);
                            }
                            else if($_SESSION['subgroup']=='G') {
                                $group_id_inp=4;
                                $group_list=implode(", ",$_SESSION['subgroups_g']);
                            }                        
                            else if($_SESSION['subgroup']=='P') {
                                $group_id_inp=6;    
                                $group_list=implode(", ",$_SESSION['subgroups_p']);
                            }
                            try {
                                $sql_fetch_group_names = "select * from groups where subgroup_ind = 'Y'  and group_id in (".$group_list.")";
                                echo "<input type='checkbox' name='subgroups[]' id='check-all' class='all-sec' value='".implode(", ",$_SESSION['subgroups_all'])."' />&nbsp;&nbsp;All</br>";
                                foreach($conn->query($sql_fetch_group_names) as $row_group)   {
                                    $group_name = $row_group["group_nm"];
                                    $sub_group_id = $row_group["group_id"];

                                    echo "<input type='checkbox' onchange='fetchGroupPosts(".$group_id.")' name='subgroups[]' id='' class='subgroup-sec' value='".$sub_group_id."' />&nbsp;&nbsp;<span class='grp-names'>".$group_name."</span></br>";
                                }   
                            }
                            catch(PDOException $e)  {
                                echo "error occurred";
                            }
						?>
					
				</div></br>
				<div class="sort-section">
					<span class="menu-title side-title">Sort by</span>
					<select id="sort-id" class="form-control" onchange="fetchGroupPosts(<?php echo $group_id; ?>)">
						<option value="1">Recent</option>
						<option value="2">Most upvoted</option>
						<option value="3">Most viewed</option>
					</select>
				</div></br>
			</div>
			<div class="col-sm-10" id="middle-container">
				<?php
				try	{
					
						$sql="select t.qstn_id,
									 t.qstn_titl,
									 t.qstn_desc,
									 t.posted_by,
									 t.up_votes,
									 t.down_votes,
									 t.topic_id,
									 t.created_ts,
									(case when t.answer_ts >= t.comment_ts then t.answer_ts
											else t.comment_ts
									   end) score	
							from 
							(select  a.qstn_id,
									 a.qstn_titl,
									 a.qstn_desc,
									 a.posted_by,
									 a.up_votes,
									 a.down_votes,
									 a.topic_id,
									 a.created_ts,
									coalesce(max(UNIX_TIMESTAMP(d.created_ts)),0) as answer_ts,
									coalesce(max(UNIX_TIMESTAMP(e.created_ts)),0) as comment_ts
							 from questions a 
                               inner join group_posts a1
                               on a.qstn_id=a1.post_id
                               and a1.parent_group_id=".$group_id."                            
                               and a1.group_id = ".$_SESSION["subgroup_id"]."
							   inner join qstn_tags b
							   on a.qstn_id=b.qstn_id
							   inner join tags c 
							   on b.tag_id=c.tag_id 
							   left outer join answers d 
							   on d.qstn_id = a.qstn_id 
							   left outer join comments e 
							   on e.ans_id = d.ans_id
							   							   
							   group by a.qstn_id 
							   order by a.created_ts desc) t
							   order by score desc";
							   
								include "forum/fetch_answers1.php";
								if($stmt->rowCount() <=0)	{
									echo '<div class="alert alert-info">
										  No questions posted in the given subgroup. Try using different filter
									  </div>';
								}
					
				}
				catch(PDOException	$e)	{
					
				}
				?>
			</div>
    </div>	
</div>	
	<?php include "footer.php"; ?>
</body>
</html>
