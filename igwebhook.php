<?php

  require __DIR__."/_compag.php";

  class cIGMentionEventChangeValue {
      public $media_id;
      public $comment_id;
  
      public function __construct($media_id, $comment_id)
      {
          $this->media_id = $media_id;
          $this->comment_id = $comment_id;
      }
  }

  class cIGMentionEventChange {
      public $field;
      public $value;

      public function __construct($field, cIGMentionEventChangeValue $value) {
          $this->field = $field;
          $this->value = $value;
      }
  }

  class cIGMentionEventEntry {
      public $id;
      public $time;
      public $changes;

      public function __construct($id, $time, $changes) {
          $this->id = $id;
          $this->time = $time;
          $this->changes = $changes;
      }
  }

  class cIGMentionEvents {
      public $entry;
      public $object;

      public function __construct($json_string) {
          $data = json_decode($json_string, true);

          if ($data === null) {
              throw new Exception("Invalid JSON");
          }

          if (!isset($data['entry']) || !isset($data['object'])) {
              throw new Exception("Invalid JSON structure");
          }

          $this->object = $data['object'];
          $this->entry = [];

          foreach ($data['entry'] as $entryData) {
              $id = $entryData['id'];
              $time = $entryData['time'];
              $changes = [];

              foreach ($entryData['changes'] as $change) {
                  $field = $change['field'];
                  $valueData = $change['value'];
                  $value = new cIGMentionEventChangeValue($valueData['media_id'], $valueData['comment_id']);
                  $changes[] = new cIGMentionEventChange($field, $value);
              }

              $this->entry[] = new cIGMentionEventEntry($id, $time, $changes);
          }
      }
  }

  $hubmode = $_GET["hub_mode"] ?? "";
  if ($hubmode) {
    $hubchallenge = $_GET["hub_challenge"] ?? "";
    $hubverifytoken = $_GET["hub_verify_token"] ?? "";
    $log->add(date("Y-m-d H:i:s")." [".$_SERVER['REMOTE_ADDR']."] challenge:".json_encode($_GET),"###_igwebhook.log");
    die ($hubchallenge);
  }

  //$body = file_get_contents('php://input');
  $body = '{"entry": [{"id": "0", "time": 1708943384, "changes": [{"field": "mentions", "value": {"media_id": "17887498072083520", "comment_id": "17887498072083520"}}]}], "object": "instagram"}';
  if (!$body) die();

  $log->add(date("Y-m-d H:i:s")." [".$_SERVER['REMOTE_ADDR']."] mention:".$body,"###_igwebhook.log");

  try {

    $events = new cIGMentionEvents($body);

    foreach($events->entry as $entry) {
      $time = $entry->time;
      foreach ($entry->changes as $change) {
        /** @var \cIGMentionEventChange $change */
        $mediaid = $change->value->media_id;
        $commentid = $change->value->comment_id;
        file_put_contents(__DIR__."/db/".date("ymd")."_table_mentions.csv",implode(",",[$mediaid,$commentid,$time])."\n",FILE_APPEND);
      }    
    }


  } catch (Exception $e) {
    
    $log->error($e,"###_webhooks-error.log");

  }
