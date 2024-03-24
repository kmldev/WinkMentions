<?php

	error_reporting(E_ALL);
	//ini_set('display_errors', '1');

  @date_default_timezone_set('Europe/Rome');	

  $issviluppo = false;
  $isstage = false;  
  $error_level_default = E_ERROR;

  try {
  
    if (isset($_SERVER['SERVER_NAME'])) {
      $sn = $_SERVER['SERVER_NAME'];
      $issviluppo = (($sn == 'zetta') OR (substr($sn,0,10)=='192.168.1.'));     
    }
  
    if (isset($_SERVER["SCRIPT_NAME"])) {    
      $isstage = ((strpos($_SERVER["SCRIPT_NAME"],'/_stage/') !== FALSE) AND (date('Ymd')<20180627));    
    }

    if ($issviluppo OR $isstage) $error_level_default = E_ALL;

  } catch (Exception $e) {

    // foo

  }

  class LogClass {

    var $path = '';
    var $fn = '';
    var $debug = false;

    var $history = [];

    function __construct($name=false,$path=false,$isdebug=false) {

      if ($name === TRUE) {
        $name = false;
        $isdebug = true;
      }

      $this->path = $path ?: dirname($_SERVER['SCRIPT_FILENAME']).'/log/';
      $this->fn = $name ?: '###_console.log';
      $this->fn = str_replace('###',date('Ymd'),$this->fn);
      $this->debug = $isdebug;

      $_SESSION['HISTORY'] = '';

    }

    function __destruct() {
      if ($this->debug AND $this->history) {
        $_SESSION['HISTORY'] = json_encode($this->history);
      }
    }

    function getHistory() {
      $js = [];
      if (isset($_SESSION['HISTORY'])) {
        $js = @json_decode($_SESSION['HISTORY']);
        if ($js === null) $js = [];
        $_SESSION['HISTORY'] = '';
      }
      $js = $this->history + $js;
      $this->history = [];
      return $js;
    }

    function add($msg,$name=false) {

      if (is_object($msg)) {
        $extras = (property_exists($msg,'extras')) ? $msg->extras : []; 
        $msg = '['.$msg->getCode().'] '.$msg->getMessage().' line:'.$msg->getLine().' on '.$msg->getFile()."\n".$msg->getTraceAsString()."\n";
        $msg.=implode("\n",$extras);
        $msg.="\n\n";
      }

      $fn = $name ? str_replace('###',date('Ymd'),$name) : $this->fn;
      $fn = preg_match("/^[.\/]/",$name) ? $fn : $this->path.$fn;
      @$ris = file_put_contents($fn,$msg."\n",FILE_APPEND);
      return $ris;
    }

    function error($msg,$name=false) {
      if ($this->debug) $this->history[] = [ 'level'=>'error','msg'=>$msg ];
      return $this->add($msg,$name);      
    }

    function notice($msg,$name=false) {
      if ($this->debug) $this->history[] = [ 'level'=>'notice','msg'=>$msg ];
      return $this->add($msg,$name);      
    }

    function debug($msg,$name=false) {
      //if ($this->debug) $this->history[] = [ 'level'=>'debug','msg'=>$msg ];
      if ($this->debug) $this->add($msg,$name);
      else return true;
    }

  }

  $log = new LogClass($issviluppo);

  set_error_handler('errorLogger');
  function errorLogger($errno, $errstr, $errfile, $errline) {
    global $log;
    $ee = new ErrorException(date('Ymd His').' '.$errstr,0,$errno,$errfile,$errline);
	  //$msg = date('Ymd His')." - NO:$errno ERR:$errstr FILE:$errfile LINE:$errline\n";
    if (!$log) $log = new LogClass();
    //$ris = $log->error($msg);
    $ris = $log->error($ee);
		if (!$ris) {
		  echo $ee->getMessage().' line '.$ee->getLine().'@'.$ee->getFile();
		}
  }	  


  function isConcorsoFinito() {
	 return (date('Ymd')>20190306); 
  }

  function getActualURL() {  
    $url = getURLFull(false);
    return substr($url,5);  
  }

  if (!function_exists('random_int')) {
    function random_int($min, $max) {
      return mt_rand($min,$max);
    }
  }

  function getToken($length){
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
//    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    $max = strlen($codeAlphabet); // edited

   for ($i=0; $i < $length; $i++) {
       $token .= $codeAlphabet[random_int(0, $max-1)];
   }

   return $token;
  }

  require __DIR__.'/inc/pagdef.php';
  require __DIR__.'/inc/managedb.php';
  require __DIR__.'/inc/util.inc.php';

  function getElencoProvince() {
    $arrprov = array();
    $arrprov[''] = '- -';
    $eleprov = @file_get_contents('dati/ita_prov_sigle.txt');
    if ($eleprov) {
      $ll = explode(' ',$eleprov);
      foreach($ll as $rr) {
        $rr = trim($rr);
        $arrprov[$rr] = $rr;
      }
    }
    return $arrprov;
  }

	function getElencoIndicizzato($fn,$fullval=false) {
    $ff = file($fn,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
    $arr = array();
    $nn = 1;
    foreach($ff as $row) {
		  $row = trim($row);
      if ($fullval) {
        if ($fullval === TRUE) $arr[$row]=$row;
        else {
          $rr = explode($fullval,$row);
          $arr[$rr[0]]=$rr[1];
        }
      } else {
        $arr[$nn]=$row;
      }
      $nn++;
    }
    return $arr;
  }

	function getElencoRange($nmin,$nmax,$sz=2) {	
	  $lst = array();	
		for($idx=$nmin;$idx<=$nmax;$idx++) {		
		  $nn = substr('0'.$idx,-($sz));			
		  $lst[$nn] = $nn;		
		}	
	  return $lst;	
	} 	
	
	function getElencoRangeAnno($nmin,$nmax,$sz=4) {	
	  $lst = array();	
		for($idx=$nmin;$idx<=$nmax;$idx++) {		
		  $nn = substr('0'.$idx,-($sz));			
		  $lst[$nn] = $nn;		
		}	
	  return $lst;	
	} 	
  
  function formatNumero($num,$dec=0) {
    return number_format($num,$dec,",",".");
  }

  function formatEuro($num,$dec=2) {
    global $log;

    try {
      if (!is_numeric($num)) $num = 0;
      return number_format($num,$dec,",",".");
    } catch (Exception $e) {
      $log->error($e);
      return 0;
    }

  }

  function formatPerc($val) {
    return min(100,floor($val));    
  }  


  include __DIR__.'/usrclass.php';
  
  if (!session_id()) {
    session_start();
  }

/*  
  if (session_id()) {
    setcookie('PHPSESSID', session_id(), null, '/', null, null);
  }  
*/  

  register_shutdown_function('session_write_close');

	$xuser = UserClass::session();	

  $pagename = basename($_SERVER['PHP_SELF']);

	$pagetitle = 'Mention Tracker';

	$msgerr = '';
  $customcss = '';
