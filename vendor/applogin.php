<?php

$con = new mysqli('localhost', 'root', 'mysqlroot', 'smdev_test');
$username=$_POST["username"];
$userpassword=$_POST["password"];
$mysql_qry=" select * from users where user_id='".$username."';";
$result =mysqli_query($con,$mysql_qry);

if(($username!= NULL)&&($userpassword!=NULL)){
if(mysqli_num_rows($result)>0){
    $data=$result->fetch_array();
        
    
        if (md5($userpassword) ==$data['encrypt_pwd']) {
    echo  "login successful  ";
    echo "$username";
        
    }
    else{
        
        echo "invalid username or password";
    }
    
    
    
}
else{
    
    echo "Please Enter Correct Login Details";
}}
else if($username==NULL){
    echo "please enter username ";
}
else{
    echo "please enter password";
}
?>