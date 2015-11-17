<?php
/**
 * The Redirect endpoint for customapp serves as a general point for all OAuth authentication management.
 * It analizes the request and responds accordingly.
 */

session_start();

/** Case 1: first response of the OAuth provider with the authentication code to exchange for a token */
if (isset($_GET["code"]) && isset($_GET["redirect_uri"])) {
    $code = $_GET["code"];
    $redirect_uri = $_GET["redirect_uri"];
    error_log("CustomApp: received code " . $code . " with redirect URI: " . $redirect_uri);
    require('RESTHandler.php');
    $rh = new RESTHandler();
    // exchange code for an access token and refresh token.
    if ($result = $rh->authenticateWithMyOAuthProvider($redirect_uri, $code)) {
        error_log("CustomApp: Result from access_token: " . var_export($result, true));
        // set session parameters
        $_SESSION["access_token"] = $result["access_token"];
        $_SESSION["refresh_token"] = $result["refresh_token"];
        // redirect to index.
        header("Location: index.php");
    } else { // error. Code invalid.
        header("Location: unable_authenticate.php");
    }

/** Case 2: interactive response of the OAuth provider, with the access and refresh tokens */
} else if (isset($_GET["access_token"]) && isset($_GET["refresh_token"]) && isset($_GET["expires"])) {
    // set session parameters
    $_SESSION["access_token"] = $_GET["access_token"];
    $_SESSION["refresh_token"] = $_GET["refresh_token"];
    // redirect to index.
    header("Location: index.php");

/** Case 3: we already have a session with the access and refresh tokens. Redirect to index */
} else if (isset($_SESSION["access_token"]) && isset($_SESSION["refresh_token"])) {
    header("Location: index.php");

/** Case 4: Authentication error */
} else if (isset($_GET["error"])) {
    $errorType = $_GET["error"];
    $description = isset($_GET["error_description"]) ? "&error_description=" . urlencode($_GET["error_description"]) : "";
    header("Location: unable_authenticate.php?error=" .$errorType . $description);
/** Else some kind of error happened. Redirect to "unable to authenticate" message */
} else { ob_clean(); header("Location: unable_authenticate.php"); }

?>