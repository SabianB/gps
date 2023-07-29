<?php

use api\Request;
use api\Response;
use endpoints\src\Productos;

chdir(__DIR__);
require_once 'configs.php';
chdir(__DIR__);
global $configs;

require_once 'loader.php';

$request = new Request(array('action' => 'no_value'));
$response = new Response();
if (isset($_GET['code'])) {
    $image = $_GET['code'] ?? '';
    if (!is_dir($configs['paths']['resources']) || !is_readable($configs['paths']['resources'])) {
        $response->printError("Server misconfiguration in resources folder, no permissions found, please contact the software provider");
    }
    $internalUuid = new Productos(new Request([
        "endpoint" => "productos",
        "action" => "obtenerInternalUuid",
        "uuid" => $image
    ]), $response);
    $internalUuid->obtenerInternalUuid();
}
$response->printError('Your petition is not valid', 400);


