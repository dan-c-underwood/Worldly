<?php
//Connect to the database
include 'server.php'
$connection = server_connect()

$questionID = $_POST['questionID'];

$sql = 	"SELECT COUNT(*) FROM Answer "
		. "WHERE Answer.QuestionID = '$questionID'";

$result = mysql_query($sql);

$count = mysql_result($result, 0);

$sql =	"SELECT Question FROM Question "
		. "WHERE Question.QuestionID = '$questionID'";

$result = mysql_query($sql);

$question = mysql_result($result, 0);

for($i = 0; $i < $count; $i++)
{
	$output[$question][$i]["ID"] = $i;

	$sql = "SELECT Answer.UserID FROM Answer "
			 . "WHERE Answer.QuestionID='$questionID'";

	$result = mysql_query($sql);

	$userID = mysql_result($result, $i);

	$sql = "SELECT Answer.Value FROM Answer "
		   . "WHERE Answer.UserID='$userID'";

	$result = mysql_query($sql);

	$value = mysql_result($result, 0);

	$chosenAnswer = "Answer".$value;

	$sql = "SELECT ".$chosenAnswer." FROM Question "
		   . "WHERE Question.QuestionID='$questionID'";

	$result = mysql_query($sql);

	$answer = mysql_result($result, 0);

	$output[$question][$i]["Answer"] = $answer;

	$sql = "SELECT Location FROM User WHERE User.UserID='$userID'";

	$result = mysql_query($sql);

	$location = mysql_result($result, 0);

	$output[$question][$i]["Location"] = $location;

	$sql = "SELECT Gender FROM User WHERE User.UserID='$userID'";

	$result = mysql_query($sql);

	$gender = mysql_result($result, 0);

	$output[$question][$i]["Gender"] = $gender;

	$sql = "SELECT AgeGroup FROM User WHERE User.UserID='$userID'";

	$result = mysql_query($sql);

	$ageGroup = mysql_result($result, 0);

	$output[$question][$i]["AgeGroup"] = $ageGroup;

}

header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=dataForQuestionID".$questionID.".json");
header("Content-Type: application/xml");
header("Content-Transfer-Encoding: binary");

echo json_encode($output);
?>
