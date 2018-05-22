<?php
session_start();
if(isset($_SESSION['logged_in']) and $_SESSION['logged_in'])
	$logged_in=1;
else
	$logged_in=0;

include "connectDb.php";
$message="";
$q_topic_id=$q_titl=$q_desc=$topic_id="";
if ($_SERVER["REQUEST_METHOD"] == "POST")	{
	$q_topic_id=(int)htmlspecialchars(stripslashes(trim($_POST['sub_topic'])));
	$q_titl=htmlspecialchars(stripslashes(trim($_POST['qtitl'])));
	$q_desc=trim($_POST['qdesc']);
	$tags=htmlspecialchars(stripslashes(trim($_POST['tags'])));
	#check for empty field
	$group_id=(int)trim($_POST['qgroups']);
	$subgroup_list=$_POST['subgroups'];
	$request_typ=0;
	if(!empty($q_topic_id) && !empty($q_titl) && !empty($q_desc) && !empty($tags) && !empty($group_id))	{
		if(($group_id > 0 && !empty($subgroup_list)) or ($group_id == 0))	{

			if($group_id == 0)	{
				$subgroups = "";
			}
			else	{
				$len=count($subgroup_list);
				$final_subgroup_list=$subgroup_list[0];
				for($i=1; $i<$len; $i++)	{
					$final_subgroup_list.=", ".$subgroup_list[$i];
				}
				$subgroups = $final_subgroup_list;
			}
			try		{
				
				$sql_call_sp_post_qstn="call post_question(:user_id,
														:request_typ,
														:qstn_title,
														:qstn_desc,
														:qstn_topic_id,
														:qstn_tags,
														:group_id,
														:subgroup_id_list,
														@err_cd,
														@err_desc)";

				
				$stmt_call_sp_post_qstn=$conn->prepare($sql_call_sp_post_qstn);
				$stmt_call_sp_post_qstn->bindParam(':user_id',$_SESSION['user'],PDO::PARAM_STR, 50);			
				$stmt_call_sp_post_qstn->bindParam(':request_typ',$request_typ,PDO::PARAM_INT);				
				$stmt_call_sp_post_qstn->bindParam(':qstn_title',$q_titl,PDO::PARAM_STR, 3000); 
				$stmt_call_sp_post_qstn->bindParam(':qstn_desc',$q_desc,PDO::PARAM_STR, 20000); 
				$stmt_call_sp_post_qstn->bindParam(':qstn_topic_id',$q_topic_id,PDO::PARAM_INT); 
				$stmt_call_sp_post_qstn->bindParam(':qstn_tags',$tags,PDO::PARAM_STR, 2000); 
				$stmt_call_sp_post_qstn->bindParam(':group_id',$group_id,PDO::PARAM_INT);
				$stmt_call_sp_post_qstn->bindParam(':subgroup_id_list',$subgroups,PDO::PARAM_STR, 20);
				
				$stmt_call_sp_post_qstn->execute();
				$stmt_call_sp_post_qstn->closeCursor();
				$row_sp = $conn->query("select @err_cd as error_code,@err_desc as error_desc")->fetch();
				
				$error_code=$row_sp['error_code'];
				$error_desc=$row_sp['error_desc'];
				if(!strcmp($error_code,'00000'))	{
					$message = '<div id="no-qstn-msg-section" class="alert alert-success">Question posted</div>';
					header("location:forum/myposts");
				}
				else	{
					$message = '<div id="no-qstn-msg-section" class="alert alert-danger">'.$error_desc.'</div>';
				} 
			}
			catch(PDOException $e)	{
				$message = $subgroups.' <div id="no-qstn-msg-section" class="alert alert-danger">Internal server error. Please try again later - '.$e->getMessage().'</div>';
			}
		}
		else	{
			$message = '<div id="no-qstn-msg-section" class="alert alert-danger">Please select subgroups</div>';
		}
	}
	else {
		$message = '<div id="no-qstn-msg-section" class="alert alert-danger">Please enter mandatory fields</div>';
	}
}
	
	function get_time_diff($timestamp_ans)	{
#	date_default_timezone_set("Asia/Kolkata");

	$timestamp_cur=date("Y-m-d H:i:sa");
	/* echo $timestamp_ans."</br>"; 
	echo $timestamp_cur; */
	
	$year1=substr($timestamp_ans,0,4);
	$month1=substr($timestamp_ans,5,2);
	$day1=substr($timestamp_ans,8,2);
	$hr1=substr($timestamp_ans,11,2);
	$min1=substr($timestamp_ans,14,2);
	$sec1=substr($timestamp_ans,17,2);


	$year2=substr($timestamp_cur,0,4);
	$month2=substr($timestamp_cur,5,2);
	$day2=substr($timestamp_cur,8,2);
	$hr2=substr($timestamp_cur,11,2);
	$min2=substr($timestamp_cur,14,2);
	$sec2=substr($timestamp_cur,17,2);

	if($year1 == $year2)	{
		if($month1 == $month2)	{
			if($day1 == $day2)	{
				if($hr1 == $hr2)	{
					if($min1 == $min2)	{
						if($sec1 == $sec2)	{
							$value=0;	
							$string="seconds";
						}
						else{
							$diff_sec=(int)$sec2-(int)$sec1;
							$value=$diff_sec;	
							$string="seconds";
						}
					}
					else{
						$diff_min=(int)$min2-(int)$min1;
						$value=$diff_min;
						$string="minutes";
					}
				}
				else{
					$diff_hr=(int)$hr2-(int)$hr1;
					$value=$diff_hr;
					$string="hours";
				}
			}
			else	{
				$diff_day=(int)$day2-(int)$day1;
				$value=$diff_day;
				$string="days";
			}
		}
		else	{
			$diff_mon=(int)$month2-(int)$month1;
			$value=$diff_mon;
			$string="months";
		}
	}
	if($value==1)
		$string=substr($string,0,strlen($string)-1);
	return $value.' '.$string.' ago';
}

