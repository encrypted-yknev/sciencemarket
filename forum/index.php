<?php
session_start();

if(isset($_SESSION['logged_in']) and $_SESSION['logged_in'])
	$logged_in=1;
else
	$logged_in=0;

include "../connectDb.php";
include "functions/get_time.php";
include "functions/get_time_offset.php";


?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Science Market - Question and Answer forum</title>
<meta name="description" content="Question and answer forum. Post questions. Answer questions. Discussion forums. clear your doubts. Portal for people to connect and discuss" >
<link rel="stylesheet" type="text/css" href="../styles/header.css">
<link rel="stylesheet" type="text/css" href="../styles/dashboard.css">
<link rel="stylesheet" type="text/css" href="../styles/qa_forum.css">
<link rel="stylesheet" type="text/css" href="../styles/footer.css">
<link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../styles/bootstrap.min.css">
<!-- Latest compiled JavaScript -->
<script src="../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../js/qa_forum.js"></script>
<!--<script type="text/javascript" src="js/posts_vote.js"></script>-->
<script type="text/javascript" src="../js/header.js"></script></head>
<body>
<div id="block"></div>
<?php include "../header.php"; ?>
	</br>
	<div class="container">
			<?php 
			if($logged_in == 1)
				include "common_code.php"; 
			else
				include "common_code_guest.php"; 
			?>
			<div class="col-sm-7">

				<div id="qstn-res">
				
				<?php
					try	{
					
                        if($logged_in == 1)	{
						
                        if(isset($_GET['tag']))	{
							$url_tag_id=$_GET['tag'];
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
                             inner join qstn_tags g
                             on g.qstn_id = a.qstn_id
                             where a.posted_by <> '".$_SESSION['user']."'
                             and g.tag_id = ".$url_tag_id."
                             
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
                            order by x.created_ts desc limit 10" ;
						}
						else	{
					
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
                             
                             where a.posted_by <> '".$_SESSION['user']."'
                             
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
						
						}
					} 
					else	{
						if(isset($_GET['tag']))	{
							$url_tag_id=$_GET['tag'];   
							$sql="select a.qstn_id
	                               ,a.qstn_titl
                                   ,a.qstn_desc
	                               ,a.posted_by
	                               ,a.up_votes
	                               ,a.down_votes
	                               ,a.topic_id
	                               ,a.created_ts
	                               ,b.parent_group_id
                                   ,d.group_nm as group_nm
	                               ,' ' as subgroups 
                             from questions a 
                             inner join group_posts b 
                             on a.qstn_id = b.post_id 
                             and b.parent_group_id = 0
                             inner join groups d 
                             on d.group_id = b.parent_group_id 
                             inner join qstn_tags g
                             on g.qstn_id = a.qstn_id
                             where g.tag_id = ".$url_tag_id." 
                             
                             order by a.created_ts desc limit 10 
                             ";
						}
						else	{
							$sql="select    x.qstn_id
                                   ,x.qstn_titl
                                   ,x.qstn_desc
                                   ,x.posted_by
                                   ,x.up_votes
                                   ,x.down_votes
                                   ,x.topic_id
                                   ,x.created_ts
                                   ,x.parent_group_id
                                   ,x.group_nm
                                   ,x.subgroups
                                   ,(case when x.answer_ts >= x.comment_ts then x.answer_ts else x.comment_ts end) score		
                            from (
                                select  a.qstn_id
                                       ,a.qstn_titl
                                       ,a.qstn_desc
                                       ,a.posted_by
                                       ,a.up_votes
                                       ,a.down_votes
                                       ,a.topic_id
                                       ,a.created_ts 
                                       ,coalesce(max(UNIX_TIMESTAMP(g.created_ts)),0) as answer_ts 
                                       ,coalesce(max(UNIX_TIMESTAMP(h.created_ts)),0) as comment_ts
                                       ,b.parent_group_id
                                       ,d.group_nm as group_nm
                                       ,' ' as subgroups
                                 from questions a 
                                 inner join group_posts b 
                                 on a.qstn_id = b.post_id 
                                 and b.parent_group_id = 0
                                 inner join groups d 
                                 on d.group_id = b.parent_group_id 
                                 left outer join answers g 
                                 on g.qstn_id = a.qstn_id 
                                 left outer join comments h 
                                 on h.ans_id = g.ans_id 
                                 
                                 group by a.qstn_id
	                                 ,a.qstn_titl
                                     ,a.qstn_desc
	                                 ,a.posted_by
	                                 ,a.up_votes
	                                 ,a.down_votes
	                                 ,a.topic_id
	                                 ,a.created_ts 
		                             ,b.parent_group_id
		                             ,d.group_nm
                                ) as x 
                                order by score desc limit 10";
							}
						}
						include "fetch_answers1.php";
						if($stmt->rowCount() <=0)	{
							echo '<div class="alert alert-info">
								  <strong>Oops!!</strong> We can\'t find any relevant questions for you. Please add some interests so that we can display questions which might be relevant to you. <strong><a href="profile.php">Click here</a></strong>
							  </div>';
						}
						$qstn_array=array();
						if($logged_in == 1)	{
							if(isset($_GET['tag']))	{
								$sql_fetch_all_qstn="select    x.qstn_id
                                                              ,x.created_ts
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
                                                         inner join qstn_tags g
                                                         on g.qstn_id = a.qstn_id
                                                         where a.posted_by <> '".$_SESSION['user']."'
                                                         and g.tag_id = ".$url_tag_id."
                                                         
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
                                                        order by x.created_ts desc";
							}
							else	{
							$sql_fetch_all_qstn = "select    x.qstn_id
    	                                                    ,x.created_ts
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
                                                     
                                                     where a.posted_by <> '".$_SESSION['user']."'
                                                     
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
                                                    order by x.created_ts desc";
							}
						}
						else	{
							if(isset($_GET['tag']))	{
								$sql_fetch_all_qstn="select a.qstn_id
	                                                       ,a.created_ts
                                                     from questions a 
                                                     inner join group_posts b 
                                                     on a.qstn_id = b.post_id 
                                                     and b.parent_group_id = 0
                                                     inner join groups d 
                                                     on d.group_id = b.parent_group_id 
                                                     inner join qstn_tags g
                                                     on g.qstn_id = a.qstn_id
                                                     where g.tag_id = ".$url_tag_id."
                                                     
                                                     order by a.created_ts desc";
							}
							else	{
								$sql_fetch_all_qstn = "select    x.qstn_id
                                                                ,x.created_ts
                                                                ,(case when x.answer_ts >= x.comment_ts then x.answer_ts else x.comment_ts end) score		
                                                            from (
                                                            select  a.qstn_id
                                                                   ,a.qstn_titl
                                                                   ,a.qstn_desc
                                                                   ,a.posted_by
                                                                   ,a.up_votes
                                                                   ,a.down_votes
                                                                   ,a.topic_id
                                                                   ,a.created_ts 
                                                                   ,coalesce(max(UNIX_TIMESTAMP(g.created_ts)),0) as answer_ts 
                                                                   ,coalesce(max(UNIX_TIMESTAMP(h.created_ts)),0) as comment_ts
                                                                   ,b.parent_group_id
                                                                   ,d.group_nm as group_nm
                                                                   ,' ' as subgroups
                                                             from questions a 
                                                             inner join group_posts b 
                                                             on a.qstn_id = b.post_id 
                                                             and b.parent_group_id = 0
                                                             inner join groups d 
                                                             on d.group_id = b.parent_group_id 
                                                             left outer join answers g 
                                                             on g.qstn_id = a.qstn_id 
                                                             left outer join comments h 
                                                             on h.ans_id = g.ans_id 

                                                           group by a.qstn_id
	                                                         ,a.qstn_titl
                                                             ,a.qstn_desc
	                                                         ,a.posted_by
	                                                         ,a.up_votes
	                                                         ,a.down_votes
	                                                         ,a.topic_id
	                                                         ,a.created_ts 
		                                                     ,b.parent_group_id
		                                                     ,d.group_nm
                                                            ) as x 
                                                            order by score desc";
							}
						}
						foreach($conn->query($sql_fetch_all_qstn) as $row_qid)	{
							$row_qstn_id=$row_qid['qstn_id'];
							array_push($qstn_array,$row_qstn_id);
						}
						$qstn_arr_str=implode("|",$qstn_array);
				}
				catch(PDOException	$e)	{
					echo 'Error fetching Question ';
				}
				?>
				</div>
				<div id="scroll-msg">
					<div id="btn-section">
						<button id="explore-btn" class="btn btn-primary" onclick="fetchMoreQuestions()">Explore more</button>
					</div>
				</div>
			</div>
			<div class="col-sm-3">
				<div id="tags-box">
					<span id="tag-box-title">Popular tags</span></br></br>
					<?php
						try	{
							$sql_fetch_tags_list="  select a.tag_id,a.tag_name,count(1) as cnt_follower
													from tags a
													inner join qstn_tags b 
													on a.tag_id = b.tag_id
													group by a.tag_id,a.tag_name
													order by 3 desc limit 50
												  ";
							$stmt_fetch_tags_list=$conn->prepare($sql_fetch_tags_list);
							$stmt_fetch_tags_list->execute();
							
							if($stmt_fetch_tags_list->rowCount() < 0)	{
								echo "Nothing to show now";
							}
							else	{
								while($row_tags_list=$stmt_fetch_tags_list->fetch())	{
									$tag_id=$row_tags_list['tag_id'];
									$tag_nm=$row_tags_list['tag_name'];
									$count_tags=$row_tags_list['cnt_follower'];
									
									echo "<span class='badge tag-name-list'><a href='".$slashes."forum/index.php?tag=".$tag_id."'>".$tag_nm."</a></span>";
								}
							}
						}
						catch(PDOException $e)	{
							echo "Some error occured in the server";
						}
					
					
					?>
					</br></br>
				</div></br>
			</div>
		</div>
	</div>
	<input id="qid-array-list" type="hidden" value="<?php echo $qstn_arr_str; ?>" />
	<input id="page-locate-data" type="hidden" value="<?php echo $slashes; ?>" />
	<input id="scroll-flag" type="hidden" value="1" />
	<?php include "../footer.php"; ?>
</body>
</html>
