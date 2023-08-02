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
<body>
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="row">
                <div class="col">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Estadisticas</a></li>
                        <li class="breadcrumb-item active">Estacionamiento</li>
                    </ol>
                </div><!--end col-->
            </div><!--end row-->
        </div><!--end page-title-box-->
    </div><!--end col-->
</div>
<div class="row" style="height: 95%">
    <div class="col-lg-2">
        <div class="container mt-5">
            <div class="row">
                <div class="col text-center">
                    <label for="fechaInicio" class="form-label">Fecha de inicio</label>
                    <input type="datetime-local" class="form-control" id="fechaInicio">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col text-center">
                    <label for="fechaFin" class="form-label">Fecha de fin</label>
                    <input type="datetime-local" class="form-control" id="fechaFin" value="<?php echo gmdate('Y-m-d\TH:i', time() - 5 * 3600); ?>">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col text-center">
                    <label for="tiempo" class="form-label">Tiempo minimo estacionado (Minutos)</label>
                    <input type="number" class="form-control" id="tiempo" value="5">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col text-center">
                    <button class="btn btn-success" onclick="objEst.search(fechaini, fechafin, getElementById('tiempo').value, false, true)">Buscar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-10">
        <div >
            <div class="card mb3">
                <div class="card-body">
                    <div id="containerTableItems" class="table-responsive">
                        <table id="parktable" class="table table-bordered dt-responsive nowrap"
                               style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                            <tr>
                                <th>Latitud</th>
                                <th>Longitud</th>
                                <th>Inicio del estacionamiento</th>
                                <th>Fin del estacionamiento</th>
                                <th>Ver en el mapa</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Sin registros</td>
                                <td>Sin registros</td>
                                <td>Sin registros</td>
                                <td>Sin registros</td>
                                <td>Sin registros</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>


</div>

<div class="modal fade" id="estacionamientoModal" tabindex="-1" aria-labelledby="exampleModalFullscreenLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalFullscreenLabel">Full screen modal</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="map"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-soft-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    initDataTable('parktable');
    objMapa.IniciarMapa();

    var fechaInput = document.getElementById("fechaInicio");
    var fechaInputFin = document.getElementById("fechaFin");
    var fechaini;
    var fechafin;
    var fecha2 = new Date(fechaInputFin.value);
    fecha2.setHours(fecha2.getHours() - 5);
    var formattedDate2 = fecha2.toISOString().substring(0, 19).replace("T", " ");
    fechafin = formattedDate2;
    fechaInput.addEventListener("input", function() {
        var fecha = new Date(this.value);
        fecha.setHours(fecha.getHours() - 5);
        var formattedDate = fecha.toISOString().substring(0, 19).replace("T", " ");
        this.value = formattedDate;
        fechaini = formattedDate;
    });
    fechaInputFin.addEventListener("input", function() {
        fecha2 = new Date(this.value);
        fecha2.setHours(fecha2.getHours() - 5);
        formattedDate2 = fecha2.toISOString().substring(0, 19).replace("T", " ");
        this.value = formattedDate2;
        fechafin = formattedDate2;
    });

</script>



</body>