<?php

use api\Response;
use utils\Functions;

require_once 'vendor/autoload.php';
require_once 'configs.php';
require_once './api/Roles.php';
require_once './api/DataBaseTypes.php';
require_once './api/DataTypes.php';
require_once './api/Response.php';
require_once './api/Request.php';
require_once './api/JWTConfig.php';
require_once './utils/SimpleXLSXGen.php';

$response = new Response();

set_exception_handler(function ($ex) use ($response) {
    $response->addValue('error', [
        'type' => 'internal',
        'error' => (array)$ex,
    ])->printError($ex->getMessage());
});

require_once './utils/Functions.php';
require_once './utils/ServerData.php';
//require_once './utils/Fichas.php';
require_once './utils/Mailing.php';
require_once './database/Connection.php';
require_once './database/AbsDatabase.php';
require_once './database/sql/SQLDatabase.php';
//require_once './database/mongo/MongoDatabase.php';
require_once './endpoints/Crud.php';
require_once './endpoints/EndPoint.php';
(new utils\Functions)->includeDir("./endpoints/src/");
