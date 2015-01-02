<?php //Connect to the database
session_start();

//Connect to the database
include 'server.php'
$connection = server_connect()

$userID = $_SESSION['userID'];

$questionID = $_POST['questionID'];

$sql = "SELECT VoteID "
       . "FROM Vote "
	   . "WHERE UserID = '$userID' "
	   . "AND QuestionID = '$questionID'";

$query = mysql_query($sql);

$votePresent = mysql_num_rows($query);

if($votePresent > 0)
{
  $sql = "DELETE FROM Vote "
         . "WHERE QuestionID = '$questionID' and UserID = $userID";

  $success = mysql_query($sql);

  echo "deleted";
}
else if($votePresent == 0)
{
  $sql = "INSERT INTO Vote (QuestionID, UserID) "
         . "VALUES ('$questionID', $userID)";

  $success = mysql_query($sql);

  echo "added";
}
else
{
  echo false;
}

?>
