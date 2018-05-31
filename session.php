<?php
try 	{
	$sql_fetch_user_details="select *
							from users
							where user_id='".$userid."'";
	
	$stmt=$conn->prepare($sql_fetch_user_details);
	$stmt->execute();
	$row=$stmt->fetch();
	$name=addslashes($row['disp_name']);
	$user=addslashes($row['user_id']);
	$mail=$row['email_addr'];
	$ph_num=$row['ph_num'];
	$dob=$row['dob'];
	$desc=addslashes($row['description']);
	$shrt_bio=addslashes($row['shrt_bio']);
	$location=addslashes($row['location']);
	$upvotes=$row['up_votes'];
	$downvotes=$row['down_votes'];
	$pro_img=$row['pro_img_url'];
/*  
  $subgroup_id=$row['subgroup_id'];
	if(is_null($subgroup_id))   {
        $subgroup_id=0;
    }
*/
	$sql_get_follow_dtls="select count(1) count1 from followers where following_user_id='".$userid."'";
	foreach($conn->query($sql_get_follow_dtls) as $res_fl1)
		$follow_cnt1 = $res_fl1['count1'];
	$sql_get_following_dtls="select count(1) count2 from followers where user_id='".$userid."'";
	foreach($conn->query($sql_get_following_dtls) as $res_fl2)
		$follow_cnt2 = $res_fl2['count2'];
		
	$tags_list=array();
	$tags_str="";
	try	{
		$sql_check_interests = "select b.tag_name 
								from tags b 
								inner join user_tags a 
								on b.tag_id = a.tag_id
								where a.user_id = '".$userid."'";
		$stmt_check_interests = $conn->prepare($sql_check_interests);
		$stmt_check_interests->execute();
		
		if($stmt_check_interests->rowCount() > 0)	{
			while($row_interests = $stmt_check_interests->fetch())	{
				array_push($tags_list,$row_interests['tag_name']);
			}
			$tags_str=implode($tags_list,", ");
		}
	}
	catch(PDOException $e)	{
		
	}
}

catch(PDOException $e)	{
	echo $e->getMessage();
}

try {
    $sql_check_subgroups = "select count(1) as grp_cnt from group_mbr where user_id = '".$userid."'";
    $stmt_check_subgroups = $conn->prepare($sql_check_subgroups);
	$stmt_check_subgroups->execute();
    $row_grp_cnt = $stmt_check_subgroups->fetch();
    $grp_count = $row_grp_cnt['grp_cnt'];
    if($grp_count > 0)  {
        $_SESSION["is_group_user"]=1;
    }
    else    {
        $_SESSION["is_group_user"]=0;
    }
}
catch(PDOException $e)  {

}

$_SESSION["logged_in"]=true;
$_SESSION["user"]=$user;
$_SESSION["pro_img"]=$pro_img;
$_SESSION["name"]=$name;
$_SESSION['mail']=$mail;
$_SESSION['desc']=$desc;
$_SESSION['shrt_bio']=$shrt_bio;
$_SESSION['location']=$location;
$_SESSION['ph_num']=$ph_num;
$_SESSION['dob']=$dob;
$_SESSION["up_votes"]=$upvotes;
$_SESSION["down_votes"]=$downvotes;
$_SESSION["flw_1"]=$follow_cnt1;
$_SESSION["flw_2"]=$follow_cnt2;
$_SESSION["interest"]=$tags_str;
$_SESSION["interest_list"]=$tags_list;
/*
    $_SESSION['subgroup_id']=$subgroup_id; 
    $_SESSION["subgroup"]=get_subgroup($subgroup_id);
*/
/*
    # initializing group id visibility for different subgroups 
    $_SESSION["subgroups_all"]=Array(2,3,4,5,6);
    $_SESSION["subgroups_a"]=Array(2,3,4,5,6);
    $_SESSION["subgroups_f"]=Array(2,3,4,5,6);
    $_SESSION["subgroups_u"]=Array(5);
    $_SESSION["subgroups_g"]=Array(4,6);
    $_SESSION["subgroups_p"]=Array(4,6);
*/
/*
    function get_subgroup($subgroup_id) {
        
        if($subgroup_id == 2)
            return 'A';
        else if($subgroup_id == 3)
            return 'F';
        else if($subgroup_id == 5)
            return 'U';
        else if($subgroup_id == 4)
            return 'G';
        else if($subgroup_id == 6)
            return 'P';
        else
            return "";
    }
*/
?>
