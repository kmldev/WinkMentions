<?php

  ini_set('memory_limit','128M');

  require './_compag.php';
  require './verificaadmin.inc.php';
  require './dashboard.utils.php';

  require './meta.setup.php';

  use Instagram\FacebookLogin\FacebookLogin;
  use Instagram\AccessToken\AccessToken;
  use Instagram\User\Tags;

  require './libs/instabasic.config.php';
  require './libs/instabasic.sdk.php';

  if (!$winkmentionsapp->validateAccessToken()) {
    die("NO ACCESS TOKEN");
    exit;
  }
  
  $accesstoken = $winkmentionsapp->getAccessToken();

  // $config = array( // instantiation config params
  //     'user_id' => '17841464601321305',
  //     'access_token' => $accesstoken,
  // );
  
  // // instatiate tags for use
  // $tags = new Tags( $config );
  // // get posts user is tagged in
  // $userTags = $tags->getSelf();
  
  // var_dump($userTags);

    try {

      $iggraph = new cInstaGraph($accesstoken);

      $accounts = $iggraph->getAccounts();

      $me = $accounts[0];

      echo "--- Parsing: ".$me->instagram_business_account."\n";

      $me = new cInstaGraphMe($me->instagram_business_account,$me->name);      
      $iggraph->setMe($me);

      $userTags = $iggraph->getTags();
      var_dump($userTags);

    } catch (\Exception $e) {
      echo $e->getMessage();
    }

