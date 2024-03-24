<?php

class InstaBasicException extends Exception {
  private $error_type;
  function __construct($error_type, $message, $code = 0, Exception $previous = null) {
    $this->error_type = $error_type;
    parent::__construct($message, $code, $previous);
  }
  public function getErrorType() {
    return $this->error_type;
  }
  public function __toString() {
    return __CLASS__ . ": [{$this->error_type}] [{$this->code}]: {$this->message}\n";
  }
  static public function createFromResponse($response) {
    $error_type = $response['error_type'];
    $message = $response['message'];
    $code = $response['code'];
    return new InstaBasicException($error_type, $message, $code);
  }
}

class InstaBasicOAuthException extends Exception
{
    public $message;
    public $type;
    public $code;
    public $error_subcode;
    public $fbtrace_id;

    public function __construct($json) {
        $data = json_decode($json, true);

        if ($data === null || !isset($data['error'])) {
            throw new InvalidArgumentException("Invalid JSON or missing 'error' field");
        }

        $error = $data['error'];

        $this->message = isset($error['message']) ? $error['message'] : '';
        $this->type = isset($error['type']) ? $error['type'] : '';
        $this->code = isset($error['code']) ? $error['code'] : 0;
        $this->error_subcode = isset($error['error_subcode']) ? $error['error_subcode'] : 0;
        $this->fbtrace_id = isset($error['fbtrace_id']) ? $error['fbtrace_id'] : '';

        parent::__construct($this->message, $this->code);
    }

    public static function isError($json) {
        $data = is_array($json) ? $json : json_decode($json, true);
        return isset($data['error']);
    }
}

class cInstaBasic {
  private $client_id = "";
  private $client_secret = "";

  private $access_token = "";
  private $access_token_expires = 0;
  private $user_id = "";

  // Scopes
  const SCOPE_USER_PROFILE = 'user_profile';
  const SCOPE_USER_MEDIA = 'user_media';

  public function __construct($client_id = "", $client_secret = "") {
    if ($client_id === true) {
      if (class_exists("InstaBasicConfig")) {
        $this->client_id = InstaBasicConfig::client_id;
        $this->client_secret = InstaBasicConfig::client_secret;
      } else {
        throw new Exception("InstaBasicConfig class not found");
      }
    } else if ($client_id) {
      $this->$client_id = $client_id;
      $this->$client_secret = $client_secret;
    }
  }

  public function getAuthorizeURL($redirect_uri, $scopes = []) {
    global $web;
    $url = 'https://api.instagram.com/oauth/authorize';
    $params = array(
      'client_id' => $this->client_id,
      'redirect_uri' => urlencode($redirect_uri),
      'scope' => implode(",", $scopes),
      'response_type' => 'code'
    );

    $request = $url . '?' . implode("&", array_map(fn ($val, $key) => $key . "=" . $val, $params, array_keys($params)));

    return $request;

    // $ch = curl_init();
    // curl_setopt($ch, CURLOPT_URL, $request);

    // $web->line("@GREEN@" . $request);

    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
    // curl_setopt($ch, CURLOPT_HEADER, true); // Include headers in the response
    // $response = curl_exec($ch);
    // $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // // Separate headers from response
    // $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    // $headers = substr($response, 0, $header_size);
    // $response = substr($response, $header_size);

    // curl_close($ch);

    // if ($httpCode == 200) {
    //   return $response;
    // } else {
    //   throw new InstaBasicException("HTTP_AUTH", "Error getting graph/me: $httpCode\n" . $response . "\n" . $headers);
    // }
  }

  public function getBusinessLoginForInstagram($redirect_uri, $scopes = []) {
    $url = 'https://www.facebook.com/dialog/oauth';
    $params = array(
      'client_id' => $this->client_id,
      'display' => 'page',
      'extras' => [
        'setup' => [
          'channel' => 'IG_API_ONBOARDING',
        ],
      ],
      'redirect_uri' => urlencode($redirect_uri),
      'response_type' => 'token',
      'scope' => implode(",", $scopes),
    );

    $request = $url . '?' . implode("&", array_map(fn ($val, $key) => $key . "=" . $val, $params, array_keys($params)));

    return $request;
  }

