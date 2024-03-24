<?php
  if (!defined('INCLUDE_PATH')) {
	  define('INCLUDE_PATH',__DIR__.'/');
	}

  require_once(INCLUDE_PATH."tbs_class.php");
  
  /**
   * @return clsTinyButStrong
   */
  function pagina($nav = '',$schema = true) {
    global $contenutopagina,$ismobile;
  
	  if (!$schema && defined('PAGINA_SCHEMA')) {
		  $schema = PAGINA_SCHEMA;
		}
	
	  if ($nav === TRUE) {
		  $nav = '';
			$schema = '';
		}
	
    if ('' == $nav) {
      $nav = '_'.basename($_SERVER['PHP_SELF'],'.php').'.html';

    }
    
		if ($ismobile) {
		  $nav = 'mobi/'.$nav;
		}
		
    if ($schema === FALSE) {
      $schema = $nav;
    } else {
      if ($schema === TRUE) {
        $schema = '_main.html';
      }
    }
    
    $contenutopagina = $nav;
  
    $pagename = basename($_SERVER['PHP_SELF']);
	  $page = str_replace(".php", "", $pagename);
    $TBS = new clsTinyButStrong;

    $TBS->LoadTemplate($schema);
    
		if ($ismobile) {
		  $src = $TBS->Source;
			$src = str_replace('img/','mobi/img/',$src);
			$src = str_replace('css/','mobi/css/',$src);
			$TBS->Source = $src;
	  }
		
    return $TBS;
  }
?>
