<?php

  DEFINE('SESSION_NAME_ADMIN','IGTEST24b_admlogin');
  
  class ClsAdminAccess {

    static private $usrlist = [
      'admin-ig' => ['fg45KPee#',['name'=>'IG Test','avatar'=>'avt_ig.png','levels'=>['dataentry','report']]],
      'test-fb' => ['demoTest22',['name'=>'IG Test','avatar'=>'avt_ig.png','levels'=>['dataentry','report']]],
      //'smtsrv_1290' => ['nrr-f92-hew',['name'=>'SMART-SERVICE','avatar'=>'avt_report.png','levels'=>['dataentry']]]
      //'cclor-445n' => ['zg2-g33-kal',['name'=>'CUSTOMER-CARE','avatar'=>'avt_report.png','levels'=>['dataentry']]]
    ];

    private $logged = false;
    private $infos = [];
  
    function __construct() {

      $this->restoreSessionAdmin();
      
    }

    private static function getSessionHash() {
      $hz = 'DEFAULT_SESSION_HASH:'.SESSION_NAME_ADMIN;
      try {
        $remoteip = (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $hz = SESSION_NAME_ADMIN.':'.$remoteip.':'.$_SERVER['HTTP_USER_AGENT'];
      } catch(Exception $e) {
        // foo        
      }
      return sha1($hz).md5($hz);
    }

    function reset() {
      $_SESSION[SESSION_NAME_ADMIN] = null;
      $_SESSION[SESSION_NAME_ADMIN.'-infos'] = serialize([]);
    }

    function controllaLogin($user,$password) {

      $this->reset();
    
      if (isset(self::$usrlist[$user])) {

        $user = self::$usrlist[$user];

        if ($user[0] == $password) {
          $_SESSION[SESSION_NAME_ADMIN] = self::getSessionHash();
          $_SESSION[SESSION_NAME_ADMIN.'-infos'] = serialize($user[1]);
          $this->restoreSessionAdmin();
          return TRUE;
        }

      }

      return FALSE;
    }
    
    function restoreSessionAdmin() {

      if (isset($_SESSION[SESSION_NAME_ADMIN]) && $_SESSION[SESSION_NAME_ADMIN] == self::getSessionHash()) {

        $this->logged = true;
        $this->infos = unserialize($_SESSION[SESSION_NAME_ADMIN.'-infos']);

        return true;
      }

      $this->reset();
  
      return false;
  
    }

    function controllaAdmin() {

      return $this->logged;
      
    }

    function hasLevel($lev) {

      if (!$this->infos) return false;
      $lst = ($this->infos['levels']) ?: [];
      return (in_array($lev,$lst));

    }

    function get($name) {
      if (!$this->infos) return false;
      return isset($this->infos[$name]) ? $this->infos[$name] : '';      
    }

  }


  $skippages = ['index.php','login.php','dologin.php'];


  if (!session_id()) session_start();
  
  $admlogin = new ClsAdminAccess();

  $page = basename($_SERVER['SCRIPT_NAME']);

  if (!in_array($page,$skippages)) {

    if (!$admlogin->controllaAdmin()) {
      header("Location: login.php");
      exit;
    }

  }
