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
require_once("MysqliDb.php");

define ("DDBB_HOST", "localhost");
define ("DDBB_USER", "oauth");
define ("DDBB_PASS", "oauth");
define ("DDBB_NAME", "oauth");
define ("OAUTH_MAX_TOKEN_LENGTH", 80);
define ("OAUTH_TOKEN_EXPIRATION", 3600);

/**
 * DbHandler Class
 * @author    Ignacio Nieto Carvajal <contact@digitalleaves.com>
 **/
class DbHandler {
    /** database connector */
    private $db;
    
    /** DbHandler initializer */
    public function __construct() {
        $this->db = new \MysqliDb(DDBB_HOST, DDBB_USER, DDBB_PASS, DDBB_NAME);
    }

    /** 
     * Gets the data of a user based on its API Key.
     * Returns the data of the user if found, false otherwise.
     * @param string $apiKey the API Key of the user.
     */
    public function getUserDataByApiKey($apiKey) {
        $sanitizedApiKey = $this->db->escape($apiKey);
        $this->db->where("api_key", $sanitizedApiKey);
        if ($userData = $this->db->getOne("users")) {
            return $userData;
        } else { return false; }
    }
    
    /**
     * Gets the data of a user based on its id.
     * Returns the data of the user if found, false otherwise.
     * @param string $id the id of the user.
     */
    public function getUserDataByEmail($email) {
        $sanitizedEmail = $this->db->escape($email);
        $this->db->where("email", $sanitizedEmail);
        if ($userData = $this->db->getOne("users")) {
            return $userData;
        } else { return false; }
    }

    /**
     * Registers the user. Returns true if registration was successful and false otherwise.
     * @param string $name name for the user.
     * @param string $email email for the user.
     * @param string $password password for the user.
     */
    public function register($name, $email, $password) {
        // check email first
        if ($this->getUserDataByEmail($email) !== false) { return false; }
        
        // generate a unique API Key first
        do {
            $apiKey = $this->randomToken();
        } while ($this->getUserDataByApiKey($apiKey) !== false);
        // generate password hash
        require_once("PassHash.php");
        $hash = PassHash::hash($password);
        
        // insert the new user.
        $sanitizedName = $this->db->escape($name);
        $sanitizedEmail = $this->db->escape($email);
        $data = array(
            "name" => $sanitizedName, 
            "email" => $sanitizedEmail, 
            "password" => $hash, 
            "api_key" => $apiKey, 
            "creation_date" => $this->db->now());
        if ($this->db->insert("users", $data) !== false) { return true; } else { return false; }
    }
    
    /**
     * Logs the user in. Returns a userData if the login is successful and false otherwise.
     * @param string $email email for the user.
     * @param string $password password for the user.
     */
    public function login($email, $password) {
        // get user data first
        if ($userData = $this->getUserDataByEmail($this->db->escape($email))) {
            require_once("PassHash.php");
            if (PassHash::check_password($userData["password"], $password)) {
                // prepare the needed info
                $result = array(
                    "id" => $userData["id"], 
                    "name" => $userData["name"], 
                    "email" => $userData["email"], 
                    "api_key" => $userData["api_key"]);
                return $result;
            } else { return false; }
        } else { return false; }
    }
    
    /**
     * Access the data of an OAuth-validated App and returns its data if found.
     * @param String $clientId Client ID for the App to get data from.
     * @returns Array an associative array with the data of the App, or false if none found.
     */
    public function getAppDataByClientId($clientId) {
        $sanitizedId = $this->db->escape($clientId);
        $this->db->where("client_id", $sanitizedId);
        if ($appData = $this->db->getOne("apps")) {
            return $appData;
        } else { return false; }
    }
    
    /**
     * Generates an Access Token and stores it in the database.
     * @param String $userId User ID identifying the user this request is made for.
     * @param String $clientId Client ID for the App this code is going to be associated to.
     * @returns the generated auth code if it was properly stored, false if an error occurred.
     */
    public function generateAccessToken($userId, $clientId) {
        $code = $this->randomToken($userId);
        $data = array(
            "user_id" => $userId, 
            "type" => "access", 
            "value" => $code, 
            "expiration" => $this->db->now("1h"), 
            "client_id" => $clientId);
        if ($this->db->insert("tokens", $data)) { return $code; } else { return false; }
    }
    
