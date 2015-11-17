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

require 'vendor/autoload.php';
$app = new Slim\App();

// constants
define('OAUTH_BEARER_TOKEN_REGEXP', '/Bearer\s+(?<token>.+)$/');

// Global user id.
$userData = null;

/** Utility functions */

/**
 * Authenticates the user via API Key with the REST interface.
 */
$authenticate = function ($request, $response, $next) {
    $headers = apache_request_headers();
    error_log("Headers: " . var_export($headers, true));
    if (isset($headers["api_key"])) { // do we have an api_key?
        $api_key = $headers["api_key"];
        // check user by API Key
        require_once("include/DbHandler.php");
        $db = new \DbHandler();
        if ($retrievedData = $db->getUserDataByApiKey($api_key)) { // API Key Valid
            global $userData;
            $userData = $retrievedData;
            $response = $next($request, $response);
        } else { // API Key invalid.
            $invalidCredData = array("error" => true, "msg" => "Authentication error. Invalid credentials.");
            echoResponse($response, $invalidCredData, 401);
        }
    } else if (isset($headers["Authorization"])) { // are we authenticating through a Bearer access token?
        $authLine = $headers["Authorization"];
        if ($retrievedData = getUserFromAuthorizationHeader($authLine)) { // Bearer Access Token Valid
            global $userData;
            $userData = $retrievedData;
            $response = $next($request, $response);
        } else {
            $invalidCredData = array("error" => true, "msg" => "Authentication error. Access token invalid.");
            echoResponse($response, $invalidCredData, 401);
        }
    } else { // api_key not found
        $noApiKeyData = array("error" => true, "msg" => "Authentication error. API Key missing.");
        echoResponse($response, $noApiKeyData, 401);
    }
    return $response;
};

/**
 * Checks for a valid acess token in the request's headers. The Access Token will be a bearer token
 * in the format: "Bearer as6sab87dasd76asd87asd87asd6f57". If it's valid, the data for
 * the user is returned, and authentication succeeds. Otherwise, false is returned.
 */
function getUserFromAuthorizationHeader($authLine) {
    // perform a match for a Bearer token
    preg_match(OAUTH_BEARER_TOKEN_REGEXP, $authLine, $matches);
    if (isset($matches["token"])) { // we do have a token. Now check in ddbb.
        $token = $matches["token"];
        require_once("include/DbHandler.php");
        $db = new \DbHandler();
        return $db->getUserFromBearerAccessToken($token);
    }
    return false;
}

/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoResponse($response, $data, $status_code = 200) {
    // setting response content type to json
    $newResponse = $response->withHeader('Content-type', 'application/json');
    // return response
    $newResponse->withStatus($status_code)->write(json_encode($data));
    return $response;
}

/**
 * Convenience method for returning a 400 bad request response.
 */
function badRequest($response, $msg = "Bad Request. The request could not be understood by the server due to malformed syntax.") {
    $data = array("error" => true, "msg" => $msg);
    return echoResponse($response, $data, 400);
} 

/** Routes. */

/** Root endpoint */
$app->get('/', function ($request, $response, $args) {
    $data = array("error" => false, "msg" => "Welcome to the OAuth provider tutorial project!");
    return echoResponse($response, $data);
});

/** Login endpoint */
$app->post('/login', function ($request, $response, $args) {
    $jsonData = $request->getParsedBody();
    if (isset($jsonData["email"]) && isset($jsonData["password"])) {
        $email = $jsonData["email"];
        $password = $jsonData["password"];
        // invoke login call.
        require_once("include/DbHandler.php");
        $db = new DbHandler();
        if ($loggedUserData = $db->login($email, $password)) {
            global $userData;
            $userData = $loggedUserData;
            $data = array("error" => false, "user" => $userData);
            return echoResponse($response, $data);
        } else {
            $data = array("error" => true, "msg" => "Unable to login. Incorrect credentials.");
            return echoResponse($response, $data, 401);
        }
    } else { return badRequest($response); }
});

/** Registration endpoint */
$app->post('/register', function ($request, $response, $args) {
    $jsonData = $request->getParsedBody();
    if (isset($jsonData["email"]) && isset($jsonData["name"]) && isset($jsonData["password"])) {
        $email = $jsonData["email"];
        $name = $jsonData["name"];
        $password = $jsonData["password"];
        // invoke login call.
        require_once("include/DbHandler.php");
        $db = new DbHandler();
        if ($db->register($name, $email, $password)) {
            $data = array("error" => false, "msg" => "You are successfully registered!");
            return echoResponse($response, $data);
        } else {
            $data = array("error" => true, "msg" => "Unable to register. Are you sure you are not already registered?.");
            return echoResponse($response, $data, 400);
        }
    } else { return badRequest($response); }
});

/** Profile info of the user endpoint */
$app->get('/me', function ($request, $response, $args) {
    global $userData;
    $result = array(
        "id" => $userData["id"], 
        "name" => $userData["name"], 
        "email" => $userData["email"], 
        "api_key" => $userData["api_key"]);
    $data = array("error" => false, "user" => $result);
    return echoResponse($response, $data);
})->add($authenticate);

/** OAuth provider routes: Access token returns a token from a valid code. */
$app->post('/access_token', function ($request, $response, $args) {
    $jsonData = $request->getParsedBody();
    if (isset($jsonData["client_id"]) && isset($jsonData["client_secret"]) && 
        isset($jsonData["redirect_uri"]) && isset($jsonData["code"])) {
        // get parameters
        $clientId = $jsonData["client_id"];
        $clientSecret = $jsonData["client_secret"];
        $redirectURI = $jsonData["redirect_uri"];
        $code = $jsonData["code"];
        
        // check client id and client secret.
        error_log("MyOAuthProvider: Checking App...");
        require_once("include/DbHandler.php");
        $db = new DbHandler();
        if (!$db->checkApp($clientId, $clientSecret)) {
            return badRequest($response, "Invalid App credentials. Please check your App ID and Secret.");
        }
        
        // check code and generate access token for the user.
        error_log("MyOAuthProvider: Creating access token from authorization code.");
        if ($data = $db->createAccessTokensFromAuthorizationCode(urldecode($code), $clientId)) {
            return echoResponse($response, $data);
        } else { return badRequest($response); }
    } else { return badRequest($response); }
});


/** OAuth provider routes: Refresh the access token with a refresh token. */
$app->post('/refresh', function ($request, $response, $args) {
    $jsonData = $request->getParsedBody();
    if (isset($jsonData["client_id"]) && isset($jsonData["client_secret"]) && isset($jsonData["refresh_token"])) {
        // get parameters
        $clientId = $jsonData["client_id"];
        $clientSecret = $jsonData["client_secret"];
        $refreshToken = $jsonData["refresh_token"];
        
        // check client id and client secret.
        error_log("MyOAuthProvider: Checking App...");
        require_once("include/DbHandler.php");
        $db = new DbHandler();
        if (!$db->checkApp($clientId, $clientSecret)) {
            return badRequest($response, "Invalid App credentials. Please check your App ID and Secret.");
        }
        
        // check code and generate access token for the user.
        error_log("MyOAuthProvider: Refreshing access token with refresh token: $refreshToken");
        if ($data = $db->refreshAccessTokenWithRefreshToken($refreshToken, $clientId)) {
            return echoResponse($response, $data);
        } else { return badRequest($response); }
    } else { return badRequest($response); }
});

/**
 * Run the Slim application
 */
$app->run();

?>