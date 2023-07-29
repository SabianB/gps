<?php
chdir(__DIR__);
require_once '../settings/config.php';
chdir(__DIR__);

function serverQuery(string $token, array $request){
    global $config;
    $init = curl_init( $config['serverApi'] );
    curl_setopt($init, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded', 'Cookie: token=' . $token)); // Inject the token into the header
    curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($init, CURLOPT_POSTFIELDS, json_encode($request));
    curl_setopt($init, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($init, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($init);
    $result=preg_replace("/\xEF\xBB\xBF/", "",$result);
    curl_close($init);
    return json_decode($result, true);
}

function serverQueryNoToken(array $request)
{
    global $config;
    $init = curl_init($config['serverApi']);
    curl_setopt($init, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($init, CURLOPT_POSTFIELDS, json_encode($request));
    curl_setopt($init, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($init, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($init);
    $result=preg_replace("/\xEF\xBB\xBF/", "",$result);
    curl_close($init);
    return json_decode($result, true);
}