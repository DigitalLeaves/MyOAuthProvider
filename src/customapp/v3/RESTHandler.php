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

define('REST_AUTH_NONE', 0);
define('REST_AUTH_BASIC', 1);
define('REST_AUTH_OAUTH', 2);

define('REST_AUTH_HEADER_AUTHORIZATION', "Authorization");
define('REST_AUTH_HEADER_BEARER', "Bearer");

define('DECODE_FORMAT_NONE', 0);
define('DECODE_FORMAT_JSON', 1);
define('DECODE_FORMAT_XML', 2);

define('MYOAUTHPROVIDER_CLIENT_ID', 'nkU350F4PfKLf9umf798qX6jlP2ya501');
define('MYOAUTHPROVIDER_CLIENT_SECRET', 'v2Q0m12CB5tM36xJ4K4g2Vv06ASD52HD');

/**
 * Class to handle all REST operations
 * This class will allow to perform request to REST APIs (GET, POST, PUT, DELETE) using curl
 *
 * @author Ignacio Nieto Carvajal
 * @link URL http://digitalleaves.com
 */
class RESTHandler {
	private $authType;
	private $authHeaderName;
	private $authHeaderValue;

	/** 
	 * Builds a new RESTHandler
	 */
	function __construct() {
		$this->authType = REST_AUTH_NONE;
		$this->authHeaderName = NULL;
		$this->authHeaderValue = NULL;
    }

	/**
	 * Performs a request using certain method to a given URL, optionally decoding the results from JSON
	 * @param $method String GET, POST, PUT, DELETE
	 * @param $url String string containing the URL to call
	 * @param $decodeJSON one of DECODE_FORMAT_NONE, DECODE_FORMAT_JSON, DECODE_FORMAT_XML
	 * @param $data Array array of data to include in the request.
	 */
	public function performRequest($method, $url, $decodeFormat, $data = false)
	{
	    $curl = curl_init();
	
	    switch ($method)
	    {
	        case "POST":
	            curl_setopt($curl, CURLOPT_POST, true);
	
	            if ($data)
	                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	            break;
	        case "PUT":
	            curl_setopt($curl, CURLOPT_PUT, true);
	            if ($data)
	                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	            break;
	        case "DELETE":
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
	            if ($data)
	                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	        case "GET":
	        default:
	            if ($data)
	                $url = sprintf("%s?%s", $url, http_build_query($data));
	    }
	
	    // Optional Authentication:
	    if (isset($this->authType)) {
	    	if ($this->authType == REST_AUTH_BASIC) {
		    	curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
				if (isset($authHeaderName) && isset($authHeaderValue)) { curl_setopt($curl, CURLOPT_USERPWD, $authHeaderName.":".$authHeaderValue); }
	    	} else if ($this->authType == REST_AUTH_OAUTH) {
		    	$headers = array($this->authHeaderName . ": " . $this->authHeaderValue);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

	    	}
	    }
	
		// url and return transfer
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	
		// other options
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);

	
	    $result = curl_exec($curl);
	    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

	    curl_close($curl);
	
		// return depending on the format
		if ($decodeFormat == DECODE_FORMAT_XML) { 
			$xml = simplexml_load_string($result);
			$json = json_encode($xml);
			return json_decode($json, true);
		} else if ($decodeFormat == DECODE_FORMAT_JSON) { 
			return json_decode($result, true);
		} else {
			return $result;
		}
	}
	
	/** 
	 * Sets the authentication options for the curl request.
	 */
	public function setAuthentication($authType, $authHeaderValue, $authHeaderName = NULL) {
		if ($authType == REST_AUTH_BASIC) {
			$this->authHeaderName = $authHeaderName;
			$this->authHeaderValue = $authHeaderValue;
		} else if ($authType == REST_AUTH_OAUTH) {
			$this->authHeaderValue = $authHeaderValue;
			$this->authHeaderName = NULL;
		} else {
			$this->authHeaderValue = NULL;
			$this->authHeaderName = NULL;
		}
	}
    
    /**
     * Performs authentication with "My OAuth Provider"
     * @returns the OAuth tokens if successfully authenticated, false otherwise.
     */
	public function authenticateWithMyOauthProvider($redirect_uri, $code) {
       // build request URL and POST parameters
	   $url = "http://myoauthprovider.com:8080/api/access_token";
       $params = array(
           "client_id" => MYOAUTHPROVIDER_CLIENT_ID, 
           "client_secret" => MYOAUTHPROVIDER_CLIENT_SECRET,
           "redirect_uri" => $redirect_uri,
           "code" => $code); 
        // perform request and analyze results
        if ($result = $this->performRequest("POST", $url, DECODE_FORMAT_JSON, $params)) {
            if (isset($result["error"]) && ($result["error"] == false)) {
                // return tokens and expiration time.
                $tokens = array();
                $tokens["access_token"] = $result["access_token"];
                $tokens["expires"] = $result["expires"];
                $tokens["refresh_token"] = $result["refresh_token"];
                return $tokens;
            }
       }
       return false;
	}

    /**
     * Get the profile data from "My OAuth Provider"
     * @returns the profile information for the user identified with $access_token, false otherwise.
     */
	public function getProfileDataFromMyOAuthProvider($access_token) {
        $url = "http://myoauthprovider.com:8080/api/me";
        $this->authType = REST_AUTH_OAUTH;
        $this->authHeaderName = REST_AUTH_HEADER_AUTHORIZATION;
        $this->authHeaderValue = REST_AUTH_HEADER_BEARER . " " . $access_token;
        if ($result = $this->performRequest("GET", $url, DECODE_FORMAT_JSON)) {
            if (isset($result["user"])) {
                error_log("CustomApp: Results from get profile data: " . var_export($result, true));
                // return user data.
                $retrievedUser = $result["user"];
                $userData = array();
                $userData["name"] = $retrievedUser["name"];
                $userData["email"] = $retrievedUser["email"];
                return $userData;
            }
        }
        return false;
	}
    
    /**
     * Requests a access token refresh using the refresh token.
     * @returns the new access token if successful. False otherwise.
     */
    public function refreshAccessToken($refresh_token) {
        $url = "http://myoauthprovider.com:8080/api/refresh";
        $params = array(
           "client_id" => MYOAUTHPROVIDER_CLIENT_ID, 
           "client_secret" => MYOAUTHPROVIDER_CLIENT_SECRET,
           "refresh_token" => $refresh_token
        );
        // perform request and analyze results
        if ($result = $this->performRequest("POST", $url, DECODE_FORMAT_JSON, $params)) {
            if (isset($result["error"]) && ($result["error"] == false)) {
                return $result;
            }
       }
       return false;
    }
}

?>