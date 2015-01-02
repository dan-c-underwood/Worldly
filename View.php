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

  $questionID = filter_input(INPUT_GET, 'questionID', FILTER_SANITIZE_STRING);

  $sql = "SELECT Status"
            . " FROM Question"
            . " WHERE Question.QuestionID ='$questionID'";

	$query = mysql_query($sql);

	$status = mysql_result($query, 0);

  $sql = "SELECT Question"
              . " FROM Question"
              . " WHERE Question.QuestionID ='$questionID'";

	$query = mysql_query($sql);

	$question = mysql_result($query, 0);

  for($answerNo = 1; $answerNo <= 6; $answerNo++)
  {
    $sql = "SELECT Answer" . $answerNo
           . " FROM Question"
	   . " WHERE Question.QuestionID = '$questionID'";

    $query = mysql_query($sql);

	if(mysql_result($query, 0) != null)
	{
	  $results[$answerNo] = mysql_result($query, 0);
	}
	else
	{
	  $results[$answerNo] = null;
	}
  }

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

  if(@$_POST['submit'])
  {
    mysqli_query($connection,"INSERT INTO Vote (QuestionId, UserId)
    VALUES ('QuestionID', 'UserId')");
  }
?>

<!DOCTYPE html>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Worldly: You Ask It, We Map It</title>

<head>
<script type="text/javascript">

var map;
var questionMap;
var answerSet;

function question(questionID)
{
  $.post("pullResults.php", { 'questionID' : questionID, dataType: "question" }, function(data){
    var question = data;
	var status = '<?php echo $status; ?>';
	if(status == 'reported')
	{
		document.getElementById("questionTitle").innerHTML = "This question has been reported";
		document.getElementById("questionTitle").style.color = 'red';
	}
	else
	{
		document.getElementById("questionTitle").innerHTML = question;
	}
	questionMap = new map(questionID);
  });
}

function answers(questionID)
{
	var status = '<?php echo $status ?>';
	if(status != 'reported')
	{
		$.post("pullResults.php", { 'questionID' : questionID, dataType: "answers" }, function(answerData){
			answers = $.parseJSON(answerData);
			totalAnswers = 0;
			for(var answerID = 1; answerID <= 6; answerID++)
			{
				if(answers[answerID] != null && answers[answerID] != '')
				totalAnswers = totalAnswers + 1;
			}

			$.ajax({
				type: "POST",
				url: "getAnswer.php",
				data: { questionID : questionID },
				dataType: "html",
				async: false
			}).done(function(selectedAnswer) {

			answerButtons("addAnswerTable", totalAnswers, answers, selectedAnswer);
		});
	  });
	}
}

function answerButtons(tableID, numAnswers, answers, selectedAnswer)
{
	var divWidth = (151 * numAnswers) + 'px';

	var answer = 1;

	while(answer <= numAnswers)
	{
	  document.getElementById('holdAllAnswers').style.width = divWidth;
	  var firstRow=document.getElementById(tableID).rows[0];
	  var x=firstRow.insertCell(-1);

	  var button = document.createElement("input");
	  button.type="submit";
	  button.id="answer" + answer + "question";
	  button.onclick= function() { answerQuestion(this.id) };
	  button.value=answers[answer];

	  if(answer == selectedAnswer)
	  {
	    switch(answer)
		{
			case 1 : highlightColor = "#FF8080"; break;
			case 2 : highlightColor = "#99FF99"; break;
			case 3 : highlightColor = "#FFFF99"; break;
			case 4 : highlightColor = "#FF66FF"; break;
			case 5 : highlightColor = "#99FFFF"; break;
			case 6 : highlightColor = "#CC99FF"; break;
		}
		button.style.backgroundColor=highlightColor;
	  }

	  x.appendChild(button);

	  answer++;
	}

}


var key = 'e3989c63a20c4e83aa5d3644bc050b27';
var tiles = 'http://{s}.tile.cloudmade.com/'+key+'/22677/256/{z}/{x}/{y}.png';
var attr = 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>';

function map(questionID)
{
var dataInput;
var mapName = "map";
var allRegionsTotal = 0;

map = L.map(mapName).setView([56.477, -2.246], 5);

L.tileLayer(tiles, {
                attribution: attr
      }).addTo(map);

function getColour(givenRef)
{
	var colour = 'white';
	var maxAnswer = 0;
	var maxVotes = 0;
	var i;

	for(i = 1; i <= 6; i++)
	{
	  if(dataInput[givenRef][i.toString()] > maxVotes)
	  {
		maxAnswer = i;
		maxVotes = dataInput[givenRef][i.toString()];
	  }
	  else if(dataInput[givenRef][i.toString()] == maxVotes)
	  {
		maxAnswer = 0;
	  }
	}

	colour = getColourHex(maxAnswer);
	return colour;
}

function getColourHex(value)
{
return value == 1 ? '#FF8080':
value == 2 ? '#99FF99':
value == 3 ? '#FFFF99':
value == 4 ? '#FF66FF':
value == 5 ? '#99FFFF':
value == 6 ? '#CC99FF':
'white';
}

var dataLayer;

function createDataLayer()
    {
map.removeLayer(dataLayer);
$.post("pullResults.php", { 'questionID' : questionID }, function(data){
dataInput = $.parseJSON(data);
dataLayer = L.geoJson(ukBoundaries, {
style: style,
onEachFeature: onEachFeature
});
      dataLayer.addTo(map);
});
}

function style(feature) {
return {
fillColor: getColour(feature.properties.ref),
weight: 2,
opacity: 1,
color: 'white',
dashArray: '3',
fillOpacity: 0.7
};
    }

    function highlightFeature(e) {
var layer = e.target;

layer.setStyle({
weight: 5,
color: '#666',
dashArray: '',
fillOpacity: 0.7
});

if (!L.Browser.ie && !L.Browser.opera) {
layer.bringToFront();
}

info.update(layer.feature.properties);
results.update(layer.feature.properties);
    }

    function resetHighlight(e) {
dataLayer.resetStyle(e.target);
info.update();
results.update();
    }

    function zoomToFeature(e) {
map.fitBounds(e.target.getBounds());
}

function onEachFeature(feature, layer) {
layer.on({
mouseover: highlightFeature,
mouseout: resetHighlight,
click: zoomToFeature
});
}

var dataInput;

$.post("pullResults.php", { 'questionID' : questionID, dataType: "map" }, function(data){
	dataInput = $.parseJSON(data);
	for(var ref in dataInput)
	{
		for(var index in dataInput[ref])
		{
		  allRegionsTotal += dataInput[ref][index];
		}
	}
	results.update();
    dataLayer = L.geoJson(ukBoundaries, {
    style: style,
    onEachFeature: onEachFeature
  });
  dataLayer.addTo(map);
  answerSet = new answers(questionID);
});

var results = L.control();

results.onAdd = function () {
this._div = L.DomUtil.create('div', 'results');
this.update();
return this._div;
};

results.update = function (props) {
this._div.innerHTML = '<h4>Results</h4>' + (props ?
'<b>' + generateResults(props.ref) + '</b><br />'
: 'Total responses for all regions: ' + allRegionsTotal);
};

function generateResults(ref)
{
	var resultsString = "";
	var totalVotes = 0.0;

	for(var i = 1; i <= totalAnswers; i++)
	{
	  totalVotes += parseFloat(dataInput[ref][i]);
	}
	for(var i = totalAnswers; i >= 1; i--)
	{
	  resultsString += ('<div style = "text-shadow: black 0.1em 0.1em 0.2em; margin-left:5px; float:right; color:' + getColourHex(i) + '"> ' + i + ": ")
	  if(totalVotes != 0)
	    resultsString += ((parseInt((parseFloat(dataInput[ref][i]) / totalVotes) * 100)) + "% ");
      else
	    resultsString += "0% ";
      resultsString += "</div>";
	}
	resultsString += ("<br><br> Total responses in this region: " + totalVotes)
	return resultsString;
}

results.addTo(map);

var info = L.control();

info.onAdd = function () {
this._div = L.DomUtil.create('div', 'info'); // create a div with a class "info"
this.update();
return this._div;
};


// method that we will use to update the control based on feature properties passed
info.update = function (props) {
this._div.innerHTML = '<h4>World Region Map</h4>' + (props ?
'<b>' + props.name + '</b><br />'
: 'Hover over a region');
};

    info.addTo(map);
}

function answerQuestion(answerValue)
{
	answerValue = answerValue.charAt(6);
	var questionStatus = "<?php echo($status); ?>";
	var noLogin = <?php echo($notLoggedIn); ?>;

	if(questionStatus == 'active')
	{
		if(noLogin == true)
		{
			var loginNavigate = confirm("To answer a question you must be logged in, login now?");
			if(loginNavigate == true)
			{
				window.location.replace("Account.php");
			}
		}
		else
		{
			for(var i = 1; i <= 6; i++)
			{
			  document.getElementById('answer' + i + 'question').style.backgroundColor = null;
			}
			var successColor;
			$.post("answerQuestion.php", { questionID : <?php echo($questionID); ?>, answer : answerValue }, function(success){
			  answerValue = parseInt(answerValue);
			  if(success)
			  {
				window.location.reload(true);
			  }
			  else
			  {
				alert("Something went wrong, please try again.");
			  }
			});
		}
	}
}
</script>

<script>
  var status = '<?php echo $status; ?>';
	function pageStyle()
        {
	    if(status == pending)
        {
	        document.getElementById('pagestyle').setAttribute('href', "ViewPageCSS.css");
	    }
		else if(status == closed)
             {
				  document.getElementById('pagestyle').setAttribute('href', "ViewpageCSS2.css");
				  var Vote = document.getElementById('Vote');
				  Vote.innerHTML = '';
            }
        }
</script>
<script>style = new pageStyle();</script>
<link id="pagestyle" rel="stylesheet" type="text/css" href="">
</head>
<script type="text/javascript">
  var question = '<?php echo $question; ?>';
  	function question()
  	{
        document.getElementById("questionTitle").innerHTML = '<h1>' + question + '</h1'>;
    }
</script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css" />
<script type="text/javascript" src="http://cdn.leafletjs.com/leaflet-0.5/leaflet.js"></script>
<script src="uk-boundaries.js"></script>
<script src="http://code.jquery.com/jquery-1.8.3.js"></script>
<script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>
<link rel="stylesheet" type="text/css" href="WorldlyCSS.css">
<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.5/leaflet.css" />
<link rel="icon" href="globe.ico" type="image/x-icon">
<link rel="shortcut icon" href="globe.ico" type="image/x-icon">
<body>

  <ul id="menu" alt="basicMenuBar">
  <div id="contents1" alt="menuContents">
    <li><a href="#" class="logo" alt="logo"><span></span></a></li>
    <li><a href="Home.php" class="home"><span></span></a></li>
    <li><a href="Questions.php" class="questions" alt="questionsMenu"><span></span></a></li>
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
   <div id="whiteSpace" style="padding-bottom:40px" alt="workingArea">

   <div id="fb_share">
        <?php
		$url=urlencode('http://potnoodle.cs.man.ac.uk/~underwd2/Questions.php');
   		$summary=urlencode('I just voted on a Question! Visit Worldly, so you can vote too!');
		$image=urlencode('http://potnoodle.cs.man.ac.uk/~underwd2/facebookWorldly.jpg');
        ?>

        <a onClick="window.open('http://www.facebook.com/sharer.php?s=100&amp;p[title]=<?php echo $question;?>&amp;p[summary]=<?php echo $summary;?>&amp;p[url]=<?php echo $url; ?>&amp;&amp;p[images][0]=<?php echo $image;?>','sharer','toolbar=0,status=0,width=548,height=325');" href="javascript: void(0)">
           <img src="facebook.jpg" alt="Facebook" width="60" height="18">
        </a>
   </div>

   <table style="font-color:black;margin-left:auto; margin-right:auto;">
    <tr>
	<td style="width:40px; "><img id="questionIcon" src="<?php echo $specialInfo; ?>" style="float:left" width="32" height="32"></td>
	<td><div id="questionTitle"></div></td>
    </tr>
   </table>
   <?php
   if($status == 'pending')
   {
		echo '<div id="map" style="display:none;" alt="map"></div>';
   }
   else
   {
		echo '<div id="map" alt="map"></div>';
   }
   ?>
	 <div id="holdAnswers">
		<div id="holdAllAnswers">

		<table id="addAnswerTable" alt="buttonsForAnswer">
			<tr>
			</tr>
		</table>
	<script>question = new question(<?php echo $questionID; ?>);</script>
	</div>
	</div>

        <div id="Vote" style="margin-top:20px">
	<?php

	$sql = "SELECT Vote.VoteID "
		. "FROM Vote "
		. "WHERE Vote.QuestionID = '$questionID' "
		. "AND Vote.UserID = '$userID'";

	 $result = mysql_query($sql);

	 $voted = mysql_num_rows($result);

   if($status == 'pending')
   {
		if($voted > 0)
		{
			echo '<input class="tabButtonsMain" id="voteButton" style="margin-left:5px;width:130px;border-color:#FF9966;border-width:3px" onClick="vote()" type="button" value="Vote"/>';
		}
		else
		{
			echo '<input class="tabButtonsMain" id="voteButton" style="margin-left:5px; width:130px;" onClick="vote()" type="button" value="Vote"/>';
		}
	echo '<form METHOD="Link" ACTION="Questions.php"><input class="tabButtonsMain" style="float:right; margin-right:5px" type="submit" value="Go back"/></form>';
   }

   else if($status == 'closed')
   {
      echo '<form action="generateData.php" method="post">
				<input type="hidden" name="questionID" value='.$questionID.' />
				<input class="tabButtonsMain" id="dataButton" style="margin-left:5px; width:130px;" type="submit" value="Download Data"/>
			</form>';
	  echo '<form METHOD="Link" ACTION="History.php"><input class="tabButtonsMain" style="float:right; margin-right:5px" type="submit" value="Go back"/></form>';
   }
   else
   {
   	 echo '<form METHOD="Link" ACTION="Home.php"><input class="tabButtonsMain" style="float:right; margin-right:5px" type="submit" value="Go back"/></form>';
   }

	if($status != 'reported')
	{
		echo '<input class="deleteB" id="reportButton" style="float:left; margin-left:5px" onClick="report()" type="button" value="Report"/>';
	}
   ?>

        </div>

</div>

</body>
<script>
function vote() {
	var questionID = <?php echo($questionID); ?>;
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
				voteButton.style.borderColor="#FF9966";
				voteButton.style.borderWidth="3px";
				alert("Your vote has been registered successfully!");
			}
			else if(success == "deleted")
			{
				voteButton.style.borderColor=null;
				voteButton.style.borderWidth=null;
			}
			else
			{
				alert("Something went wrong, please try again");
			}
		});
	}
}

function report() {
	var questionID = <?php echo($questionID); ?>;

	if(confirm("Are you sure you want to report this question?"))
	{
		$.post("reportQuestion.php", { questionID : questionID }, function(success) {
			if(success)
			{
				alert("Question reported successfully.");
				window.location.reload(true);
			}
			else
				alert("Something went wrong.");
		});
	}
}
</script>
</html>


</html>
