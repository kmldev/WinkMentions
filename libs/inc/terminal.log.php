<?php
  
  class ClsProgressAnsiLog {
    var $term = null;
    var $maxvalue = 0;
    var $length = 0;
    var $color = '';
    var $tmstart = 0;
    
    private $rendered = false;
    private $lastpos = 0;

    function __construct($term,$maxvalue=100,$color='',$length=20) {
      $this->term = $term;
      $this->maxvalue = $maxvalue;
      $this->color = $color;
      $this->length = $length;
      $this->tmstart = time();
    }

    function progress($val=0) {
      $pp = min( $this->length , round( $val * $this->length / $this->maxvalue ) );
      if (!$this->rendered) {
        $this->term->echo('Working...');
        $this->rendered = true;
      }
      if (($pp !== $this->lastpos) and ($pp === $this->length)) {
        $this->term->echo('...done in '.(time()-$this->tmstart).'s');
      }      
      $this->lastpos=$pp;
    }

    function complete() {
      $this->progress($this->maxvalue);
    }

    function close(){
      $this->term = null;
    }

    function __destruct() {
      if ($this->term) $this->close();
    }

  }

  class ClsTerminAnsiLog {

    private $log = null;

    const COLORS = [
      'bold'=>'**'
    ];

    function __construct($addtoname='') {

      $name = str_replace('.php',$addtoname.'.log',($_SERVER["SCRIPT_NAME"] ? basename($_SERVER["SCRIPT_NAME"]) : 'noname.php'));
      $this->log = new LogClass('###_'.$name);

    }
    
    function progressBar($maxvalue=100,$color='',$length=20) {
      return new ClsProgressAnsiLog($this,$maxvalue,$color,$length);
    }

    function out($str='',$lvl='') {
      if ($lvl) {
        if (isset(self::COLORS[$lvl])) $lvl = self::COLORS[$lvl];
        else $lvl = " [{$lvl}] ";
      }
      $this->log->add(date('YmdHis')." {$lvl}".$str);
    }

    function __destruct() {      
    }

    static function getColor($col,$bright=null,$blink=false) {
      return '';
    }

    static function resetColor() {
      return '';
    }

    function parse($str) {

      $out = preg_replace_callback(
        "/@([A-Z;]{3,17})@([^@]*)/",
        function($found){
          return $found[2];
        },
        $str);
      return $out;

    }


    /**
     * @return ClsTerminAnsiLog
     */
    function echo($str) {
      $out = $this->parse($str);
      $this->out($out);
      return $this;
    }

    /**
     * @return ClsTerminAnsiLog
     */
    function line($str) {
      $this->out($this->parse($str));
      return $this;
    }

     /**
     * @return ClsTerminAnsiLog
     */
    function color($col) {
      throw new Exception("Not supported", 1);
    }

    /**
     * @return ClsTerminAnsiLog
     */
    function blink() {
      throw new Exception("Not supported", 1);
    }

    /**
     * @return ClsTerminAnsiLog
     */
    function backspace() {
      throw new Exception("Not supported", 1);
    }

    /**
     * @return ClsTerminAnsiLog
     */
    function nl() {
      return $this;
    }

    /**
     * @return ClsTerminAnsiLog
     */    
    function repeat($str,$repeat=1) {
      $cc = str_repeat($str,$repeat);
      $this->echo($cc);
      return $this;
    }

    /**
     * @return ClsTerminAnsiLog
     */    
    function back($repeat=1) {
      throw new Exception("Not supported", 1);
    }

    /**
     * @return ClsTerminAnsiLog
     */    
    function delete($repeat=1) {
      throw new Exception("Not supported", 1);
    }


    /**
     * @return ClsTerminAnsiLog
     */
    function reset() {
      return $this;
    }

    /**
     * @return ClsTerminAnsiLog
     */
    function default() {
      return $this;
    }

  }