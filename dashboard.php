<?php

  require './_compag.php';
  require './verificaadmin.inc.php';
  require './dashboard.utils.php';

  require './meta.setup.php';
  require './libs/instabasic.config.php';
  require './libs/instabasic.sdk.php';
  
  use Instagram\FacebookLogin\FacebookLogin;
  use Instagram\AccessToken\AccessToken;
  use Instagram\User\User;

   $config = array( // instantiation config params
     'app_id' => '4944463346552351', // facebook app id
     'app_secret' => '5054166e9895b4e93359d385965e126e', // facebook app secret
   );
  $config = $winkmentionsapp->getConfigArray();

  // uri facebook will send the user to after they login
  $redirectUri = $winkmentionsapp->getRedirectUri();

  $permissions = array( // permissions to request from the user
      'instagram_basic',
      // 'instagram_content_publish', 
      // 'instagram_manage_insights', 
      'instagram_manage_comments',
      // 'pages_show_list', 
      // 'ads_management', 
      // 'business_management', 
      // 'pages_read_engagement'
      // 'public_profile',
      // 'instagram_graph_user_profile',
      //'instagram_graph_user_media'
  );

  $IG_scopes = [
    'instagram_basic',
    'instagram_manage_comments',
    'pages_read_engagement',
    'pages_show_list',
    'business_management',
  ];

  // instantiate new facebook login
  $facebookLogin = new FacebookLogin( $config );  
  $igbusinessLogin = new cInstaBasic(true);

  //$totpdv = db_select_value('buste','COUNT( DISTINCT(codcliente) )',['@campagna'=>$dashboard->campagna]);
  $totpdv = 3; // db_select_value('anagrafica','COUNT(1)',['@campagna'=>$dashboard->campagna]);

  $totcoupon = 3; //db_select_value('buoni','COUNT(1)',['@campagna'=>$dashboard->campagna]);
  $totbuste = 0; //db_select_value('buste','COUNT(1)',['@campagna'=>$dashboard->campagna]);
  $totcouponvalidi = 0; //db_select_value('buoni','COUNT(1)',['@campagna'=>$dashboard->campagna,'is_valido'=>1]);
  $perccouponvalidi = ($totcoupon) ? floor($totcouponvalidi * 100 / $totcoupon) : 0;
  $valcoupon = 0; //formatEuro( db_select_value('buoni','SUM(valore)',['@campagna'=>$dashboard->campagna]) );

  $hasdataentry = 0;

  $account_attivi = [];

  try {

    if (!$winkmentionsapp->validateAccessToken()) {
      //$link_fb_connect = $facebookLogin->getLoginDialogUrl( $redirectUri, $permissions );
      $link_fb_connect = $igbusinessLogin->getBusinessLoginForInstagram($redirectUri, $IG_scopes);

      $_SESSION["PAGE_SUBSCRIBED"] = false;

    } else {
      $link_fb_connect = '';

      $iggraph = new cInstaGraph($winkmentionsapp->getAccessToken());
      $accounts = $iggraph->getAccounts();

      foreach($accounts as $user) {

        $igconfig = array(
          'user_id' => $user->instagram_business_account,
          'access_token' => $user->access_token,
        );
        
        $iguser = new User($igconfig);
        $userInfo = $iguser->getSelf();

        $account_attivi[] = [
          'name' => $user->name,
          'id' => $user->instagram_business_account,
          'biography' => $userInfo["biography"],
          'mediacount' => $userInfo["media_count"],
          'picture' => $userInfo["profile_picture_url"],
          'followerscount' => $userInfo["followers_count"],
        ];

        if ($_SESSION["PAGE_SUBSCRIBED"]??false) {
          try {
            if (!cMetaAppSetup::enablePageSubscription($user->id,$user->access_token)) throw new Exception("Page subscribe failed", 1);
          } catch (Exception $e) {
            $log->add("CANNOT SUBSUBSCRIBE PAGE:".$user->id,"###_pagesubscribe_error.log");
          }          
        }

      }

      $_SESSION["PAGE_SUBSCRIBED"] = true;

    }

  } catch (Exception $e) {

    if ($e->getCode() == 190) {  // Token expired
      $winkmentionsapp->revokeAccessToken();      
    }

    $log->error($e,'###_dashboard.log');

  }

  
  $TBS = pagina();
  $TBS->MergeBlock('cc',$campagne_attive);
  $TBS->MergeBlock('acc',$account_attivi);
  $TBS->Show();  	
