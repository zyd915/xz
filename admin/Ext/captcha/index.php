<?php
// Session start must be the first line, whether you include it or not :) 
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>How to create CAPTCHA image verification in PHP and jQuery | PGPGang.com</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <style type="text/css">
      img {border-width: 0}
      * {font-family:'Lucida Grande', sans-serif;}
    </style>
    <script type="text/javascript" src="jquery-1.8.0.min.js"></script>
   <script type="text/javascript">
$(document).ready(function(){
$("#new").click(function() {
$("#captcha").attr("src", "captcha.php?"+Math.random());
});    
});
</script>
  </head>
  <body>
    <div>
      <h2>How to create CAPTCHA image verification in PHP and jQuery example.&nbsp;&nbsp;&nbsp;=> <a href="http://www.phpgang.com/">Home</a> | <a href="http://demo.phpgang.com/">More Demos</a></h2>
<?php 
if(empty($_POST))
{
    echo '
<form method="post" action="index.php"> 
<span style="float: left;margin-top: 7px;margin-right:10px;">CAPTCHA Code:</span>
<img src="captcha.php" border="0" alt="CAPTCHA!" id="captcha"><a href="#new" id="new"><img src="reload.png" style="width: 35px;margin-left:10px;" /></a>
<br /> 
Enter CAPTCHA: <input type="text" name="key" value="" /> 
<br /><br /> 
<input type="submit" value=" Verify Captcha " /> 
</form>';
}
else
{
    if(strlen($_SESSION['key']) && $_POST['key'] == $_SESSION['key'])
    {
        echo "Captcha Verified!!!";
    }
    else
    {
        echo "Invalid Captcha.... <a href='index.php'>try again</a>";
    }
}
?>
</body>
</html>