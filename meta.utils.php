<?php

  require_once './vendor/autoload.php';

  class cMetaAppSetup {
    public $name = "";
    public $id_app = "";
    public $secret_key = "";
    public $redirecturi = "";
    public $accesstoken = "";

    function __construct($name,$id_app, $secret_key, $redirecturi) {
      $this->name = $name;
      $this->id_app = $id_app;
      $this->secret_key = $secret_key;
      $this->redirecturi = $redirecturi;
      $this->restoreAccessToken();
    }

    function restoreAccessToken() {
      $acctok = $_SESSION[$this->name."::accesstoken"] ?? "";
      $this->accesstoken = $acctok;
    }

    function parseAccessToken($accesstoken) {
      if (is_array($accesstoken))
        $token = $accesstoken["access_token"] ?? "";
      else
        $token = $accesstoken;
      $this->accesstoken = $token;
      return $token;
    }

    function saveAccessToken($accesstoken) {
      if ($accesstoken === false) {
        $this->accesstoken = "";
        if (isset($_SESSION[$this->name."::accesstoken"])) unset($_SESSION[$this->name."::accesstoken"]);
      } else {
        $this->accesstoken = $this->parseAccessToken($accesstoken);
        $_SESSION[$this->name."::accesstoken"] = $this->accesstoken;          
      }
      return $this->validateAccessToken();
    }

    function getAccessToken() {
      return $this->accesstoken;
    }

    function validateAccessToken() {
      return $this->accesstoken != "";
    }

    function revokeAccessToken() {
      $this->saveAccessToken(false);
    }

    function getConfigArray() {
      return [
        "app_id" => $this->id_app,
        "app_secret" => $this->secret_key
      ];
    }

    function getRedirectUri() {
      return $this->redirecturi;
    }

    static function enablePageSubscription($idpage, $accesstoken) {
        // Set up cURL session
        $ch = curl_init();
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v19.0/$idpage/subscribed_apps?subscribed_fields=feed&access_token=$accesstoken");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
    
        // Execute cURL request
        $response = curl_exec($ch);
        
        // Get HTTP response code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Check for errors
        if($response === false) {
            throw new Exception('cURL Error: ' . curl_error($ch));
        }
        
        // Close cURL session
        curl_close($ch);
        
        // Check HTTP response code
        if($httpCode !== 200) {
            throw new Exception("HTTP Error: $httpCode");
        }
        
        // Parse response
        $responseData = json_decode($response, true);
        
        // Check if subscription was successful
        if(isset($responseData['success']) && $responseData['success'] === true) {
            return true;
        } else {
            return false;
        }
    }   

  }