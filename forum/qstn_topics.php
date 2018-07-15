<?php
if($_SERVER['REQUEST_METHOD'] == 'POST')	{
    
    if($logged_in == 1)	{
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

        from (select a.qstn_id
                    ,a.qstn_titl
                    ,a.qstn_desc
                    ,a.topic_id
                    ,a.posted_by
                    ,a.up_votes
                    ,a.down_votes
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
            inner join 	topics b 
            on a.topic_id = b.topic_id 
            where ";
	
            $sql_fetch_all_qstn="select   t.qstn_id

                            from (select a.qstn_id
                                        ,a.qstn_titl
                                        ,a.qstn_desc
                                        ,a.topic_id
                                        ,a.posted_by
                                        ,a.up_votes
                                        ,a.down_votes
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
                                inner join 	topics b 
                                on a.topic_id = b.topic_id 
                                where ";
    }
    else    {
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

        from (select a.qstn_id
                    ,a.qstn_titl
                    ,a.qstn_desc
                    ,a.topic_id
                    ,a.posted_by
                    ,a.up_votes
                    ,a.down_votes
                    ,a.created_ts
                    ,a1.parent_group_id
                    ,g.group_nm 
                    ,group_concat(distinct h.group_nm order by h.group_nm asc separator ',') as subgroups
            from questions a 
            inner join group_posts a1 
            on a1.post_id = a.qstn_id 
            inner join groups g 
            on g.group_id = a1.parent_group_id
            left outer join groups h
            on h.group_id = a1.group_id
            inner join 	topics b 
            on a.topic_id = b.topic_id 
            where ";
	    
        $sql_fetch_all_qstn="select   t.qstn_id

                            from (select a.qstn_id
                                        ,a.qstn_titl
                                        ,a.qstn_desc
                                        ,a.topic_id
                                        ,a.posted_by
                                        ,a.up_votes
                                        ,a.down_votes
                                        ,a.created_ts
                                        ,a1.parent_group_id
                                        ,g.group_nm 
                                        ,group_concat(distinct h.group_nm order by h.group_nm asc separator ',') as subgroups
                                from questions a 
                                inner join group_posts a1 
                                on a1.post_id = a.qstn_id 
                                inner join groups g 
                                on g.group_id = a1.parent_group_id
                                left outer join groups h
                                on h.group_id = a1.group_id
                                inner join 	topics b 
                                on a.topic_id = b.topic_id 
                                where ";
    }
	$topic_nm = trim($_POST['filter']);
	$sort_order = trim($_POST['sort']);
	if(!empty($topic_nm) && !empty($sort_order))	{
		
		if($topic_nm != 'All topics')	{
			$sql.= "b.topic_desc = '".$topic_nm."' and ";
			$sql_fetch_all_qstn.="b.topic_desc = '".$topic_nm."' and ";
		}
		
		if($sort_order == 'Recent' or $sort_order == 'My posts' or $sort_order == 'Default')
			$sort = 'a.created_ts';
		else if($sort_order == 'Most upvoted')
			$sort = 'a.up_votes';
		
		if($logged_in == 1)	{
			if($sort_order == 'My posts')	{
				$sql.="b.parent_topic = ".$parent_topic_id." and a.posted_by='".$_SESSION['user']."' 
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
                       order by ".$sort." desc
                        ) t 
                       where (t.parent_group_id = 0 or t.user_ids is not null) limit 10";
				$sql_fetch_all_qstn.="b.parent_topic = ".$parent_topic_id." and a.posted_by='".$_SESSION['user']."' 
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
                                      order by ".$sort." desc) t 
                       where (t.parent_group_id = 0 or t.user_ids is not null)";
			}
			else	{
				$sql.="b.parent_topic = ".$parent_topic_id." 
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
                       order by ".$sort." desc) t 
                       where (t.parent_group_id = 0 or t.user_ids is not null) limit 10";

				$sql_fetch_all_qstn.="b.parent_topic = ".$parent_topic_id."
                    group by a.qstn_id
                            ,a.qstn_titl
                            ,a.qstn_desc
                            ,a.posted_by
                            ,a.up_votes
                            ,a.down_votes
                            ,a.topic_id
                            ,a.created_ts
                            ,a1.parent_group_id
                            ,g.group_nm ) t 
                       where (t.parent_group_id = 0 or t.user_ids is not null)
                       order by ".$sort." desc";
			}
		}
		else	{
			$sql.="b.parent_topic = ".$parent_topic_id." 
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
                       order by ".$sort." desc) t 
                       limit 10";
			$sql_fetch_all_qstn.="b.parent_topic = ".$parent_topic_id." 
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
                        order by ".$sort." desc) t ";
		}
	}
	else 	{
        if($logged_in == 1) {
		    $sql.=" b.parent_topic = ".$parent_topic_id." 
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
                    
                    order by a.created_ts desc) t 
                    where (t.parent_group_id = 0 or t.user_ids is not null) limit 10";

		    $sql_fetch_all_qstn.="b.parent_topic = ".$parent_topic_id." 
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
            
                    order by a.created_ts desc) t 
                    where (t.parent_group_id = 0 or t.user_ids is not null)";
        }
        else    {
            $sql.=" b.parent_topic = ".$parent_topic_id." 
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
                
                order by a.created_ts desc) t 
                limit 10";

		    $sql_fetch_all_qstn.="b.parent_topic = ".$parent_topic_id." 
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
        
                order by a.created_ts desc) t ";        
        }
	}
}
else	{
    
    if($logged_in == 1) {
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
                inner join 	topics b 
                on a.topic_id = b.topic_id 
                where b.parent_topic = ".$parent_topic_id."  

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
                
                order by a.created_ts desc) t 
                where (t.parent_group_id = 0 or t.user_ids is not null) limit 10";

	    $sql_fetch_all_qstn="select   t.qstn_id

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
                inner join 	topics b 
                on a.topic_id = b.topic_id 
                where b.parent_topic = ".$parent_topic_id." 
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
                
                order by a.created_ts desc) t 
                where (t.parent_group_id = 0 or t.user_ids is not null)";
    }
    else    {
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
                      ,group_concat(distinct h.group_nm order by h.group_nm asc separator ',') as subgroups 
                from questions a 
                inner join group_posts a1 
                on a1.post_id = a.qstn_id 
                inner join groups g 
                on g.group_id = a1.parent_group_id
                left outer join groups h
                on h.group_id = a1.group_id  
                inner join 	topics b 
                on a.topic_id = b.topic_id 
                where b.parent_topic = ".$parent_topic_id."  

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
                
                order by a.created_ts desc) t 
                limit 10";

	    $sql_fetch_all_qstn="select   t.qstn_id

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
                      ,group_concat(distinct h.group_nm order by h.group_nm asc separator ',') as subgroups 
                from questions a 
                inner join group_posts a1 
                on a1.post_id = a.qstn_id 
                inner join groups g 
                on g.group_id = a1.parent_group_id
                left outer join groups h
                on h.group_id = a1.group_id  
                inner join 	topics b 
                on a.topic_id = b.topic_id 
                
                where b.parent_topic = ".$parent_topic_id." 
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
                
                order by a.created_ts desc) t 
                ";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Science Market - Questions on <?php echo $page_title; ?></title>
