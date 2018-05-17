<?php
session_start();
if(!$_SESSION["logged_in"])	{
	header("location:index.php");
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "connectDb.php";
include "forum/functions/get_time.php";
include "forum/functions/get_time_offset.php";

function convert_utc_to_local($utc_timestamp)	{
	
	try	{
		$date_utc=new DateTime($utc_timestamp,new DateTimeZone('UTC'));
		if(isset($_COOKIE['user_tz']))
			$date_utc->setTimeZone(new DateTimeZone($_COOKIE['user_tz']));	
		else
			$date_utc->setTimeZone(new DateTimeZone('UTC'));	
		$date_final = $date_utc->format('Y-m-d H:i:s');
		return $date_final;
	}
	catch(Exception $e)	{
		echo 'Some error occurred';
	}
}

    if(isset($_POST['group_id']))    {
        $group_id=$_POST['group_id'];
    }

    if(isset($_POST['sort'])) {
        $sort_order=$_POST['sort'];
        if($sort_order == 1)
            $sort_order_col = 'created_ts';
        else if($sort_order == 2)
            $sort_order_col = 'up_votes';
        else if($sort_order == 3)
            $sort_order_col = 'views';
    }
    else    {
        $sort_order_col = 'score';
    }
    
    if(isset($_POST['subgroups']))  {
        $subgroups=$_POST['subgroups'];
        $subgroup_list = implode(", ",$subgroups);   
    }
    else    {
        $subgroup_list = implode(", ",$_SESSION["subgroups_all"]);
    }
    
    try	{
        if($subgroup_list == "0") {
            $sql="select t.qstn_id,
			         t.qstn_titl,
			         t.qstn_desc,
			         t.posted_by,
			         t.up_votes,
			         t.down_votes,
			         t.topic_id,
			         t.created_ts,
                     t.views,
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
                     a.views,
			        coalesce(max(UNIX_TIMESTAMP(d.created_ts)),0) as answer_ts,
			        coalesce(max(UNIX_TIMESTAMP(e.created_ts)),0) as comment_ts
	         from questions a 
               inner join group_posts a1
               on a.qstn_id=a1.post_id
               and a1.parent_group_id=".$group_id."   
               inner join users a2
               on a2.user_id=a.posted_by
               and a2.user_id = '".$_SESSION['user']."'
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
	           order by ".$sort_order_col." desc";
        }
        else    {

            $sql="select t.qstn_id,
			         t.qstn_titl,
			         t.qstn_desc,
			         t.posted_by,
			         t.up_votes,
			         t.down_votes,
			         t.topic_id,
			         t.created_ts,
                     t.views,
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
                     a.views,
			        coalesce(max(UNIX_TIMESTAMP(d.created_ts)),0) as answer_ts,
			        coalesce(max(UNIX_TIMESTAMP(e.created_ts)),0) as comment_ts
	         from questions a 
               inner join users a2
               on a2.user_id=a.posted_by
               and a2.user_id <> '".$_SESSION['user']."'
               and a2.subgroup_id in (".$subgroup_list.") 
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
	           order by ".$sort_order_col." desc";
        }
		    include "forum/fetch_answers1.php";
		    if($stmt->rowCount() <=0)	{
			    echo '<div class="alert alert-info">
				      No questions posted in the given subgroup. Try using different filter
			      </div>';
		    }
	
    }
    catch(PDOException	$e)	{
	    echo $sql;
    }

