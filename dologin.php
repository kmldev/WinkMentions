<?php

  require "./verificaadmin.inc.php";

  if (isset($_POST['login'])) {

    if ($admlogin->controllaLogin($_POST['usr'],$_POST['pwd'])) {

      echo json_encode(['tm' => time(), 'logged' => '1']);
      exit;

    } else {

      echo json_encode(['tm' => time(), 'error' => 'Dati di accesso non validi.']);
      exit;

    }

  }
