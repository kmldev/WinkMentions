<?php

  ini_set('memory_limit','128M');

  require './_compag.php';
  require './verificaadmin.inc.php';
  require './dashboard.utils.php';

  if (!$admlogin->hasLevel('report')) {
    cambiaPagina('dashboard.php');
    exit;
  }

  $totpdv = db_select_value('anagrafica','COUNT(1)',['@campagna'=>$dashboard->campagna]);
  $totcoupon = db_select_value('buoni','COUNT(1)',['@campagna'=>$dashboard->campagna]);
  $totbuste = db_select_value('buste','COUNT(1)',['@campagna'=>$dashboard->campagna]);
  $totcouponvalidi = db_select_value('buoni','COUNT(1)',['@campagna'=>$dashboard->campagna,'is_valido'=>1]);
  $perccouponvalidi = ($totcoupon) ? floor($totcouponvalidi * 100 / $totcoupon) : 0;
  $valcoupon = formatEuro( db_select_value('buoni','SUM(valore)',['@campagna'=>$dashboard->campagna]) );

  $TBS = pagina();
  $TBS->MergeBlock('cc',$campagne_attive);
  $TBS->Show();  	
