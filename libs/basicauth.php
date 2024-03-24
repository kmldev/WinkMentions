<?php

  require __DIR__."/inc/terminal.web.php";
  require __DIR__."/instabasic.sdk.php";
  require __DIR__."/instabasic.config.php";

  $web = new ClsTerminAnsiWeb();

  try {

    $web->line("Response...");

    $insta = new cInstaBasic(true);
  
    $code = $insta->getCallbackCode();
  
    $web->line("@YELLOW@Code: ".$code);  

    $token = $insta->getAccessToken($code);

    $web->line("@GREEN@Token: ".$token);

    cInstaUserToken::save($insta->getUserId(),$token);

  } catch (Exception $e) {
    
    $web->color(ClsTerminAnsiWeb::RED);
    $web->line($e->getMessage());
    $web->line($e->getTraceAsString());
    
  }

  $web->close();