  public function getCallbackCode() {
    if (isset($_GET['code'])) {
      return $_GET['code'];
    }
    return "";
  }

  public function getAccessToken($code) {
    if ($this->hasAccessTokenExpired() == false) {
      return $this->access_token;
    }

    $url = 'https://api.instagram.com/oauth/access_token';
    $params = array(
      'client_id' => $this->client_id,
      'client_secret' => $this->client_secret,
      'grant_type' => 'authorization_code',
      'redirect_uri' => InstaBasicConfig::redirect_uri,
      'code' => $code
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
    $response = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($response, true);
    if (isset($json['access_token']) && isset($json['user_id'])) {
      $this->access_token = $json['access_token'];
      $this->user_id = $json['user_id'];
      $this->access_token_expires = strtotime("+1 hour");
      return $this->access_token;
    } else {
      throw new InstaBasicException($json['error_type'], $json['error_message'], $json['code']);
    }
  }

  public function hasAccessTokenExpired() {
    return $this->access_token_expires < time();
  }

  public function getUserId() {
    return $this->user_id;
  }

  public function revokeAccessToken() {
    $this->access_token = "";
    $this->access_token_expires = 0;
  }
}

class cInstaGraph {
  private $access_token;
  /** @var cInstaGraphMe */
  private $me = null;

  public function __construct($access_token,$iduser=0) {
    $this->access_token = $access_token;
    if ($iduser) {
      $me = new cInstaGraphMe($iduser,"");
      $this->me = $me;
    }
  }

  private function getGraph($id, $method = "", $fields = []) {
    if (is_array($method)) {
      $fields = $method;
      $method = "";
    }
    $url = 'https://graph.instagram.com/' . $id;
    if ($method) $url .= '/' . $method;
    $params = [];
    if ($fields) $params = ['fields' => implode(',', $fields)];
    $params['access_token'] = $this->access_token;
    $url .= '?' . implode('&', array_map(fn ($val, $key) => $key . "=" . $val, $params, array_keys($params)));

    //var_dump($url);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
    curl_setopt($curl, CURLOPT_HEADER, true); // Include headers in the response
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    // Separate headers from response
    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $header_size);
    $response = substr($response, $header_size);

    curl_close($curl);

    if ($httpCode != 200) throw new InstaBasicException("HTTP_AUTH", "Error getting graph/{$id}: $httpCode\n" . $response . "\n" . $headers);

    if ($response) {
      $data = json_decode($response, true);
      return $data;
    } else {
      // Handle cURL request failure
      return null;
    }
  }

  private function getFacebookGraph($id, $method = "", $fields = []) {
    if (is_array($method)) {
      $fields = $method;
      $method = "";
    }
    $url = 'https://graph.facebook.com/v19.0/' . $id;
    if ($method) $url .= '/' . $method;
    $params = [];
    if ($fields) $params = ['fields' => implode(',', $fields)];
    $params['access_token'] = $this->access_token;
    $url .= '?' . implode('&', array_map(fn ($val, $key) => $key . "=" . $val, $params, array_keys($params)));

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
    curl_setopt($curl, CURLOPT_HEADER, true); // Include headers in the response
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    // Separate headers from response
    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $header_size);
    $response = substr($response, $header_size);

    curl_close($curl);

    if ($httpCode == 400) {
      if (InstaBasicOAuthException::isError($response)) throw new InstaBasicOAuthException($response);
    }

    if ($httpCode != 200) throw new InstaBasicException("HTTP_AUTH", "Error getting graph/{$id}: $httpCode\n" . $response . "\n" . $headers);

