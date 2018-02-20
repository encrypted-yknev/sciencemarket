<?php
session_start();
use Aws\S3\Exception\S3Exception;
require 's3_connect.php';
$final_url="";

require "connectDb.php";

$success=false;


$pro_pic_success="<span style='color:#3c763d;'>Upload your image so that people can recognise you</span>";
	 
	if(isset($_FILES["propic"]))	{
	  $img = $_FILES["propic"];
	  $file_name = $img["name"];
	  $temp_file=$img["tmp_name"];								#temporary image name on server.
	  $extension = explode('.',$file_name);
	  $extension = strtolower(end($extension));
	  
	  $key = md5(uniqid());
	  $tmp_file_name = $key.$extension;
	  $tmp_file_path = "files/".$tmp_file_name;
	
	  if(strlen($file_name) > 0)	{
		  $file_size=$img["size"];
		  if($file_size>0 and $file_size<= 500000)	{		 
			    $type_num   = exif_imagetype($temp_file);
				if($type_num == 2 or $type_num == 3)		{		# 2 - JPEG. 3 - PNG
				   move_uploaded_file($tmp_name,$tmp_file_path);
				   
				   try	{
						$s3->putObject(
						array(
							'Bucket'=>$config['s3']['bucket'],
							'Key'=>"uploads/".$file_name,
							'Body'=>fopen($tmp_file_path,'rb'),
							'ACL'=>'public-read'
						)
						);
						$final_url=$s3->getObjectURL($config['s3']['bucket'],'uploads/'.$file_name);
						unlink($tmp_file_path);
						
   					   $_SESSION["pro_img"]=$final_url;
					   $sql_updt_user = "update users set pro_img_url = '".$final_url."' where user_id = '".$_SESSION['user']."'";
					   $stmt_updt_user=$conn->prepare($sql_updt_user);
					   $stmt_updt_user->execute();
					   if($stmt_updt_user->rowCount() > 0)	{
						   $success=true;
						   $pro_pic_success = "File uploaded successfully";
					   }
					   else	{
						   $pro_pic_success = "Image uploaded with some errors";
					   }
					}
					catch(S3Exception $e)	{
					   $pro_pic_success = "Error uploading file";
					} 				   
				    catch(PDOException $e)	{
					   $pro_pic_success = "Some error occurred";
				    }
				  }	
				  else	{
						$pro_pic_success = "Invalid image type. Only JPEG/PNG allowed";
				  }
			   }	 
			  else	{
				  $pro_pic_success = "File size exceeds. Max 500KB";
			  } 
		  }
		  else	{
			  $pro_pic_success = "Please select an image to upload";
		  }
	}
	else	{
	  $pro_pic_success="File not sent to the server";
	}
	
	$return_data["textMsg"]=$pro_pic_success;
	$return_data["succ_cd"]=($success==true)?1:0;
	
	echo json_encode($return_data);
	
 ?>