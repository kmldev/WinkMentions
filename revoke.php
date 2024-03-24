<?php

  file_put_contents(__DIR__."/log/revoke.log",date("Y-m-d H:i:s")." ".$_SERVER['REMOTE_ADDR']." ".json_encode($_GET)."\n",FILE_APPEND);
  