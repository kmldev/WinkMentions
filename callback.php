<?php

require_once 'config.php';

if (isset($_GET['code'])) {
    // Exchange authorization code for access token
    $accessTokenUrl = 'https://api.instagram.com/oauth/access_token';

    $params = [
        'client_id' => CLIENT_ID,
        'client_secret' => CLIENT_SECRET,
        'redirect_uri' => REDIRECT_URI,
        'code' => $_GET['code'],
        'grant_type' => 'authorization_code'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $accessTokenUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (isset($data['access_token'])) {
        // Store the access token securely (e.g., in a session or database) for future use
        session_start();
        $_SESSION['access_token'] = $data['access_token'];
        header('Location: profile.php');
        exit;
    } else {
        echo 'Error exchanging authorization code for access token';
    }
} else {
    echo 'Authorization code not found';
}