    /**
     * Generates an Refresh Token code and stores it in the database.
     * @param String $userId User ID identifying the user this request is made for.
     * @param String $clientId Client ID for the App this code is going to be associated to.
     * @returns the generated auth code if it was properly stored, false if an error occurred.
     */
    public function generateRefreshToken($userId, $clientId) {
        // generate a new one.
        $code = $this->randomToken($userId);
        $data = array(
            "user_id" => $userId, 
            "type" => "refresh", 
            "value" => $code, 
            "expiration" => $this->db->now("1Y"), 
            "client_id" => $clientId);
        if ($this->db->insert("tokens", $data)) { return $code; } else { return false; }
    }
    
    /**
     * Generates an Access Token code and stores it in the database.
     * @param String $userId User ID identifying the user this request is made for.
     * @param String $clientId Client ID for the App this code is going to be associated to.
     * @returns the generated auth code if it was properly stored, false if an error occurred.
     */
    public function generateAuthCode($userId, $clientId) {
        $code = $this->randomToken($userId);
        $data = array(
            "user_id" => $userId, 
            "type" => "code", 
            "value" => $code, 
            "expiration" => $this->db->now("1h"), 
            "client_id" => $clientId);
        if ($this->db->insert("tokens", $data)) { return $code; } else { return false; }
    }
    
    /**
     * Checks the app data to verify that client ID and client secret are valid.
     * @param String $clientId Client ID for the App.
     * @param String $clienSecret Client Secret for the App.
     * @returns true if the App is valid, false otherwise.
     */
    public function checkApp($clientId, $clientSecret) {
        if ($appData = $this->getAppDataByClientId($clientId)) {
            return ($appData["client_secret"] == $clientSecret);
        } else { return false; }
    }
    
    /**
     * Checks if the authorization token provided is valid and has not expired.
     * @returns true if there is a valid unexpired token matching the one provided as a parameter, false otherwise.
     */
    function getUserFromBearerAccessToken($token) {
        $this->db->where("value", $token)->where("type", "access")->where("expiration >= now()");
        if ($tokenData = $this->db->getOne("tokens")) { // The access token is valid. Get user data from user_id
            $this->db->where("id", $tokenData["user_id"]);
            if ($result = $this->db->getOne("users")) { return $result; } else { return false; }
        } else { return false; }
    }
    
    /**
     * Checks the code for the previous authorization request. If successful, generates an access token and returns it.
     * @param String $code The authorization code that was sent.
     * @param String $clienSecret Client Secret for the App.
     * @returns true if the App is valid, false otherwise.
     */
    public function createAccessTokensFromAuthorizationCode($code, $clientId) {
        // check code first.
        $this->db->where("value", $code)->where("client_id", $clientId)->where("type", "code")->where("expiration >= now()");
        if ($codeData = $this->db->getOne("tokens")) {
            if ($accessToken = $this->generateAccessToken($codeData["user_id"], $clientId)) {
                $refreshToken = $this->generateRefreshToken($codeData["user_id"], $clientId);
                $data = array(
                    "access_token" => $accessToken, 
                    "expires" => OAUTH_TOKEN_EXPIRATION, 
                    "refresh_token" => $refreshToken,
                    "error" => false
                );
                return $data;
            }
        }
        return false;
    }
        
    
    /** Utility functions */
    
    /**
     * Generates a random token. If a prefix is specified, the token will start with the string prefix_.
     */
    public function randomToken($prefix = null) {
        $token = isset($prefix) ? $prefix . "_" : "";
        $token .= base64_encode(openssl_random_pseudo_bytes(40));
        if (strlen($token) > OAUTH_MAX_TOKEN_LENGTH) {
            $token = substr($token, 0, OAUTH_MAX_TOKEN_LENGTH);
        }
        return $token;
    }
} // END class

?>