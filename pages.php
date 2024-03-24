<?php

  ini_set('memory_limit','128M');

  require './_compag.php';
  require './verificaadmin.inc.php';
  require './dashboard.utils.php';

  require './meta.setup.php';

  use Instagram\User\User;

  if (!$winkmentionsapp->validateAccessToken()) {
    die("NO ACCESS TOKEN");
    exit;
  }
  
  $accesstoken = $winkmentionsapp->getAccessToken();

  $config = array(
      'access_token' => $accesstoken,
  );
  
  // instantiate and get the users info
  $user = new User( $config );

  // get the users pages
  $pages = $user->getUserPages();

  var_dump($pages);