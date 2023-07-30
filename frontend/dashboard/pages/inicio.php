<?php
chdir(__DIR__);
require_once '../auth/request_sub_page.php';
chdir(__DIR__);
require_once '../utils/networking.php';
chdir(__DIR__);

global $token;
global $config;

$token = $_COOKIE['token'];
$response = serverQuery($token, array(
    'endpoint' => 'Authentication',
    'action' => 'decodeToken',
    'token' => $token
));

?>



<body>

Inicio

</body>
