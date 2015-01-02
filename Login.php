<?php
session_start();
$_SESSION['userID'] = "";

//Connect to the database
//Connect to the database
include 'server.php'
$connection = server_connect()

//Encryption code
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

    if($hash == $existingHash)
	{
	  return true;
	}
	else
	{
	  return false;
	}
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

//get the values from the form before
$username = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
$password = $_POST['password'];

$bcrypt = new Bcrypt(15);

function setID($username)
{
  //search for the username in the database
  $sql1 = "SELECT UserID FROM User WHERE UserName='$username'";
  $foundRecordQuery = mysql_query($sql1);
  $foundRecordID = mysql_result($foundRecordQuery, 0);
  $_SESSION['userID'] = $foundRecordID;
}//setID

function isFound($username)
{
  //search for the username in the database
  $sql1 = "SELECT UserID FROM User WHERE UserName='$username'";
  $foundRecordQuery = mysql_query($sql1);
  $foundRecordID = mysql_result($foundRecordQuery, 0);

  //check to see that the user exists
  if(!empty($foundRecordID))
    return true;
  else
    return false;
}//isFound

//automatically called
if(isFound($username))
{
  $sql = "SELECT Password FROM User WHERE UserName='$username'";
  $foundPasswordQuery = mysql_query($sql);
  $foundPassword = mysql_result($foundPasswordQuery, 0);
  $isGood = $bcrypt->verify($password, $foundPassword);

 if($isGood)
  {
    setID($username);
	echo true;
  }
  else
  {
	$_SESSION['userID'] = '-1';
	echo false;
  }
}
else
{
  $_SESSION['userID'] = '-1';
  echo false;
}
?>
