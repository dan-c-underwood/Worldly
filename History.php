<?php session_start();
if (!(isset($_SESSION['userID'])) || ($_SESSION['userID'] == -1))
{
    $notLoggedIn = 'true';
}
else
{
    $notLoggedIn = 'false';
}
//Connect to the database
include 'server.php'
$connection = server_connect()

$search = '';

if(isset($_GET['specialInfo']))
{
	$specialInfo = filter_input(INPUT_GET, 'specialInfo', FILTER_SANITIZE_STRING);

	$search .= "&specialInfo=".$specialInfo;

	switch($specialInfo)
	{
		case "normal":
			$specialInfo = "normal";
			break;
		case "featured":
			$specialInfo = "featured";
			break;
		case "sponsored":
			$specialInfo = "sponsored";
			break;
		case "all":
			$specialInfo = "normal', 'featured', 'sponsored";
			break;
	}
}

else
{
	$specialInfo = "normal', 'featured', 'sponsored";
}

date_default_timezone_set('Europe/London');
$currentDate = date("Y-m-d");

if(isset($_GET['range']))
{
	$range = $_GET['range'];
	$search .= "&range=".$range;

	$startDate = new DateTime($currentDate);

	switch($range)
	{
		case "week":
			$startDate->modify('-1 week');
			break;
		case "month":
			$startDate->modify('-1 month');
			break;
		case "year":
			$startDate->modify('-1 year');
			break;
		case "all":
			$startDate = DateTime::createFromFormat('Y-m-d', '2000-01-01');
			break;
	}
	$startDate = $startDate->format('Y-m-d');
}
else
{
	$startDate = DateTime::createFromFormat('Y-m-d', '2000-01-01');
	$startDate = $startDate->format('Y-m-d');
}

?>
<!DOCTYPE html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title alt="WorldlyTitle">Worldly: You Ask It, We Map It</title>
<link rel="stylesheet" type="text/css" href="WorldlyCSS.css">
<link rel="stylesheet" type="text/css" href="VoteOnQuestionPageCSS.css">
<link rel="icon" href="globe.ico" type="image/x-icon">
<link rel="shortcut icon" href="globe.ico" type="image/x-icon">
<script src="http://code.jquery.com/jquery-1.8.3.js"></script>
<script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>
<meta charset="utf-8" />
</head>
<body>

  <ul id="menu" alt="basicMenuBar">
  <div id="contents1" alt="menuContents">
    <li><a href="#" class="logo" alt="logo"><span></span></a></li>
    <li><a href="Home.php" class="home" alt=""><span></span></a></li>
    <li><a href="Questions.php" class="questions" alt="questionsMenu"><span></span></a></li>
    <li><a href="#" class="historyActive" alt="historyActive"><span></span></a></li>
    <li><a href="" class="restOfBar" alt="freeBarSpace"></a></li>
    <?php if(isset($_SESSION['userID']))
          {
            if($_SESSION['userID'] == -1)
              {echo '<li><a href="Account.php" class="account"><span></span></a></li>'; }
            else
              {echo '<li><a href="AccountDetail.php" class="account"><span></span></a></li>'; }
          }
          else
          { echo '<li><a href="Account.php" class="account"><span></span></a></li>'; }
    ?>
    </div>
 </ul>

<div id="aboutUsFAQTab">
<form name="logged" method="post" action="Account.php">
<?php if(!(isset($_SESSION['userID'])) || ($_SESSION['userID'] == -1))
      {
        echo '<input class="tabButtonsMain"type="submit" style="float:right" value="Login/Register">';
      }
      else
      {
        echo '<input type="hidden" name="action" value="logout"><input class="tabButtonsMain"type="submit" style="float:right" value="Logout">';
      }
?>
</form>
<input class="tabButtonsMain" type="submit" value="About Us" onClick="parent.location='aboutus.php'"/>
<input class="tabButtonsMain" type="submit" value="FAQ" onClick="parent.location='faq.php'"/>
</div>

 <div id="AskArea" alt="workingArea">

    <div id="addNewQuestion" alt="titleOfAddQuestionPage"><p id="textTitleForAddNew">Search Our Question Archives</p></div>

    <div id="divAddQuestion" alt="formToAddNewQuestion">

	<table id="searchTable" width="auto">
        <tr>
            <td> From:</td>
            <td class="historyFieldFromTypeTable"><form id="selectRange" action="">
                   <select id="dateRangeSelect" class="fromANDtype">
	              <option <?php if ($_GET['range']=="week"){echo 'selected';} ?> value="week">Past week</option>
	     	      <option <?php if ($_GET['range']=="month"){echo 'selected';} ?> value="month">Past month</option>
	              <option <?php if ($_GET['range']=="year"){echo 'selected';} ?> value="year">Past year</option>
	              <option <?php if ($_GET['range']=="all"){echo 'selected';} ?> value="all">All time</option>
	           </select>
                </form>
	</td>
        </tr>
        <tr>
            <td>Type:</td>
            <td class="historyFieldFromTypeTable"><form id="selectSpecial" action="">
		  <select id="specialInfoSelect" class="fromANDtype">
		     <option <?php if ($_GET['specialInfo']=="normal"){echo 'selected';} ?> value="normal">Normal</option>
		     <option <?php if ($_GET['specialInfo']=="featured"){echo 'selected';} ?> value="featured">Featured</option>
		     <option <?php if ($_GET['specialInfo']=="sponsored"){echo 'selected';} ?> value="sponsored">Sponsored</option>
		     <option <?php if ($_GET['specialInfo']=="all"){echo 'selected';} ?> value="all">All</option>
		  </select>
	       </form>
	</td>
        </tr>
    	</table>

        <div id="divButtonSearch"><input  class="tabButtonsMain" id="searchButton" type="button" value="Search"/></div>
    </div>
