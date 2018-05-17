<?php
	

	
		$con = new mysqli('localhost', 'root', 'mysqlroot', 'smdev_test');
$userid=$_POST["user"];
		$name = $con->real_escape_string($_POST['name']);
		$mail = $con->real_escape_string($_POST['mail']);
		$pwd = $con->real_escape_string($_POST['pwd']);
		
             $hashed_pwd=md5($pwd);
		$query=mysqli_query($con,"select * from users where user_id like '$userid'; ");
$queryemail=mysqli_query($con,"select * from users where email_addr like '$mail'; ");
if($_SERVER["REQUEST_METHOD"]=="POST")	{
if($name&&$mail&&$pwd&&$userid){
    
if(mysqli_num_rows($query)>0){
    
    echo "username already exsists";
    
}
else if (mysqli_num_rows($queryemail)>0){
    echo "email already exsits";
    
    
}

else if(strlen($pwd)<8){
    echo "Password should be 8 or more than 8 char long";
    
}
    else{
		if(filter_var($mail,FILTER_VALIDATE_EMAIL)== true){
			$sql = $con->query("SELECT id FROM users WHERE email_addr='$mail'");
			if ($sql->num_rows > 0) {
				echo "Email already exists in the database!";
			} else {
				
			
			$sql="insert into users
			(user_id,disp_name,encrypt_pwd,email_addr,status)
			values
			('".$userid."','".$name."','".$hashed_pwd."','".$mail."','A')";

				$con->query($sql);

               

                
                    echo "You have been registered! Please verify your email!";
                
			}
		    
		}
		else{
		    echo "please enter a valid email address";
		}
    }
}
else{
    echo "please fill all feilds";
}}
?>
