<?php
session_start();
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

$sql = "SELECT QuestionID"
       . " FROM Question"
       . " WHERE Question.Status = 'active'"
	   . " ORDER BY FIELD(SpecialInfo, 'featured', 'sponsored', 'normal'), QuestionID";

$questionIDs = mysql_query($sql);

$specialInfo = array();

for($i = 1; $i <= 5; $i++)
{
	$questionID = mysql_result($questionIDs, $i - 1);

	$sql = "SELECT SpecialInfo "
		   . "FROM Question "
		   . "WHERE Question.QuestionID = '$questionID'";

	$result = mysql_query($sql);

	$specialInfo[$i] = mysql_result($result, 0);

	switch($specialInfo[$i])
	{
		case "normal":
			$specialInfo[$i] = "greyGlobe.png";
			break;
		case "featured":
			$specialInfo[$i] = "blueGlobe.png";
			break;
		case "sponsored":
			$specialInfo[$i] = "goldenGlobe.png";
			break;
	}
}

?>
<!DOCTYPE html>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Worldly: You Ask It, We Map It</title>

<head>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css" />
<script type="text/javascript" src="http://cdn.leafletjs.com/leaflet-0.5/leaflet.js"></script>
<script src="uk-boundaries.js"></script>
<script src="http://code.jquery.com/jquery-1.8.3.js"></script>
<script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>
<link rel="stylesheet" type="text/css" href="WorldlyCSS.css">
<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.5/leaflet.css" />
<link rel="icon" href="globe.ico" type="image/x-icon">
<link rel="shortcut icon" href="globe.ico" type="image/x-icon">
<!--[if lte IE 8]>
<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.4/leaflet.ie.css" />
<![endif]-->

</head>
<script type="text/javascript">

var map;
var questionMap;
var answerSet;
var totalAnswers = new Array();

function question(mapNo)
{
  $.post("returnActiveQuestion.php", { 'mapNo' : mapNo }, function(data){
    var returnValues = $.parseJSON(data);
    var questionID = returnValues.questionID[0];
    var question = returnValues.question;
    document.getElementById("questionTitle" + mapNo).innerHTML = question;
	questionMap[mapNo] = new map(mapNo, questionID);
  });
}

function answers(mapNo, questionID)
{
  $.post("pullResults.php", { 'questionID' : questionID, dataType: "answers" }, function(answerData){
	answers = $.parseJSON(answerData);
	totalAnswers[mapNo] = 0;
	for(var answerID = 1; answerID <= 6; answerID++)
	{
	  if(answers[answerID] != null && answers[answerID] != '')
	    totalAnswers[mapNo] = totalAnswers[mapNo] + 1;
	}

	$.ajax({
	  type: "POST",
	  url: "getAnswer.php",
	  data: { questionID : questionID },
	  dataType: "html",
	  async: false
	  }).done(function(selectedAnswer) {
		  answerButtons("addAnswerTable" + mapNo, totalAnswers[mapNo], mapNo, answers, selectedAnswer);
	});
  });
}

