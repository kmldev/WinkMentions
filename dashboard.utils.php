<?php

  require_once './verificaadmin.inc.php';

  DEFINE('CAMPAGNAID',3); 
  DEFINE('CAMPAGNAID_MAX',3);

  class DashBoardClass {
    public $name = 'Dashboard';
    public $logo = '';

    public $campagna = CAMPAGNAID_MAX;   
    public $campagnaname = ""; 

    public $valorebuono = 0;
    public $hascodiceunico = false;

    public $data_start = 0;
    public $data_end = 0;
    public $user_avatar = '';
    public $user_name = '';

    const STORAGEID = 'dashboad2';

    var $criteri = []; //[[10,5],[20,10],[30,15],[40,20],[50,25],[60,30],[70,40],[80,50],[90,60],[100,70]]; // campagna #1

    function __construct($name=false,$logo=false) {
      if ($name) $this->name = $name;
      if ($logo) $this->logo = $logo;

      $_SESSION[self::STORAGEID] = serialize($this);
    }

    function getBustaRimborso($val) {

      $rr = 0;

      $rr = floor($val * 10);

/*      
      if ($val > 0 ) {

        if ($this->campagna > 1) {

          $nn = min(205,$val);
          $rr = (floor( ($nn - 1)  / 10 ) + 1) * 5; 

        } else {

          foreach($this->criteri as $row) {
            $rr = $row[1];
            if ($row[0]>=$val) break;
          }

        }

      }
*/      

      return $rr;

    }

    /**
     * @return DashBoardClass
     */
    static function restore($name=false,$logo=false) {

      if (isset($_SESSION[self::STORAGEID])) {
        $ses = unserialize($_SESSION[self::STORAGEID]);
        if ($ses) { // ($ses instanceof DashBoardClass) {
          return $ses;        
        }
      }
      return new DashBoardClass($name,$logo);
      
    }

    function remove() {
      $_SESSION[self::STORAGEID] = null;
      unset($_SESSION[self::STORAGEID]);
    }

    function save() {
      $_SESSION[self::STORAGEID] = serialize($this);
    }

    function __destruct() {
      $_SESSION[self::STORAGEID] = serialize($this);
    }

  }

  function getDataScadenza($ean=false,$valassis=false) {

    global $dashboard;

    switch ($dashboard->campagna) {
      case 1:
        return "2021-05-02";
        break;        
      case 2:
        return "2021-06-01";
        break;
      case 3:
        return "2021-11-01";
        break;
      default:
        return "";
        break;      
    }

  }

  function getCampagnaName($id) {

    $lst = [
//      1=>"Coupon REDKEN 1",
//      2=>"LP - Coupon colore",
      3=>"Mentions Test",
    ];
    return $lst[$id] ?? "Campaign ".$id;

  }


  $dashboard = DashBoardClass::restore('Mention Tracker','./img/mentions-logo.png');

  $dashboard->data_start = 0;
  $dashboard->data_end = 0;

  $dashboard->user_avatar = './avatars/'.$admlogin->get('avatar');
  $dashboard->user_name = $admlogin->get('name');

  $camp = isset($_GET['cp']) ? intval($_GET['cp']) : false;

  if ($camp != 3) $camp = 3;

  if ($camp) {
    $camp = max(1,min($camp,CAMPAGNAID_MAX));
    $dashboard->campagna = $camp;
    $dashboard->campagnaname = getCampagnaName($camp);
    $dashboard->save();
  }

  $dashboard->hascodiceunico = false;

  switch ($dashboard->campagna) {

    case 1:
      $dashboard->valorebuono = 10;    
      $dashboard->save();
      break;

    case 2:
      $dashboard->valorebuono = 10;
      $dashboard->save();
      break;

    case 3:
      $dashboard->valorebuono = 10;
      $dashboard->hascodiceunico = true;
      $dashboard->save();
      break;
      
  }

  $campagne_attive = [];
  foreach(range(3,CAMPAGNAID_MAX) as $vv) {
    $campagne_attive[] = [
      'n'=>$vv,
      'd'=>getCampagnaName($vv),
      'cls'=> ($vv == $dashboard->campagna) ? 'active' : ''
    ];
  }

  $campagne_attive = array_reverse($campagne_attive);

  $hasdataentry = ($admlogin->hasLevel('dataentry')) ? '1' : '0';
  $hasreport = ($admlogin->hasLevel('report')) ? '1' : '0';