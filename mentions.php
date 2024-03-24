<?php

require './_compag.php';
require './verificaadmin.inc.php';
require './dashboard.utils.php';

require './meta.setup.php';

use Instagram\FacebookLogin\FacebookLogin;
use Instagram\AccessToken\AccessToken;
use Instagram\User\Tags;

require './libs/instabasic.config.php';
require './libs/instabasic.sdk.php';

if (!$winkmentionsapp->validateAccessToken()) {
    cambiaPagina("dashboard.php");
    exit;
}

$accesstoken = $winkmentionsapp->getAccessToken();

$iguser = array_key_first($_GET);

$config = array( // instantiation config params
    'user_id' => $iguser,
    'access_token' => $accesstoken,
);

// Fetch user media
$instaBasic = new InstagramBasic($accesstoken); // Assuming this is the class for handling Instagram Basic Display API calls
$userMedia = $instaBasic->getUserMedia($iguser);

// Process media for mentions
$mentions = array();
foreach ($userMedia as $media) {
    // Extract mentions from captions
    $caption = $media['caption'] ?? '';
    preg_match_all('/@([^\s]+)/', $caption, $captionMentions);
    $mentions = array_merge($mentions, $captionMentions[1]);

    // Extract mentions from comments
    $comments = $media['comments'] ?? [];
    foreach ($comments as $comment) {
        $commentText = $comment['text'] ?? '';
        preg_match_all('/@([^\s]+)/', $commentText, $commentMentions);
        $mentions = array_merge($mentions, $commentMentions[1]);
    }
}

// Now $mentions array contains all the mentions from user's posts and reels
// You can use $mentions array as per your requirement

$filters = json_encode(
    ['mode'=>'pdv']
);

$TBS = pagina();
$TBS->MergeBlock('cc',$campagne_attive);
$TBS->Show();
?>

