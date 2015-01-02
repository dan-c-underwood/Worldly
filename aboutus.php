<?php session_start(); ?>
<!DOCTYPE html>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Worldly: You Ask It, We Map It</title>

<head>
<script type="text/javascript" src="http://cdn.leafletjs.com/leaflet-0.5/leaflet.js"></script>
<link rel="stylesheet" type="text/css" href="WorldlyCSS.css">
<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.5/leaflet.css" />
<link rel="icon" href="globe.ico" type="image/x-icon">
<link rel="shortcut icon" href="globe.ico" type="image/x-icon">
<script>
var key = 'e3989c63a20c4e83aa5d3644bc050b27';
var tiles = 'http://{s}.tile.cloudmade.com/'+key+'/997/256/{z}/{x}/{y}.png';
var attr = 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>';
</script>

<style type="text/css">
h1 {
  font-size: 30px;
  color:#505050;
  font-family: Arial, serif;
  padding: 10px 20px 20px;
}
h2 {
  font-size: 20px;
  color:#505050;
  text-decoration:underline;
  font-family: Arial, serif;
  padding: 0px 20px 0px;
}  
.marker {
  font-size: 11px;
  color:#505050;
  font-family: Arial, serif;
  text-align: center;
}
p {
  font-size: 15px;
  color:#505050;
  font-family: Arial, serif;
  padding: 0px 20px 0px;
}

.lists {
  font-size: 15px;
  color:#505050;
  font-family: Arial, serif;
  padding: 0px 20px 0px;
}
</style>
</head>

