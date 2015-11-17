<?php
    // check session.
    session_start();
    if (isset($_SESSION["user_id"])) {
        $name = $_SESSION["name"];
        $email = $_SESSION["email"];
        $userId = $_SESSION["user_id"];
    } else { // redirect to login.
        header("Location: login.php");
    }
?>
<!DOCTYPE html>
<html>
<head>
<title>OAuth Provider Main Screen</title>
<!-- For-Mobile-Apps -->
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="OAuth Provider Login Form" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- //For-Mobile-Apps -->
<!-- Style --> <link rel="stylesheet" href="css/style.css" type="text/css" media="all">
</head>
<body>
<div class="container">
<h1>HOWDY, <?php print $name; ?>!</h1>
     <div class="contact-form">
	 <div class="signin">
         <p>You are successfully logged in! Your data:</p>
         <p><strong>User ID: </strong><?php print $userId; ?></p>
         <p><strong>Name: </strong><?php print $name; ?></p>
         <p><strong>Email: </strong><?php print $email; ?></p>
         <br/>
         <button id="logoutButton">Logout!</button>
     </div>
	 </div> 
</div>
<div class="footer">
     <p>Copyright &copy; 2015 My OAuth Provider. All Rights Reserved</p>
</div>
<script src="js/jquery-1.11.3.min.js"></script>
<script>
$("#logoutButton").click(function (ev) {
    ev.preventDefault();
    window.location.href = "logout.php";
});
</script>
</body>
</html>