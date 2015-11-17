<?php
    // check session.
    session_start();
    $error = null;

    if (isset($_SESSION["user_id"])) {
        // set user session variables.
        $name = $_SESSION["name"];
        $email = $_SESSION["email"];
        $userId = $_SESSION["user_id"];

        // verify required parameters.
        if (isset($_GET["client_id"]) && isset($_GET["redirect_uri"])) {
            $clientId = $_GET["client_id"];
            $redirectURI = urldecode($_GET["redirect_uri"]);
            error_log("MyOAuthProvider: requesting code for client_id: " . $clientId);
            // other options: response_type for different response types (TODO)
            // other options: response code parameter (default = "code") 
            $codeParameterName = "code";
            if (isset($_GET["code_parameter"])) { $codeParameterName = $_GET["code_parameter"]; }

            // check client_id and generate response code.
            require_once("api/include/DbHandler.php");
            $db = new DbHandler();
            if ($appData = $db->getAppDataByClientId($clientId)) {
                // Check if a previous auth existed.
                if ($authData = $db->previousUserAuthorizationData($userId, $clientId)) {
                    $alreadyAuthURI = $redirectURI . "?" . http_build_query($authData);
                } else {
                    // generate a new oauth code and store it temporarily in the database.
                    $code = $db->generateAuthCode($userId, $clientId);
                    error_log("MyOAuthProvider: generated code: " . $code);

                    // generate authorize and reject URIs.
                    $successData = array($codeParameterName => urlencode($code), "redirect_uri" => $redirectURI);
                    $authorizeURI = $redirectURI . "?" . http_build_query($successData);
                    $errorData = array("error" => "access_denied", "error_description" => "The user denied your request.");
                    $rejectURI = $redirectURI . "?" . http_build_query($errorData);                    
                }
            } else { // App not found. Unauthorized.
               $error = "Unknown or invalid App. Please make sure your App is authorized.";
            }
        } else { $error = "Missing parameters or malformed request. Unable to process authorization."; }
        
        
    } else { // redirect to login.
        $currentURI = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header("Location: login.php?redirect=" . base64_encode($currentURI));
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
<?php if (isset($error)) { ?>
<h1>Error!</h1>
     <div class="contact-form">
	 <div class="signin">
         <p>Error authorizing app: <?php print $error; ?></p>
     </div>
	 </div> 
</div>
<?php } else if (isset($alreadyAuthURI)) { ?>
<h1>Authorize <?php print $appData["name"]; ?>!</h1>
     <div class="contact-form">
	 <div class="signin">
         <p>You have already authorized the App <?php print $appData["name"]; ?> to access your personal information, including your name and email.</p>
         <button id="authorizeButton">Cool! Let's continue then</button>
     </div>
	 </div> 
</div>    
<?php } else { ?>
<h1>Authorize <?php print $appData["name"]; ?>!</h1>
     <div class="contact-form">
	 <div class="signin">
         <p>The App <?php print $appData["name"]; ?> is requesting your permission to access your personal information, including your name and email. Do you want to allow it?</p>
         <button id="authorizeButton">Sure! Authorize it!</button>
         <br/><br/>
         <button id="cancelButton">Hell no!</button>
     </div>
	 </div> 
</div>
<?php } ?>
<div class="footer">
     <p>Copyright &copy; 2015 My OAuth Provider. All Rights Reserved</p>
</div>
<script src="js/jquery-1.11.3.min.js"></script>
<script>
$("#authorizeButton").click(function (ev) {
    ev.preventDefault();
    window.location.href = <?php print '"' . (isset($alreadyAuthURI) ? $alreadyAuthURI : $authorizeURI) . '"'; ?>;
});
$("#cancelButton").click(function (ev) {
    ev.preventDefault();
    window.location.href = <?php print '"' . $rejectURI . '"'; ?>;
});
</script>
</body>
</html>