<body>
 
  <ul id="menu" alt="basicMenuBar">
  <div id="contents1" alt="menuContents">
    <li><a href="#" class="logo" alt="logo"><span></span></a></li>	
    <li><a href="Home.php" class="home" alt="homeMenu"><span></span></a></li>
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
  
  <div id="whiteSpace" alt="workingArea">
  </br>
  </br>
  <div id="map"></div>
  <script>
  var map = L.map("map").setView([53.4675, -2.234], 13);

  L.tileLayer(tiles, {
    attribution: attr
  }).addTo(map);
  
  var groupMarker = L.marker([53.4675, -2.234]).addTo(map);
  
  groupMarker.bindPopup("<img src='groupImage.jpg' alt='Wordly group photo' width='125' style='float:left' style='padding-right:'>"
                        + "<p class = 'marker'><b>Hello world!</b><br>We are the developers of Wordly. Five computer "
						+ "science students at the University of Manchester. Click on the markers "
						+ "around for more details about Worldly!</p>").openPopup();

  var gitHubMarker = L.marker([53.445, -2.214]).addTo(map);
  
  gitHubMarker.bindPopup("<a href='https://github.com/dan-c-underwood/Worldly'><img src='Octocat.png' alt='GitHub link' width='130'></a>");
  
  var techMarker = L.marker([53.455, -2.260]).addTo(map);
  
  techMarker.bindPopup("<p class = 'marker'><b>We have used the following technologies:</b><br>"
                       + "<ul>"
					   + "<li><a href='http://www.w3.org/html/'>HTML</a></li>"
					   + "<li><a href='http://uk.php.net/'>PHP</a></li>"
					   + "<li><a href='http://www.mysql.com/'>MySQL</a></li>"
					   + "<li><a href='http://jquery.com/'>jQuery</a></li>"
					   + "<li><a href='http://leafletjs.com/'>Leaflet</a></li>"
					   + "</ul></p>");
					
  var whyMarker = L.marker([53.465, -2.270]).addTo(map);
  
  whyMarker.bindPopup("<p class = 'marker'><b>Why Worldly?</b><br>"
                       + "Having seen what exists in terms of more conventional opinion and survey sites we felt that a geographically "
					   + "centred site was missing. This is where Worldly comes in, this is our concept for a site where users can, for free, "
					   + "have access to large amounts of community generated data. The name comes from its definition: Worldly as in 'Worldly-wise'. "
					   + "We feel this name best embodies what this site is about.</p>");
					   
  var whyMarker = L.marker([53.465, -2.270]).addTo(map);
  
  whyMarker.bindPopup("<p class = 'marker'><b>Why Worldly?</b><br>"
                       + "Having seen what exists in terms of more conventional opinion and survey sites we felt that a geographically "
					   + "centred site was missing. This is where Worldly comes in, this is our concept for a site where users can, for free, "
					   + "have access to large amounts of community generated data. The name comes from its definition: Worldly as in 'Worldly-wise'. "
					   + "We feel this name best embodies what this site is about.</p>");
  
  var futureMarker = L.marker([53.485, -2.220]).addTo(map);
  
  futureMarker.bindPopup("<p class = 'marker'><b>What are the future plans for Worldly?</b><br>"
                       + "<ul>"
					   + "<li>Filtering of results on maps.</li>"
					   + "<li>Comments sections for questions.</li>"
					   + "<li>User based moderation.</li>"
					   + "<li>Expansion of number of regions covered (starting with North America).</li>"
					   + "<li>Constructing an API to allow users and developers to access the data we have collected.</li>"
					   + "</ul></p>");	
					   
  </script>
  <h1>Question creation guidelines and the "Featured" status</h1>
    
	<h2>Question guidelines</h2>
	<p>In order to achieve the most votes for your questions, it is recommended you follow these guidelines:
	<ul>
	   <li class = "lists">Questions should be as non-biased as possible. E.g. A question such as "Do you think that people should vote
	       yes in this election?" leads the answerer towards a 'yes' answer. A better way of writing it would be
		   "How do you think people should vote in this election?"; this is a much more neutral question.</li>
		   </br>
	   <li class = "lists">Correct grammar and spelling should be used.</li>
	   </br>
	   <li class = "lists">Answers should be specific enough for a person to choose from but cover a wide enough area as possible.</li>
	   </br>
	   <li class = "lists">Questions should ideally be applicable to people no matter their location, this is the purpose of the site; to
	       see how opinions change across geographical regions.</li>
		</br>
	</ul>
	</p>
	
	<h2>The "Featured" status</h2>
	<p>This is our way of rewarding users who consistently produce content that is well appreciated by the community.
	   When a user has had enough questions become 'live' they will gain this status, any further questions created
	   by them will be flagged as "featured" as a sign that they will likely be of high quality and will be shown 
	   prominently on the home page (even higher than sponsored results).</p>
	   
	<h2>Globe Icons</h2>
	<p>The following globe icons allow users to identify information about questions:
	<ul>
	   <li class = "lists"><img id="questionInfoIcon" src="greyGlobe.png" style="padding-right:1em" width="15" height="15">This grey globe represents
	   the majority of questions on the site. This icon means that the question you are looking at is a normal question.</li>
		   </br>
	   <li class = "lists"><img id="questionInfoIcon" src="goldenGlobe.png" style="padding-right:1em" width="15" height="15">The golden globe is used to
	   represent any question that is sponsored, i.e., a company, organisation or individual has paid to make it appear.</li>
		   </br>
	   <li class = "lists"><img id="questionInfoIcon" src="blueGlobe.png" style="padding-right:1em" width="15" height="15">The blue globe represents
	   the best questions and users on the site. It represents any question made by a user that has the featured status (as mentioned above). A featured
	   user will also gain this symbol on their account page.</li>
		   </br>
	</ul>
	</p>
  
  <h1>Terms of Use</h1>
	
	<h2>Privacy</h2>
	<p>We agree to protect your identity by only asking for non-identifying demographic information and to protect
	   any password set on this site using encryption.</p>
	   
	<h2>Javascript and Cookies</h2>
	<p>By using this site, you will need to make use of cookies, by having read this any further usage by you, the user,
	   is with the agreement that you accept the use of these. You will also accept that by using this site to its full
	   capabilities you must have Javascript enabled.</p>
	   
	<h2>Data</h2>
	<p>We cannot guarantee the security of any data stored on this site and will therefore not be liable for its loss,
	   however we will make every effort to provide access to the results of the questions shown on this site using the
	   guidelines set out by the <a href="http://okfn.org/opendata/">Open Knowledge Foundation</a>. The exception to this
	   will be for sponsored questions where the sponsoring party will have exclusive access to the data for a limited time.</p>
	   
	<h2>Sponsored questions</h2>
	<p>The user will accept that some questions on this site will be sponsored and therefore may carry more bias than
	   others. While we will do our utmost to keep sponsored questions as neutral as possible we will not have complete control.
	   We will, however, always allow sponsored questions to be identifiable. Users also accept that the data generated by answering
	   sponsored questions will be used by the sponsoring party for potential financial gain and that Wordly hosts these questions for
	   the purpose of financial gain.</p>
	   
	<h2>Accounts</h2>
	<p>By creating an account on this site you accept that we may not be always able to provide access to it. You also
	   accept that we have the right to remove your account and all linked information if you break the following rules
	   repeatedly. If you come across questions that break these rules you can report them with the 'report' button. An administrator will
	   then look to see whether they should be allowed.
	   <ol>
	   <li class = "lists">Your username must not contain offensive language.</li>
	   </br>
	   <li class = "lists">Any question (including its answers) must not contain offensive language.</li>
	   </br>
	   <li class = "lists">Questions must not be offensive to any group of people (e.g. sexist, homophobic, racist...).</li>
	   </br>
	   <li class = "lists">You must not create 'spam' questions (questions with no meaning created in large quantities).</li>
	   </br>
	   <li class = "lists">Questions must not be used to advertise a site, product or any other type of object.</li>
	   </ol>
	   </p>
	   
	
  </div>
   
</body>
</html>	


</html>	