<meta name="description" content="<?php echo $page_desc; ?>" >
<link rel="stylesheet" type="text/css" href="../../../styles/header.css">
<link rel="stylesheet" type="text/css" href="../../../styles/dashboard.css">
<link rel="stylesheet" type="text/css" href="../../../styles/qa_forum.css">
<link rel="stylesheet" type="text/css" href="../../../styles/footer.css">
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../../../styles/bootstrap.min.css">
<script src="../../../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../../../js/qa_forum.js"></script>
<!--<script type="text/javascript" src="js/posts_vote.js"></script>-->
<script type="text/javascript" src="../../../js/header.js"></script></head>
<body>
<div id="block"></div>
<?php include "../../../header.php"; ?>
	</br>
	<div class="container">
		<?php 
			if($logged_in == 1)
				include "../../common_code.php"; 
			else
				include "../../common_code_guest.php"; 
			?>
			<div class="col-sm-10">
								<form class="form-inline" id="filter-form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
					<div class="form-group" id="filter-section">
						<label for="filter-values">Choose topics</label>
						<select class="form-control dropdown-opt" id="filter-values" name="filter">
						<option>All topics</option>
						<?php
							try	{
								$sql_fetch_sub_topics = "select topic_id,topic_desc from topics where parent_topic = ".$parent_topic_id;
								foreach($conn->query($sql_fetch_sub_topics) as $row_sub_topics)	{
									$sub_topic_name = $row_sub_topics['topic_desc'];
									$sub_topic_id = $row_sub_topics['topic_id'];
									echo '<option value="'.$sub_topic_id.'">'.$sub_topic_name.'</option>';
								}
							}
							catch(PDOException $e)	{
								
							}
						?>
						</select>
						<script>
							document.getElementById("filter-values").value="<?php echo $topic_nm; ?>";
						</script>
					</div>
					<div class="form-group" id="sort-section">
						<label for="sort-values">Sort questions</label>
						<select class="form-control dropdown-opt" id="sort-values" name="sort">
							<option value="0">Default</option>
							<option value="1">Recent</option>
							<option value="2">Most upvoted</option>
							<?php if($logged_in == 1)	{ ?>
							<option value="3">My posts</option>
							<?php }	?>
						</select>
						<script>
							document.getElementById("sort-values").value="<?php echo $sort_order; ?>";
						</script>
					</div>
					
					<button type="submit" id="filter-submit" class="btn btn-default">Go</button>
				</form></br>
				<div id="qstn-res">
				
				<?php
					try	{
					$query_string="";
					include "../../fetch_answers1.php";
					if($stmt->rowCount() <=0)	{
						echo '<div class="alert alert-info">
								No questions posted on this topic yet. Please do check after some time
						  </div>';
					}
					$qstn_array=array();
					foreach($conn->query($sql_fetch_all_qstn) as $row_qid)	{
						$row_qstn_id=$row_qid['qstn_id'];
						array_push($qstn_array,$row_qstn_id);
					}
					$qstn_arr_str=implode("|",$qstn_array);
				}
				catch(PDOException	$e)	{
					echo 'Error fetching Questions - '.$e->getMessage();
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
	<?php include "../../../footer.php"; ?>
</body>
</html>
