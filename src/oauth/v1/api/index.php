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

// Global user id.
$userData = null;

/** Utility functions */
$authenticate = function ($request, $response, $next) {
    $headers = apache_request_headers();
    if (isset($headers["api_key"])) { // do we have an api_key
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
    } else { // api_key not found
        $noApiKeyData = array("error" => true, "msg" => "Authentication error. API Key missing.");
        echoResponse($response, $noApiKeyData, 401);
    }
    return $response;
};

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
function badRequest($response) {
    $data = array("error" => true, "msg" => "Bad Request. The request could not be understood by the server due to malformed syntax.");
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

/**
 * Run the Slim application
 */
$app->run();

?>