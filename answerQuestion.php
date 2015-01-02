<?php //Connect to the database
session_start();

//Connect to the database
include 'server.php'
$connection = server_connect()

$userID = $_SESSION['userID'];

$questionID = $_POST['questionID'];
$answer = $_POST['answer'];

$sql = "SELECT AnswerID "
       . "FROM Answer "
	   . "WHERE UserID = '$userID' "
	   . "AND QuestionID = '$questionID'";

$query = mysql_query($sql);

$answerPresent = mysql_num_rows($query);

if($answerPresent > 0)
{
  $sql = "UPDATE Answer SET QuestionID = '$questionID', UserID = '$userID', Value = '$answer'"
         . " WHERE UserID = '$userID' "
		 . "AND QuestionID = '$questionID'";

  $success = mysql_query($sql);

  echo $success;
}
else
{
  $sql = "INSERT INTO Answer (QuestionID, UserID, Value) "
         . "VALUES ('$questionID', $userID, $answer)";

  $success = mysql_query($sql);

  echo $success;
}

?>