function get_group_list($subgroup_char) {
    if($subgroup_char=='A' or $subgroup_char=='F') {
        return "";
    }
    else    {
        
        if($subgroup_char=='U')
            $group_id_inp=5;
        else if($subgroup_char=='G')
            $group_id_inp=4;
        else if($subgroup_char=='P')
            $group_id_inp=6;    
        try {
            $sql_fetch_group_id="select ExtractValue(subgroup_confg,'//groups/group_id') as 'group_ids' from groups where subgroup_ind='Y' and group_id=".$group_id_inp;
            $stmt=$conn->prepare($sql_fetch_group_id);
            $stmt->execute();
            
            while($result=$stmt->fetch())   {
                $group_id_spaces = $row_group_id["group_ids"];
            } 
            $group_id_comma=str_replace(" ",",",$group_id_spaces);
        }
        catch(PDOException $e)  {
            return "";
        }
        return $group_id_comma;
    }
}
?>


<!DOCTYPE html>
<html>
<head>
<title>Science Market - Question & Answer forum. Post, discuss & comment</title>
<meta charset="utf-8">
<meta name="description" content="Science Market. Having doubts? ask and post questions. discuss. forums. answer questions. comment on users posts. Clear your doubts. online portal to discuss on different topics">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="styles/header.css">
<link rel="stylesheet" type="text/css" href="/styles/qstn.css" >
<link rel="stylesheet" type="text/css" href="styles/footer.css">
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>

<link rel="stylesheet" href="styles/bootstrap.min.css">
<script src="js/bootstrap.min.js"></script>
<script src="ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="js/qstn.js"></script>
<script type="text/javascript" src="js/header.js"></script>
<!-- start 

<script src="//cdnjs.cloudflare.com/ajax/libs/pagedown/1.0/Markdown.Converter.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pagedown/1.0/Markdown.Editor.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pagedown/1.0/Markdown.Sanitizer.js"></script>

