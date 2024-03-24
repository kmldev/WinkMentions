<?php

  ini_set('memory_limit','128M');

  require './_compag.php';
  require './verificaadmin.inc.php';
  require './dashboard.utils.php';

  require './meta.setup.php';
  use Instagram\User\User;

  require './libs/instabasic.config.php';
  require './libs/instabasic.sdk.php';

  if (!$winkmentionsapp->validateAccessToken()) {
    die("NO ACCESS TOKEN");
    exit;
  }

  $igbusinessLogin = new cInstaBasic(true);
  
  $accesstoken = $winkmentionsapp->getAccessToken();
  $iggraph = new cInstaGraph($accesstoken);
  
  $config = array(
      'access_token' => $accesstoken,
  );

  // get the users pages
  $pages = $iggraph->getAccounts();

  //var_dump($pages);

  try {

    if (isset($pages['data'])) {
      $data = $pages['data'];
      $page = $data[0];
      $igid = $page["instagram_business_account"]["id"];
      $igtoken = $page["access_token"];
      $newgraph = new cInstaGraph($igtoken);
      $me = $newgraph->getMe();
      var_dump($me);
    }

  } catch (Exception $e) {
    
    echo $e->getMessage();
    exit;
  }

