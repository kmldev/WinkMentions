<?php

  ini_set('memory_limit','128M');

  require './_compag.php';
  require './verificaadmin.inc.php';
  require './dashboard.utils.php';
  require './libs/instabasic.sdk.php';

  require './meta.setup.php';

  if (!$winkmentionsapp->validateAccessToken()) {
    die("NO ACCESS TOKEN");
    exit;
  }
  
  $accesstoken = $winkmentionsapp->getAccessToken();

  try {

    $graph = new cInstaGraph($accesstoken);
    $me = $graph->getMe(); 

  } catch (InstaBasicException $e) {
    var_dump($e->getMessage());
  }


  var_dump($me);