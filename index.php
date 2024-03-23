<?php
session_start();

// Replace these with your actual app credentials
$clientId = '944463346552351';
$clientSecret = '5054166e9895b4e93359d385965e126e';
$redirectUri = 'https://winkmentions-0e9019ab59d4.herokuapp.com/callback.php';

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