</div>


<div id=whiteSpace1>
<div id="DivVoteForQuestion"><p id="textTitleForAddNew">Results</p></div>
<div id="voteAndMoreQuestions">
<div id="questions">
<?php
$questionTitle = "Maximum of digits for the text is 100 ";
echo '<table CELLPADDING=2 CELLSPACING=0
id="questionsTable" width="auto">
     <tr>
      <th>No.</th>
      <th>Type</th>
      <th>Question Title</th>
      <th>View More</th>
     </tr>';

$sql = "SELECT COUNT(*) "
       . "FROM Question "
       . "WHERE Question.Status = 'closed' "
	   . "AND ( Question.SpecialInfo IN ('$specialInfo') "
	   . "AND Question.ClosedDate BETWEEN '$startDate' AND '$currentDate' )";

$result = mysql_query($sql);

$count = mysql_result($result, 0);

if (isset($_GET['from']))
{
  $startValue = $_GET['from'];
}
else
{
  $startValue = 1;
}

$showQuestions = 20;


$sql = "SELECT Question.QuestionID "
	   . "FROM Question "
	   . "LEFT JOIN (SELECT QuestionID, COUNT(*) cnt "
	   . "      FROM Vote "
	   . "      GROUP BY QuestionID "
	   . "      ) Vote ON (Question.QuestionID=Vote.QuestionID) "
	   . "WHERE Question.Status = 'closed' "
	   . "AND ( Question.SpecialInfo IN ('$specialInfo') "
	   . "AND Question.ClosedDate BETWEEN '$startDate' AND '$currentDate' ) "
	   . "ORDER BY Vote.cnt DESC "
	   . "LIMIT ".($startValue - 1).", 20";


	   echo "<script> alert(".$sql."); </script>";

$questionIDs = mysql_query($sql);
$count1 = $startValue - 1;
$notTrue = 0;

for ($question = 1; $question <= $showQuestions; $question++ )
{
	$questionID = mysql_result($questionIDs, ($question - 1));

	$sql = "SELECT Question "
	       . "FROM Question "
		   . "WHERE Question.QuestionID = '$questionID'";

	$result = mysql_query($sql);

	$questionTitle = mysql_result($result, 0);

	$sql = "SELECT SpecialInfo "
		   . "FROM Question "
		   . "WHERE Question.QuestionID = '$questionID'";

	$result = mysql_query($sql);

	$specialInfo = mysql_result($result, 0);

	switch($specialInfo)
	{
		case "normal":
			$specialInfo = "greyGlobe.png";
			break;
		case "featured":
			$specialInfo = "blueGlobe.png";
			break;
		case "sponsored":
			$specialInfo = "goldenGlobe.png";
			break;
	}

	if($questionTitle != "")
	{
		$notTrue = 0;
		$count1++;

		echo '<tr class="alt" style = "margin-top:5px">
		<td id="questionNo'.$questionID.'"><span id="questionNo">'.$count1.' </span></td>
		<td style="width:40px; "><img id="questionInfoIcon" src='.$specialInfo.' style="float:left" width="32" height="32"></td>
		<td id="questionCell'.$questionID.'">'.$questionTitle.'</td>
		  <td id="seeMoreCell'.$questionID.'"><input class="tabButtonsMain" id="buttonSeeMore" type=button onClick="location.href="" value="View"></td>
		  </tr>';
	}
	else
	{
		$notTrue = 1;
	}
}
if($notTrue == 0)
{
	$newStart = $startValue + 20;
	$link = "'History.php?from=".$newStart.$search."'";
	echo '</table><div id="centeredMoreQuestionsButton">
	<input class="tabButtonsMain" id="moreQuestions" type="button" style="float:center" onClick="parent.location='.$link .'" value="More Questions"/>
	</div><br>';
}
if($startValue > 20)
{
	$previousStart = $startValue - 20;
	$linkBack = "'History.php?from=".$previousStart.$search."'";
	echo '</table><div id="centeredMoreQuestionsButton">
	<input class="tabButtonsMain" id="moreQuestions" type="button" style="float:center" onClick="parent.location='.$linkBack .'" value="Back"/>
	</div>';
}

?>
</table>
</div>
</div>
</div>
</div>
<script>
$(document).ready(function(){
	$('input[id="buttonSeeMore"]').click(function(event) {
		event.preventDefault();
		var tdid = $(this).closest('td').attr('id'); // table row ID
		var questionID = tdid.substring(11);
		window.location.replace("View.php?questionID=" + questionID);
	});
});

$(document).ready(function(){
	$('input[id="searchButton"]').click(function(event) {
		event.preventDefault();
		var specialInfo = document.forms["selectSpecial"]["specialInfoSelect"].value;
		var range = document.forms["selectRange"]["dateRangeSelect"].value;
		window.location.replace("History.php?specialInfo=" + specialInfo + "&range=" + range);
	});
});
</script>
</body>

</html>
