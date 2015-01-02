<?php
session_start();

//Connect to the database
include 'server.php'
$connection = server_connect()

if(isset($_POST['action']))
{
  if($_POST['action'] == "delete")
  {
    if((isset($_SESSION['userID'])) && !($_SESSION['userID'] == '-1'))
    {
      $userID = $_SESSION['userID'];
      $sql = "DELETE FROM User WHERE userID='$userID'";
      mysql_query($sql);
    }
    session_destroy();
  }//if delete
  else if ($_POST['action'] == "logout")
  {
    session_destroy();
  }//if logout
}//if set

if (($_SESSION['userID'] != -1 && $_SESSION['userID'] != null))
{
	header("Location: AccountDetail.php");
	exit;
}
?>
<!DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<head>

<title>Worldly: You Ask It, We Map It</title>

<script src="http://code.jquery.com/jquery-1.8.3.js"></script>
<script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>
<link rel="stylesheet" type="text/css" href="WorldlyCSS.css">
<link rel="icon" href="globe.ico" type="image/x-icon">
<link rel="shortcut icon" href="globe.ico" type="image/x-icon">
<script language=javascript type="text/javascript">

  function validateRegister()
  {
    var username = document.forms["registerForm"]["userReg"].value;
    if (username == null || username == "")
    {
      alert("You must enter a username");
      return false;
    }

    var password = document.forms["registerForm"]["passReg"].value;
    var confPassword = document.forms["registerForm"]["confPass"].value;
    if (password.length < 6)
    {
      alert("Your password must be at least 6 characters long.");
      return false;
    }
    else if (password != confPassword)
    {
      alert("Your chosen passwords do not match.");
      return false;
    }

    if (valid == false)
	{
	  alert("Username already taken, please enter another");
	  return false;
	}
	else if (valid == true)
	{
	  return true;
	}
  }

</script>


<style type="text/css">
#map
{
  height: 485px;
  width: 375px;
  margin-left:auto;
  margin-right:auto;
}
.info {
  padding: 3px 4px;
  font: 14px/16px Arial, Helvetica, sans-serif;
  background: white;
  background: rgba(255,255,255,0.8);
  box-shadow: 0 0 15px rgba(0,0,0,0.2);
  border-radius: 5px;
}
.info h4 {
  margin: 0 0 5px;
  color: #777;
}

</style>
<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.5/leaflet.css" />
 <!--[if lte IE 8]>
     <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.4/leaflet.ie.css" />
 <![endif]-->
</head>

<body>

<ul id="menu" alt="basicMenuBar">
  <div id="contents1" alt="menuContents">
    <li><a href="#" class="logo" alt="logo"><span></span></a></li>
    <li><a href="Home.php" class="home"><span></span></a></li>
    <li><a href="Questions.php" class="questions" alt="questionsMenu"><span></span></a></li>
    <li><a href="History.php" class="history" alt="historyMenu"><span></span></a></li>
    <li><a href="" class="restOfBar" alt="freeBarSpace"></a></li>
    <?php if((isset($_SESSION['userID'])) && !(isset($_POST['action'])))
          {
            if($_SESSION['userID'] == -1)
              {echo '<li><a href="Account.php" class="accountActive"><span></span></a></li>'; }
            else
              {echo '<li><a href="AccountDetail.php" class="accountActive"><span></span></a></li>'; }
          }
          else
          { echo '<li><a href="Account.php" class="accountActive"><span></span></a></li>'; }
    ?>
  </div>
  </ul>

 <div class="container">
    <div class="login">
      <label id="loginText">Login to Worldly</label>
      <form name="loginForm" id="loginForm">
        <p>Username: <input type="text" name="login" value=""></p>
        <p>Password: <input type="password" name="password" value=""></p>
        <p><input class="tabButtonsMain" type="submit" name="commit" value="Login"></p> <!--id="loginButton"-->
      </form>
    </div>

    <div class="register">
      <label id="registerText">Register</label>
      <form id="registerForm" name="registerForm" onsubmit="return validateRegister()" method="post" action="AccountDetail.php">
        <p>Username:<input type="text" id="userReg" name="userReg"></p>
        <p>Password:<input type="password" name="passReg"></p>
	<p>Confirm Password:<input type="password" name="confPass"></p>
	<p>Age:
		<select name="ages" alt="SelectAgeGroup">
		<option value="13-18" alt="13-18" selected>13-18</option>
		<option value="19-25" alt="19-25">19-25</option>
                <option value="26-40" alt="26-40">26-40</option>
                <option value="41-65" alt="41-65">41-65</option>
	        <option value="65+" alt="65+">65+</option>
		</select>
	</p>
	<p>Gender:<input type="radio" name="gender" alt="male" value="Male" checked>Male
                     <input type="radio" name="gender" alt="female" value="Female">Female</td></p>
	<p>Location:
		        <select id="locationList" name="location">
		        <option value="southernEngland" alt="SouthEngland" selected>Southern England</option>
		        <option value="midlands" alt="midlands">Midlands</option>
		        <option value="northernEngland" alt="NorthernEngland">Northern England</option>
                        <option value="scotland" alt="scotland">Scotland</option>
                        <option value="wales" alt="wales">Wales</option>
                        <option value="isleOfMan" alt="IsleOfMan">Isle of Man</option>
  		        <option value="northernIreland" alt="NorthernIreland">Northern Ireland</option>
                    	<option value="republicOfIreland" alt="RepublicOfIreland">Republic of Ireland</option>
		        </select>
	</p>
	<input type="hidden" name="action" value="new">
        <p><input  class="tabButtonsMain" type="submit" name="commit" value="Register"></p> <!--id="loginButton"-->
      </form>
    </div>
    <script type="text/javascript">
  var typingTimer = null;
  var doneTypingInterval = 500;
  var valid = false;