    if ($response) {
      $data = json_decode($response, true);
      return $data;
      // if (isset($data['id']) && isset($data['username'])) {
      //   return new cInstaGraphMe($data['id'], $data['username']);
      // } else {
      //   // Handle error response
      //   return $data;
      // }
    } else {
      // Handle cURL request failure
      return null;
    }
  }

  public function setMe(cInstaGraphMe $me) {
    $this->me = $me;
  }

  public function getUserId() {
    return $this->me->id;
  }
  
  public function getMe() {
    $data = $this->getGraph('me', ['id', 'username']);
    var_dump($data);
    if (isset($data['id']) && isset($data['username'])) {
      return new cInstaGraphMe($data['id'], $data['username']);
    } else {
      // Handle error response
      return $data;
    }

  }

  function getTags() {
    return $this->getFacebookGraph($this->getUserId(), 'tags', ['id','username']);
  }

  function getMentions() {
    return $this->getFacebookGraph($this->getUserId(), ['mentioned_media.media_id']);
  }

  function getMedia() {
    $response = $this->getGraph('me', 'media',['id', 'caption']);
    return new cInstaMediaResponse($response);
  }

  function getAccounts() {
    $response = $this->getFacebookGraph("me", 'accounts', ['id','name','access_token','instagram_business_account']);
    return cInstaAccount::parseAccounts($response);
  }

  function subscribeToFacebookApp($pageId, $subscribefield) {
    $url = "https://graph.facebook.com/v19.0/{$pageId}/subscribed_apps?subscribed_fields={$subscribefield}&access_token=".$this->access_token;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_POST, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    return array(
        'http_code' => $httpCode,
        'response' => $response
    );
  }

}

class cInstaGraphMe {
  public $id;
  public $username;

  public function __construct($id, $username) {
    $this->id = $id;
    $this->username = $username;
  }
}

class cInstaBasicMedia {
  public $id;
  public $caption;

  public function __construct($data) {
      $this->id = $data['id'];
      $this->caption = $data['caption'];
  }
}

class cInstaMediaResponse {
  public $media_list = [];
  public $before_cursor;
  public $after_cursor;

  public function __construct($json_data) {
      if (isset($json_data['data'])) {
          foreach ($json_data['data'] as $item) {
              $media = new cInstaBasicMedia($item);
              $this->media_list[] = $media;
          }
      }

      if (isset($json_data['paging']['cursors'])) {
          $cursors = $json_data['paging']['cursors'];
          $this->before_cursor = isset($cursors['before']) ? $cursors['before'] : null;
          $this->after_cursor = isset($cursors['after']) ? $cursors['after'] : null;
      }
  }
}

class cInstaUserToken {
  public $user_id;
  public $token;
  public $token_expires;

  const SALT = "%2a>%10%T4ImbDRHK0L/W8o4LfRp8ObdAw.Wtp1kos8pBIG6nlPCUo1ml8jHi";

  function __construct($user_id, $token) {
    $this->user_id = $user_id;
    $this->token = $token;
    $this->token_expires = strtotime("+1 hour");
  } 

  static function getFilename($user_id) {
    $fn = __DIR__."/tmp/".hash("sha256", self::SALT.$user_id).".json";
    return $fn;
  }

  /**
   * Undocumented function
   *
   * @param string $user_id
   * @return void|cInstaUserToken
   */
  static function restore($user_id) {
    $filecontainer = static::getFilename($user_id);
    if (file_exists($filecontainer)) {
      $data = file_get_contents($filecontainer);
      if ($data) {
        $token = unserialize($data,['allowed_classes'=>['cInstaUserToken']]);
        if ($token and $token->token_expires >= time()) return $token;
      }
    }
    return null;
  }

  static function save($user_id, $token) {
    $token = new cInstaUserToken($user_id,$token);
    $filecontainer = static::getFilename($user_id);
    file_put_contents($filecontainer, serialize($token));
    return $token;
  }

}

class cInstaAccount {
  public $id;
  public $name;
  public $access_token;
  public $instagram_business_account;

  public function __construct($data) {
    $this->id = $data['id'];
    $this->name = $data['name'];
    $this->access_token = $data['access_token'];
    $this->instagram_business_account = $data['instagram_business_account']['id'];
  }

  static function parseAccounts($response) {
    $accounts = [];
    $data = $response["data"] ?? [];
    foreach ($data as $item) {
      $account = new cInstaAccount($item);
      $accounts[] = $account;
    }
    return $accounts;
  }

}