function answerButtons(tableID, numAnswers, numQuestion, answers, selectedAnswer)
{
	var divWidth = (151 * numAnswers) + 'px';

	var answer = 1;

	while(answer <= numAnswers)
	{
	  document.getElementById('holdAllAnswers' + numQuestion).style.width = divWidth;
	  var firstRow=document.getElementById(tableID).rows[0];
	  var x=firstRow.insertCell(-1);

	  var button = document.createElement("input");
	  button.type="submit";
	  button.id="answer" + answer + "question" + numQuestion;
	  button.onclick= function() { answerQuestion(numQuestion, this.id) };
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

function map(mapNo, questionID)
{
var dataInput;
var mapName = "map" + mapNo;
var allRegionsTotal = 0;

map[mapNo] = L.map(mapName).setView([56.477, -2.246], 5);

L.tileLayer(tiles, {
                attribution: attr
      }).addTo(map[mapNo]);

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
/*
function createDataLayer(mapNo)
    {
map[mapNo].removeLayer(this.layer);
$.post("pullResults.php", { 'questionID' : questionID }, function(data){
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
  dataLayer.addTo(map[mapNo]);
});
}
*/
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
map[mapNo].fitBounds(e.target.getBounds());
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
  dataLayer.addTo(map[mapNo]);
  answerSet[mapNo] = new answers(mapNo, questionID);
});

var results = L.control();

results.onAdd = function () {
this._div = L.DomUtil.create('div', 'results');
this.update();
return this._div;
};

results.update = function (props) {
this._div.innerHTML = '<h4>Results</h4>' + (props ?
'<b>' + generateResults(props.ref, mapNo) + '</b><br />'
: 'Total responses for all regions: ' + allRegionsTotal);
};

function generateResults(ref, mapNo)
{
	var resultsString = "";
	var totalVotes = 0.0;

	for(var i = 1; i <= totalAnswers[mapNo]; i++)
	{
	  totalVotes += parseFloat(dataInput[ref][i]);
	}
	for(var i = totalAnswers[mapNo]; i >= 1; i--)
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

results.addTo(map[mapNo]);

var info = L.control();

info.onAdd = function () {
this._div = L.DomUtil.create('div', 'info');
this.update();
return this._div;
};


// method that we will use to update the control based on feature properties passed
info.update = function (props) {
this._div.innerHTML = '<h4>  World Region Map</h4>' + (props ?
'<b>' + props.name + '</b><br />'
: 'Hover over a region');
};

info.addTo(map[mapNo]);

}

function answerQuestion(numQuestion, answerValue)
{
	answerValue = answerValue.charAt(6);

	var noLogin = <?php echo($notLoggedIn); ?>;

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
		for(var i = 1; i <= totalAnswers[numQuestion]; i++)
		{
		  document.getElementById('answer' + i + 'question' + numQuestion).style.backgroundColor = null;
		}
		var successColor;
		$.post("answerQuestion.php", { questionID : numQuestion, answer : answerValue }, function(success){
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

</script>

<body>

  <ul id="menu" alt="basicMenuBar">
  <div id="contents1" alt="menuContents">
    <li><a href="#" class="logo" alt="logo"><span></span></a></li>
    <li><a href="#" class="homeActive" alt=""><span></span></a></li>
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

 <div id="AskArea" alt="workingArea">

    <div id="questionSpace" alt="fieldToTypeQuestion">
      <table id="askMe" alt="tableWithButtonAndTextfield">
        <tr id="askMe" alt="tableRow">
        <form name='askQuestion' method='post' action='Questions.php'>
	<td><input id="question" type="text" name="question" alt="typeQuestion" style="color:#5F5F5F" placeholder="What do you want to ask?"></td>
	<td> <input class="SpecialButtons" type="submit" value="Ask!" alt="buttonASK"/></td>
	</form>
	</tr>
      </table>
   </div>
   </div>
    <div id="fastLinks">
<input class="tabButtons" type="submit" value="Question 1" onClick="parent.location='#questionTitle1'"/>
<input class="tabButtons" type="submit" value="Question 2" onClick="parent.location='#questionTitle2'"/>
<input class="tabButtons" type="submit" value="Question 3" onClick="parent.location='#questionTitle3'"/>
<input class="tabButtons" type="submit" value="Question 4" onClick="parent.location='#questionTitle4'"/>
<input class="tabButtons" type="submit" value="Question 5" onClick="parent.location='#questionTitle5'"/>
</div>

   <div id="whiteSpace" style="padding-bottom:70px" alt="workingArea">
   <div id="fb_share">
        <?php
		$questionID = mysql_result($questionIDs, 0);
		$sql = "SELECT Question "
				. "FROM Question "
				. "WHERE Question.QuestionID = '$questionID'";
		$result = mysql_query($sql);
		$question = mysql_result($result, 0);
		$url=urlencode('http://potnoodle.cs.man.ac.uk/~underwd2/View.php?questionID='.$questionID.'');
   		$summary=urlencode('I just answered on a Question! Visit Worldly, so you can answer questions too!');
		$image=urlencode('http://potnoodle.cs.man.ac.uk/~underwd2/facebookWorldly.jpg');
        ?>

        <a onClick="window.open('http://www.facebook.com/sharer.php?s=100&amp;p[title]=<?php echo $question;?>&amp;p[summary]=<?php echo $summary;?>&amp;p[url]=<?php echo $url; ?>&amp;&amp;p[images][0]=<?php echo $image;?>','sharer','toolbar=0,status=0,width=548,height=325');" href="javascript: void(0)">
           <img src="facebook.jpg" alt="Facebook" width="60" height="18">
        </a>
   </div>

   <table style="font-color:black;margin-left:auto; margin-right:auto;">
	<tr>
	<td style="width:40px; "><img id="question1Icon" src="<?php echo $specialInfo[1]; ?>" style="float:left" width="32" height="32"></td>
   	<td><div id="questionTitle1"></div></td>
	</tr>
	</table>

    <div id="map1" alt="map1"></div>
	<div id="holdAnswers">
		<div id="holdAllAnswers1">

		<table id="addAnswerTable1" alt="buttonsForAnswer">
			<tr>
			</tr>
		</table>

	<script>question1 = new question(1);</script>
   </div>
    <input class="tabButtonsMain"style="float:right; margin-right:5px" type="submit" value="Back to top" onClick="parent.location='#menu'"/>
   </div></div>

   <div id="whiteSpace" style="padding-bottom:70px" alt="workingArea">
     <div id="fb_share">
        <?php
		$questionID = mysql_result($questionIDs, 1);
		$sql = "SELECT Question "
				. "FROM Question "
				. "WHERE Question.QuestionID = '$questionID'";
		$result = mysql_query($sql);
		$question = mysql_result($result, 0);
		$url=urlencode('http://potnoodle.cs.man.ac.uk/~underwd2/View.php?questionID='.$questionID.'');
   		$summary=urlencode('I just answered on a Question! Visit Worldly, so you can answer questions too!');
		$image=urlencode('http://potnoodle.cs.man.ac.uk/~underwd2/facebookWorldly.jpg');
        ?>

        <a onClick="window.open('http://www.facebook.com/sharer.php?s=100&amp;p[title]=<?php echo $question;?>&amp;p[summary]=<?php echo $summary;?>&amp;p[url]=<?php echo $url; ?>&amp;&amp;p[images][0]=<?php echo $image;?>','sharer','toolbar=0,status=0,width=548,height=325');" href="javascript: void(0)">
           <img src="facebook.jpg" alt="Facebook" width="60" height="18">
        </a>
     </div>

     <table style="font-color:black;margin-left:auto; margin-right:auto;">
	<tr>
	<td style="width:40px; "><img id="question2icon" src="<?php echo $specialInfo[2]; ?>" style="float:left" width="32" height="32"></td>
   	<td><div id="questionTitle2"></div></td>
	</tr>
	</table>

     <div id="map2" alt="map2"></div>
     <div id="holdAnswers">
		<div id="holdAllAnswers2">

		<table id="addAnswerTable2" alt="buttonsForAnswer">
			<tr>
			</tr>
		</table>



	<script>question2 = new question(2);</script>
   </div>
    <input class="tabButtonsMain"style="float:right; margin-right:5px" type="submit" value="Back to top" onClick="parent.location='#menu'"/>
   </div></div>

   <div id="whiteSpace" style="padding-bottom:70px" alt="workingArea">
     <div id="fb_share">
        <?php
		$questionID = mysql_result($questionIDs, 2);
		$sql = "SELECT Question "
				. "FROM Question "
				. "WHERE Question.QuestionID = '$questionID'";
		$result = mysql_query($sql);
		$question = mysql_result($result, 0);
		$url=urlencode('http://potnoodle.cs.man.ac.uk/~underwd2/View.php?questionID='.$questionID.'');
   		$summary=urlencode('I just answered on a Question! Visit Worldly, so you can answer questions too!');
		$image=urlencode('http://potnoodle.cs.man.ac.uk/~underwd2/facebookWorldly.jpg');
        ?>

        <a onClick="window.open('http://www.facebook.com/sharer.php?s=100&amp;p[title]=<?php echo $question;?>&amp;p[summary]=<?php echo $summary;?>&amp;p[url]=<?php echo $url; ?>&amp;&amp;p[images][0]=<?php echo $image;?>','sharer','toolbar=0,status=0,width=548,height=325');" href="javascript: void(0)">
           <img src="facebook.jpg" alt="Facebook" width="60" height="18">
        </a>
     </div>

    <table style="font-color:black;margin-left:auto; margin-right:auto;">
	<tr>
	<td style="width:40px; "><img id="question3icon" src="<?php echo $specialInfo[3]; ?>" style="float:left" width="32" height="32"></td>
   	<td><div id="questionTitle3"></div></td>
	</tr>
	</table>
     <div id="map3" alt="map3"></div>

     <div id="holdAnswers">
		<div id="holdAllAnswers3">

		<table id="addAnswerTable3" alt="buttonsForAnswer">
			<tr>
			</tr>
		</table>



	<script>question3 = new question(3);</script>
   </div>
    <input class="tabButtonsMain"style="float:right; margin-right:5px" type="submit" value="Back to top" onClick="parent.location='#menu'"/>
   </div></div>

  <div id="whiteSpace" style="padding-bottom:70px" alt="workingArea">
     <div id="fb_share">
        <?php
		$questionID = mysql_result($questionIDs, 3);
		$sql = "SELECT Question "
				. "FROM Question "
				. "WHERE Question.QuestionID = '$questionID'";
		$result = mysql_query($sql);
		$question = mysql_result($result, 0);
		$url=urlencode('http://potnoodle.cs.man.ac.uk/~underwd2/View.php?questionID='.$questionID.'');
   		$summary=urlencode('I just answered on a Question! Visit Worldly, so you can answer questions too!');
		$image=urlencode('http://potnoodle.cs.man.ac.uk/~underwd2/facebookWorldly.jpg');
        ?>

        <a onClick="window.open('http://www.facebook.com/sharer.php?s=100&amp;p[title]=<?php echo $question;?>&amp;p[summary]=<?php echo $summary;?>&amp;p[url]=<?php echo $url; ?>&amp;&amp;p[images][0]=<?php echo $image;?>','sharer','toolbar=0,status=0,width=548,height=325');" href="javascript: void(0)">
           <img src="facebook.jpg" alt="Facebook" width="60" height="18">
        </a>
     </div>

     <table style="font-color:black;margin-left:auto; margin-right:auto;">
	<tr>
	<td style="width:40px; "><img id="question4icon" src="<?php echo $specialInfo[4]; ?>" style="float:left" width="32" height="32"></td>
   	<td><div id="questionTitle4"></div></td>
	</tr>
	</table>
     <div id="map4" alt="map4"></div>
     <div id="holdAnswers">
		<div id="holdAllAnswers4">

		<table id="addAnswerTable4" alt="buttonsForAnswer">
			<tr>
			</tr>
		</table>

	<script>question4 = new question(4);</script>
   </div>
    <input class="tabButtonsMain"style="float:right; margin-right:5px" type="submit" value="Back to top" onClick="parent.location='#menu'"/>
   </div></div>

   <div id="whiteSpace" style="padding-bottom:70px" alt="workingArea">
     <div id="fb_share">
        <?php
		$questionID = mysql_result($questionIDs, 4);
		$sql = "SELECT Question "
				. "FROM Question "
				. "WHERE Question.QuestionID = '$questionID'";
		$result = mysql_query($sql);
		$question = mysql_result($result, 0);
		$url=urlencode('http://potnoodle.cs.man.ac.uk/~underwd2/View.php?questionID='.$questionID.'');
   		$summary=urlencode('I just answered on a Question! Visit Worldly, so you can answer questions too!');
		$image=urlencode('http://potnoodle.cs.man.ac.uk/~underwd2/facebookWorldly.jpg');
        ?>

        <a onClick="window.open('http://www.facebook.com/sharer.php?s=100&amp;p[title]=<?php echo $question;?>&amp;p[summary]=<?php echo $summary;?>&amp;p[url]=<?php echo $url; ?>&amp;&amp;p[images][0]=<?php echo $image;?>','sharer','toolbar=0,status=0,width=548,height=325');" href="javascript: void(0)">
           <img src="facebook.jpg" alt="Facebook" width="60" height="18">
        </a>
     </div>

    <table style="font-color:black;margin-left:auto; margin-right:auto;">
	<tr>
	<td style="width:40px; "><img id="question5icon" src="<?php echo $specialInfo[5]; ?>" style="float:left" width="32" height="32"></td>
   	<td><div id="questionTitle5"></div></td>
	</tr>
	</table>
     <div id="map5" alt="map5"></div>

	<div id="holdAnswers">
		<div id="holdAllAnswers5">

		<table id="addAnswerTable5" alt="buttonsForAnswer">
			<tr>
			</tr>
		</table>

	<script>question5 = new question(5);</script>
   </div>
   <input class="tabButtonsMain"style="float:right; margin-right:5px" type="submit" value="Back to top" onClick="parent.location='#menu'"/>
   </div></div>
   </div>
   </div>

</body>
</html>
