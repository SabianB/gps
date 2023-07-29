<?php
chdir(__DIR__);
include_once '../utils/networking.php';
function OnTokenFail()
{
    setcookie("token", "", time() - 3600, '/');
    header('Location: ../login/');
    die();
}
$token = '';
if (isset($_COOKIE['token'])) {
    $token = $_COOKIE['token'];
    $response = serverQuery($token, array(
        'endpoint' => 'Authentication',
        'action' => 'decodeToken',
        'token' => $token
    ));
    if (!isset($response['status'])) {
        OnTokenFail();
    } else {
        if (!$response['status']) {
            OnTokenFail();
        }
    }
} else {
    OnTokenFail();
}
