<?php
chdir(__DIR__);
require_once '../../auth/request_sub_page.php';
chdir(__DIR__);
require_once '../../utils/networking.php';
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
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="row">
                    <div class="col">
                     <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Tracking</li>
                    </ol>
                    </div><!--end col-->
                </div><!--end row-->
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <div class="row" style="height: 95%">
        <div class="col-lg-2">
            <div class="container d-flex align-items-center justify-content-center h-100">
                <div class="row">
                    <div>
                        <div class="text-center">
                            <button class="btn btn-success btn-lg" id="btniniciar" onclick="objMapa.IniciarTracking()">Iniciar</button>
                        </div>

                        <div class="text-center mt-4">
                            <button class="btn btn-danger btn-lg" onclick="objMapa.Stop()">Detener</button>
                        </div>
                    </div>
            </div>
        </div>

    </div>
        <div class="col-lg-10">
            <div id="map"></div>
        </div>

<script>
    objMapa.IniciarMapa();
</script>
</body>


