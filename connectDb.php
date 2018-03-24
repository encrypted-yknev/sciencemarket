<?php

/*	Connection parameters to connect to mysqlDb */
$username="root";
$password="123";
$servername="localhost";
$dbname = "smdev_test"; //Set your database name here. No hardcoding.

try	{
	$conn=new PDO("mysql:host=$servername;dbname=$dbname",$username,$password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

catch(PDOException $e)	{
	echo "Error in connection. Please try after some time.";
}

?>
