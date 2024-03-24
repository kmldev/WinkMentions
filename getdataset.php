<?php

  ini_set('memory_limit','128M');

  require './_compag.php';
  require './verificaadmin.inc.php';
  require './dashboard.utils.php';

  require './meta.setup.php';
  require './libs/instabasic.config.php';
  require './libs/instabasic.sdk.php';

  use Instagram\User\MentionedMedia;


  
  $post = ['draw'=>0,'start'=>0,'length'=>0,'search'=>[],'order'=>[],'columns'=>[],'fase'=>0,'ww'=>'','mode'=>'','cc'=>''];
  
  $postkeys = array_keys($_POST);

  foreach($postkeys as $item) {
    if (isset($post[$item])) $post[$item] = $_POST[$item];
  }

  //$log->notice($post,'###_dataset.debug.log');

  $sels = "SELECT ";
  $tabs = " FROM anagrafica a";
  $whrs = " WHERE campagna={$dashboard->campagna} ";
  $srch = "";

  switch ($post['mode']) {

    case 'pdv':
      $sels.= "a.codcliente,a.ragsociale,a.via,a.cap,a.localita,a.provincia,a.telefono,a.piva";
      break;

    default:
      break;

  }

  if ($post['search']) {
    $ss = $post['search']['value'];
    if ($ss) $srch = " AND (a.ragsociale LIKE '%{$ss}%' OR a.piva LIKE '%{$ss}%' OR a.localita LIKE '%{$ss}%')";
  }

  $qry = $sels.$tabs.$whrs.$srch.' ';

  if ($post['order']) {
    $ord = 'ORDER BY ';
    foreach($post['order'] as $cols) {
      $ord.= (1+$cols['column']).' '.$cols['dir'].', ';
    }
    $qry.=substr($ord,0,-2).' ';
  }

  if ($post['start'] || $post['length'] ) {
    $qry.='LIMIT '.$post['start'];
    if ($post['length']) $qry.=','.$post['length'];    
  }

  $data = [];
  // $res = db_query($qry);
  // $idx = 0;  

  // while($res && $res->next_record()) {

  //   $idx++;

  //   //$id = $res->Record['user_id'];

  //   $link = 'addbusta.php?'.http_build_query(['w'=>$res->Record['codcliente']]);
  //   $res->Record['codcliente'] = '<a href="'.$link.'">'.$res->Record['codcliente'].'</a>';


  //   $row = array_values( $res->Record );    

  //   $data[] = $row;

  // }

  // $data = [
  //   ["@test1","This is a post test","#test","https://test/img.png"],
  //   ["@test5","This is yet a post test","#test","https://test/img2.png"],
  //   ["@test12","This is always a post test","#test","https://test/img4.png"],
  //   ["@test34","This is a great post test","#test","https://test/img6.png"],
  // ];

  $data = [];

  //ini_set('auto_detect_line_endings',TRUE);
  $handle = fopen(__DIR__.'/db/240227_table_mentions.csv','r');
  while ( ($row = fgetcsv($handle) ) !== FALSE ) { 

    $caption = "";
    $mediaurl = "";
    $username = "";
    $tags = "";

    try {
      
      $config = array( // instantiation config params
        'user_id' => InstaBasicConfig::user_id,
        'media_id' => $row[0], // id of post the user was mentioned
        'access_token' => $winkmentionsapp->getAccessToken(),
      );
      $mentionedMedia = new MentionedMedia( $config );
      $mentionedMediaInfo = $mentionedMedia->getSelf();

      if (isset($mentionedMediaInfo["caption"])) {

        $caption = $mentionedMediaInfo["caption"];
        $mediaurl = $mentionedMediaInfo["media_url"];
        $username = $mentionedMediaInfo["username"];
        $matches = [];      
        preg_match_all('/#([\p{L}\p{Mn}]+)/u',$caption,$matches);
        $tags = implode(",",array_map(fn($item)=>$item[0],$matches));

      } else {
      
        $username = "#APIERROR#";

      }

    } catch (Exception $e) {

      $log->error($e,"###_getmentionedmedia.log");
      $username = "#APIERROR#";
      
    }

    $data[] = [$row[0],$row[1],$username,$caption,$tags,$mediaurl];

  }
  //ini_set('auto_detect_line_endings',FALSE);
  fclose($handle);

  $tot = count($data); //db_query_value('SELECT COUNT(1) '.$tabs.$whrs);
  $totfiltered = count($data); //db_query_value('SELECT COUNT(1) '.$tabs.$whrs.$srch);

  $reply = [
    'draw' => $post['draw'],
    'recordsTotal' => $tot,
    'recordsFiltered' => $totfiltered,
    'data' => $data
  ];

  echo json_encode($reply);
  exit;