$('#loginForm').submit(function(event) {
  event.preventDefault();
  login();
});

function login() {
  var username =  document.forms["loginForm"]["login"].value;
  var password = document.forms["loginForm"]["password"].value;
  if(username == null || username == '' || password == null || password == '')
  {
    alert("You must enter your details to login.");
  }
  else
  {
	$.ajax({
	  type: "POST",
	  url: "Login.php",
	  data: { login : username, password : password },
	  dataType: "html"
	  }).done(function(success) {
		  if(success)
		  {
		    alert("You have logged in successfully.");
			window.location.replace("AccountDetail.php");
	      }
		  else
		  {
		    alert("Either your username or password was incorrect, please try again.");
	      }
    });
  }
}


$('#userReg').keyup(function(){
    clearTimeout(typingTimer);
    if ($('#userReg').val) {
        typingTimer = setTimeout(doneTyping, doneTypingInterval);
    }
});

var valid = false;

function doneTyping () {
  var username = $("#userReg").val();
  $.post("CheckUsername.php", { 'newUsername' : username }, function(validity){
	if(validity == true)
    {
      $("#userReg").css("border-color","#00FF00");
	  valid = true;
	}
	else if(validity == false)
	{
	  $("#userReg").css("border-color","#FF0000");
	  valid = false;
	}

  });
}

$('#userReg').click(function(){
    $(this).css('border-color','#c4c4c4 #d1d1d1 #d4d4d4');
});
	</script>
    <script type="text/javascript" src="http://cdn.leafletjs.com/leaflet-0.5/leaflet.js"></script>
    <script src="uk-boundaries.js"></script>

    <div id="map"></div>

    <script type="text/javascript">
    var map = L.map('map').setView([55.500, -2.500], 5);

    var key = 'e3989c63a20c4e83aa5d3644bc050b27';
    var tiles = 'http://{s}.tile.cloudmade.com/'+key+'/22677/256/{z}/{x}/{y}.png';
    var attr = 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>';

    L.tileLayer(tiles, {
                attribution: attr
    }).addTo(map);

    function getColour(name) {
        return name == 'Wales'               ? '#009900' :
               name == 'Isle of Man'         ? '#FF9900' :
               name == 'Southern England'    ? '#FF0000' :
			   name == 'Midlands'            ? '#FF3300' :
			   name == 'Northern England'    ? '#FF6600' :
			   name == 'Scotland'            ? '#0033FF' :
			   name == 'Northern Ireland'    ? '#9900CC' :
			   name == 'Republic of Ireland' ? '#00FF33' :
                                               'white';
    }

    function style(feature) {
	    return {
	        fillColor: getColour(feature.properties.name),
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
    }

    function resetHighlight(e) {
	    geojsonuk.resetStyle(e.target);
	    info.update();
    }

    function selectRegion(e) {
		var layer = e.target;
		var region = layer.feature.properties.name;
		locationList = document.getElementById('locationList');
		for(var i = 0, length = locationList.length; i < length; i++){
			if(locationList.options[i].text == region){
                locationList.selectedIndex = i;
                break;
			}
		}
	}

	function onEachFeature(feature, layer) {
			layer.on({
				mouseover: highlightFeature,
				mouseout: resetHighlight,
				click: selectRegion
			});
	}

	geojsonuk = L.geoJson(ukBoundaries, {
			style: style,
			onEachFeature: onEachFeature
	}).addTo(map);


    var info = L.control();

	info.onAdd = function (map) {
	    this._div = L.DomUtil.create('div', 'info');
	    this.update();
	    return this._div;
	};

	// method that we will use to update the control based on feature properties passed
	info.update = function (props) {
	    this._div.innerHTML = '<h4>World Region Map</h4>' +  (props ?
	        '<b>' + props.name + '</b><br />'
	        : 'Select a region');
	};

    info.addTo(map);

    </script>
  </div>

</body>

</html>
