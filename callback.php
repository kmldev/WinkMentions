<?php
session_start();

// Replace these with your actual app credentials
$clientId = 'YOUR_FACEBOOK_APP_ID';
$clientSecret = 'YOUR_FACEBOOK_APP_SECRET';
$redirectUri = 'YOUR_CALLBACK_URL';

// Check if authorization code is present
if (!isset($_GET['code'])) {
    // Handle error, redirect user to error page
    header('Location: error.html');
    exit();
}

// Exchange authorization code for access token
$code = $_GET['code'];
$tokenUrl = 'https://graph.facebook.com/v12.0/oauth/access_token';
$tokenParams = array(
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
    'redirect_uri' => $redirectUri,
    'code' => $code
);

$curl = curl_init($tokenUrl);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($tokenParams));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($curl);
curl_close($curl);

// Store access token in session
$_SESSION['access_token'] = json_decode($response)->access_token;

// Redirect user to fetch_mentions.php
header('Location: fetch_mentions.php');
exit();
?>
