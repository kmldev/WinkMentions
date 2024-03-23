<?php
session_start();

// Endpoint to receive webhook notifications for Instagram mentions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Extract data from the received notification
    $urlMedia = $data['media_url'];
    $caption = $data['caption'];
    $username = $data['username'];

    // Handle the notification (e.g., store it in a database, send an email notification)
    // Replace this with your actual logic to handle the webhook notification
}

$clientId = '807919424489341';
$clientSecret = 'f932eb1a52c50ddede29bba0328a27f2';
$redirectUri = 'https://winkmentions-0e9019ab59d4.herokuapp.com/callback.php';
$authUrl = 'https://api.instagram.com/oauth/authorize';
$tokenUrl = 'https://api.instagram.com/oauth/access_token';

if (isset($_GET['code'])) {
    // Exchange authorization code for access token
    $code = $_GET['code'];
    $tokenData = array(
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'grant_type' => 'authorization_code',
        'redirect_uri' => $redirectUri,
        'code' => $code
    );

    $curl = curl_init($tokenUrl);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($tokenData));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($curl);
    curl_close($curl);

    // Store access token in session (you might want to store it in a database)
    $_SESSION['access_token'] = json_decode($response)->access_token;

    // Redirect to frontend or wherever you want to handle the access token
    header('Location: mentions.html');
    exit();
}

// Redirect users to Instagram authorization URL
$authParams = array(
    'client_id' => $clientId,
    'redirect_uri' => $redirectUri,
    'scope' => 'user_profile,user_media',
    'response_type' => 'code'
);

$authUrl = $authUrl . '?' . http_build_query($authParams);
header('Location: ' . $authUrl);
?>
