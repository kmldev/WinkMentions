<?php

  require './_compag.php';

  require './meta.setup.php';

  use Instagram\FacebookLogin\FacebookLogin;
  use Instagram\AccessToken\AccessToken;

  if (count($_GET) == 0) {
?>
  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parsing Meta auth</title>
  </head>
  <body>
    <script>
      let hash = window.location.hash;
      if (hash.length > 0) {
        window.location.href = window.location.href.replace("#", "");
      }
    </script>
  </body>
  </html>

<?php    
    exit;
  }

  $config = $winkmentionsapp->getConfigArray();

  // we also need to specify the redirect uri in order to exchange our code for a token
  $redirectUri = $winkmentionsapp->getRedirectUri();

  // instantiate our access token class
  $accessToken = new AccessToken( $config );

  if (isset($_GET['code'])) {
  
      // exchange our code for an access token
    $newToken = $accessToken->getAccessTokenFromCode( $_GET['code'], $redirectUri );
            
    if ( !$accessToken->isLongLived() ) { // check if our access token is short lived (expires in hours)
        // exchange the short lived token for a long lived token which last about 60 days
      $newToken = $accessToken->getLongLivedAccessToken( $newToken['access_token'] );
    }

  }
  else if (isset($_GET['access_token'])) {
    //$newToken = $accessToken->getLongLivedAccessToken( $_GET['access_token'] );
    $newToken = $_GET['access_token'];
  }
  else if (isset($_GET['long_lived_token'])) {
    $newToken = $_GET['long_lived_token'];
  }

  //var_dump($newToken);

  if ($winkmentionsapp->saveAccessToken($newToken)) {
    cambiaPagina("dashboard.php");
    exit;
  }

  die("CANNOT GET ACCESS TOKEN!");
  