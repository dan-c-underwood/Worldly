<?php

//Connect to the database
include 'server.php'
$connection = server_connect()

$questionID = $_POST['questionID'];

$sql = 	"UPDATE Question "
		. "SET Status='reported' "
		. "WHERE Question.QuestionID='$questionID'";

echo mysql_query($sql);

?>
