<?php
/**
 * Copyright (c) 2015 Ignacio Nieto Carvajal <contact@digitalleaves.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software 
 * and associated documentation files (the "Software"), to deal in the Software without restriction, 
 * including without limitation the rights to use, copy, modify, merge, publish, distribute, 
 * sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is 
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or 
 * substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT 
 * NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. 
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE 
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 **/

$validated = true;

if (isset($_POST["email"])) { $email = $_POST["email"]; } 
else { $validated = false; }
if (isset($_POST["password"])) { $password = $_POST["password"]; } 
else { $validated = false; }

if ($validated) {
    require_once("api/include/DbHandler.php");
    $db = new DbHandler();
    if ($userData = $db->login($email, $password)) {
        session_start();
        $_SESSION["user_id"] = $userData["id"];
        $_SESSION["name"] = $userData["name"];
        $_SESSION["email"] = $email;
        $_SESSION["api_key"] = $userData["api_key"];
        ob_clean();
        print "success";
    } else {
        ob_clean();
        print "Incorrect credentials. Please try again.";
    }
} else {
    ob_clean();
    print "Some parameters missing or empty. Please try again.";
}
?>