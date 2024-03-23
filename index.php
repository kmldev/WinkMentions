<?php
session_start();

// Replace these with your actual app credentials
$clientId = 'YOUR_FACEBOOK_APP_ID';
$clientSecret = 'YOUR_FACEBOOK_APP_SECRET';
$redirectUri = 'YOUR_CALLBACK_URL';

// Check if user is already authenticated
if (isset($_SESSION['access_token'])) {
    header('Location: fetch_mentions.php');
    exit();
}

// Redirect user to Facebook for authentication
$authUrl = 'https://www.facebook.com/v12.0/dialog/oauth';
$authParams = array(
    'client_id' => $clientId,
    'redirect_uri' => $redirectUri,
    'scope' => 'instagram_basic',
    'response_type' => 'code'
);
$authUrl = $authUrl . '?' . http_build_query($authParams);
header('Location: ' . $authUrl);
exit();
?>
