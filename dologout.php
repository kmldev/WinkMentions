<?php

  require './meta.setup.php';
  
  $winkmentionsapp->revokeAccessToken();

  session_start();
  session_destroy();
  
  require './verificaadmin.inc.php';