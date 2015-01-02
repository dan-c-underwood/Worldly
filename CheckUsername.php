<?php
session_start();

//Connect to the database
include 'server.php'
$connection = server_connect()

//get the values from the form before
$username = $_POST['newUsername'];
$oldUsername = $_SESSION['userName'];

//whether or not the username is already taken
function IDExists($username)
{
  //search for the username in the database
  $sql1 = "SELECT UserID FROM User WHERE UserName='$username'";
  $foundRecordQuery = mysql_query($sql1);
  $foundRecordID = mysql_result($foundRecordQuery, 0);

  if (!empty($foundRecordID))
    //if it exists then the username is already taken
    //echo's false for javascript
    echo false;
  else
    //if it doesn't exist then the username is free to use
    echo true;
}//IDExists

//if the username has changed
if(!($oldUsername == $username))
{
  IDExists($username);
}
else
{
  echo true;
}
?>
