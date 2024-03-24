<?php 

  DEFINE('DGF_FIELDTYPE_LABEL',1);

  DEFINE('DGF_FIELDTYPE_HIDDEN',2);
  DEFINE('DGF_FIELDTYPE_PASSWORD',3);  
  
  DEFINE('DGF_FIELDTYPE_TEXT',8);  
  DEFINE('DGF_FIELDTYPE_AREA',9);  
  
  DEFINE('DGF_FIELDTYPE_CHECKBOX',11);
  DEFINE('DGF_FIELDTYPE_MULTICHECKBOX',12);

  DEFINE('DGF_FIELDTYPE_RADIO',21);
  
  DEFINE('DGF_FIELDTYPE_UPLOAD',31);
  
  DEFINE('DGF_FIELDTYPE_SELECT',41);
  
  DEFINE('DGF_CONTROLTYPE_DATEPICKER',51);
  
  DEFINE('DGF_FIELDTYPE_IMAGE',61);
  
  DEFINE('DGF_BUTTON_SUBMIT',101);
	DEFINE('DGF_BUTTON_IMAGE',102);

  
  DEFINE('DGFIELD_EMPTY','**EMPTY**');
  DEFINE('DGFIELD_NOTEMPTY','/.+/');
  DEFINE('DGFIELD_TELEFONOFISSO','/^[+0][-\/\.0-9]*$/');
  DEFINE('DGFIELD_CELLULARE','/^3[-\/\.0-9]*$/');
  DEFINE('DGFIELD_TELEFONOGENERICO','/^[+03][1-9][-\/\.0-9]*$/');

  //DEFINE('DGFIELD_CODICEFISCALE','/^[A-Z]{6}[\d]{2}[A-Z][\d]{2}[A-Z][\d]{3}[A-Z]$/i');
  DEFINE('DGFIELD_CODICEFISCALE','/^(?:(?:[B-DF-HJ-NP-TV-Z]|[AEIOU])[AEIOU][AEIOUX]|[B-DF-HJ-NP-TV-Z]{2}[A-Z]){2}[\dLMNP-V]{2}(?:[A-EHLMPR-T](?:[04LQ][1-9MNP-V]|[1256LMRS][\dLMNP-V])|[DHPS][37PT][0L]|[ACELMRT][37PT][01LM])(?:[A-MZ][1-9MNP-V][\dLMNP-V]{2}|[A-M][0L](?:[1-9MNP-V][\dLMNP-V]|[0L][1-9MNP-V]))[A-Z]$/i'); // tutti i CF e omocodia  DEFINE('DGFIELD_CAP','/[0-9]{5}/');
  DEFINE('PARTITA_IVA','/^[0-9]{11}$/');
  DEFINE('DGFIELD_PARTITA_IVA','/^[0-9]{11}$/');
  DEFINE('DGFIELD_PARTITA_IVA_ITA_SM','/^[0-9]{11}|SM[0-9]{5}$/'); /**  Check partita IVA Italia e San Marino */

  DEFINE('DGFIELD_DATA_ITA','/^[0-3][0-9]\/[0-1][0-9]\/[1-2][0-9]{3}$/');

  DEFINE('DGFIELD_PASSWORD','/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/');
  DEFINE('DGFIELD_PASSWORD_SIMPLE_8_20','/^[\wàèéìòù\\\^\$\.\!\?\*\+\(\) ]{8,20}$/i');  // len 8-20 char
  DEFINE('DGFIELD_PASSWORD2','/^(?=.*[A-Z])(?=.*[0-9])[A-Za-z0-9@!%$&£#+*=_\-]{8,20}$/'); // lettere, numeri, segni, almeno 1 maiuscola, almeno 1 numero

  DEFINE('DGFIELD_EMAIL','/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/i');  //(RFC 5322 Official Standard)
  
  class dgform {
    var $fvar;
    var $formname = '';
    var $submitname = 'Submit';
      
    function __construct($formname='') {
      $this->fvar = array();
      $this->formname = $formname;
    }
    
    function getField($fname,$fprop='') {
      if ($fprop == '') {
        return $this->fvar[$fname];
      } else {
        return $this->fvar[$fname][$fprop];
      }
    }

    function getFields() {
      return $this->fvar;
    }
    
    function getFieldValue($fname) {
      if (isset($GLOBALS[$fname])) {
        $fv = $GLOBALS[$fname];
      } else {
        $fv = $this->fvar[$fname]['v'];
      }
    
      return $fv;    
    }

    function getValues() {
      $arr = array();
      foreach($this->fvar as $kk => $props) {
        if (substr($kk,0,1) != '_') {
          $arr[$kk] = $this->getFieldValue($kk);
        }
      }
      return $arr;
    }
    
    function getFieldNames() {
      return array_keys($this->fvar);
    }
    
    function validateFields() {
      $lst = array();
      foreach($this->fvar as $kk => $props) {
        $cc = $props['c'];
        if ($cc) {
          if (is_array($cc)) {
            if ($cc[0] == DGFIELD_EMPTY) {
              if ($this->getFieldValue($kk) == '') {
                continue;
              }
            }
            $cc = $cc[1];
          }

          if (is_callable($cc)) {
            $chk = call_user_func($cc,$this->getFieldValue($kk), $this);
            if ($chk === FALSE) $lst[] = $props['f'];
            elseif (is_string($chk)) $lst[] = $chk;
          }
          else if (!preg_match($cc,$this->getFieldValue($kk))) {
            $lst[] = $props['f'];
          }
        }
      }
      return (count($lst)>0) ? $lst : FALSE;
    }
    
    function setField($fname,$ffield,$fvalue) {
      $this->fvar[$fname][$ffield] = $fvalue;
    }
    
    function hasField($fname) {
      return (isset($this->fvar[$fname]));
    }
    
    function addField($fname,$ftype,$fvalue='',$fvalidated=FALSE,$ffullname=FALSE) {
      $fd = array();
      $fd['n'] = $fname;
      $fd['t'] = $ftype;
      $fd['v'] = $fvalue;
      $fd['c'] = $fvalidated;
      $fd['f'] = ($ffullname) ? $ffullname : $fname;
      $GLOBALS[$fname] = $fvalue;

      $this->fvar[$fname] = $fd;
      
      return $fd;
    }
    
    function addFieldLabel($fname,$fvalue='') {
      $this->addField($fname,DGF_FIELDTYPE_LABEL,$fvalue);
    }

    function addFieldText($fname,$fvalue='',$fvalidated=FALSE,$ffullname=FALSE) {
      $this->addField($fname,DGF_FIELDTYPE_TEXT,$fvalue,$fvalidated,$ffullname);
    }
    
    function addFieldHidden($fname,$fvalue='') {
      $this->addField($fname,DGF_FIELDTYPE_HIDDEN,$fvalue);
    }    

    function addFieldPassword($fname,$fvalue='') {
      $this->addField($fname,DGF_FIELDTYPE_PASSWORD,$fvalue);
    }    
    
    function addFieldArea($fname,$fvalue='') {
      $this->addField($fname,DGF_FIELDTYPE_AREA,$fvalue);
    }
  
    function enableTinyMCE($fname,$basepath = '',$theme = 'advanced') {
      $fd = $this->getField($fname);
      $fd['opt_tinymce'] = 1;
      $fd['opt_tinymce_theme'] = $theme;
      $fd['opt_tinymce_basepath'] = $basepath;
      $this->fvar[$fname] = $fd;
      return $fd;
    }
  
    function addFieldImage($fname,$fvalue='',$fpath='',$fw=0,$fh=0,$opts=3) {
      $fd = $this->addField($fname,DGF_FIELDTYPE_IMAGE,$fvalue);
      $fd['img_path'] = $fpath;
      $fd['img_w'] = $fw;
      $fd['img_h'] = $fh;
      $fd['opts'] = $opts;
      $this->fvar[$fname] = $fd;
      return $fd;
    }
  
    function addFieldCheckBox($fname,$fchecked=false,$fvalue='1') {
      $fd = $this->addField($fname,DGF_FIELDTYPE_CHECKBOX,($fchecked) ? $fvalue : '');

      $fd['cdv'] = $fvalue; // checked value

      $this->fvar[$fname] = $fd;
      
      return $fd;
    }

    function addFieldMultiCheckBox($fname,$fvalues=array(),$fmultichecked='') {
      $chk = array();
      if (is_array($fmultichecked)) {
        $chk = $fmultichecked;
      }
      $fd = $this->addField($fname,DGF_FIELDTYPE_MULTICHECKBOX,$chk);
      $fd['checklist'] = $fvalues;
      $this->fvar[$fname] = $fd;
      return $fd;
    }
    
    
    function addFieldRadio($fname,$fvalue='',$farray=array()) {
      $fd = $this->addField($fname,DGF_FIELDTYPE_RADIO,$fvalue);
      $fd['radio'] = $farray;
      $this->fvar[$fname] = $fd;      
      return $fd;            
    }
    
    function addFieldUpload($fname,$fpath) {
      $fd = $this->addField($fname,DGF_FIELDTYPE_UPLOAD);
      $fd['path'] = $fpath;
      $fd['uploaded'] = false;
      $this->fvar[$fname] = $fd;
      return $fd;
    }
    
    function addFieldSelect($fname,$fvalue,$fselect,$fvalidated=FALSE,$ffullname=FALSE) {
      $fd = $this->addField($fname,DGF_FIELDTYPE_SELECT,$fvalue,$fvalidated,$ffullname);

      $fd['sel'] = $fselect; // selection of values

      $this->fvar[$fname] = $fd;
      
      return $fd;
    }

    function setAutoSubmit($fname,$fautosubmit = true) {
      $fd = $this->getField($fname);
      if ($fautosubmit) {
        $fd['opt_autosubmit'] = true;
      } else {
         unset($fd['opt_autosubmit']);
      }
      $this->fvar[$fname] = $fd;
    }    
    
    function addButtonSubmit($fname,$fvalue='') {
      $this->addField($fname,DGF_BUTTON_SUBMIT,$fvalue);
      $this->submitname = $fname;
    }

    function addButtonImage($fname,$fvalue='') {
      $this->addField($fname,DGF_BUTTON_IMAGE,$fvalue);
      $this->submitname = $fname;
    }
    
    function addSelectionList($fname,$farray) {
      $fd = $this->getField($fname);
      $fd['opt_slist'] = $farray;
      $this->fvar[$fname] = $fd;
    }
    
    function addDatePicker($fname,$feditable) {
      $this->addField('_'.$fname,DGF_CONTROLTYPE_DATEPICKER,'');
      $fd = $this->getField($fname);      
      if ($feditable == false) {
        $fd['t'] = DGF_FIELDTYPE_LABEL;
        $fd['opt_datepicker'] = '_'.$fname;
      }
      $this->fvar[$fname] = $fd;
    }
    
    function clearForm($excludelist = array()) {
      foreach ($this->fvar as $kk => $props) {
        if (!in_array($kk,$excludelist)) {
          $this->fvar[$kk]['v'] = '';
          $GLOBALS[$kk] = '';
        }
      }
    }
    
    function formSubmitted($bname='') {
      if ($bname == '') {
        $bname = $this->submitname;
      }
      
      if ($bname === TRUE) {
        $bname = $this->formname.'_submit';
      }
      
//			echo $bname;
			
      return ((isset($_POST[$bname])) || (isset($_POST[$bname.'_x'])));
    }
    
    function parsePOST($decodeutf8=false) {
      foreach($this->fvar as $kk => $props) {
        $fv = '';
        $err = '';
      
        if (isset($_POST[$kk])) {
				  if ($decodeutf8) {				
            $fv = is_array($_POST[$kk]) ? $_POST[$kk] : utf8_decode($_POST[$kk]);
					} else {
					  $fv = $_POST[$kk];
					}
        }

        switch ($props['t']) {
          case DGF_FIELDTYPE_LABEL:
          case DGF_FIELDTYPE_TEXT:
          case DGF_FIELDTYPE_PASSWORD:
          case DGF_FIELDTYPE_HIDDEN:          
            $fv = stripslashes($fv);
            break;

          case DGF_FIELDTYPE_CHECKBOX;
            $fv = stripslashes($fv);
            if ($fv !== $props['cdv']) $fv = '';  // prevent injection
            break;
            
          case DGF_FIELDTYPE_AREA:
            $fv = stripslashes($fv);
            
            if (isset($props['opt_tinymce_basepath']) && ($props['opt_tinymce_basepath'] != '')) {
            
              $fv = str_replace($props['opt_tinymce_basepath'],'~/',$fv);
            
//              $fv = eregi_replace('(<img .*)(src=["|\']?)'.$props['opt_tinymce_basepath'].'([^"\']*)("|\')?([^/>]*)','\\1\\2~/\\3\\4',$fv);
//              $fv = eregi_replace('(<a .*)(href=["|\']?)'.$props['opt_tinymce_basepath'].'([^"\']*)("|\')?([^/>]*)','\\1\\2~/\\3\\4',$fv);

//              echo '#'.htmlentities($fv);
            }
            
            break;
          case DGF_FIELDTYPE_MULTICHECKBOX:
            if (!is_array($fv)) {
              $fv = array();
            }
            break;
          case DGF_FIELDTYPE_UPLOAD:  
            if ($this->checkFileUpload($kk)) {
              $fv = $this->uploadFileForm($kk,$props['path']);
              if ($fv === TRUE) {
                $this->fvar[$kk]['uploaded'] = true;  
                $err = '';  
                $fv = $this->getField($kk,'filename');
              } else {
                $this->fvar[$kk]['uploaded'] = false;
                $err = $fv;  
                $fv = '';                
              }
            }
            break; 
          case DGF_BUTTON_IMAGE:
					  if (isset($_POST[$kk.'_x'])) {
						  $fv = 1;
						}
						break;
        }
        
        $GLOBALS[$kk] = $fv;
        
//        echo $kk . ' - ' . $fv . '<br>';
        
        $this->fvar[$kk]['v'] = $fv;
        $this->fvar[$kk]['error'] = $err;  
      }
    }
    
    function formInit($action='') {
//      $str = "name='".$this->formname."' method='post' action='".$action."'";
      
      $str = "name='".$this->formname."' ";
      return $str;
    }
    
    function formField($fname) {
      $ff = $this->fvar[$fname];
      $ft = $ff['t'];
      
      $fv = $this->getFieldValue($fname);
      
      $ho = '';
      
      if (!is_array($fv)) {
        $fv = htmlentities($fv);
      }
      
      echo $ft."@";
      die();
      
      switch ($ft) {
        case DGF_FIELDTYPE_LABEL:
          $ho.=' id=\''.$ff['n'].'\' name=\''.$ff['n'].'\' value="'.$fv.'" READONLY ';
          break;
        case DGF_FIELDTYPE_TEXT:
        case DGF_FIELDTYPE_HIDDEN:
        case DGF_FIELDTYPE_PASSWORD:
          $ho.=' id=\''.$ff['n'].'\' name=\''.$ff['n'].'\' value="'.$fv.'" ';
          break;
        case DGF_FIELDTYPE_CHECKBOX:          
          $ho.=' id=\''.$ff['n'].'\' name=\''.$ff['n'].'\' value=\''.$ff['cdv'].'\' ';
          if ($fv == $ff['cdv']) {
            $ho.= ' checked ' ;
          }
        break;

        case DGF_FIELDTYPE_MULTICHECKBOX:          
          $ho = array();
        
          foreach($ff['checklist'] as $vals) {
          
            echo $vals.'#';
          
            $rr = ' id=\''.$ff['n'].'\' name=\''.$ff['n'].'[]\' value=\''.$vals.'\' ';
            if ((is_array($fv)) && (in_array($vals,$fv))) {
              $rr.= ' checked ' ;
            }            
            $ho[$vals] = $rr;
          }
          break;
          
        case DGF_FIELDTYPE_UPLOAD:
          $ho.=' id=\''.$ff['n'].'\' name=\''.$ff['n'].'\' ';
          break;
        case DGF_FIELDTYPE_AREA:
          $ho = array();
          $ho['p'] = ' id=\''.$ff['n'].'\' name=\''.$ff['n'].'\' ';
          $ho['a'] = $fv;
          break;
          
        case DGF_FIELDTYPE_SELECT:
          $ho.=' name="'.$ff['n'].'" id="'.$ff['n'].'" ';

          if (isset($ff['opt_autosubmit'])) {
            $ho.=' onchange="document.getElementById(\''.$this->formname.'\').submit()" ';
          }
          
          foreach($ff['sel'] as $kk => $srow) {
            $srow = htmlentities($srow);
            $ho.="><OPTION value=\"$kk\" ".(($kk == $fv)? ' selected ' : '').">$srow</OPTION";
          }
          
          break;
        case DGF_BUTTON_SUBMIT:
          $ho.=' id=\''.$ff['n'].'\' name=\''.$ff['n'].'\' value="'.$fv.'" ';
          break;
          
        case DGF_FIELDTYPE_RADIO:
          $ho = array();
          
          foreach($ff['radio'] as $val) {
            $rr = ' id=\''.$ff['n'].'\' name=\''.$ff['n'].'\' value="'.$val.'" ';
            if ($val == $fv) {
              $rr.=' checked=\'checked\' ';
            }
            
            $ho[$val] = $rr;
          }
          break;
        
        case DGF_CONTROLTYPE_DATEPICKER:
          $rr = substr($ff['n'],1);
          $ho = "onclick=\"caldn.select(document.getElementById('$rr'),'_$rr','dd/MM/yyyy'); return false;\" name=\"_$rr\" id=\"_$rr\"";
          break;
          
      }
      
      return $ho;
    }

    function checkFileUpload($fname) {
      return ((isset($_FILES[$fname])) && ($_FILES[$fname]['name']!=''));
    }
    
    function uploadFileForm($fname,$fpath) {
      $msgerr = '';
    
      if ((isset($_FILES[$fname])) && ($_FILES[$fname]['name']!='')) {
        if ($_FILES[$fname]['error']!=0) {
          $msgerr= 'Upload error: '.$_FILES[$fname]['error'];
          return $msgerr;
        }
        $imgtype = $_FILES[$fname]['type'];
//          if (($imgtype != 'image/gif') && ($imgtype != 'image/pjpeg') && ($imgtype != 'image/jpeg')) {
//            die('Formato immagine non valido, accettati solo JPG e GIF!');
//          }
       
//        if (basename($fpath) == '') { 
          $nfile = $_FILES[$fname]['name'];// assign the file name to a variable.
//        } else {
//          $nfile = basename($fpath);
//          $fpath = dirname($fpath);
//        }
        $nfile = str_replace(" ","_",$nfile);// change spaces to _.
        $nfile = str_replace("%20","",$nfile);// change URL encoded space to nospace.
        
        $localfile = $fpath.$nfile;
        
        $num = 1;
        
        $orgfile = $nfile;
        
        while (file_exists($localfile)) {          
          if (!(strpos($orgfile,'.') === true)) {
            $exfile = pathinfo($orgfile);
						
            $nfile = substr($orgfile,0,-($exfile['extension']+1)).$num.'.'.$exfile['extension'];
            
//            echo $nfile;
            
//            $exfile = explode('.',$nfile);
//            $nfile = $exfile[0].'1.'.$exfile[1];
          } else {
            $nfile=$orgfile.$num;
          }
          $num++;
          
          $localfile = $fpath.$nfile;
        }
        
        $this->setField($fname,'filename',$nfile);
        
        $localfile = $fpath.$nfile;

        $ret = move_uploaded_file($_FILES[$fname]['tmp_name'],$localfile);
        if ((!$ret) || (!file_exists($localfile))) {
          $msgerr = 'Error saving image from '.$_FILES[$fname]['tmp_name'].' to '.$localfile;
          return $msgerr;
        }
        
        if ($msgerr == '') {
          return true;
        } else {
          return $msgerr;
        }
      }
    }    
    
    function setSelectList($fname,$fselect) {
      $this->setField($fname,'sel',$fselect);    
    }
    
    function setRequired($fname,$msgfieldmissing='') {
      if (is_object($fname)) {
        $fd = $fname;
      } else {
        $fd = $this->getField($fname);
      }
      $fd['opt_required'] = true;
      $this->fvar[$fd['n']] = $fd;
    }
    
    function prepareTBS() {
      $hasSelectionList = false;
      $hasDatePicker = false;
      $body = array();
      $body['header'] = '';
      $head = '';    
    
      $arrfield = array();
      
      $arrfield['form'] = $this->formInit();
      
      $arrfield['form_submit'] = "> <input type='hidden' name='{$this->formname}_submit' value='".sha1(time())."' ";
      
      foreach($this->fvar as $ff) {
        $arrfield[$ff['n']] = $this->formField($ff['n']);
        
        if (isset($ff['opt_slist'])) {
          $hasSelectionList = true;

          $arrfield[$ff['n']].= '>&nbsp;<a href="#" onClick="dropdownmenu(this, event, dgfselection_'.$ff['n'].');return false"><img src="dgf_combo/selection_marker.gif" width="16" height="16" border="0"></a';

          
          $body['header'].='<script> var dgfselection_'.$ff['n'].'=new Array(); ';
          $idx = 0;
          foreach($ff['opt_slist'] as $rr => $vv) {
            $body['header'].=' dgfselection_'.$ff['n'].'['.$idx.']=\'<a href="#" onclick="dgfsetselection(\\\''.$ff['n'].'\\\',\\\''.$rr.'\\\');return false">'.$vv.'</a>\'; ';
            $idx++;
          }
          
          $body['header'].='</script>';
        }
        
        if (isset($ff['opt_datepicker'])) {        
          $arrfield[$ff['n']].= "onclick=\"caldn.select(document.getElementById('".$ff['n']."'),'_".$ff['n']."','dd/MM/yyyy'); return false;\"";
          $hasDatePicker = true;
        }
        
      }
      
      if ($hasSelectionList) {
        $head = ' <script language="JavaScript" type="text/javascript" src="dgf_combo/anylink.js"></script> <link href="dgf_combo/anylink.css" rel="stylesheet" type="text/css"> ';
      }
      
      if ($hasDatePicker) {
        $body['header'].='<DIV ID="laydatanascita" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>'.
'<script language="JavaScript" type="text/javascript" id="jscaldn">'.
  'var caldn = new CalendarPopup("laydatanascita");'.
  'caldn.setCssPrefix("CALBLU");'.
//  'caldn.showNavigationDropdowns();'.
'</script>';

      $head.='<script language="JavaScript" type="text/javascript" src="dgf_calendarpicker/calendarpopup.js"></script>';
      }
      
      $arrfield['body'] = $body;
      $arrfield['head'] = $head;
      
      return $arrfield;
    }    
    
    function getTagStatement($source,$tag,$name) {
      $ris = false;
      
      $idx = 0;
      do {
        $stx = 0;
        $nxt = stripos($source,'<'.$tag,$idx);
        if ($nxt !== FALSE) {
          $stx = $nxt;
          $nxt = stripos($source,'>',$nxt);
          if ($nxt !== FALSE) {
            $par = substr($source,$stx,$nxt-$stx+1);
            $xpar = $nxt;
            
            if (preg_match('/name=["\']'.$name.'["\']/i',$par)) { 
              if (substr($par,-2,1) != '/') {
                $nxt = stripos($source,'</'.$tag.'>',$nxt);
                if ($nxt !== FALSE) {
                  $nxt+=strlen('</'.$tag.'>');
                } else {
                  die("TAG $tag NOT CLOSED: $par");
                }
              }
              
              $ris = array($stx,$nxt,$par,$xpar);
              
              $idx = strlen($source);
            } else {
              $idx = $nxt;
            }
            
          } else {
            die("TAG $tag NOT TERMINATED");
          }
        } else {
          $idx = strlen($source);
        }        
      } while ($idx < strlen($source));
      
      return $ris;
    }
    
    function includeHTML(&$source,$tag,$include) {
      $pp = stripos($source,'</'.$tag.'>');
      if ($pp !== FALSE) {
        $out = substr($source,0,$pp).$include.substr($source,$pp);
        
//        echo htmlentities($out);
        
        $source = $out;      
        return TRUE;
      } else {
        return FALSE;
      }
    }
    
    function prepareSource(&$tbs) {
		  $htmlsource = $tbs->Source;
		
      $flg_tinymce = false;
      $els_tinymce = array('advanded' => array(), 'simple' => array());
      
			$flg_calendar = false;
			
      $out = '';      
      $idx = 0;
      
      $ris = false;
      
      if ($this->formname == '') {
        $source = $htmlsource;
      } else {      
        $ris = $this->getTagStatement($htmlsource,'form',$this->formname);
				
				if (!$ris) {
				  return false;
				}
				
        $source = substr($htmlsource,$ris[0],$ris[1]-$ris[0]+1);
      }
      
//      $out = $source;

      $out = substr($htmlsource,0,$ris[0]);

      do {
        $nxt = strpos($source,'<',$idx);
        
        if ($nxt !== FALSE) {
          if ($idx < $nxt) {
            $out.=substr($source,$idx,$nxt - $idx);
          }
          
//          $out.='<';
          
          $nxt++;
          
          $edx = strpos($source,'>',$nxt);
          
          if ($edx !== FALSE) {
            $mat = substr($source,$nxt-1,$edx - $nxt + 2);
            
//            echo htmlentities($mat).'<br>';
            
            $res = array();
            if (preg_match('/name=["\']([a-zA-Z0-9_\-\[\]]+)["\']/i',$mat,$res)) {          
						
              $ckn = (substr($res[1],-2)=='[]') ? substr($res[1],0,-2) : $res[1];
            
              if ($this->hasField($ckn)) {
                $val = '';
                $prop = '';
                $cont = '';
              
                $keyb = '';
                $keyn = $ckn;
                $realn = $res[1];
								$kval = '';
                
//								echo htmlentities($mat).'<br>';
  
                if (strpos($keyn,'.') !== FALSE) {
                  $posb = strpos($keyn,'.');
                  $keyb = substr($keyn,$posb);
                  $keyn = substr($keyn,0,$posb-1);
                } else {
								  $ival = array();
								  if (preg_match('/value=["\']([a-zA-Z0-9_]+)["\']/i',$mat,$ival)) {									  
									  $kval = $ival[1];
//										echo "#$kval#";
									}
								}
              
                $val = $this->getFieldValue($keyn);
              
                if (!is_array($val)) {
                  $val = htmlentities($val);
                }

/**/

                $ff = $this->getField($keyn);
                $ft = $ff['t'];
                $fv = $this->getFieldValue($keyn);
            
                switch ($ft) {
                  case DGF_FIELDTYPE_LABEL:
                    $prop = ' READONLY ';
                    break;
                  case DGF_FIELDTYPE_CHECKBOX:          
                    if ($fv == $ff['cdv']) {
                      $prop = ' CHECKED ' ;
                    }
                  break;

                  case DGF_FIELDTYPE_MULTICHECKBOX:
                  
                    if (isset($_POST[$keyn])) {
                      if (in_array($kval,$_POST[$keyn])) {         
                        $prop = ' CHECKED ' ;
                      }
                    } else {
                      if (in_array($kval,$val)) {         
                        $prop = ' CHECKED ' ;
                      }
                    }
                  
                    $val = $kval;
                  
/*                  
                    foreach($ff['checklist'] as $vals) {
                      if ($vals == $keyb) {
//                    $rr = ' name=\''.$ff['n'].'[]\' value=\''.$vals.'\' ';
                        if ((is_array($fv)) && (in_array($vals,$fv))) {
                          $prop = ' CHECKED ' ;
                          break;
                        }            
                      }
                    }
*/                    
                    break;
                    
                  case DGF_FIELDTYPE_AREA:
//                    print_r($this->getField($keyn));
                    $cont = $val;
                    $val = '';
                    
                    if (isset($ff['opt_tinymce'])) {
                      $flg_tinymce = true;
                      $els_tinymce[$ff['opt_tinymce_theme']][] = $ff['n'];
                      
                      if ($ff['opt_tinymce_basepath'] != '') {
                        
                      
//                        $cont = eregi_replace('(<img .*)(src=["|\']?)~/([^"\']*)("|\')?([^/>]*)','\\1\\2'.$ff['opt_tinymce_basepath'].'\\3\\4',$cont);
  
                        $cont = str_replace('~/',$ff['opt_tinymce_basepath'],$cont);
  
//                        echo htmlentities($cont);
                      }                      
                    }
                    
                    break;
                    
                  case DGF_FIELDTYPE_SELECT:
                    $val = '';

                    if (isset($ff['opt_autosubmit'])) {
                      $prop.=' onchange="document.getElementById(\''.$this->formname.'\').submit()" ';
                    }
                    
                    foreach($ff['sel'] as $kk => $srow) {
//                      $srow = htmlentities($srow);
                      if (is_array($srow)) {
                        $srowval = $srow['val'];
                        $srowid = $srow['key'];
                        $data = '';
                        foreach($srow as $dk => $dv) if (($dk!='val')AND($dk!='key')) $data.='data-'.$dk.'="'.str_replace('"','\"',$dv).'" ';
                        
                        $cont.="<OPTION value=\"{$srowid}\" {$data}".(($kk == $fv)? ' selected ' : '').">$srowval</OPTION>";
                      
                      } else {
                        $cont.="<OPTION value=\"$kk\" ".(($kk == $fv)? ' selected ' : '').">$srow</OPTION>";
                      }
                    }
                    
                    break;
                  case DGF_BUTTON_SUBMIT:
                    break;
                    
                  case DGF_FIELDTYPE_RADIO:
                    $ho = array();
                    
//                    foreach($ff['radio'] as $val) {
//                  $rr = ' name=\''.$ff['n'].'\' value="'.$val.'" ';
                      if ($kval == $fv) {
                        $prop =' CHECKED=\'CHECKED\' ';
                      }
											$val = $kval; //mantieni valore HTML
                      
//                  $ho[$val] = $rr;
//                    }
                    break;
                  
                  case DGF_CONTROLTYPE_DATEPICKER:
                    $rr = substr($ff['n'],1);
                    $prop = "onclick=\"caldn.select(document.getElementById('$rr'),'_$rr','dd/MM/yyyy'); return false;\" name=\"_$rr\" id=\"_$rr\"";
										
										$flg_calendar = TRUE;
										
                    break;
                    
                    
                  case DGF_FIELDTYPE_IMAGE:
                    $val = '';
                    
                    if (is_array($fv)) {
                      $fico = $fv[0];
                      $fv = $fv[1];
                    } else {
                      $fico = $ff['img_path'].$fv;
                    }
                    
                    if ($fv == '') {
                      $img_src = 'imgtools/no_image.gif';
                    } else {
                      if (file_exists($fico)) {
//                      $img_src = $ff['img_path'].$fv;
                        $img_src = $fico;
                      } else {
                        $img_src = 'imgtools/error_image.gif';
                      }
                    }
                    
                    $mat = '<table border="0" align="center" cellpadding="2" cellspacing="0">'.
												   '<tr><td align="center" valign="top">';
                    
                    $mat.= '<a href="#" onclick="selImg(\''.$keyn.'\'); return false"><img'.
                           ' src="'.$img_src.'" alt="clicca per selezionare una nuova immagine" '.
                           'name="'.$keyn.'_src" width="'.$ff['img_w'].'" height="'.$ff['img_h'].'" border="0" class="boximg" '.
                           'id="'.$keyn.'_src" style="background-color: #FDE7C6; margin: 1px;" /></a>'.
                           "<input type='hidden' id='{$keyn}' name='{$keyn}' value='{$fv}' />";

                    if ($ff['opts']>0) {
                      $mat.= '<br clear="all" /><a href="#" '.
                             'onclick="selImg(\''.$keyn.'\'); return false">[seleziona]</a><br />';
                    }                           
                           
                    if ($ff['opts']>1) {                          
										  $mat.='<p><span class="subtesto">consigliate: '.$ff['img_w'].'x'.$ff['img_h'].' </span><br /> '.
													 '</p>';
                    }
                    
                    $mat.= '</td></tr></table>';                    
                    break;
                }
            
                if ($val != '') {
                  if (stripos($mat,'value=') !== FALSE) {
                    $mat = preg_replace('/value=["\']([a-zA-Z0-9]*)["\']/i',"value='$val'",$mat);
                    $val = '';
                  } else {
                    $val = ' value="'.$val.'" ';
                  }              
                }

                if (substr($mat,-2,1) == '/') {
                  $mat = substr($mat,0,-3).$val.' '.$prop.'/>';
                } else {
                  $mat = substr($mat,0,-1) . $val.' '.$prop .'>';
                  if ($cont != '') {
                    $mat.=$cont;
//                    echo htmlentities($cont);
                  }
                }
            
                $out.= $mat;
            
//            $out = $par.substr($source,$kr[1]);

/**/
                
                
                
              } else {
                $out.=$mat;
              }
            } else {
              $out.=$mat;
            }
            
            $idx=$edx+1;
          } else {
            $idx = $idx + 1;          
            $out.= '<';
          }
        } else {
          $out.=substr($source,$idx);
          $idx = strlen($source)+1;
        }
        
      } while ($idx < strlen($source));

      if ($flg_tinymce) {
        $txt = file_get_contents(INCLUDE_PATH.'dgf_tinymce.js');
        
        $txt = str_replace('[mce_els_advanced]',implode(',',$els_tinymce['advanced']),$txt);
        $txt = str_replace('[mce_els_simple]',implode(',',$els_tinymce['simple']),$txt);
        
        $this->includeHTML($out,'HEAD',$txt);
      }      
			
      if ($ris !== FALSE) {      
        $out.=substr($htmlsource,$ris[1]+1);
      }
			
			if ($flg_calendar) {
			  $this->includeHTML($out,'HEAD','<script language="JavaScript" type="text/javascript" src="dgf_calendarpicker/calendarpopup.js"></script>');
			
			  $this->includeHTML($out,'BODY','<DIV ID="laydatanascita" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>'.
'<script language="JavaScript" type="text/javascript" id="jscaldn">'.
  'var caldn = new CalendarPopup("laydatanascita");'.
  'caldn.setCssPrefix("CALBLU");'.
//  'caldn.showNavigationDropdowns();'.
'</script>');
			}

      $tbs->Source = $out; 
			
			return TRUE;			
    }
    
  }
?>