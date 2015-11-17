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
    
    /** Utility functions */
    protected function randomToken() {
        return base64_encode(openssl_random_pseudo_bytes(40));
    }
    
} // END class

?>