<?php
//Connect to the database
include 'server.php'
$connection = server_connect()

$questionToAccess = $_POST['mapNo'];

$sql = "SELECT QuestionID"
       . " FROM Question"
       . " WHERE Question.Status = 'active'"
	   . " ORDER BY FIELD(SpecialInfo, 'featured', 'sponsored', 'normal'), QuestionID";

$query = mysql_query($sql);

if($query == false)
{
  $questionID = null;
}
else
{
  $questionID = mysql_result($query, ($questionToAccess - 1));
}

$sql = "SELECT Question"
       . " FROM Question"
       . " WHERE Question.QuestionID ='$questionID'";

$query = mysql_query($sql);

if($query == false)
{
  $question = null;
}
else
{
  $question = mysql_result($query, 0);
}

echo json_encode(array('questionID' => $questionID, 'question' => $question));

?>