<link rel="stylesheet" 
      href="//cdn.rawgit.com/balpha/pagedown/master/demo/browser/demo.css" />

<style>
    .wmd-button > span {
        background-image: 
          url('//cdn.rawgit.com/derobins/wmd/master/images/wmd-buttons.png');
        background-repeat: no-repeat;
        background-position: 0px 0px;
        width: 20px;
        height: 20px;
        display: inline-block;
    }
</style>
 end -->

</head>
<body onload="setTimeout(validateUser(<?php echo $logged_in; ?>),3000)">
<div id="block"></div>
<?php include "header.php"; ?>
<div id="block-container"></div>

<div class="container">
	<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" onclick="window.location='index.php'">&times;</button>
          <h4 class="modal-title">Alert</h4>
        </div>
        <div class="modal-body">
          <p><strong>Ooops!! You can't post question as a guest user. You will be redirected to the login page soon or after you click on Close</strong></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="window.location='index.php'">Close</button>
        </div>
      </div>
      
    </div>
  </div>
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
					<div id="media-image">
						<img src="img/logo4.svg" width="55" height="55"/>
						<img src="img/logo.svg" width="150" height="50"/>
					</div>
				</td>
			</tr>
		</table></br>
		<div id="page-title"><span>Ask Question</span></div></br>
		<div class="row">
			<div class="col-sm-3">
				<!--
				<div id="row-1">
					<a href="qstn.php" class="btn btn-info">Ask Questions</a>
				</div> -->
			</div>
			<div class="col-sm-6">
				<div id="row-2">
					<input type="text" class="form-control" id="srch-box-media" placeholder="Search questions" />
				</div>
			</div>
		</div>
	</div>
	<div id="options-menu">
		<div id="proimg">
			<img id="propic" src="<?php echo $_SESSION['pro_img']; ?>" />
		</div></br></br>
		<ul class="nav nav-pills nav-stacked">
			<li><a href="profile.php" ><span class="glyphicon glyphicon-user" ></span>&nbsp;My Profile</a></li>
			<li><a href="dashboard.php"><span class="glyphicon glyphicon-home" ></span>&nbsp;Dashboard</a></li>
			<li><a href="forum" ><span class="glyphicon glyphicon-question-sign"></span>&nbsp;Q/A Forum</a></li>
			<li><a href="" ><span class="glyphicon glyphicon-transfer" ></span>&nbsp;Expert Connect</a></li>
			<li><a href="" ><span class="glyphicon glyphicon-refresh" ></span>&nbsp;Collaborate</a></li>
			<li><a href="" ><span class="glyphicon glyphicon-gift" ></span>&nbsp;Favours</a></li>
			<li><a href="logout.php"><span class="glyphicon glyphicon-off"></span>&nbsp;Logout</a></li>
		</ul>
	</div>
	</br>
	<div class="row">
		<div id="ask-qstn" class="col-sm-6">
			<h4>Clear your doubts. Ask questions</h4>
			<form id="qstn-form" name="ask-qstn-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
				Select topic:
				<select class="form-control inp-box" id="q-topic" style="width:50%;" name="qtopic" onchange="getSubTopics(this.value)" onfocus="getInputInfo(1)">
					<option value="" selected>--- Select topic ---</option>
					<?php
						try	{	
							$sql="select topic_code,topic_desc,topic_id from topics where parent_topic=0";
							foreach($conn->query($sql) as $row)	{
								$row_topic_id=$row["topic_id"];
								$row_topic_desc=$row["topic_desc"];
								echo '<option value="'.$row_topic_id.'">'.$row_topic_desc.'</option>';
							}
						}
						catch(PDOException $e)	{
							
						}
					?>
				</select></br>
				Select sub-topic:
					
				<select class="inp-box form-control" style="width:50%;" id="q-sub-topic" value="<?php echo $q_topic; ?>" name="sub_topic" onfocus="getInputInfo(2)">
					<option value="" selected>--- Select sub-topics ---</option>
				</select></br>
				Question title : <input class="inp-box form-control" style="width:100%;" id="q-titl" type="text" value="<?php echo $q_titl; ?>" name="qtitl" onfocus="getInputInfo(3)"
				onkeyup="showQstnResults(this.value)"/></br>
				
				
				Ask your question : <textarea class="inp-box" id="q-desc" rows="5" cols="50" value="<?php echo $q_desc; ?>" name="qdesc" placeholder="Whats on your mind?"  onfocus="getInputInfo(4)"></textarea>
				<script>
					CKEDITOR.replace('qdesc');
				</script>
				
