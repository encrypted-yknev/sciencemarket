<?php
session_start();
if(!$_SESSION["logged_in"])	{
	header("location:index.php");
}
include "connectDb.php";
$group_id=htmlspecialchars(stripslashes(trim($_REQUEST['group_id'])));
if(!empty($group_id))	{
	try	{
        /* Fetch all subgroups for a given group */
        $subgroup_all = "";
        $sql_fetch_all_subgroups = "select subgroup_id 
                                      from subgroups 
                                      where parent_group_id = ".$group_id;
        
        foreach($conn->query($sql_fetch_all_subgroups) as $row) {
            $subgroup_stg = $row['subgroup_id'];
            $subgroup_all = $subgroup_all.$subgroup_stg.' ';
        }
        $subgroup_all = str_replace(" ",",",trim($subgroup_all));
        
        /* Fetch all subgroups which are visible to logged in user for a given group */
        $sql_fetch_visbl_subgroups = "select replace(trim(extractvalue(subgroup_hier_confg,'/subgroups/subgroup_id')),' ',',') as 'subgroups' 
                                from subgroups t1
                                inner join group_mbr t2
                                on t1.subgroup_id = t2.subgroup_id
                                and t2.user_id = '".$_SESSION['user']."'
                                where parent_group_id = ".$group_id;
        
        
        $stmt_fetch_visbl_subgroups=$conn->query($sql_fetch_visbl_subgroups);
        $stmt_fetch_visbl_subgroups->execute();
        $result_subgroups = $stmt_fetch_visbl_subgroups->fetch();
        $subgroup_ids = $result_subgroups['subgroups'];
        
        /* Display subgroup id and names for the list fetched above */
		$sql_fetch_group_names="select t1.group_id,t1.group_nm
                                from groups t1
                                inner join subgroups t2
                                on t1.group_id = t2.subgroup_id
                                where t2.parent_group_id = 1
                                and t2.subgroup_id in (".$subgroup_ids.")";
                               
		echo "<input type='checkbox' name='subgroups[]' id='check-all' class='all-sec' value='".$subgroup_all."' onclick='changeSubgroups()' />&nbsp;&nbsp;All</br>";
        foreach($conn->query($sql_fetch_group_names) as $row_group)   {
            $group_name = $row_group["group_nm"];
            $group_id = $row_group["group_id"];

            echo "<input type='checkbox' name='subgroups[]' id='' class='subgroup-sec' value='".$group_id."' />&nbsp;&nbsp;<span class='grp-names'>".$group_name."</span></br>";
        } 
	}
	catch(PDOException $e)	{
		
	}
}
?>
