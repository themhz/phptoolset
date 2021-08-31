<?php

require __DIR__ . '/../vendor/autoload.php';


$client = new Google\Client();
//$client->setAuthConfig('client_credentials.json');
$client->addScope(Google\Service\Drive::DRIVE);
// Your redirect URI can be any registered URI, but in this example
// we redirect back to this same page
$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
$client->setRedirectUri($redirect_uri);

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
}

