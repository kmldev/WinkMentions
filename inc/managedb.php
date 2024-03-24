<?php
  /*
  ** ManageDB
  ** 2003-04 (c) Fastek snc - Fastekonline.it
  ** 2005-11 (c) Indigo srl - indigo.it
  ** 2018 (c) Antimateria Milano
  ** L'utilizzo gratuito è concesso solo per uso personale e mantenendo questo copyright
  */

  require_once(__DIR__.'/db_mysqli.php');  // MySQLi
  require_once(__DIR__.'/../cfg/vardb.inc.php');

  function db_cnx() {
    $dbpar = leggiParametriDB();
  
    $cnxDB = mysqli_connect($dbpar['host'],$dbpar['user'],$dbpar['pwd'],$dbpar['db']) ;
    return $cnxDB;
  }

  function db_newconnection() {
    $dbpar = leggiParametriDB();

    return new DB_Sql($dbpar['host'],$dbpar['db'],$dbpar['user'],$dbpar['pwd']);
  }

  function db_open() {
    global $myDB;
    $dbpar = leggiParametriDB();
    $myDB=new DB_Sql($dbpar['host'],$dbpar['db'],$dbpar['user'],$dbpar['pwd']);
  }

  function db_query($str_query,$conn=false) {
    global $myDB;

    if (!is_object($conn)) {
		  if ($conn) {
			  $tmp = db_newconnection();
				$tmp->query($str_query);
				return $tmp;
			} else {
				if (!isset($myDB) || (!$myDB->query_id())) {
					db_open();
				}
			}

//			file_put_contents('log/sqlerror.log',$str_query."\n",FILE_APPEND);
			
      $ris = $myDB->query($str_query);

      return ($ris) ? $myDB : false;
    } else {
      $conn->query($str_query);
      return $conn;
    }
  }
  
  function db_query_value($str_query,$conn=false) {
    $res = db_query($str_query,$conn);
    if ($res AND $res->next_record()) {
      if (count($res->Record) == 1) {
        return  array_values($res->Record)[0];
      } else {
        $val = array();
        foreach($res->Record as $nn=>$ff) {
          if (is_string($nn)) {
            $val[$nn] = $ff;
          }
        }
        return $val;
      }
    } else {
      return FALSE;
    }
  }  

  function db_exe($str_query,$conn=false) {
    global $myDB;

    if ($conn===false) {

      if (!isset($myDB) || (!$myDB->link_id())) {
        db_open();
      }

      $myDB->query($str_query);
      $affrow = $myDB->affected_rows();
      $myDB->noresult();

    } else {
      if ($conn === true) {
         $conn = db_newconnection();
      }
      $conn->query($str_query);
      $affrow = $conn->affected_rows();
      $conn->noresult();
    }
    
    return $affrow;
  }
  
  function db_insert($tab,$qf) {
    return db_exe(queryBuilder::insert($tab,$qf));
  }

  function db_update($tab,$qf,$qk) {
    return db_exe(queryBuilder::update($tab,$qf,$qk));
  }

  function db_replace($tab,$qf) {
    return db_exe(queryBuilder::replace($tab,$qf));
  }

  function db_select($tab,$select='*',$wheres=array(),$limit='') {
    return db_query(queryBuilder::select($tab,$select,$wheres));
  }

  function db_select_value($tab,$select='*',$wheres=array(),$limit='') {
    return db_query_value(queryBuilder::select($tab,$select,$wheres));
  }

  function db_safe($txt) {
    return str_replace(array('\\', "\0", "\n", "\r", "\x1a"),array('\\\\', '\\0', '\\n', '\\r', '\\Z'),$txt);
  }
  
  function db_begin() {
    global $myDB;
    if (!isset($myDB) || (!$myDB->link_id())) {
      db_open();
      db_query('SELECT 1;');  // create LINK_ID connection
    }
    $myDB->begin();
  }
  
  function db_commit() {
    global $myDB;
    $myDB->commit();
  }
  
  function db_rollback() {
    global $myDB;
    $myDB->rollback();
  }
  
  function db_lastid() {
    global $myDB;
    return $myDB->lastInsertId();

/*    
    $res = db_query('SELECT LAST_INSERT_ID() as cod');
    if ($res->next_record()) {
      return $res->f('cod');
    } else {
      return '';
    }
*/    
  }

  function db_getlasterrno() {
    global $myDB;
    return $myDB->Errno;
  }
  
  function db_record2globals($res,$fld=array()) {
    if (count($fld) == 0) {
      $fld = array();
      foreach($res->Record as $kk => $vv) {
        if (!is_numeric($kk)) {
          $fld[] = $kk;
        }
      }
    }
    
    foreach($fld as $kk) {
      $GLOBALS[$kk] = $res->Record[$kk];
    }
  }
  
  class queryFields {
    var $qfield = array();
    var $qtype = array();
    
    function __construct($lst = false) {
      if ($lst) {
        foreach($lst as $nn => $ff) {
          if ($nn[0] == '!') {
            $this->addCommand(substr($nn,1),$ff);
          }
          elseif ($nn[0] == '@') {
            $this->addString(substr($nn,1),$ff);
          }
          elseif ($nn[0] == '#') {
            $this->addNumeric(substr($nn,1),$ff);
          }
          elseif ($nn[0] == '*') {
            $this->addSpecial(substr($nn,1),$ff);
          }
          elseif (is_float($ff)) {
            $this->addFloat($nn,$ff);
          }
          elseif (is_numeric($ff)) {
            $this->addNumeric($nn,$ff);
          }
          else {
            $this->addString($nn,$ff);
          }
        }
      }
    }
    
    function addString($name,$val,$nullable = false) {
      $this->qfield[$name] = db_safe($val);
      $this->qtype[$name] = ($nullable) ? 'S' : 's';
    }
    function addNumeric($name,$val) {
      $this->qfield[$name] = $val;
      $this->qtype[$name] = 'n';
    }
    function addFloat($name,$val) {
      $this->qfield[$name] = $val;
      $this->qtype[$name] = 'f';
    }
    function addDate($name,$val) {
      $this->qfield[$name] = $val;
      $this->qtype[$name] = 'd';
    }
    function addCommand($name,$val) {
      $this->qfield[$name] = $val;
      $this->qtype[$name] = 'x';
    }
    function addSpecial($name,$val) {
      $this->qfield[$name] = $val;
      $this->qtype[$name] = '*';
    }
    
    function clear() {
      $this->qfield = [];
      $this->qtype = [];
    }
    function count() {
      return count($this->qfield);
    }    
    
    function toParam($str) {
      if (is_array($str)) {
        $lst = [];
        foreach($str as $term) {
          $lst[] = "'".str_replace("'","\'",$term)."'";
        }
        return $lst;
      }
      return "'".str_replace("'","\'",$str)."'";
    }
    
    function asQuery($name) {
      switch ($this->qtype[$name]) {
        case 'n':
        case 'f':
          //if (('' == $this->qfield[$name])AND('0' != $this->qfield[$name])) {
          if ($this->qfield[$name] === null) {
            return 'NULL';
          } else {
            return $this->qfield[$name] ?: 0;
          }
          break;
        case 'd':
          return $this->toParam($this->qfield[$name]);
          break;
        case 'x':
          return $this->qfield[$name];
          break;
        case 's':
          if ($this->qfield[$name] == '') {
            return 'NULL';
          } else {
            return $this->toParam($this->qfield[$name]);
          }
          break;
        default:
          return $this->toParam($this->qfield[$name]);
      }
    }

    function asQueryFull($name,$fieldname='') {
      if ('' == $fieldname) {
        $ret=$name;
      } else {
        $ret=$fieldname;
      }
      
      switch ($this->qtype[$name]) {
        case 'n':
        case 'f':
        case 'd':
        case 'x':
        case '0':
          $retval = $this->asQuery($name);
          break;
        case '*':
          return $ret.' '.$this->qfield[$name];
          break;
        default:
          if ($this->qfield[$name] == null) {
            return $ret.' IS NULL';
          }
        
          $retval = $this->toParam($this->qfield[$name]);

          if (is_array($retval)) return $ret.' IN ('.implode(',',$retval).')';

          if (substr_count($retval,'%') > 1) {
            return $ret.' LIKE '.$retval;
          }
      }

      if ($retval === null) {
        return $ret.' = NULL';
      }

      return $ret.'='.$retval;
    }
 
    function getNames() {
      return array_keys($this->qfield);
    }

  }
  
  class queryBuilder {
     static function insert($table,$fields) {
       
       if (is_array($fields)) $fields = new queryFields($fields);
       
       $qry='INSERT INTO '.$table.' (';
       $names = $fields->getNames();
       $qry.=implode(',',$names).') VALUES (';
       foreach($names as $kk) {
         $qry.=$fields->asQuery($kk).',';
       }
       $qry=substr($qry,0,-1).')';
       
       return $qry;
     }

     static function replace($table,$fields) {
       $qry='REPLACE '.$table.' (';

       if (is_array($fields)) $fields = new queryFields($fields);

       $names = $fields->getNames();
       $qry.=implode(',',$names).') VALUES (';
       foreach($names as $kk) {
         $qry.=$fields->asQuery($kk).',';
       }
       $qry=substr($qry,0,-1).')';

       return $qry;
     }


     static function update($table,$fields,$wheres) {
       $qry='UPDATE '.$table.' SET ';
       
       if (is_array($fields)) $fields = new queryFields($fields);
       if (is_array($wheres)) $wheres = new queryFields($wheres);
       
       $names = $fields->getNames();
       foreach($names as $kk) {
         $qry.=$kk.'='.$fields->asQuery($kk).',';
       }
       $qry=substr($qry,0,-1).' WHERE ';

       $names = $wheres->getNames();
       foreach($names as $kk) {
         $qry.=$wheres->asQueryFull($kk).' AND ';
       }
       $qry = substr($qry,0,-5);

       return $qry;
     }

     static function select($table,$fields = '*',$wheres=FALSE,$footer='') {
       $qry = 'SELECT '.$fields.' FROM '.$table;
       
       if ($wheres != FALSE) {
         
         if (is_array($wheres)) $wheres = new queryFields($wheres);
         
         $qry.= ' WHERE ';
         $names = $wheres->getNames();
         foreach($names as $kk) {
           $qry.=$wheres->asQueryFull($kk).' AND ';
         }
         $qry = substr($qry,0,-5);
       }
       
       $qry.=' '.$footer;

       return $qry;
     }

     static function delete($table,$wheres,$footer='') {
       
       if (is_array($wheres)) $wheres = new queryFields($wheres);
       
       $qry = 'DELETE FROM '.$table;
       $qry.= ' WHERE ';
       $names = $wheres->getNames();
       foreach($names as $kk) {
         $qry.=$wheres->asQueryFull($kk).' AND ';
       }
       $qry = substr($qry,0,-5).' '.$footer;

       return $qry;
     }

  }
?>
