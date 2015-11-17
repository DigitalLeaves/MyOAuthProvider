<!DOCTYPE html>
<html>
<head>
<title>OAuth Provider Login Form</title>
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
<h1>ENTER YOUR CREDENTIALS</h1>
     <div class="contact-form">
	 <div class="signin">
     <form action="loginFunctions.php" id="loginform" method="post">
	     <input name="email" type="text" class="user" value="Enter Your email" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Enter Your email';}" />
		 <input name="password" type="password" class="pass" value="Password" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Password';}" />
         <input type="submit" value="Login" />
         <div id="loginmessage" style="display: none;"></div>
     </form>
	 </div>
	 </div> 
</div>
<div class="footer">
     <p>Copyright &copy; 2015 My OAuth Provider. All Rights Reserved</p>
</div>
<script src="js/jquery-1.11.3.min.js"></script>
<script>
$("#loginform").submit(function (ev) {
    ev.preventDefault();
    $("#loginmessage").html("");
    $("#loginmessage").hide();
    
    var action = $("#loginform").attr("action");
    var formData = $("#loginform").serialize();
    $.post(action, formData, function(data) {
        if (data == "success") {
            <?php 
            if (isset($_GET["redirect"])) {
                print 'window.location = "' . base64_decode($_GET["redirect"]) . '";';
            } else { 
            ?>
                window.location = "index.php";
            <?php } ?>
        } else {
            $("#loginmessage").html(data);
            $("#loginmessage").fadeIn();
        }
    });
});
</script>
</body>
</html>