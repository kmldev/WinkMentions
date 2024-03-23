<?php
session_start();

// Check if user is authenticated
if (!isset($_SESSION['access_token'])) {
    // Redirect user to index.php for authentication
    header('Location: index.php');
    exit();
}

// Fetch user mentions from Instagram Basic Display API
// Replace this with your actual API call to fetch user mentions

// Sample API call
$userAccessToken = $_SESSION['access_token'];
$instagramApiUrl = 'https://graph.instagram.com/me/media?fields=id,media_type,media_url,caption&access_token=' . $userAccessToken;
$response = file_get_contents($instagramApiUrl);
$mentions = json_decode($response, true);

// Display fetched mentions (you can format it as needed)
echo '<pre>';
print_r($mentions);
echo '</pre>';
?>
