<?php
session_start();
if(isset($_SESSION['logged_in']) and $_SESSION['logged_in'])	{
	$logged_in=1;

include "../../connectDb.php";
include "../functions/get_time.php";
include "../functions/get_time_offset.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Science Market - Relevant questions</title>
<meta name="description" content="Explore list of questions relevant to you. You choice of questions. Questions that interests you. discuss and explore answers to each questions">
<link rel="stylesheet" type="text/css" href="../../styles/header.css">
<link rel="stylesheet" type="text/css" href="../../styles/dashboard.css">
<link rel="stylesheet" type="text/css" href="../../styles/qa_forum.css">
<link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../../styles/footer.css">
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../../styles/bootstrap.min.css">
<!-- Latest compiled JavaScript -->
<script src="../../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../../js/qa_forum.js"></script>
<!--<script type="text/javascript" src="js/posts_vote.js"></script>-->
<script type="text/javascript" src="../../js/header.js"></script></head>
<body>
<div id="block"></div>
<?php include "../../header.php"; ?>
	</br>
	<div class="container">
			<?php 
			if($logged_in == 1)
				include "../common_code.php"; 
			else
				include "../common_code_guest.php"; 
			?>
			<div class="col-sm-10">

				<div id="qstn-res">
				
				<?php
					try	{

					$query_string="";
					$sql_fetch_user_interests="select b.tag_name 
									   from user_tags a
									   inner join tags b
									   on a.tag_id=b.tag_id
									   where a.user_id='".$_SESSION['user']."'";
					foreach($conn->query($sql_fetch_user_interests) as $result_user_interest)	{
						$query_string=$query_string.$result_user_interest['tag_name']." ";
					}
					$query_string=substr($query_string,0,strlen($query_string)-1);
					
					$sql="select    x.qstn_id
	                               ,x.qstn_titl
                                   ,x.qstn_desc
	                               ,x.posted_by
	                               ,x.up_votes
	                               ,x.down_votes
	                               ,x.topic_id
	                               ,x.created_ts
	                               ,x.parent_group_id
	                               ,x.user_group_nm
                                   ,x.group_nm
	                               ,x.subgroups

                            from 
                            (select a.qstn_id
	                               ,a.qstn_titl
                                   ,a.qstn_desc
	                               ,a.posted_by
	                               ,a.up_votes
	                               ,a.down_votes
	                               ,a.topic_id
	                               ,a.created_ts
	                               ,b.parent_group_id
	                               ,e.group_nm as user_group_nm
                                   ,d.group_nm as group_nm
	                               ,group_concat(distinct f.group_nm order by f.group_nm asc separator ', ') as subgroups 
                             from questions a 
                             inner join group_posts b 
                             on a.qstn_id = b.post_id 
                             inner join group_mbr c on 
                             c.group_id=b.parent_group_id 
                             and c.user_id = '".$_SESSION['user']."' 
                             left outer join groups d 
                             on d.group_id = b.parent_group_id 
                             left outer join groups e 
                             on e.group_id = c.subgroup_id 
                             and e.subgroup_ind = 'Y'
                             left outer join groups f
                             on f.group_id = b.group_id
                             and e.subgroup_ind = 'Y'
                             where a.posted_by<>'".$_SESSION['user']."'
							 and match(a.qstn_titl,a.qstn_desc) against ('".$query_string."' in NATURAL LANGUAGE MODE)
                             
                             group by a.qstn_id
	                                 ,a.qstn_titl
                                     ,a.qstn_desc
	                                 ,a.posted_by
	                                 ,a.up_votes
	                                 ,a.down_votes
	                                 ,a.topic_id
	                                 ,a.created_ts 
		                             ,b.parent_group_id
		                             ,e.group_nm
		                             ,d.group_nm
                              ) as x 
                            where (x.subgroups is NULL or x.subgroups like binary concat(concat('%',x.user_group_nm),'%'))  
                            order by x.created_ts desc limit 10";
					
						include "../fetch_answers1.php";
						if($stmt->rowCount() <=0)	{
								echo '<div class="alert alert-info">
									  <strong>Oops!!</strong> We can\'t find any relevant questions for you. Please add some interests so that we can display questions which might be relevant to you. <strong><a href="profile.php">Click here</a></strong>
								  </div>';
							}
						$qstn_array=array();
						$sql_fetch_all_qstn = "select x.qstn_id
							   from
								 (
								select 
									a.qstn_id,
                                    a.created_ts,
									e.group_nm as user_group_nm,
                                    group_concat(distinct f.group_nm order by f.group_nm asc separator ', ') as subgroups

                                    from questions a 
                                    inner join group_posts b 
                                    on a.qstn_id = b.post_id 
                                    inner join group_mbr c on 
                                    c.group_id=b.parent_group_id 
                                    and c.user_id = '".$_SESSION['user']."'
                                    left outer join groups e 
                                    on e.group_id = c.subgroup_id 
                                    and e.subgroup_ind = 'Y'
                                    left outer join groups f
                                    on f.group_id = b.group_id
                                    and e.subgroup_ind = 'Y'
                                    where a.posted_by<>'".$_SESSION['user']."'
							        and match(a.qstn_titl,a.qstn_desc) against ('".$query_string."' in NATURAL LANGUAGE MODE)
				
                                    group by a.qstn_id
                                            ,a.created_ts
		                                    ,e.group_nm
                                   
                            order by a.created_ts desc
					                ) as x
			                where (x.subgroups is NULL or x.subgroups like binary concat(concat('%',x.user_group_nm),'%'))  
                            order by x.created_ts desc";

						foreach($conn->query($sql_fetch_all_qstn) as $row_qid)	{
							$row_qstn_id=$row_qid['qstn_id'];
							array_push($qstn_array,$row_qstn_id);
						}
						$qstn_arr_str=implode("|",$qstn_array);
				}
				catch(PDOException	$e)	{
					echo 'Error fetching Question';
				}
				?>
			</div>
			<div id="scroll-msg">
				<div id="btn-section">
						<button id="explore-btn" class="btn btn-primary" onclick="fetchMoreQuestions()">Explore more</button>
					</div>
			</div>
			</div>
		</div>
	</div>
	<input id="qid-array-list" type="hidden" value="<?php echo $qstn_arr_str; ?>" />
	<input id="page-locate-data" type="hidden" value="<?php echo $slashes; ?>" />
	<input id="scroll-flag" type="hidden" value="1" />
	<?php include "../../footer.php"; ?>
</body>
</html>
<?php	}
else	{
	$logged_in=0;
	header("location:../../index.php");
}
	?>
