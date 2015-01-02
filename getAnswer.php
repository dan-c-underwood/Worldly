<?php
session_start();
//Connect to the database
include 'server.php'
$connection = server_connect()

$questionID = $_POST['questionID'];
$userID = $_SESSION['userID'];

$sql = 	"SELECT Value "
		. "FROM Answer "
		. "WHERE Answer.QuestionID = '$questionID' "
		. "AND Answer.UserID = '$userID'";

$result = mysql_query($sql);

if($result == false)
{
	echo 0;
}
else
{
	$value = mysql_result($result, 0);
	echo $value;
}
