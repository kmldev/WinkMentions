<?php

  function leggiParametriDB($dbname = 'default') {

    if (!$dbname) $dbname = 'default';
    elseif ($dbname===TRUE) $dbname = 'default';
  
    $connlst = [	
	      'default'   => array('host'=>'zetta.local',  'db' =>'lorealcoupon',  'user' => 'local',   'pwd' =>''),
    ];
  
    return $connlst[$dbname];

  }
