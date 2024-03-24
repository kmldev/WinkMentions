<?php

  require './_compag.php';    
  require './verificaadmin.inc.php';

  if ($admlogin->controllaAdmin()) {
    header('Location: dashboard.php');
    exit;
  }

  header("Location: login.php");
  exit;