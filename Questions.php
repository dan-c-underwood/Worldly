<?php
session_start();
 if (!(isset($_SESSION['userID'])) || ($_SESSION['userID'] == -1))
{
	$notLoggedIn = 'true';
}
else
{
	$notLoggedIn = 'false';
	$userID = $_SESSION['userID'];
}
//Connect to the database
include 'server.php'
$connection = server_connect()

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

<script>
   var count = 2;
   function addAnswer(tableID) {
       if(count < 6){
            var table = document.getElementById(tableID);

            var rowCount = table.rows.length;

            var row = table.insertRow(rowCount);
            var cell1 = row.insertCell(0);
            var element1 = document.createElement("label");
            element1.type = "label";
            element1.innerHTML="Answer";
            cell1.appendChild(element1);

            var cell2 = row.insertCell(1);
            cell2.innerHTML = rowCount + 1;

	    count++;
            var cell3 = row.insertCell(2);
            var element2 = document.createElement("input");
            element2.type = "text";
            element2.id = "answer" + count + "question";
            element2.name = "answer" + count;
            element2.setAttribute('onkeypress', ' return check_content();');
            element2.onkeypress = function() {return check_content();};
            cell3.appendChild(element2);

           // alert(element2.id);
       }
       else
            window.alert ("Your question must not have more than 6 answers!");
   }

   function deleteAnswer(tableID) {

             try {
		if (count > 2){
                  var table = document.getElementById(tableID);
		  var rowCount = table.rows.length;

		  document.getElementById(tableID).deleteRow(rowCount - 1);
		  count--;
            	}
		else
		  alert("Your question must have at least 2 answers!");
	      }
            catch(e) {
                alert(e);
            }
   }
           </script>
</head>

<body>

  <ul id="menu" alt="basicMenuBar">
  <div id="contents1" alt="menuContents">
    <li><a href="#" class="logo" alt="logo"><span></span></a></li>
    <li><a href="Home.php" class="home" alt=""><span></span></a></li>
    <li><a href="#" class="questionsActive" alt="questionsMenu"><span></span></a></li>
    <li><a href="History.php" class="history" alt="historyMenu"><span></span></a></li>
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

    <div id="addNewQuestion" alt="titleOfAddQuestionPage"><p id="textTitleForAddNew">Ask Your Question</p></div>

    <div id="divAddQuestion" alt="formToAddNewQuestion">
      <form name="usersQuestion" id="submitNewQuestion">
    	<div id="divUserQuestion">
    	  Question:<input id="userQuestion" name="question" type="text" value="<?php echo $_POST['question']; echo $_SESSION['question']; ?>" onkeypress=" return check_content();"/>
    	</div>
	<table id="addAnswerTable" width="auto">
        <tr>
            <td>Answer</td>
            <td> 1 </td>
            <td><input id="answer1question" type="text" name="answer1" value="<?php echo $_SESSION['answer1'];?>" onkeypress=" return check_content();"></td>
        </tr>
        <tr>
            <td>Answer</td>
            <td> 2 </td>
            <td><input id="answer2question" type="text" name="answer2" value="<?php echo $_SESSION['answer2'];?>" onkeypress=" return check_content();"></td>
        </tr>
    	</table>

  	<input class="tabButtonsMain" id="addAnswerButton" type="button" value="Add Answer" onclick="addAnswer('addAnswerTable')" />
	<input class="tabButtonsMain" id="deleteAnswerButton" type="button" value="Remove Answer" onclick="deleteAnswer('addAnswerTable')" /><br>
        <input  class="tabButtonsMain" id="userSubmitNewQuestion" type="submit" value="Submit">
      </form>
    </div>
</div>

<div id=whiteSpace1>
<div id="DivVoteForQuestion"><p id="textTitleForAddNew">Vote on pending questions</p></div>
<div id="voteAndMoreQuestions">
<div id="questions">
<?php
$vote = 0;
$questionTitle = "Maximum of digits for the text is 50 ";
echo '<table CELLPADDING=2 CELLSPACING=0
     id="questionsTable" width="auto">
     <tr>
      <th>No.</th>
      <th colspan="2" align=left>Votes</th>
      <th>Type</th>
      <th>Question Title</th>
      <th>View More</th>
     </tr>';

