<?php

use database\Connection;
use api\Request;
use api\Response;
use endpoints\src\Maps;

require_once 'loader.php';
global $response;

function failRequest($code)
{
    http_response_code($code);
    die();
}

global $configs;
$petition = $_SERVER['REQUEST_METHOD'];
if ($petition != 'POST') {
    if (isset($_GET['lat']) && isset($_GET['long'])) {
        (new Maps(new Request([
            'id_user' => 1,
            'latitud' => $_GET['lat'],
            'longitud' => $_GET['long'],
        ]), $response))->RegistrarLugar();
    }
    $response->addValue('data', $configs['app'])->printResponse();
}
$request = @json_decode(file_get_contents('php://input'), true);
if (!$request) {
    // $request = $_POST;
    if (!isset($_POST['request'])) {
        failRequest(400);
        return;
    } else {
        $request = @json_decode($_POST['request'], true);
    }
    //failRequest(400);
}
if (!isset($request['endpoint']) || !isset($request['action'])) {
    failRequest(400);
    return;
}
$endpoint = ucfirst($request['endpoint']);
$action = $request['action'];
$request = new Request($request);
if (!class_exists("endpoints\\src\\$endpoint")) {
    (new Response())->printError('The EndPoint does not exist', 404);
}
$request->allow_cors();
$endpoint = "endpoints\\src\\$endpoint";
// CREAR un CONSTRUCTOR DEL ENDPOINT
$object = new $endpoint($request, $response);
if (!method_exists($object, $action)) {
    (new Response())->printError('The action does not exists for the target endpoint', 404);
}
$availableOperations = (array_diff(get_class_methods($endpoint)
    , get_class_methods("endpoints\\EndPoint")));

unset($availableOperations[array_search('__construct', $availableOperations)]);
$availableOperations = array_values($availableOperations);
if (!in_array($action, $availableOperations)) {
    $response->addValue('error', [
        'type' => 'bad input',
        'error' => [
            'code' => 400
        ]
    ])->printError('The action does not exists for the target endpoint', 400);
}
$realEndpoint = $request->getValue('endpoint');
$request->removeValue('source');
$request->removeValue('operation');
if ($response->cleared()) {
    $response->addValue($realEndpoint, [
        'action' => $object->{$action}()
    ])->printResponse();
}
