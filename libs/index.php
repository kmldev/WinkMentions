<?php

  require __DIR__."/inc/terminal.web.php";
  require __DIR__."/instabasic.sdk.php";
  require __DIR__."/instabasic.config.php";

  $web = new ClsTerminAnsiWeb();

  try {

    $web->line("Starting...");

    // $clientid = "432487875874038";
    // $clientsecret = "2af03e27c0a219d3c0a52a8c09765a83";
  
    $redirecturl = "https://antistage.com/socialgallery/basicauth.php";
    $scopes = [cInstaBasic::SCOPE_USER_PROFILE,cInstaBasic::SCOPE_USER_MEDIA];
  
    $token = cInstaUserToken::restore(InstaBasicConfig::user_id);

    if (!$token) {

      $insta = new cInstaBasic(true);
      $loginurl = $insta->getAuthorizeURL($redirecturl,$scopes);

      //$web->line("URL: ".$loginurl);
      echo "<a href='$loginurl'>Login IG Basic</a>";
    
    } else {

      $web->line("Token: ".$token->token);

      $graph = new cInstaGraph($token->token);
      $me = $graph->getMe();
      $web->line("ME: ".json_encode($me));
      $graph->setMe($me);

      $tags = $graph->getTags();
      $web->line("TAGS: ".json_encode($tags));

      // $igbusiness = $graph->getAccounts();
      // $web->line("Accounts: ".json_encode($igbusiness));

    }


    // $media = $graph->getMedia();
    // $web->line("MEDIA: ".json_encode($media));
  
  } catch (Exception $e) {
    
    $web->color(ClsTerminAnsiWeb::RED);
    $web->line($e->getMessage());
    $web->line($e->getTraceAsString());
    
  }

  $web->close();