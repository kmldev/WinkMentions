<?php
	
	DEFINE('USR_SESSION_NAME','IGTST5599_h31');

  DEFINE('USER_ROLE_ADMIN',1);
  DEFINE('USER_ROLE_USER',2);

  class UserException extends Exception {
    var $extras = [];
    function __construct($message = "", $code = 0, $previous = NULL) {
      if (is_array($message)) {
        $this->extras = array_splice($message,1);
        $this->message = $message[0];
      }
    }
  };
  class UserDuplicateException extends UserException {};

  class UserClass {
	
		var $cod = 0;
		var $user = '';
		var $pwd = '';
		var $email = '';
    var $uq = '';
		
		var $nome = '';
		var $cognome = '';
    var $codfiscale = '';
    
    var $telefono = '';
        
		var $lasterror = '';
	
	  function __construct($autorestore=FALSE) {
		  if ($autorestore) {
			  $uu = $this->restoreSession();
				if ($uu) {
				  return $uu;
				}
			}			
		}
	
	  static function restoreSession() {
		
			if (isset($_SESSION[USR_SESSION_NAME])) {
				$user = $_SESSION[USR_SESSION_NAME];
				
				if ($user instanceof UserClass) {				
				  return $user;
				}
			}
			
			return FALSE;
		
		}

    
 /**
 * @return UserClass
 */
		static function & session() {
		  $user = UserClass::restoreSession();
			if (!$user) {
			  $user = new UserClass;
			}
			
			return $user;
		}
		
	  function saveSession($user = FALSE) {
		  if (!$user) {
			  $user = & $this;
			}
		  $_SESSION[USR_SESSION_NAME] = $user;
		}
		
	  function safeParam($par) {
		  return db_safe($par);
		}

	  function clear() {
		  $this->cod = 0;
			$this->user = '';
			$this->email = '';
			$this->nome = '';
			$this->cognome = '';
      $this->uq = '';
		}
    
	  function getCod() {
		  return $this->cod;
		}
		
		function getNomeCompleto() {
		  return trim($this->nome.' '.$this->cognome);
		}

		function getNome() {
		  return trim($this->nome);
		}
		
	  function setError($msg) {
		  $this->lasterror = $msg;
		  return FALSE;
		}
		
		function getError() {
		  return $this->lasterror;
    }
    
/*    
	  function new($post) {

		  $this->cod = false;
      
      $form = ['nome','cognome','codfiscale','telefono','email','privacy1','privacy2'];
      $dbfields = ['@first_name','@last_name','@fiscal_code','@telefono','@email','@consensus_privacy','@consensus_regulation'];

      $set = [];

      foreach($form as $kk) {
        $set[] = self::safeParam($post[$kk]);
      }

      $qf = array_combine($dbfields,$set);

      $qf['user_role'] = USER_ROLE_USER;
      $qf['is_active'] = '1';

      $qf['!timestamp_registration_utc'] = 'NOW()';
      $qf['@ip'] = getClientIP();

      $retry = 0;
        
      do {        

        $retry++;

        //$uq = 'b'.strtolower(substr(sha1(mt_rand(99,99999)."fzj9382_%mm"),5,10));      
        //$qf->addString('uniq',$uq);
        
        $ris = db_insert('tbl_users',$qf);          
        usleep(5000);          

      } while (($ris!=1) AND ($retry<1));
        
      if ($ris != 1) {
        $dberr = db_getlasterrno();
        if ($dberr == SQLERROR_DUPLICATE_KEY) throw new UserDuplicateException(['Il codice fiscale è già stato registrato in precedenza.','CODFISC:'.$post['codfiscale']]);
        throw new UserException("Non è stato possibile completare la tua registrazione, verifica che i dati siano stati inseriti in modo corretto.");
      }

      $rep = ($ris==1) ? true : false;        

      $this->clear();

      return $rep;
			
    }
    */
    
	}
