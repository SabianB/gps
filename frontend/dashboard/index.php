<?php
chdir(__DIR__);
require_once 'auth/request_page.php';
chdir(__DIR__);
require_once 'utils/networking.php';
chdir(__DIR__);

global $config;
global $token;


$response = serverQueryNoToken(array(
    'endpoint' => 'Authentication',
    'action' => 'decodeToken',
    'token' => $token
));
//if ($response['status']) {
//    $data = $response['data']['user_data'];
//    $nombres = $data['nombres'] . ' ' . $data['apellido_paterno'] . ' ' . $data['apellido_materno'];
//}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8"/>
    <title>GPS Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="">

    <!-- jvectormap -->
    <link href="../plugins/jvectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet">

    <!-- App css -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/css/metisMenu.min.css" rel="stylesheet" type="text/css"/>
    <link href="../plugins/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/css/app.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="../plugins/sweet-alert2/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.3/css/fileinput.min.css" media="all"
          rel="stylesheet" type="text/css"/>

    <!-- DataTables -->
    <link href="../plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
    <link href="../plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.min.css" crossorigin="anonymous">

    <!--Leaflet css-->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

    <style>

        html, body {
            height: 100%;
            padding: 0;
            margin: 0;
        }
        #map {
            /* configure the size of the map */
            width: 100%;
            height: 100%;
        }
    </style>
</head>

<body class="">

<?php chdir(__DIR__); require_once 'components/sidebar.php' ?>
<div class="page-wrapper">
    <?php chdir(__DIR__); require_once 'components/topbar.php' ?>
    <!-- Page Content-->
    <div class="page-content">
        <div class="container-fluid" style="height: 85VH;" id="web_content">

        </div><!-- container -->

        <footer class="footer text-center text-sm-start">
            &copy;
            <script>
                document.write(new Date().getFullYear())
            </script>
        </footer><!--end footer-->
    </div>
    <!-- end page content -->
</div>
<!-- end page-wrapper -->


<!-- jQuery  -->
<script src="../assets/js/jquery.min.js"></script>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/metismenu.min.js"></script>
<script src="../assets/js/waves.js"></script>
<script src="../assets/js/feather.min.js"></script>
<script src="../assets/js/simplebar.min.js"></script>
<script src="../assets/js/moment.js"></script>
<script src="../plugins/sweet-alert2/sweetalert2.min.js"></script>

<!-- App js -->
<script src="../assets/js/app.js"></script>
<script src="../js/config.js"></script>
<script src="../js/empiric_frontend_framework.js"></script>
<script src="../plugins/file-input/fileinput.min.js"></script>
<script src="../plugins/xlsx/xlsx_0.17.4_xlsx.full.min.js"></script>
<script src="../plugins/xlsx/xlsx.bundle.js"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<!-- Required datatable js -->
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Pages js -->
<script src="js/pages/mapa.js"></script>
<script src="js/pages/estadisticas/estacionamiento.js"></script>
<script src="js/pages/estadisticas/horarios.js"></script>
<script src="js/pages/estadisticas/velocidad.js"></script>
<script src="js/pages/ciudades/ciudades.js"></script>


<!--SheetJS-->



<script>
    window.addEventListener('popstate', function (event) {
    });
    window.addEventListener('load', function (event) {
        setPage(window.location.hash);
    });
    window.addEventListener('hashchange', function (event) {
        if (event.newURL === event.oldURL) {
            return;
        }
        setPage(window.location.hash);
    }, false);

    function setActive(currentHash) {
        let elements = document.querySelectorAll('#sidebar-menu a');
        for (let i = 0, len = elements.length; i < len; i++) {
            elements[i].setAttribute('class', 'nav-main-link');
            if (elements[i].getAttribute('href') === currentHash) {
                elements[i].setAttribute('class', 'nav-main-link active');
            }
        }
    }

    function setPage(current_hash) {
        let urlParams = new URL(window.location.href.replace(/#/g, "?"));
        let urlHash = urlParams.search.replace('?', '').trim();
        current_hash = current_hash.split('=')[0];
        let hash = current_hash.replace('#', '');
        let hashValue = urlParams.searchParams.get(hash);
        let fullUrlParams = `?${urlHash}`;
        switch (current_hash) {
            case "#logout":
                delete_cookie('token');
                loadPage('pages/inicio.php');
                //window.location.reload();
                break;
            case "#dashboard":
                loadPage('pages/inicio.php');
                break;
            case "#historial":
                loadPage('pages/historial/index.php');
                break;
            case "#tracking":
                loadPage('pages/tracking/index.php');
                break;
            case "#estacionamiento":
                loadPage('pages/estadisticas/estacionamiento.php');
                break;
            case "#horarios_salidas":
                loadPage('pages/estadisticas/horarios.php');
                break;
            case "#velocidad_promedio":
                loadPage('pages/estadisticas/velocidad.php');
                break;
            case "#ciudades":
                loadPage('pages/ciudades/ciudades.php');
                break;
            default:
                window.location.hash = "dashboard";
                break;
        }
        setActive(current_hash);
    }
</script>

</body>

</html>