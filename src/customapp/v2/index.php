<?php

session_start();
if (isset($_SESSION["access_token"]) && isset($_SESSION["refresh_token"])) { // we already have a session.
    $access_token = $_SESSION["access_token"];
    $refresh_token = $_SESSION["refresh_token"];
    // request data from the OAuth provider
    require_once('RESTHandler.php');
    $rh = new RESTHandler();
    if ($result = $rh->getProfileDataFromMyOAuthProvider($access_token)) {
        $name = $result["name"];
        $email = $result["email"];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Custom 3rd Party App</title>
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
    
<?php if (isset($access_token) && isset($refresh_token)) { ?>
<h1>WELCOME TO CUSTOM 3RD PARTY APP!</h1>
     <div class="contact-form">
	 <div class="signin">
     <h2>These are your tokens for My OAuth Provider:</h2>
     <p>access token: <?php print $access_token; ?></p>
     <p>refresh token: <?php print $refresh_token; ?></p>
     
     <!-- Show user info if available -->
     <?php if (isset($name) && isset($email)) { ?>
     <h2>This is the data retrieved from My OAuth Provider using that tokens:</h2>
     <p>name: <?php print $name; ?></p>
     <p>email: <?php print $email; ?></p>
     <?php } else { ?>
     <p style="color: red;">Error: I was unable to use that data to retrieve your profile info.</p>        
     <?php } ?>
     
     <button id="logout">Logout</button>
	 </div>
	 </div> 
</div>
<?php } else { ?>
<h1>CUSTOM 3RD PARTY APP LOGIN</h1>
     <div class="contact-form">
	 <div class="signin">
     <p>Custom 3rd Party App uses the new "My OAuth Provider" for social login. Please, open an account in Sexy Social Login first and then click the button below!</p>
     <button id="sociallogin">Login using My OAuth Provider</button>
	 </div>
	 </div> 
</div>
<?php } ?>
<div class="footer">
     <p>Copyright &copy; 2015 Custom 3rd Party App. All Rights Reserved</p>
</div>
<script src="js/jquery-1.11.3.min.js"></script>
<script>
/** Do the social login from CustomApp. Call My OAuth Provider. */
$("#sociallogin").click(function (ev) {
    ev.preventDefault();
    var redirectURI = encodeURIComponent("http://customapp.com:8080/redirect.php");
    var clientID = "nkU350F4PfKLf9umf798qX6jlP2ya501";
    var url = "http://myoauthprovider.com:8080/auth.php?redirect_uri="  + redirectURI + "&client_id=" + clientID;
    window.location = url;
});
    
/** Logout from Custom App: destroy session and related data. */
$("#logout").click(function (ev) {
    ev.preventDefault();
    window.location = "logout.php";
});
</script>
</body>
</html>