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


<div class="row justify-content-center align-items-center" style="min-height: 85%;">
    <div class="col-md-6 text-center">
        <img src="https://univercimas.com/wp-content/uploads/2021/05/Logo-de-la-Universidad-Estatal-del-Sur-de-Manabi-UNESUM.jpg" alt="Imagen" class="img-fluid">
    </div>
</div>


</body>
