<?php

  class ClsProgressAnsi {
    var $term = null;
    var $maxvalue = 0;
    var $length = 0;
    var $color = '';

    private $rendered = false;
    private $lastpos = 0;

    function __construct($term,$maxvalue=100,$color='',$length=20) {
      $this->term = $term;
      $this->maxvalue = $maxvalue;
      $this->color = $color;
      $this->length = $length;
    }

    function progress($val=0) {
      $pp = min( $this->length , round( $val * $this->length / $this->maxvalue ) );
      if (!$this->rendered) {
        $this->term->echo('[')->repeat('-',$this->length)->echo(']')->back($this->length+1)->color($this->color);
        $this->rendered=true;
        $this->lastpos=0;
      } else {
        if ($pp!=$this->lastpos) $this->term->repeat('☰',max(0,$pp-$this->lastpos));
        $this->lastpos = $pp;
      }
    }

    function close() {
      $this->term->nl();
      $this->term = null;
    }

    function complete() {
      $this->progress($this->maxvalue);
    }

    function __destruct() {
      if ($this->term) $this->close();
    }

  }

  class ClsTerminAnsi {

    const START_ESC = "\033[";

    const BLACK   = "30";
    const RED     = "31";
    const GREEN   = "32";
    const YELLOW  = "33";
    const BLUE    = "34";
    const MAGENTA = "35";
    const CYAN    = "36";
    const WHITE   = "37";
    const DEFAULT = "39";
    const BGDEFAULT = "49";
    const RESET   = "0";

    const CODE_LIST = [
      'BLACK' => self::BLACK,
      'RED' => self::RED,
      'GREEN' => self::GREEN,
      'YELLOW' => self::YELLOW,
      'BLUE' => self::BLUE,
      'MAGENTA' => self::MAGENTA,
      'CYAN' => self::CYAN,
      'WHITE' => self::WHITE,
      'DEFAULT' => self::DEFAULT,
      'RESET' => self::RESET
    ];

    function __construct() {
      //foo
    }

    function __destruct() {
      $this->reset()->nl();
    }

    function progressBar($maxvalue=100,$color='',$length=20) {
      return new ClsProgressAnsi($this,$maxvalue,$color,$length);
    }


    function out($str='',$lvl='') {

      $cmd = [
        'bold' => '@LWHITE@',
        'success' => '@GREEN@',
        'warning' => '@LYELLOW@',
        'alert' => '@RED@'
      ];

      if ($lvl and isset($cmd[$lvl])) $this->echo($cmd[$lvl]);
      $this->line($str)->reset();

    }

    static function getColor($col,$bright=null,$blink=false) {
      $out = self::START_ESC;
      if ($bright !== null) $out.= ($bright) ? ';1' : ''; //';0';
      if ($blink) $out.=';5';
      return $out.';'.$col."m";
    }

    static function getColorCode($col,$bright=null,$blink=false) {
      $out = $col;
      if ($bright !== null) $out.= ($bright) ? ';1' : ''; //';0';
      if ($blink) $out.=';5';
      return $out;
    }

    static function resetColor() {
      return self::START_ESC."0m";
    }

    /**
     * @return ClsTerminAnsi
     */
    function echo($str) {

      $out = preg_replace_callback(
        "/@([A-Z;]{3,17})@/",
        function($found){
          $mcol = $found[1];

          if (($mcol == 'RESET')or($mcol == 'DEFAULT')) {
            return self::getColor(self::CODE_LIST[$mcol]);
          }
          else {

            $cols = explode(';',$mcol);

            $cmd = "";

            foreach($cols as $tp => $col) {

              $lum = false;
              if (substr($col,0,1) == 'L') {
                $col = substr($col,1);
                $lum = true;
              }

              if (isset(self::CODE_LIST[$col])) {
                $cc = self::CODE_LIST[$col];
                if ($tp==1) $cc = ';'.preg_replace('/3([0-9])/','4${1}',$cc);               
                $cmd.=self::getColorCode($cc,$lum);
              }

            }

            //var_dump($cmd);

            $cmd = self::START_ESC.$cmd."m";

            return $cmd;

          }

          return $found[0];
        },
        $str);
      echo $out;

      return $this;

    }

    /**
     * @return ClsTerminAnsi
     */
    function line($str) {
      $this->echo($str)->nl();
      return $this;
    }

     /**
     * @return ClsTerminAnsi
     */
    function color($col,$bgcol=false) {
      $brg = false;
      if (preg_match("/^[a-z]/i",$col)) {
        if ($col[0]=='L') {
          $brg = true;
          $col = substr($col,1);
        }
        $col = self::CODE_LIST[$col] ?? self::CODE_LIST['DEFAULT'];
      }
      $out = $col.(($brg)?";1":"");

      if ($bgcol) {
        $brg = false;
        if (preg_match("/^[a-z]/i",$bgcol)) {
          if ($bgcol[0]=='L') {
            $brg = true;
            $bgcol = substr($bgcol,1);
          }
          $bgcol = self::CODE_LIST[$bgcol] ?? self::CODE_LIST['DEFAULT'];
          $bgcol = "4".substr($bgcol,1);
        }
        $out .= ";".$bgcol.(($brg)?";1":"");
      }

      echo self::START_ESC.";".$out."m";
      return $this;
    }

    /**
     * @return ClsTerminAnsi
     */
    function blink() {
      echo self::START_ESC.'5m';
      return $this;
    }

    /**
     * @return ClsTerminAnsi
     */
    function backspace() {
      echo "\b";
      return $this;
    }

    /**
     * @return ClsTerminAnsi
     */
    function nl() {
      echo "\n";
      return $this;
    }

    /**
     * @return ClsTerminAnsi
     */    
    function repeat($str,$repeat=1) {
      for($a=0;$a<$repeat;$a++) $this->echo($str);
      return $this;
    }

    /**
     * @return ClsTerminAnsi
     */    
    function back($repeat=1) {
      for($a=0;$a<$repeat;$a++) echo "\010";
      return $this;
    }

    /**
     * @return ClsTerminAnsi
     */    
    function delete($repeat=1) {
      for($a=0;$a<$repeat;$a++) echo "\177";
      return $this;
    }


    /**
     * @return ClsTerminAnsi
     */
    function reset() {
      echo self::START_ESC."0m";
      return $this;
    }

    /**
     * @return ClsTerminAnsi
     */
    function default() {
      echo self::START_ESC.";".self::DEFAULT.';'.self::BGDEFAULT."m";
      return $this;
    }


  }