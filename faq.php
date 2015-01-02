<?php session_start(); ?>
<!DOCTYPE html>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Worldly: You Ask It, We Map It</title>

<head>
<link rel="icon" href="globe.ico" type="image/x-icon">
<link rel="shortcut icon" href="globe.ico" type="image/x-icon">
<link rel="stylesheet" type="text/css" href="WorldlyCSS.css">

<style type="text/css">
#title{
  font-size: 30px;
  color:#505050;
  font-family: Arial, serif;
  padding: 30px 20px 20px;
}
h2 {
  font-size: 20px;
  color:#505050;
  text-decoration:underline;
  font-family: Arial, serif;
  padding: 0px 20px 0px;
}  
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
  
  <div id="AskAreaFAQ" alt="workingArea">
    <div id="title"><b>Wordly FAQ</b></div>
	
	<h2>What is Wordly?</h2>
	<p>Wordly is a crowd-based data-sourcing website which displays information geographically based on answers they
	   have given on questions that are user-generated. The website has been built as part of a group project, at the Unversity
	   of Manchester, by five computer science undergradutes - Dan, Hristo, Lora, Pedro and Sarah.</p>
	   
	<h2>That sounds great! How does it work?</h2>
	<p>The core element of Wordly is the use of an open-source Javascript mapping library called Leaflet. The responses
	   given by users are plotted onto these maps by accessing a back-end database and using the users given geographic
	   location.</p>
	   
	<h2>Can I get hold of this data?</h2>
	<p>Sure! For any closed question (accessible through the <a href='History.php'>History</a> page or through a direct link) on its view page there is a
		button that allows you to download all the answer data in JSON format. This creates a JSON object containing the question
		and all the answers with attached demographic information. If there are no responses for that question (unlikely!) then the
		JSON file will contain the value 'null'.</p>
	
	<h2>How do I submit questions?</h2>
	<p>You can submit your questions on the <a href='Questions.php'>Questions</a> page. Once you have submitted the question you'll be taken to the view page
	   for that question which you can share with people to get them to vote for your question! Questions can also be voted on on the Questions page where you
	   can see what questions currently have the most votes. Every week the five questions with the most votes will be made active (less if there are any sponsored questions)
	   and the vote count will be reset. This allows newer questions to have an equal chance to become active.</p>
	   
	<h2>Why isn't the website working properly?</h2>
	<p>Check that you haven't got Javascript blocked or disabled, unfortunately this language is the only way to implement a large
	   amount of the functionality on the site.</p>
	   
	<h2>What about my privacy? Do I want to be giving away my information?</h2>
	<p>We have designed the site to allow you to be anonymous as possible, the only information we collect is rough
	   demographic information. We will never ask you to give any information that would allow you, the user, to be
	   identifiable. Not only this but we encrypt your passwords using a method called bcrypt. This means that it would be
	   far more time consuming to decrypt a single users password than on most other sites. We take your privacy and
	   security seriously and at any point, you can delete your account on the <a href='Account.php'>Account</a> page. This removes your information
	   but does leave any answers and questions you are connected to. These will just become unconnected to an account</p>
	   
	<h2>How will you deal with people making multiple accounts?</h2>
    <p>This is something that we, unfortunately, have to pay the price of in return for allowing users to be anonymous.
       However, we are counting on the majority of users being genuine so that it doesn't swing the results by that much.
       In addition, we will also monitor sponsored questions to make sure accounts are not being created just to answer those
       specific questions.</p>	   
	   
	<h2>What is Worldly's business model? Why aren't there any adverts?</h2>
	<p>Wordly is designed so that income will be generated by allowing interested parties to ask questions that will be
	   guaranteed to be active, but only at a cost and after we have made sure that they meet our guidelines. These questions
	   will be marked as being 'sponsored' so users will know if they are answering a question someone is paying for. The parties
	   who may ask questions could range from political parties all the way through to research groups. In terms of expansion, if
	   Worldly expands to contain multiple regions we will set out a pricing structure based on a geographical size of the audience you
	   want to reach.</p>
	
	<h2>How is Wordly accessible to people with disabilities?</h2>
	<p>Unfortunately the nature of the site limits the accessibility of people with disabilities with sight, in the future we
	   may add in the ability to view results of questions in a purely text based format. If you have any particular issue
	   with the site, get in touch and we will try and find a solution for you.</p>

    <h2>What about a mobile version of the site?</h2>
    <p>This is something we definitely want to add in in the future and the choice of map library was influenced by its
       ability to run effectively on mobile platforms, however, our priority in the first instance has been to get a working
	   version of the site for desktops.</p>	
	
  </div>
   
</body>
</html>	


</html>	
