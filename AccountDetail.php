<?php //Connect to the database
session_start();
include 'server.php'
//connect to the database before checking the action
$connection = server_connect()

  //Encryption Code (ICO)
  class Bcrypt {
  private $rounds;
  public function __construct($rounds = 08)
  {
    if(CRYPT_BLOWFISH != 1)
    {
      throw new Exception("bcrypt not supported in this installation. See http://php.net/crypt");
    }

    $this->rounds = $rounds;
  }


  public function hash($input)
  {
    $hash = crypt($input, $this->getSalt());

    if(strlen($hash) > 13)
      return $hash;

    return false;
  }


  public function verify($input, $existingHash)
  {
    $hash = crypt($input, $existingHash);

    return $hash === $existingHash;
  }


  private function getSalt()
  {
    $salt = sprintf('$2a$%02d$', $this->rounds);

    $bytes = $this->getRandomBytes(16);

    $salt .= $this->encodeBytes($bytes);

    return $salt;
  }


  private $randomState;
  private function getRandomBytes($count)
  {
    $bytes = '';

    if(function_exists('openssl_random_pseudo_bytes') &&
        (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN'))
    { // OpenSSL slow on Win
      $bytes = openssl_random_pseudo_bytes($count);
    }

    if($bytes === '' && is_readable('/dev/urandom') &&
       ($hRand = @fopen('/dev/urandom', 'rb')) !== FALSE)
    {
      $bytes = fread($hRand, $count);
      fclose($hRand);
    }

    if(strlen($bytes) < $count)
    {
      $bytes = '';

      if($this->randomState === null)
      {
        $this->randomState = microtime();
        if(function_exists('getmypid')) {
          $this->randomState .= getmypid();
      }
    }

      for($i = 0; $i < $count; $i += 16)
      {
        $this->randomState = md5(microtime() . $this->randomState);

        if (PHP_VERSION >= '5')
        {
          $bytes .= md5($this->randomState, true);
        }
        else
        {
          $bytes .= pack('H*', md5($this->randomState));
        }
      }

      $bytes = substr($bytes, 0, $count);
    }

    return $bytes;
  }



  private function encodeBytes($input)
  {
    // The following is code from the PHP Password Hashing Framework
    $itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    $output = '';
    $i = 0;
    do
    {
      $c1 = ord($input[$i++]);
      $output .= $itoa64[$c1 >> 2];
      $c1 = ($c1 & 0x03) << 4;
      if ($i >= 16)
      {
        $output .= $itoa64[$c1];
        break;
      }


      $c2 = ord($input[$i++]);
      $c1 |= $c2 >> 4;
      $output .= $itoa64[$c1];
      $c1 = ($c2 & 0x0f) << 2;


      $c2 = ord($input[$i++]);
      $c1 |= $c2 >> 6;
      $output .= $itoa64[$c1];
      $output .= $itoa64[$c2 & 0x3f];
      }

      while (1);

    return $output;
  }
}
//End of encryption code

    function setID($username)
    {
      //search for the username in the database
      $sql1 = "SELECT UserID FROM User WHERE UserName='$username'";
      $foundRecordQuery = mysql_query($sql1);
      $foundRecordID = mysql_result($foundRecordQuery, 0);
      $_SESSION['userID'] = $foundRecordID;
    }//setID

  if((isset($_POST['action'])) && ($_POST['action'] == 'new'))
  {
    //create the new account
    $username = filter_input(INPUT_POST, 'userReg', FILTER_SANITIZE_STRING);
    $password = $_POST['passReg'];
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
    $ages = filter_input(INPUT_POST, 'ages', FILTER_SANITIZE_STRING);
    $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);

    //encrypt the password
    $bcrypt = new Bcrypt(15);
    $hash = $bcrypt->hash($password);

    //automatically called
    $sql = "INSERT INTO User (UserName, Password, Location, AgeGroup, Gender)
            VALUES ('$username', '$hash', '$location', '$ages', '$gender')";
    mysql_query($sql);
    setID($username);
  }//if create

$userID = $_SESSION['userID'];

  //set the username
  $sql = "SELECT Username FROM User WHERE UserID='$userID'";
  $sqlQuery = mysql_query($sql);
  $username = mysql_result($sqlQuery,0);
  $_SESSION['username'] = $username;

  //set the age
  $sql = "SELECT AgeGroup FROM User WHERE UserID='$userID'";
  $sqlQuery = mysql_query($sql);
  $age = mysql_result($sqlQuery,0);
  $_SESSION['age'] = $age;

  //set the gender
  $sql = "SELECT Gender FROM User WHERE UserID='$userID'";
  $sqlQuery = mysql_query($sql);
  $gender = mysql_result($sqlQuery,0);
  $_SESSION['gender'] = $gender;

  //set the location
  $sql = "SELECT Location FROM User WHERE UserID='$userID'";
  $sqlQuery = mysql_query($sql);
  $location = mysql_result($sqlQuery,0);
  $_SESSION['location'] = $location;

	if (!(isset($_SESSION['userID'])) || ($_SESSION['userID'] == -1))
	{
		header("Location: Account.php");
		exit;
	}

?>

<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Worldly: You Ask It, We Map It</title>
<link rel="stylesheet" type="text/css" href="WorldlyCSS.css">
<link rel="icon" href="globe.ico" type="image/x-icon">
<link rel="shortcut icon" href="globe.ico" type="image/x-icon">
<script src="http://code.jquery.com/jquery-1.8.3.js"></script>
<script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>
<style type="text/css">
p {
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
    <li><a href="Home.php" class="home"><span></span></a></li>
    <li><a href="Questions.php" class="questions" alt="questionsMenu"><span></span></a></li>
    <li><a href="History.php" class="history" alt="historyMenu"><span></span></a></li>
    <li><a href="" class="restOfBar" alt="freeBarSpace"></a></li>
    <?php if(isset($_SESSION['userID']))
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

    <div id="freeSpace" alt="workingArea">
     <div id="onlyOnRegister">
<?php if((isset($_POST['action'])) && ($_POST['action'] == 'new')) { echo '<p>Your account has been created, please feel free to update your details</p>'; } ?>
</div>
    <div class="accountDetailsDiv">
    <form name="logout" method="post" action="Account.php">
      <input type="hidden" name="action" value="logout">
      <input class="tabButtonsMain" type="submit" style="float:right" value="Logout">
    </form>
    <form name="deleteDetails" method="post" onsubmit="return confirm('Are you sure you want to delete your account?');" action="Account.php">
      <input class="deleteB" type="hidden" name="action" value="delete">
      <input class="deleteB" type="submit" value="Delete">
    </form>
	<?php
	$sql = "SELECT Rank "
   . "FROM User "
   . "WHERE User.UserID = '$userID'";

	$result = mysql_query($sql);

	$rank = mysql_result($result, 0);

	switch($rank)
	{
		case "member":
			$icon = "greyGlobe.png";
			break;
		case "featured":
			$icon = "blueGlobe.png";
			break;
		case "admin":
			$icon = "goldenGlobe.png";
			break;
	}
	?>
      <br><div id="detailsText">Your Details</div>
      <div id="userDetails">
      <form name="userDetails" id="updateDetails" >
       <table id="userDetailsTable">
		<tr>
                <td></td>
                </tr>
                <tr>
                <td>Username:</td>
                <td><input type="text" id="user" name="user" value="<?php echo $_SESSION['username']; ?>"></td>
                <td><img id="questionInfoIcon" src="<?php echo $icon; ?>" width="32" height="32"></td>
                </tr>
                <tr>
                <td></td>
                </tr>
                <tr>
                <td>Age:</td>
                <td><select name="ages">
                    	<option selected alt="defaultMessage">Choose group</option>
                        <option value="" style="display:none;"></option>
		        <option selected value="13-18" alt="13-18">13-18</option>
		        <option <?php if($_SESSION['age']=="19-25"){echo 'selected';} ?> value="19-25" alt="19-25">19-25</option>
                        <option <?php if($_SESSION['age']=="26-40"){echo 'selected';} ?> value="26-40" alt="26-40">26-40</option>
                        <option <?php if($_SESSION['age']=="41-65"){echo 'selected';} ?> value="41-65" alt="41-65">41-65</option>
	                <option <?php if ($_SESSION['age']=="65+"){echo 'selected';} ?> value="65+" alt="65+">65+</option>
	             </select>
                 </td>
                 </tr>
                 <tr>
                 <td>Gender:</td>
                 <td><input type="radio" name="gender" value="Male" checked>Male
                     <input type="radio" name="gender" value="Female" <?php if($_SESSION['gender']=='Female'){echo 'checked';} ?>>Female</td>
                 </tr>
                 <tr>
                 <td>Location:</td>
                 <td>
			<select name="location">
			<option selected <?php if ($_SESSION['location']=="southernEngland"){echo 'selected';} ?> value="southernEngland">Southern England</option>
			<option <?php if ($_SESSION['location']=="midlands"){echo 'selected';} ?> value="midlands">Midlands</option>
			<option <?php if ($_SESSION['location']=="northernEngland"){echo 'selected';} ?> value="northernEngland">Northern England</option>
                        <option <?php if ($_SESSION['location']=="scotland"){echo 'selected';} ?> value="scotland">Scotland</option>
                        <option <?php if ($_SESSION['location']=="wales"){echo 'selected';} ?> value="wales">Wales</option>
                        <option <?php if ($_SESSION['location']=="isleOfMan"){echo 'selected';} ?> value="isleOfMan">Isle of Man</option>
   			<option <?php if ($_SESSION['location']=="northernIreland"){echo 'selected';} ?> value="northernIreland">Northern Ireland</option>
                        <option <?php if ($_SESSION['location']=="republicOfIreland"){echo 'selected';} ?> value="republicOfIreland">Republic of Ireland</option>
			</select>
		  </td>
 		  <tr>
                  <td></td>
                  </tr>
		  <tr>
                  <td><b>Change password</b></td>
                  </tr>
                  <tr>
                  <td>Old Password:</td>
                  <td><input type="password" name="oldPass" /></td>
                  </tr>
                  <tr>
                  <td>New Password:</td>
                  <td><input type="password" name="newPass" /></td>
                  </tr>
                  <tr>
                  <td>Confirm Password:</td>
                  <td><input type="password" name="confirmPass" /></td>
                  </tr>
                 </tr>
		  <tr>
 	          <td></td>
		  </tr>
		  <tr>
 	          <td></td>
                  <td> <input class="tabButtonsMain" type="submit" value="Save Changes"/></td>
                   </tr>

            </table>
            </form>
         <?php //unset the sessions used to pass data;
           unset($_SESSION['age']);
           unset($_SESSION['gender']);
           unset($_SESSION['location']);
         ?>
    </div>

   </div>
  </div>
<script type="text/javascript">
  var typingTimer = null;
  var doneTypingInterval = 500;
  var valid = false;
  var oldUsername = "<?php echo $username ?>";

$('#updateDetails').submit(function(event) {
  event.preventDefault();
  updateInfo();
});

$('#user').keyup(function(){
    clearTimeout(typingTimer);
    if ($('#user').val) {
        typingTimer = setTimeout(doneTyping, doneTypingInterval);
    }
});

var valid = false;

function doneTyping () {
  var newUser = $("#user").val();
  $.post("CheckUsername.php", { 'newUsername' : newUser }, function(validity){
	  if (oldUsername == newUser)
	  {
	    $("#user").css("border-color","#00FF00");
	    valid = true;
	  }
	  else if(validity == true)
    {
      $("#user").css("border-color","#00FF00");
	    valid = true;
	  }
	  else if(validity == false)
	  {
	    $("#user").css("border-color","#FF0000");
	    valid = false;
	  }

  });
}

$('#user').click(function(){
    $(this).css('border-color','#c4c4c4 #d1d1d1 #d4d4d4');
});

/*
 function changePass()
{
  if (document.getElementById("changePass").checked == true)
  {
    document.getElementById("oldPass").disabled = false;
    document.getElementById("newPass").disabled = false;
    document.getElementById("confirmPass").disabled = false;
  }
  else
  {
    document.getElementById("oldPass").disabled = true;
    document.getElementById("newPass").disabled = true;
    document.getElementById("confirmPass").disabled = true;
  }
}
*/
function validateInfo()
{
    var oldPassword = document.forms["userDetails"]["oldPass"].value;
    var newPassword = document.forms["userDetails"]["newPass"].value;
    var confPassword = document.forms["userDetails"]["confirmPass"].value;
    if (!(oldPassword == null || oldPassword == ""))
    {
	  if (newPassword == null || newPassword == "")
      {
        alert("To change your password you must enter a new one!");
        return false;
      }
      else if (newPassword.length < 6)
      {
        alert("Your password must be at least 6 characters long.");
        return false;
      }
      else if (newPassword != confPassword)
      {
        alert("Your chosen passwords do not match.");
        return false;
      }
      else if(!checkLogin())
      {
        alert("The old password you entered was incorrect, please try again");
	return false;
      }
    }

    var username = document.forms["userDetails"]["user"].value;

    if (username == null || username == "")
    {
      alert("You must enter a username");
      return false;
    }


  if (oldUsername == username)
	{
	  valid = true;
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

function checkLogin() {
	var username =  '<?php echo($username); ?>';
	var password = document.forms["userDetails"]["oldPass"].value;

	var checkResult;

	$.ajax({
	  type: "POST",
	  async: false,
	  url: "Login.php",
	  data: { login : username, password : password },
	  dataType: "html"
	  }).done(function(success) {
			checkResult = success;
	});
	return checkResult;
}
function updateInfo()
{
  if(validateInfo())
  {
    var location = document.forms["userDetails"]["location"].value;
    var ages = document.forms["userDetails"]["ages"].value;
    var genderRadios = document.getElementsByName("gender");
	var user = $("#user").val()
	var newPass = document.forms["userDetails"]["newPass"].value;
	var oldPass = document.forms["userDetails"]["oldPass"].value;

    for(var i = 0, length = genderRadios.length; i < length; i++)
    {
      if(genderRadios[i].checked)
      {
        gender = genderRadios[i].value;
      }
    }
    $.ajax({
	  type: "POST",
	  url: "UpdateUser.php",
	  data: { user : user, location : location, ages : ages, gender : gender, newPass : newPass, oldPass : oldPass },
	  dataType: "html"
	  }).done(function(success) {
		  if(success)
		  {
			alert("Your details have been updated successfully");
			window.location.reload(true);
		  }
		  else
		  {
			alert("There was an error updating your details, please try again.");
		  }
    });
  }
  return false;
}

</script>

</body>

</html>
