<?php

  class ClsProgressAnsiWeb {
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
      if (!$this->rendered or ($this->lastpos != $pp)) {
        $gg = max(0,$pp);
        $g2 = max(0,$this->length - $gg);
        if (!$this->rendered) $this->term->_rawecho('<div class="pgbar">');
        $this->term->_rawecho('<div class="row">');
        $this->term->echo($this->color.'['.str_repeat('=',$gg).str_repeat('-',$g2).']');
        $this->term->_rawecho('</div>');
        $this->rendered=true;
        $this->lastpos=$pp;
        if ($pp == $this->length) $this->term->_rawecho('</div>');
      }
    }

    function complete() {
      $this->progress($this->maxvalue);
    }

    function close() {
      $this->term = null;
      //foo
    }


  }

  class ClsTerminAnsiWeb {

    const BLACK   = "#2c3e50";
    const RED     = "#c23616";
    const GREEN   = "#009432";
    const YELLOW  = "#ccae62";
    const BLUE    = "#0652DD";
    const MAGENTA = "#6F1E51";
    const CYAN    = "#22a6b3";
    const WHITE   = "#dcdde1";
    const DEFAULT = "#dcdde1";
    const RESET   = "";

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

    const HTML_OPEN = '<!DOCTYPE html><html lang=en><head><meta charset=UTF-8><meta name=viewport content="width=device-width,initial-scale=1.0"><meta http-equiv=X-UA-Compatible content="ie=edge"><title>💻</title><link href="https://fonts.googleapis.com/css2?family=Ubuntu+Mono&display=swap" rel="stylesheet"><style>.tm{background:#000;padding:2rem;border:1px solid gray;font-family:"Ubuntu Mono", monospace;color:gray;font-size:1.2rem;min-height:88vh;height:88vh;overflow-y:auto;text-shadow: 0 0 2px #555;}.tm>.row.warning{color:yellow}.tm>.row.bold{color:white}.tm>.row.alert{color:red}.tm>.row.success{color:greenyellow}.tm div.cursor{width:.2rem;height:1.1rem;display:inline-block;background:#0a0;margin-left:1px;animation:blink 1.6s linear 0s infinite;box-shadow: 0 0 4px #555;}@keyframes blink{0%{opacity:1}47%{opacity:.8}50%{opacity:0}97%{opacity:0}100%{opacity:.8}}div.pgbar>div{display:none;}div.pgbar>div:last-child{display:block;}</style></head><body><div class=tm>';

    const HTML_CLOSE = '<div class="cursor"></div></div></body></html>';

    var $act_style = [];
    var $fgbright = false;
    var $bgbright = false;

    var $buffer = '';

    var $openline = false;
    var $indent = 0;

    function __construct($autoopen=true) {

      if (session_status() != PHP_SESSION_ACTIVE) {
        if (ob_get_level() == 0) ob_start("ob_gzhandler");
      }

      if ($autoopen) $this->open();

    }

    function __destruct() {
      //$this->_rawecho(self::HTML_CLOSE);
      $this->close();
    }

    function open() {

      $buf = $this->buffer;
      $this->buffer = false;    

      //file_put_contents("out.txt","");

      $sn = ($_SERVER['SERVER_NAME']??'');
      $this->_rawecho(str_replace('</title>'," {$sn}</title>",self::HTML_OPEN));

      if ($buf) $this->_rawecho($buf);

    }

    function close() {      
      $this->_rawecho(self::HTML_CLOSE);
      if (ob_get_level()) {
        //echo ob_get_clean();
        ob_end_flush();
      }
    }

    function progressBar($maxvalue=100,$color='',$length=20) {
      return new ClsProgressAnsiWeb($this,$maxvalue,$color,$length);
    }

    function _rawecho($raw) {
      if ($this->buffer !== false) return $this->buffer.=$raw;
      echo $raw;
      //if (ob_get_level()) @ob_flush();
      //else flush();

      //file_put_contents("out.txt",$raw,FILE_APPEND);

    }

    function out($str='&nbsp;',$lvl='') {

      $cmd = [
        'bold' => '@LWHITE@',
        'success' => '@GREEN@',
        'warning' => '@LYELLOW@',
        'alert' => '@RED@'
      ];
      if ($lvl and isset($cmd[$lvl])) $this->echo($cmd[$lvl]);

      $this->line($str)->reset();

    }

    static function hex2rgb($hex) {
      list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
      return ['r'=>$r,'g'=>$g,'b'=>$b];
    }

    static function getRGBLuminance($rgb) {
      return round( $rgb['r']*0.299 + $rgb['g']*0.587 + $rgb['b']*0.114 );
    }

    static function RGBtoHSV($rgb) {
      list($r,$g,$b) = array_values($rgb);
      $r=($r/255); $g=($g/255); $b=($b/255);
      $maxRGB=max($r,$g,$b); $minRGB=min($r,$g,$b); $chroma=$maxRGB-$minRGB;
      if($chroma==0) return array('h'=>0,'s'=>0,'v'=>$maxRGB);
      if($r==$minRGB)$h=3-(($g-$b)/$chroma);
      elseif($b==$minRGB)$h=1-(($r-$g)/$chroma); else $h=5-(($b-$r)/$chroma);
      return array('h'=>60*$h,'s'=>$chroma/$maxRGB,'v'=>$maxRGB);
    } 

    static function hsv2hrgb($hsv) {;

      list($hue,$sat,$val) = array_values($hsv);

      $rgb = array(0,0,0);
     
      for($i=0;$i<4;$i++) {
        if (abs($hue - $i*120)<120) {
          $distance = max(60,abs($hue - $i*120));
          $rgb[$i % 3] = 1 - (($distance-60) / 60);
        }
      }     
      $max = max($rgb);
      $factor = 255 * ($val/1);
      for($i=0;$i<3;$i++) {     
        $rgb[$i] = round(($rgb[$i] + ($max - $rgb[$i]) * (1 - $sat/1)) * $factor);
      }
      $rgb = sprintf('#%02X%02X%02X', $rgb[0], $rgb[1], $rgb[2]);
      return $rgb;
    }

    static function getColorHex($col,$bright=null,$blink=false) {
      $out = '';

      if ($bright) {
        $hsv = self::RGBtoHSV(self::hex2rgb($col));
        $hsv['v'] = min(1,$hsv['v']*2);
        $hsv['s'] = min(1,$hsv['s']*0.8);
        $col = self::hsv2hrgb($hsv);
      }

      return $col;
    }

    function setColor($fg,$bg=false) {
      if ($fg != false) {
        $this->act_style['color']=$fg;
        unset($this->act_style['background-color']);
      }
      if ($bg != false) $this->act_style['background-color']=$bg;
    }

    function resetColor() {
      $this->act_style = '';
    }

    function getActualStyle() {
      $style = implode(';',array_map(function($item,$key){
        return $key.':'.$item;
      },$this->act_style,array_keys($this->act_style)));

      if ($this->indent<0) {
        $mx = ($this->indent*9);
        $style .= ";display:inline-block;width:0;transform:translatex({$mx}px);"; 
      }

      return $style;
    }

    function span($str) {
      if (!$str) return '';
      $style = $this->getActualStyle();      
      if ($this->indent<0) {
        $this->indent = min(0, $this->indent + strlen($str));
        if (!isset($this->act_style["background-color"])) $str="<span style='background-color:#000;'>{$str}</span>";
        //$this->indent = min(0,$this->indent+strlen($str));
        //console_log($str,$this->indent,strlen($str));        
      }
      return ($style) ? '<span style="'.$style.'">'.$str.'</span>' : $str;
    }

    function parse($str) {

      if ($str and (substr($str,0,1)!='@')) $str = '@ACTUAL@'.$str;

      $out = preg_replace_callback(
        //"/@([A-Z;]{3,17})@([^@]*)/",
        "/(@([A-Z;]{3,17}))?@?([^@]*)/",
        function($found){
          $mcol = $found[2] ?? '';

          $fg = false;
          $bg = false;

          if ($mcol == 'RESET') {
            $this->reset();
          }
          else if ($mcol == 'DEFAULT') {
            $fg = self::getColorHex(self::CODE_LIST['DEFAULT'],$this->fgbright);
            $bg = self::getColorHex(self::BLACK,$this->bgbright);
          }
          else {

            if (!$mcol) return $found[0] ? $this->span($found[0]) : '';

            $cols = explode(';',$mcol);

            foreach($cols as $tp => $col) {

              $lum = false;
              if (substr($col,0,1) == 'L') {
                $col = substr($col,1);
                $lum = true;
              }
    
              if (isset(self::CODE_LIST[$col])) {
                $hex = self::CODE_LIST[$col];
                if ($hex) {
                  if ($tp === 0) {
                    if ($col != 'DEFAULT') $this->fgbright = $lum;
                    $fg = self::getColorHex($hex,$this->fgbright);                    
                  }
                  else if ($tp === 1) {
                    if ($col != 'DEFAULT') $this->bgbright = $lum;
                    if ($col != 'DEFAULT') $bg = self::getColorHex($hex,$this->bgbright);
                    else $bg = self::getColorHex(self::BLACK,$this->bgbright);
                  }
                }
              }

            }
          
          }

          if ($fg or $bg) $this->setColor($fg,$bg);

          return ($found[3]) ? $this->span($found[3]) : '';
        },
        $str);
        
      return $out;

    }


    /**
     * @return ClsTerminAnsiWeb
     */
    function echo($str) {      

      if (!$this->openline) {
        $this->_rawecho('<div class="row">');
        $this->openline = true;
      }

      $out = $this->parse($str);
      $this->_rawecho($out);
      return $this;
    }

    /**
     * @return ClsTerminAnsiWeb
     */
    function line($str) {
      //$this->out($this->parse($str));

      if ($this->openline) {
        $this->_rawecho("</div>");
        $this->openline = false;
        $this->indent = 0;
      }

      $out = $this->parse($str);
      //$actstyle = $this->getActualStyle(); style='{$actstyle}'
      $this->_rawecho("<div class='row'>{$out}</div>");

      return $this;
    }

     /**
     * @return ClsTerminAnsiWeb
     */
    function color($col,$bgcol=false) {
      
      if (!$col) return $this;

      $brg = false;
      if ($col[0]=='L') {
        $brg = true;
        $col = substr($col,1);
      }      
      $col = self::CODE_LIST[$col] ?? self::CODE_LIST['DEFAULT'];
      $this->fgbright = $brg;

      if ($bgcol) {
        $brg = false;
        if ($bgcol[0]=='L') {
          $brg = true;
          $bgcol = substr($bgcol,1);
        }
        $bgcol = self::CODE_LIST[$bgcol] ?? self::CODE_LIST['DEFAULT'];
        $this->bgbright = $brg;
      }

      if ($bgcol) $this->setColor($col,$bgcol);
      else $this->setColor($col);

      return $this;

    }

    /**
     * @return ClsTerminAnsiWeb
     */
    function blink() {
      throw new Exception("Not supported", 1);
      //echo self::START_ESC.'5m';
      //return $this;
    }

    /**
     * @return ClsTerminAnsiWeb
     */
    function backspace() {
      throw new Exception("Not supported", 1);
      //$this->out();
      //return $this;
    }

    /**
     * @return ClsTerminAnsiWeb
     */
    function nl() {
      if ($this->openline) {
        $this->_rawecho('</div>');
        $this->openline = false;
      } else {
        $this->_rawecho('<br/>');
      }
      $this->indent = 0;
      
      return $this;
    }

    /**
     * @return ClsTerminAnsiWeb
     */    
    function repeat($str,$repeat=1) {
      $cc = str_repeat($str,$repeat);
      $this->echo($cc);
      return $this;
    }

    /**
     * @return ClsTerminAnsiWeb
     */    
    function back($repeat=1) {      
      $this->indent = $this->indent - $repeat;
      return $this;
    }

    /**
     * @return ClsTerminAnsiWeb
     */    
    function delete($repeat=1) {
      throw new Exception("Not supported", 1);
      //for($a=0;$a<$repeat;$a++) echo "\177";
      //return $this;
    }


    /**
     * @return ClsTerminAnsiWeb
     */
    function reset() {
      $this->act_style = [];
      $this->fgbright = false;
      $this->bgbright = false;
      return $this;
    }

    /**
     * @return ClsTerminAnsiWeb
     */
    function default() {
      return $this->echo('@DEFAULT@');
    }

  }