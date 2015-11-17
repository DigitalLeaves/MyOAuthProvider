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
<h1>UNABLE TO AUTHENTICATE</h1>
     <div class="contact-form">
	 <div class="signin">
     <p>Custom 3rd Party App was unable to authenticate your user with My OAuth Provider.</p>
     <?php error_log("Parameters: ".var_export($_GET, true)); ?>
     <?php if (isset($_GET["error_description"])) { ?>
     <p>Error description: <?php print $_GET["error_description"]; ?></p>
     <?php } ?>
     <button id="goBack">Go Back To Main Page</button>
	 </div>
	 </div> 
</div>
<div class="footer">
     <p>Copyright &copy; 2015 Custom 3rd Party App. All Rights Reserved</p>
</div>
<script src="js/jquery-1.11.3.min.js"></script>
<script>
$("#goBack").click(function (ev) {
    window.location = "index.php";
});
</script>
</body>
</html>