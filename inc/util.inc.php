<?php

/*
** Util.inc
** 2003-04 (c) Fastek snc - Fastekonline.it
** L'utilizzo gratuito è concesso solo per uso personale e mantenendo questo copyright
*/

function cambiaPagina($url) {
  session_write_close();
	
	$sid = session_id();
	if (!$sid) {	  
	}
	
  header('Location: '.$url);
  exit;
}

function getURLFull($detectSSL=TRUE,$forceSSL=FALSE) {
  $pageURL = 'http';
  $flgSSL = FALSE;
  
  if ($forceSSL || $detectSSL) {
    if ($forceSSL || isset($_SERVER["HTTPS"])) {
      if ($forceSSL || ($_SERVER["HTTPS"] == "on")) {$flgSSL=TRUE;}
    }
  }

  $port = '';
  
  if ($flgSSL && $_SERVER["SERVER_ADDR"] == '127.0.0.1') {
    $flgSSL = FALSE;
    $forceSSL = FALSE;
  }  
  
  if ($forceSSL) {
    $port = '';    
  } else {
    if ($_SERVER["SERVER_PORT"] != "80") {
      $port = ':'.$_SERVER["SERVER_PORT"];  
    	if (($_SERVER["SERVER_PORT"] == '443') && ($flgSSL)) {
    	  $port = '';
    	}
    }
  }
  
  if ($flgSSL) {
    $pageURL.= 's';
  }
  $pageURL .= '://';
  
//    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].dirname($_SERVER["REQUEST_URI"]);
//  } else {
    $pageURL .= $_SERVER["SERVER_NAME"].$port.dirname($_SERVER["PHP_SELF"]);
//  }

  if (substr($pageURL,-1) != '/') {
    $pageURL.='/';
  }

  return $pageURL;
}

function leggiURLPath() {
  $rootpath = $_SERVER["PHP_SELF"];
  $lpos = strrpos($rootpath,'/');
  if ($lpos >= 0) {
    $rootpath = substr($rootpath,0,$lpos);
  }
  return $_SERVER["HTTP_HOST"].$rootpath;
}

function leggiScriptRoot() {
  $rootpath = 'http://' . $_SERVER["HTTP_HOST"] . dirname($_SERVER['PHP_SELF']);

  if (!(strpos($rootpath,"/adm")===false)) {
    $lpos = strrpos($rootpath,'/');
    if ($lpos >= 0) {
      $rootpath = substr($rootpath,0,$lpos);
    }
  }

  if (substr($rootpath,-5) == '/mail') {
    $rootpath = substr($rootpath,0,-5);
  }

  return $rootpath;
}

function leggiServerPath() {
  $srvpathfull = pathinfo($_SERVER["SCRIPT_FILENAME"]);
  $srvpath = $srvpathfull["dirname"];

  if (!(strpos($srvpath,"/adm")===false)) {
    $lpos = strrpos($srvpath,'/');
    if ($lpos >= 0) {
      $srvpath = substr($srvpath,0,$lpos);
    }
  }
  
  if (substr($srvpath,-5) == '/mail') {
    $srvpath = substr($srvpath,0,-5);
  }
  
  return $srvpath;
}

function getmicrotime(){
  list($usec, $sec) = explode(" ",microtime());
  return ((float)$usec + (float)$sec);
}

function leggiPOST($nomevar) {
  if (isset($_POST[$nomevar])) {
    return $_POST[$nomevar];
  } else {
    return '';
  }
}

function creaRadiokbox($nome,$valore,$attuale) {
  echo "<input name='$nome' type='radio' value='$valore' " . (($attuale == $valore) ? "checked" : "") . ">";
}

function valorizzaRadiobox($valore,$attuale,$defradio = false) {
  if ($defradio == true) {
    if ($attuale == '') {
      $attuale = $valore;
    }
  }
  
  echo "value='$valore' " . (($attuale == $valore) ? "checked" : "");
}

function preparaCheckbox($nome,$valore,$attuale) {
  return "<input name='$nome' type='checkbox' value='$valore' " . (($attuale === true) ? "checked" : "") . ">";
}

/*
function date2str($phpdate) {
  return ($phpdate != '') ? date("d/m/Y", strtotime($phpdate)) : '';
}

function str2date($strdate) {
  if ($strdate == '') return '';
  $dd = split('[/-]',$strdate);
  return date('Y-m-d',mktime(0,0,0,$dd[1],$dd[0],$dd[2]));
}


function strdate2sqldate($strdate) {
  if ($strdate == '') return '';
  $dd = split('[/-]',$strdate);
  return $dd[2].'-'.$dd[1].'-'.$dd[0];
}

function sqldate2strdate($sqldate) {
  if ($sqldate == '') return '';
  $dt = split(' ',$sqldate.' ');
  $dd = split('-',$dt[0]);
  return $dd[2].'/'.$dd[1].'/'.$dd[0];
}


function dateToday() {
  return str2date(date('d/m/Y'));
}
*/

function html2str($hstr) {
  $ss = $hstr;
  $ss = str_replace("\\'","'",$ss);
  $ss = str_replace("<BR>","\n",$ss);
  return $ss;
}

function br2nl($data) {
  return preg_replace( '!<br.*>!iU', "\n", $data );
}

function float2str($vfloat) {
  $val = str_replace(".",",",round((float)$vfloat,2));
  return $val;
}

function currency2str($curr) {
  $cur = str_replace(".",",",round((float)$curr,2));
  if (strpos($cur,',')==0) {
    $cur = $cur.',00';
  } else {
    if (strlen($cur) == strpos($cur,',') +2) {
      $cur.='0';
    }
  }
  return '€ '.$cur;
}

function creaSelectList($elenco,$cod) {
  foreach($elenco as $kk => $des) {
    echo "<OPTION value='".$kk."'" . (($kk == $cod) ? " SELECTED " : "") . ">".$des."</OPTION>";
  }
}

function preparaSelectList($elenco,$cod) {
  $strout = '';
  foreach($elenco as $kk => $des) {
    $strout.= "<OPTION value='".$kk."'" . (($kk == $cod) ? " SELECTED " : "") . ">".$des."</OPTION>";
  }
  return $strout;
}

function boolean2str($val) {
  return ($val) ? 'sì' : 'no';
}

function rmdirr($dirname) {
    // Sanity check
    if (!file_exists($dirname)) {
        return false;
    }
 
    // Simple delete for a file
    if (is_file($dirname) || is_link($dirname)) {
        return unlink($dirname);
    }
 
    // Loop through the folder
    $dir = dir($dirname);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
         }
 
        // Recurse
        rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
    }
 
    // Clean up
    $dir->close();
    return rmdir($dirname);
}

	function carica_CSV($nf,$sep=';') {
    $arr = array();  
  
	  $rr = @file($nf);
    
    if ($rr) {
  		foreach($rr as $row) {
  		  $rec = explode($sep,$row);
  			$lin = array();
  			$kk = '';
  			foreach($rec as $ff) {
  			  $ff = str_replace(array("\n","\r","\t"),'',$ff);
  			  if (substr($ff,0,1) == '"') {
  				  $ff = substr($ff,1,-1);
  				}
  				if ($kk == '') {
  					$kk = $ff;
  				}
  				$lin[] = $ff;
  			}
  			$arr[$kk] = $lin;
  		}
    }
    
		return $arr;
	}

	function getClientIP() {
	  return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
	}
	
?>