<!-- 				<!-- start 
				<div id="wmd-button-bar"></div>
				<textarea id="wmd-input" class="wmd-input"></textarea>
				<div id="wmd-preview" class="wmd-panel wmd-preview"></div>
				<script>
					
						var converter = Markdown.getSanitizingConverter();
						var editor = new Markdown.Editor(converter);
						editor.run();
					
				</script>
				
				<!-- end  -->
				<span id="q-msg"></span></br></br>
				Choose at-least 1 tag and max 4 tags : 
				<div id="tag" class="row">
					<div class="col-sm-6">
						<input class="q-tags form-control" id="user-qstn-tags" type="text" name="q_tags" placeholder="Add tags. Press ENTER"  onfocus="getInputInfo(5)"/>
					</div>
					<div class="col-sm-6" id="alert-msg">
					</div>
				</div></br>
				<div id="tag-res"></div></br>
                
                <?php if($_SESSION['subgroup'] <> "")   { ?>
				Choose group where you want to post :
				<select class="form-control" id="q-groups" style="width:50%;" name="qgroups" onchange="getSubgroups(this.value)" onfocus="getInputInfo(6)">
					<?php
						try	{	
							$sql_fetch_groups="select a.group_id,
                                                      a.group_nm 
                                               from groups a
                                               inner join group_mbr b
                                               on a.group_id = b.group_id
                                               where a.subgroup_ind='N'
                                               and b.user_id = '".$_SESSION['user']."'";
							foreach($conn->query($sql_fetch_groups) as $row_groups)	{
								$row_group_id=$row_groups["group_id"];
								$row_group_name=$row_groups["group_nm"];
								echo '<option value="'.$row_group_id.'">'.$row_group_name.'</option>';
							}
						}
						catch(PDOException $e)	{
							
						}
					?>
				</select></br>
				<div id="subgroup-choose-sec">
                Visible to : </br>
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
                            $group_id = $row_group["group_id"];

                            echo "<input type='checkbox' name='subgroups[]' id='' class='subgroup-sec' value='".$group_id."' />&nbsp;&nbsp;<span class='grp-names'>".$group_name."</span></br>";
                        }   
                    }
                    catch(PDOException $e)  {
                        echo "error occurred";
                    }
                 ?>
				 </div></br><?php } ?>
				<p><em>Before submitting, do check out for tips on how to use tags (Message box) by placing your cursor on the tags textbox and some related questions you might be looking for</em></p>
				
				<input type="hidden" id="tags" name="tags" /> 
				<button type="submit" id="ask-qstn-submit" class="btn btn-default" onclick="getTagsName()">Post Question</button>
			</form>
			<span id="result-section"></span>
			</br>
		</div>
		<div class="col-sm-6">
			<h4>Message box</h4>
			<div id="qstn-info">
			<?php 
				if($message != "")
					echo $message;
				else	{
			?>
			<div class="alert alert-info">
			  Place your cursor over each input section for tips and suggestions on how to enter data. What is preferred and what is restricted
			</div>
				<?php } ?>
			</div>
			<h4>Related questions</h4>
			<div id="qstn-list" class="alert alert-success">
			</div>
		</div>
	</div>
</div>

<?php
	include "footer.php";
?>
</body>
</html>
