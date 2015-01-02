<?php
session_start();
//Connect to the database
include 'server.php'
$connection = server_connect()

$question = filter_input(INPUT_POST, 'question', FILTER_SANITIZE_STRING);
$answer1 = filter_input(INPUT_POST, 'answer1', FILTER_SANITIZE_STRING);
$answer2 = filter_input(INPUT_POST, 'answer2', FILTER_SANITIZE_STRING);
$answer3 = filter_input(INPUT_POST, 'answer3', FILTER_SANITIZE_STRING);
$answer4 = filter_input(INPUT_POST, 'answer4', FILTER_SANITIZE_STRING);
$answer5 = filter_input(INPUT_POST, 'answer5', FILTER_SANITIZE_STRING);
$answer6 = filter_input(INPUT_POST, 'answer6', FILTER_SANITIZE_STRING);

$createdDate = date("Y-m-d");


if((isset($_SESSION['userID'])) && !($_SESSION['userID'] == -1))
{
	$userID = $_SESSION['userID'];

	$sql = "SELECT Rank FROM User WHERE User.UserID = '$userID'";
	$result = mysql_query($sql);
	$specialInfo = mysql_result($result, 0);

	if($specialInfo != "featured")
	{
		$specialInfo = "normal";
	}

    $sql = "INSERT INTO Question(UserID, CreatedDate, SpecialInfo, Question, Answer1, Answer2, Answer3, Answer4, Answer5, Answer6) VALUES ('$userID', '$createdDate', '$specialInfo', '$question', '$answer1', '$answer2', '$answer3', '$answer4', '$answer5', '$answer6')";
    mysql_query($sql);

	$sql = "SELECT @@IDENTITY";
    $IDquery = mysql_query($sql);
    $QID = mysql_result($IDquery,0);
    echo $QID;
}
else
{
  echo false;
}
  ?>