$sql = "SELECT COUNT(*) "
       . "FROM Question "
       . "WHERE Question.Status = 'pending'";

$result = mysql_query($sql);

$count = mysql_result($result, 0);

if (isset($_GET['from']))
{
  $startValue = filter_input(INPUT_GET, 'from', FILTER_SANITIZE_STRING);
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
       . "WHERE Question.Status = 'pending' "
       . "ORDER BY Vote.cnt DESC "
       . "LIMIT ".($startValue - 1).", 20";

$questionIDs = mysql_query($sql);
$count1 = $startValue - 1;
$notTrue = 0;
//if($count <= 20)
//{
  for ($question = 1; $question <= $showQuestions; $question++ )
  {
	$questionID = mysql_result($questionIDs, ($question - 1));

	$sql = "SELECT Question "
	       . "FROM Question "
		   . "WHERE Question.QuestionID = '$questionID'";

	$result = mysql_query($sql);

	  $questionTitle = mysql_result($result, 0);

	  $sql = "SELECT COUNT(*) "
			 . "FROM Vote "
			 . "LEFT JOIN Question "
			 . "ON Question.QuestionID=Vote.QuestionID "
			 . "WHERE Question.Question = '$questionTitle'";

	  $result = mysql_query($sql);
	  $voteCount = mysql_result($result, 0);

	  $sql = "SELECT Vote.VoteID "
			 . "FROM Vote "
			 . "WHERE Vote.QuestionID = '$questionID' "
			 . "AND Vote.UserID = '$userID'";

	  $result = mysql_query($sql);

	  $voted = mysql_num_rows($result);

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
		if($voted > 0)
		{
			echo '<tr class="alt" style = "margin-top:5px">
				<td id="questionNo'.$questionID.'"><span id="questionNo">'.$count1.' </span></td>
				 <td id="voteResult'.$questionID.'"><span id="votes">'.$voteCount.' </span></td>
				 <td id="voteButtonCell'.$questionID.'"><input class="tabButtonsMain" id="voteButton" type=button value="Vote" style="border-color:#FF9966;border-width:3px"></td>
					<td style="width:40px; "><img id="questionInfoIcon" src='.$specialInfo.' style="float:left" width="32" height="32"></td>
					<td id="questionCell'.$questionID.'">'.$questionTitle.'</td>
				  <td id="seeMoreCell'.$questionID.'"><input class="tabButtonsMain" id="buttonSeeMore" type=button onClick="location.href="" value="View"></td>
				  </tr>';
		}
		else
		{
				echo '<tr class="alt" style = "margin-top:5px">
				<td id="questionNo'.$questionID.'"><span id="questionNo">'.$count1.' </span></td>
				 <td id="voteResult'.$questionID.'"><span id="votes">'.$voteCount.' </span></td>
				 <td id="voteButtonCell'.$questionID.'"><input class="tabButtonsMain" id="voteButton" type=button value="Vote"></td>
				 <td style="width:40px; "><img id="questionInfoIcon" src='.$specialInfo.' style="float:left" width="32" height="32"></td>
				<td id="questionCell'.$questionID.'">'.$questionTitle.'</td>
				  <td id="seeMoreCell'.$questionID.'"><input class="tabButtonsMain" id="buttonSeeMore" type=button onClick="location.href="" value="View"></td>
				  </tr>';
		}
	}
	else
	{
		$notTrue = 1;
	}


  }
  if( $notTrue == 0){
$newStart = $startValue + 20;
$link = "'Questions.php?from=".$newStart."'";
//echo $blah;
echo '</table><div id="centeredMoreQuestionsButton">
<input class="tabButtonsMain" id="moreQuestions" type="button" style="float:center" onClick="parent.location='.$link .'" value="More Questions"/>
</div><br>';}
if( $startValue > 20){
$previousStart = $startValue - 20;
$linkBack = "'Questions.php?from=".$previousStart."'";
//echo $blah;
echo '</table><div id="centeredMoreQuestionsButton">
<input class="tabButtonsMain" id="moreQuestions" type="button" style="float:center" onClick="parent.location='.$linkBack .'" value="Back"/>
</div>';}
/*}

else if($count > 20)
{
  while ($question <= 20) {

	$questionID = mysql_result($questionIDs, ($question - 1));

	$sql = "SELECT Question "
	       . "FROM Question "
		   . "WHERE Question.QuestionID = '$questionID'";

	$result = mysql_query($sql);

	  $questionTitle = mysql_result($result, 0);

  $sql = "SELECT COUNT(*) "
         . "FROM Vote "
         . "LEFT JOIN Question "
         . "ON Question.QuestionID=Vote.QuestionID "
         . "WHERE Question.Question = '$questionTitle'";

  $result = mysql_query($sql);
  $voteCount = mysql_result($result, 0);

	  $sql = "SELECT Vote.VoteID "
			 . "FROM Vote "
			 . "WHERE Vote.QuestionID = '$questionID' "
			 . "AND Vote.UserID = '$userID'";

	  $result = mysql_query($sql);

	  $voted = mysql_num_rows($result);

	  if($voted > 0)
	  {
		echo '<tr class="alt" style = "margin-top:5px">
			<td id="questionNo'.$questionID.'"><span id="questionNo">'.$question.' </span></td>
			 <td id="voteResult'.$questionID.'"><span id="votes">'.$voteCount.' </span></td>
			 <td id="voteButtonCell'.$questionID.'"><input class="tabButtonsMain" id="voteButton" type=button value="Vote" style="border-color:#FF9966;border-width:3px"></td>
			 <td id="questionType'.$questionID.'">type</td>
			<td id="questionCell'.$questionID.'">'.$questionTitle.'</td>
			  <td id="seeMoreCell'.$questionID.'"><input class="tabButtonsMain" id="buttonSeeMore" type=button onClick="location.href="" value="View"></td>
			  </tr>';
	  }
	  else
	  {
	  		echo '<tr class="alt" style = "margin-top:5px">
			<td id="questionNo'.$questionID.'"><span id="questionNo">'.$question.' </span></td>
			 <td id="voteResult'.$questionID.'"><span id="votes">'.$voteCount.' </span></td>
			 <td id="voteButtonCell'.$questionID.'"><input class="tabButtonsMain" id="voteButton" type=button value="Vote"></td>
			 <td id="questionType'.$questionID.'">type</td>
			<td id="questionCell'.$questionID.'">'.$questionTitle.'</td>
			  <td id="seeMoreCell'.$questionID.'"><input class="tabButtonsMain" id="buttonSeeMore" type=button onClick="location.href="" value="View"></td>
			  </tr>';
	  }


	$question = $question + 1;

  }
  $newStart = $question;
}*/

?>

</div>
</div>
</div>
<script>
$(document).ready(function(){
	$('input[id="voteButton"]').click(function(event) {
		event.preventDefault();
		var tdid = $(this).closest('td').attr('id'); // table row ID
		var questionID = tdid.substring(14);
		var noLogin = <?php echo($notLoggedIn); ?>;

		if(noLogin == true)
		{
			var loginNavigate = confirm("To vote on a question you must be logged in, login now?");
			if(loginNavigate == true)
			{
				window.location.replace("Account.php");
			}
		}
		else
		{
			$.post("voteQuestion.php", { questionID : questionID }, function(success) {
				if(success == "added")
				{
					event.target.style.borderColor="#FF9966";
					event.target.style.borderWidth="3px";
					var currentVotes = $('#voteResult' + questionID + ' span').html();
					$('#voteResult' + questionID + ' span').html(parseInt(currentVotes) + 1);
				}
				else if(success == "deleted")
				{
					event.target.style.borderColor=null;
					event.target.style.borderWidth=null;
					var currentVotes = $('#voteResult' + questionID + ' span').html();
					$('#voteResult' + questionID + ' span').html(parseInt(currentVotes) - 1);
				}
				else
				{
					alert("Something went wrong, please try again");
				}
			});
		}
	});
});

$(document).ready(function(){
	$('input[id="buttonSeeMore"]').click(function(event) {
		event.preventDefault();
		var tdid = $(this).closest('td').attr('id'); // table row ID
		var questionID = tdid.substring(11);
		window.location.replace("View.php?questionID=" + questionID);
	});
});

function checkQuestion()
{
	var noLogin = <?php echo($notLoggedIn); ?>;

	if(noLogin == true)
	{
		var loginNavigate = confirm("To ask a question you must be logged in, login now?");
		if(loginNavigate == true)
		{
			window.location.replace("Account.php");
			return false;
		}
	}
	else
	{
		if(document.getElementById('userQuestion').value == null || document.getElementById('userQuestion').value == '')
		{
			alert("You must enter a question.")
			return false;
		}
		else if(document.getElementById('answer1question').value == null || document.getElementById('answer1question').value == '' ||
				document.getElementById('answer2question').value == null || document.getElementById('answer2question').value == '')
		{
			alert("You must enter at least two answers.")
			return false;
		}
		else
		{
			return true;
		}
	}
}

function check_content(){

	var noLogin = <?php echo($notLoggedIn); ?>;

	if(noLogin == true)
	{
		var loginNavigate = confirm("To ask a question you must be logged in, login now?");
		if(loginNavigate == true)
		{
			window.location.replace("Account.php");
		}
	}

    var question = document.getElementById('userQuestion').value;
    var answer1 = document.getElementById('answer1question').value;
    var answer2 = document.getElementById('answer2question').value;
    var answer3 = null;
    var answer4 = null;
    var answer5 = null;
    var answer6 = null;
    if(document.getElementById('answer3question') != null)
    {
        var answer3 = document.getElementById('answer3question').value;
    }
    if(document.getElementById('answer4question') != null)
    {
	var answer4 = document.getElementById('answer4question').value;
    }
    if(document.getElementById('answer5question') != null)
    {
	var answer5 = document.getElementById('answer5question').value;
    }
    if(document.getElementById('answer6question') != null)
    {
	var answer6 = document.getElementById('answer6question').value;
    }

    if(question.length > 50){
        alert('Length of question must not exceed 50 characters!');
        return false;
    } else if(answer1.length > 20){
        alert('Length of answer must not exceed 20 characters!');
        return false;
    } else if(answer2.length > 20){
        alert('Length of answer must not exceed 20 characters!');
        return false;
    } else if(answer3.length > 20){
        alert('Length of answer must not exceed 20 characters!');
        return false;
    } else if(answer4.length > 20){
        alert('Length of answer must not exceed 20 characters!');
        return false;
    } else if(answer5.length > 20){
        alert('Length of answer must not exceed 20 characters!');
        return false;
    } else if(answer6.length > 20){
        alert('Length of answer must not exceed 20 characters!');
        return false;
    } else {
        return true;
    }
}

$(document).ready(function(){
	$('#submitNewQuestion').submit(function(event) {
		event.preventDefault();
		if(checkQuestion())
		{
			var question = document.getElementById('userQuestion').value;
			var answer1 = document.getElementById('answer1question').value;
			var answer2 = document.getElementById('answer2question').value;
			var answer3 = null;
			var answer4 = null;
			var answer5 = null;
			var answer6 = null;
			if(document.getElementById('answer3question') != null)
			{
				var answer3 = document.getElementById('answer3question').value;
			}
			if(document.getElementById('answer4question') != null)
			{
				var answer4 = document.getElementById('answer4question').value;
			}
			if(document.getElementById('answer5question') != null)
			{
				var answer5 = document.getElementById('answer5question').value;
			}
			if(document.getElementById('answer6question') != null)
			{
				var answer6 = document.getElementById('answer6question').value;
			}

			$.post("CreateQuestion.php", { question : question, answer1 : answer1, answer2 : answer2, answer3 : answer3, answer4 : answer4, answer5 : answer5, answer6 : answer6 }, function(success){
				if(success != false)
				{
					var questionID = success;
					alert("Question created successfully!");
					window.location.replace("View.php?questionID=" + questionID);
				}
				else
				{
					alert("Something went wrong, please try again.");
				}
			});
		}
	});
});
</script>
</body>

</html>
