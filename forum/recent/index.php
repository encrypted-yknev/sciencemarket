<?php
session_start();
if(isset($_SESSION['logged_in']) and $_SESSION['logged_in'])	{
	$logged_in=1;
}
else	{
	$logged_in=0;
}
include "../../connectDb.php";
include "../functions/get_time.php";
include "../functions/get_time_offset.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Science Market - Recently posted questions</title>
<meta name="description" content="Recently posted questions alongwith answers. Forum with recent questions and answers.">
<link rel="stylesheet" type="text/css" href="../../styles/header.css">
<link rel="stylesheet" type="text/css" href="../../styles/dashboard.css">
<link rel="stylesheet" type="text/css" href="../../styles/qa_forum.css">
<link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../../styles/footer.css">
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../../styles/bootstrap.min.css">

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
					$sql="select   t.qstn_id
	                              ,t.qstn_titl
                                  ,t.qstn_desc
                                  ,t.posted_by
	                              ,t.up_votes
                                  ,t.down_votes
                                  ,t.topic_id
                                  ,t.created_ts
                                  ,t.parent_group_id
                                  ,t.group_nm
                                  ,t.subgroups

                            from (
                            select a.qstn_id
	                              ,a.qstn_titl
                                  ,a.qstn_desc
                                  ,a.posted_by
                                  ,a.up_votes
                                  ,a.down_votes
                                  ,a.topic_id
                                  ,a.created_ts
	                              ,a1.parent_group_id
                                  ,g.group_nm
                                  ,group_concat(distinct k.user_id order by k.user_id asc separator ' ') as user_ids
                                  ,group_concat(distinct h.group_nm order by h.group_nm asc separator ',') as subgroups
                            from questions a 
                            inner join group_posts a1 
                            on a1.post_id = a.qstn_id 
                            left outer join group_mbr k
                            on k.subgroup_id = a1.group_id
                            and k.user_id = '".$_SESSION['user']."'
                            inner join groups g 
                            on g.group_id = a1.parent_group_id
                            left outer join groups h
                            on h.group_id = a1.group_id 
                            inner join qstn_tags b 
                            on a.qstn_id=b.qstn_id 
                            inner join tags c 
                            on b.tag_id=c.tag_id 
                            where a.posted_by <> '".$_SESSION['user']."' 

                            group by a.qstn_id
		                            ,a.qstn_titl
                                    ,a.qstn_desc
                                    ,a.posted_by
                                    ,a.up_votes
                                    ,a.down_votes
		                            ,a.topic_id
                                    ,a.created_ts
                                    ,a1.parent_group_id
                                    ,g.group_nm 
                                   
                            order by a.created_ts desc
                            ) t 
                            where (t.parent_group_id = 0 or t.user_ids is not null) limit 10"; 
						echo $sql;
					include "../fetch_answers1.php";
					if($stmt->rowCount() <=0)	{
						echo '<div class="alert alert-info">
								<strong>Oops!!</strong> No questions to show at this moment
						  </div>';
					}
					$qstn_array=array();
					$sql_fetch_all_qstn = "select   t.qstn_id

                                            from (
                                            select a.qstn_id
	                                              ,a.qstn_titl
                                                  ,a.qstn_desc
                                                  ,a.posted_by
                                                  ,a.up_votes
                                                  ,a.down_votes
                                                  ,a.topic_id
                                                  ,a.created_ts
	                                              ,a1.parent_group_id
                                                  ,g.group_nm
                                                  ,group_concat(distinct k.user_id order by k.user_id asc separator ' ') as user_ids
                                                  ,group_concat(distinct h.group_nm order by h.group_nm asc separator ',') as subgroups
                                            from questions a 
                                            inner join group_posts a1 
                                            on a1.post_id = a.qstn_id 
                                            left outer join group_mbr k
                                            on k.subgroup_id = a1.group_id
                                            and k.user_id = '".$_SESSION['user']."'
                                            inner join groups g 
                                            on g.group_id = a1.parent_group_id
                                            left outer join groups h
                                            on h.group_id = a1.group_id 
                                            inner join qstn_tags b 
                                            on a.qstn_id=b.qstn_id 
                                            inner join tags c 
                                            on b.tag_id=c.tag_id 
                                            where a.posted_by <> '".$_SESSION['user']."' 

                                            group by a.qstn_id
		                                            ,a.qstn_titl
                                                    ,a.qstn_desc
                                                    ,a.posted_by
                                                    ,a.up_votes
                                                    ,a.down_votes
		                                            ,a.topic_id
                                                    ,a.created_ts
                                                    ,a1.parent_group_id
                                                    ,g.group_nm 
                                                   
                                            order by a.created_ts desc
                                            ) t 
                                            where (t.parent_group_id = 0 or t.user_ids is not null)	
										";
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
